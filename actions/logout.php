<?php
/**
 * logout.php
 * Encerra a sessao do usuario logado (professor ou aluno).
 */
require_once __DIR__ . '/../includes/Auth.php';

Auth::start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Auth::verifyCsrf($_POST['csrf_token'] ?? null)) {
    header('Location: /index.php');
    exit;
}

Auth::logout();
header('Location: /index.php');
exit;
