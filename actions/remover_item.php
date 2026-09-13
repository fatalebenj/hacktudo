<?php
/**
 * remover_item.php
 * Remove um item (post-it) do mural. O professor dono pode remover
 * qualquer item; o aluno so pode remover os itens que ele mesmo publicou.
 */
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Mural.php';
require_once __DIR__ . '/../includes/Database.php';

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
$itemId = (int) ($_POST['item_id'] ?? 0);
$mural = $codigo !== '' ? Mural::buscarPorCodigo($codigo) : null;

if (!$mural || $itemId <= 0) {
    header('Location: /index.php?erro=' . urlencode('Mural nao encontrado.'));
    exit;
}

if (!Auth::verifyCsrf($_POST['csrf_token'] ?? null)) {
    header('Location: /pages/mural.php?codigo=' . urlencode($codigo) . '&erro=' . urlencode('Sessao expirada, tente novamente.'));
    exit;
}

$usuario = Auth::usuarioAtual();
$perfil = Auth::perfilAtual();

$ehDono = $perfil === 'professor' && (int) $mural['professor_id'] === (int) $usuario['id'];
$ehAlunoDoMural = $perfil === 'aluno' && Auth::muralIdAtual() === (int) $mural['id'];

if (!$ehDono && !$ehAlunoDoMural) {
    header('Location: /index.php?erro=acesso_negado');
    exit;
}

if ($ehDono) {
    // Professor pode remover qualquer item do proprio mural.
    Mural::removerItem($itemId, (int) $mural['id']);
} else {
    // Aluno so pode remover um item se for o autor dele.
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('SELECT autor_tipo, autor_id FROM mural_itens WHERE id = ? AND mural_id = ?');
    $stmt->execute([$itemId, (int) $mural['id']]);
    $item = $stmt->fetch();

    if ($item && $item['autor_tipo'] === 'aluno' && (int) $item['autor_id'] === (int) $usuario['id']) {
        Mural::removerItem($itemId, (int) $mural['id']);
    }
}

header('Location: /pages/mural.php?codigo=' . urlencode($codigo));
exit;
