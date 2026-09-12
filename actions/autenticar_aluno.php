<?php
/**
 * autenticar_aluno.php
 * Processa o login do aluno com validacao real no banco de dados.
 */
require_once __DIR__ . '/../includes/Auth.php';

Auth::start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php?forcar=aluno');
    exit;
}

if (!Auth::verifyCsrf($_POST['csrf_token'] ?? null)) {
    header('Location: /index.php?forcar=aluno&erro=' . urlencode('Sessao expirada, tente novamente.'));
    exit;
}

$nome = $_POST['nome'] ?? '';
$codigo = $_POST['codigo'] ?? '';


[$ok, $mensagemErro] = Auth::autenticarAluno($nome, $codigo);

if ($ok) {
    header('Location: /pages/painel_aluno.php');
    exit;
}

header('Location: /index.php?forcar=aluno&erro=' . urlencode($mensagemErro));
exit;
