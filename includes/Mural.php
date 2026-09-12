<?php
/**
 * Mural.php
 *
 * Logica de criacao e consulta de murais.
 * Cada mural pertence a um professor e tem um codigo alfanumerico unico
 * que os alunos usam para entrar (junto com o proprio nome).
 */

require_once __DIR__ . '/Database.php';

class Mural
{
    private const TAMANHO_CODIGO = 6;
    // Sem caracteres ambiguos (0/O, 1/I/L) para facilitar digitar o codigo.
    private const ALFABETO_CODIGO = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    /**
     * Cria um novo mural para o professor informado.
     * Retorna [true, mural] em caso de sucesso ou [false, mensagemErro].
     */
    public static function criar(int $professorId, string $titulo): array
    {
        $titulo = trim($titulo);

        if ($titulo === '') {
            return [false, 'Informe um titulo para o mural.'];
        }
        if (strlen($titulo) > 100) {
            return [false, 'O titulo do mural pode ter no maximo 100 caracteres.'];
        }

        $pdo = Database::getConnection();
        $codigo = self::gerarCodigoUnico($pdo);

        $stmt = $pdo->prepare('INSERT INTO murais (professor_id, titulo, codigo) VALUES (?, ?, ?)');
        $stmt->execute([$professorId, $titulo, $codigo]);

        $mural = self::buscarPorCodigo($codigo);

        return [true, $mural];
    }

    /**
     * Gera um codigo alfanumerico aleatorio (ex.: A3F9K1) garantindo
     * que ele ainda nao existe na tabela de murais.
     */
    private static function gerarCodigoUnico(PDO $pdo): string
    {
        do {
            $codigo = self::gerarCodigo();
            $stmt = $pdo->prepare('SELECT 1 FROM murais WHERE codigo = ?');
            $stmt->execute([$codigo]);
            $existe = (bool) $stmt->fetchColumn();
        } while ($existe);

        return $codigo;
    }

    private static function gerarCodigo(): string
    {
        $alfabeto = self::ALFABETO_CODIGO;
        $max = strlen($alfabeto) - 1;
        $codigo = '';

        for ($i = 0; $i < self::TAMANHO_CODIGO; $i++) {
            $codigo .= $alfabeto[random_int(0, $max)];
        }

        return $codigo;
    }

    public static function buscarPorCodigo(string $codigo): ?array
    {
        $codigo = strtoupper(trim($codigo));

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM murais WHERE codigo = ?');
        $stmt->execute([$codigo]);
        $mural = $stmt->fetch();

        return $mural ?: null;
    }

    public static function buscarPorId(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM murais WHERE id = ?');
        $stmt->execute([$id]);
        $mural = $stmt->fetch();

        return $mural ?: null;
    }

    /** Lista os murais criados por um professor, mais recentes primeiro. */
    public static function listarPorProfessor(int $professorId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM murais WHERE professor_id = ? ORDER BY criado_em DESC');
        $stmt->execute([$professorId]);

        return $stmt->fetchAll();
    }

    /** Conta quantos alunos ja entraram em um mural. */
    public static function contarAlunos(int $muralId): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM alunos WHERE mural_id = ?');
        $stmt->execute([$muralId]);

        return (int) $stmt->fetchColumn();
    }
}
