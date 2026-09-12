<?php
/**
 * autenticar_professor.php
 * Placeholder de processamento do login do professor.
 * Aqui entraria a validacao real (banco de dados, hash de senha, sessao, etc.)
 */
session_start();

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// TODO: validar credenciais no banco de dados
if ($email !== '' && $senha !== '') {
    $_SESSION['perfil'] = 'professor';
    $_SESSION['email']  = $email;
    header('Location: /pages/painel_professor.php');
    exit;
}

header('Location: /index.php?erro=credenciais_invalidas');
exit;
