<?php
/**
 * criar_mural.php
 * Cria um novo mural para o professor logado, gera um codigo unico
 * e leva o professor direto para a pagina do mural recem-criado.
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
$titulo = $_POST['titulo'] ?? '';

[$ok, $resultado] = Mural::criar((int) $usuario['id'], $titulo);

if (!$ok) {
    header('Location: /pages/painel_professor.php?erro=' . urlencode($resultado));
    exit;
}

$mural = $resultado;
header('Location: /pages/mural.php?codigo=' . urlencode($mural['codigo']));
exit;
