<?php
/**
 * autenticar_aluno.php
 * Placeholder de processamento do login do aluno.
 * Aqui entraria a validacao real (banco de dados, hash de senha, sessao, etc.)
 */
session_start();

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// TODO: validar credenciais no banco de dados
if ($email !== '' && $senha !== '') {
    $_SESSION['perfil'] = 'aluno';
    $_SESSION['email']  = $email;
    header('Location: /pages/painel_aluno.php');
    exit;
}

header('Location: /index.php?erro=credenciais_invalidas');
exit;
