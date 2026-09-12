<?php
/**
 * Auth.php
 *
 * Sistema de autenticacao real para Quackdro.
 * Cobre professores (login por e-mail) e alunos (login por codigo do mural).
 *
 * Recursos:
 *  - Senhas com password_hash() / password_verify() (bcrypt)
 *  - Sessoes seguras (cookie httponly, id regenerado no login)
 *  - Protecao CSRF em todos os formularios
 *  - Bloqueio temporario apos varias tentativas erradas (anti-bruteforce simples)
 */

require_once __DIR__ . '/Database.php';

class Auth
{
    private const MAX_TENTATIVAS = 5;
    private const BLOQUEIO_MINUTOS = 5;

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    // ---------------------------------------------------------------
    // CSRF
    // ---------------------------------------------------------------

    public static function csrfToken(): string
    {
        self::start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function csrfField(): string
    {
        $token = htmlspecialchars(self::csrfToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    public static function verifyCsrf(?string $token): bool
    {
        self::start();
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    // ---------------------------------------------------------------
    // Rate limiting simples por identificador (e-mail ou codigo) + perfil
    // ---------------------------------------------------------------

    private static function estaBloqueado(string $identificador, string $perfil): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT bloqueado_ate FROM login_attempts WHERE identificador = ? AND perfil = ?');
        $stmt->execute([$identificador, $perfil]);
        $row = $stmt->fetch();

        if (!$row || !$row['bloqueado_ate']) {
            return false;
        }

        return strtotime($row['bloqueado_ate']) > time();
    }

    private static function registrarFalha(string $identificador, string $perfil): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT tentativas FROM login_attempts WHERE identificador = ? AND perfil = ?');
        $stmt->execute([$identificador, $perfil]);
        $row = $stmt->fetch();

        $tentativas = ($row['tentativas'] ?? 0) + 1;
        $bloqueadoAte = null;

        if ($tentativas >= self::MAX_TENTATIVAS) {
            $bloqueadoAte = date('Y-m-d H:i:s', time() + self::BLOQUEIO_MINUTOS * 60);
            $tentativas = 0; // reseta contador apos aplicar o bloqueio
        }

        $stmt = $pdo->prepare('
            INSERT INTO login_attempts (identificador, perfil, tentativas, bloqueado_ate, atualizado_em)
            VALUES (:id, :perfil, :tentativas, :bloqueado_ate, datetime(\'now\'))
            ON CONFLICT(identificador, perfil) DO UPDATE SET
                tentativas = :tentativas,
                bloqueado_ate = :bloqueado_ate,
                atualizado_em = datetime(\'now\')
        ');
        $stmt->execute([
            'id' => $identificador,
            'perfil' => $perfil,
            'tentativas' => $tentativas,
            'bloqueado_ate' => $bloqueadoAte,
        ]);
    }

    private static function limparTentativas(string $identificador, string $perfil): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM login_attempts WHERE identificador = ? AND perfil = ?');
        $stmt->execute([$identificador, $perfil]);
    }

    // ---------------------------------------------------------------
    // Professores
    // ---------------------------------------------------------------

    public static function registrarProfessor(string $nome, string $email, string $senha): array
    {
        $nome = trim($nome);
        $email = trim(strtolower($email));

        if ($nome === '' || $email === '' || $senha === '') {
            return [false, 'Preencha todos os campos.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [false, 'E-mail invalido.'];
        }
        if (strlen($senha) < 6) {
            return [false, 'A senha precisa ter pelo menos 6 caracteres.'];
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id FROM professores WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return [false, 'Ja existe uma conta com este e-mail.'];
        }

        $hash = password_hash($senha, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO professores (nome, email, senha_hash) VALUES (?, ?, ?)');
        $stmt->execute([$nome, $email, $hash]);

        return [true, null];
    }

    public static function autenticarProfessor(string $email, string $senha): array
    {
        $email = trim(strtolower($email));

        if ($email === '' || $senha === '') {
            return [false, 'Informe e-mail e senha.'];
        }

        if (self::estaBloqueado($email, 'professor')) {
            return [false, 'Muitas tentativas erradas. Tente novamente em alguns minutos.'];
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM professores WHERE email = ?');
        $stmt->execute([$email]);
        $professor = $stmt->fetch();

        if (!$professor || !password_verify($senha, $professor['senha_hash'])) {
            self::registrarFalha($email, 'professor');
            return [false, 'E-mail ou senha invalidos.'];
        }

        self::limparTentativas($email, 'professor');
        self::iniciarSessao('professor', (int) $professor['id'], $professor['nome'], $professor['email']);

        return [true, null];
    }

    // ---------------------------------------------------------------
    // Alunos (login por nome + codigo do mural)
    // ---------------------------------------------------------------

    public static function autenticarAluno(string $nome, string $codigo): array
    {
        $nome = trim($nome);
        $codigo = trim($codigo);

        if ($nome === '' || $codigo === '') {
            return [false, 'Informe nome e codigo do mural.'];
        }

        if (self::estaBloqueado($codigo, 'aluno')) {
            return [false, 'Muitas tentativas erradas. Tente novamente em alguns minutos.'];
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM alunos WHERE codigo_mural = ?');
        $stmt->execute([$codigo]);
        $aluno = $stmt->fetch();

        self::limparTentativas($codigo, 'aluno');
        self::iniciarSessao('aluno', (int) $aluno['id'], $aluno['nome'], null);

        return [true, null];
    }

    // ---------------------------------------------------------------
    // Sessao
    // ---------------------------------------------------------------

    private static function iniciarSessao(string $perfil, int $id, string $nome, ?string $email): void
    {
        self::start();
        session_regenerate_id(true);

        $_SESSION['perfil'] = $perfil;
        $_SESSION['user_id'] = $id;
        $_SESSION['nome'] = $nome;
        $_SESSION['email'] = $email;
        $_SESSION['logado_em'] = time();
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie('PHPSESSID', '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }

    public static function usuarioLogado(): bool
    {
        self::start();
        return !empty($_SESSION['perfil']) && !empty($_SESSION['user_id']);
    }

    public static function perfilAtual(): ?string
    {
        self::start();
        return $_SESSION['perfil'] ?? null;
    }

    public static function usuarioAtual(): ?array
    {
        self::start();
        if (!self::usuarioLogado()) {
            return null;
        }
        return [
            'perfil' => $_SESSION['perfil'],
            'id' => $_SESSION['user_id'],
            'nome' => $_SESSION['nome'],
            'email' => $_SESSION['email'] ?? null,
        ];
    }

    /**
     * Garante que o visitante esteja logado com o perfil esperado.
     * Caso contrario, redireciona para a home com mensagem de erro.
     */
    public static function exigirPerfil(string $perfilEsperado): void
    {
        self::start();
        if (!self::usuarioLogado() || self::perfilAtual() !== $perfilEsperado) {
            header('Location: /index.php?erro=acesso_negado');
            exit;
        }
    }
}
