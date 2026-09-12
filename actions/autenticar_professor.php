<?php
/**
 * autenticar_professor.php
 * Processa o login do professor com validacao real no banco de dados.
 */
require_once __DIR__ . '/../includes/Auth.php';

Auth::start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php?forcar=professor');
    exit;
}

if (!Auth::verifyCsrf($_POST['csrf_token'] ?? null)) {
    header('Location: /index.php?forcar=professor&erro=' . urlencode('Sessao expirada, tente novamente.'));
    exit;
}

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

[$ok, $mensagemErro] = Auth::autenticarProfessor($email, $senha);

if ($ok) {
    header('Location: /pages/painel_professor.php');
    exit;
}

header('Location: /index.php?forcar=professor&erro=' . urlencode($mensagemErro));
exit;
