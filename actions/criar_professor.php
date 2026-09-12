<?php
/**
 * criar_professor.php
 * Processa o cadastro de um novo professor.
 */
require_once __DIR__ . '/../includes/Auth.php';

Auth::start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/cadastro_prof.php');
    exit;
}

if (!Auth::verifyCsrf($_POST['csrf_token'] ?? null)) {
    header('Location: /pages/cadastro_prof.php?erro=' . urlencode('Sessao expirada, tente novamente.'));
    exit;
}

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';
$confirmar = $_POST['confirmar_senha'] ?? '';

if ($senha !== $confirmar) {
    header('Location: /pages/cadastro_prof.php?erro=' . urlencode('As senhas nao coincidem.'));
    exit;
}

[$ok, $mensagemErro] = Auth::registrarProfessor($nome, $email, $senha);

if ($ok) {
    header('Location: /index.php?forcar=professor&sucesso=' . urlencode('Conta criada com sucesso! Faca login.'));
    exit;
}

header('Location: /pages/cadastro_prof.php?erro=' . urlencode($mensagemErro));
exit;
