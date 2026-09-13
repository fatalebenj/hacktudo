<?php
/**
 * deletar_mural.php
 * Apaga um mural do professor logado. So o dono do mural pode apagar.
 * Alunos e post-its desse mural somem junto (ON DELETE CASCADE no banco).
 */
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Mural.php';

Auth::start();
Auth::exigirPerfil('professor');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/painel_professor.php');
    exit;
}

if (!Auth::verifyCsrf($_POST['csrf_token'] ?? null)) {
    header('Location: /pages/painel_professor.php?erro=' . urlencode('Sessao expirada, tente novamente.'));
    exit;
}

$usuario = Auth::usuarioAtual();
$muralId = (int) ($_POST['mural_id'] ?? 0);

if ($muralId <= 0) {
    header('Location: /pages/painel_professor.php?erro=' . urlencode('Mural invalido.'));
    exit;
}

$mural = Mural::buscarPorId($muralId);

if (!$mural || (int) $mural['professor_id'] !== (int) $usuario['id']) {
    // Ou o mural nao existe, ou nao pertence a este professor.
    // Em ambos os casos, nao revela detalhes: so nega o acesso.
    header('Location: /index.php?erro=acesso_negado');
    exit;
}

Mural::remover($muralId, (int) $usuario['id']);

header('Location: /pages/painel_professor.php?sucesso=' . urlencode('Mural excluido.'));
exit;
