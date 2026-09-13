<?php
/**
 * postar_item.php
 * Publica um novo item (post-it) no mural. Tanto professor (dono) quanto
 * aluno (que entrou com o codigo) podem publicar, desde que tenham acesso
 * ao mural informado.
 */
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Mural.php';

Auth::start();

if (!Auth::usuarioLogado()) {
    header('Location: /index.php?erro=acesso_negado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$codigo = $_POST['codigo'] ?? '';
$mural = $codigo !== '' ? Mural::buscarPorCodigo($codigo) : null;

if (!$mural) {
    header('Location: /index.php?erro=' . urlencode('Mural nao encontrado.'));
    exit;
}

if (!Auth::verifyCsrf($_POST['csrf_token'] ?? null)) {
    header('Location: /pages/mural.php?codigo=' . urlencode($codigo) . '&erro=' . urlencode('Sessao expirada, tente novamente.'));
    exit;
}

$usuario = Auth::usuarioAtual();
$perfil = Auth::perfilAtual();

$acessoPermitido = false;
if ($perfil === 'professor' && (int) $mural['professor_id'] === (int) $usuario['id']) {
    $acessoPermitido = true;
} elseif ($perfil === 'aluno' && Auth::muralIdAtual() === (int) $mural['id']) {
    $acessoPermitido = true;
}

if (!$acessoPermitido) {
    header('Location: /index.php?erro=acesso_negado');
    exit;
}

$conteudo = $_POST['conteudo'] ?? '';
$cor = $_POST['cor'] ?? '#f5f2e9';

[$ok, $erro] = Mural::adicionarItem(
    (int) $mural['id'],
    $perfil,
    (int) $usuario['id'],
    $usuario['nome'],
    $conteudo,
    $cor
);

if (!$ok) {
    header('Location: /pages/mural.php?codigo=' . urlencode($codigo) . '&erro=' . urlencode($erro));
    exit;
}

header('Location: /pages/mural.php?codigo=' . urlencode($codigo));
exit;
