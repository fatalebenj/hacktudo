<?php
$pageTitle = 'EduPlataforma - Início';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'EduPlataforma'); ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="device-<?= htmlspecialchars($device ?? 'desktop'); ?>">

<header class="site-header">
    <div class="container header-inner">
        <a href="/index.php" class="logo">📚 EduPlataforma</a>

        <nav class="main-nav">
            <ul>
                <li><a href="/index.php">Início</a></li>
                <li><a href="/pages/sobre.php">Sobre</a></li>
            </ul>
        </nav>
    </div>
</header>
<main class="container main-content"></main>

<section class="hero">
    <h1>Bem-vindo à EduPlataforma</h1>
    <p>Ensine e aprenda em conjunto</p>
</section>

<div class="login-box login-professor">
    <h2>Cadastro do Professor</h2>
    <p class="subtitle">Crie sua conta e comece a ensinar</p>

    <form action="/actions/criar_professor.php" method="POST" class="login-form">
        <div class="form-group">
            <label for="email_professor">E-mail</label>
            <input type="email" id="email_professor" name="email" placeholder="professor@escola.com" required>
        </div>

        <div class="form-group">
            <label for="nome_professor">Nome</label>
            <input type="text" id="nome_professor" name="nome" placeholder="Seu nome" required>
        </div>

        <div class="form-group">
            <label for="senha_professor">Senha</label>
            <input type="password" id="senha_professor" name="senha" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary">Criar conta</button>
    </form>

    <p class="login-footer-text">
        Já tem conta? <a href="..\index\?forcar=professor">Clique aqui para acessar sua conta de professor</a>
    </p>
    <p class="login-footer-text">
        É aluno? <a href="..\index\?forcar=aluno">Clique aqui para acessar como aluno</a>
    </p>
</div>

</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y'); ?> EduPlataforma. Todos os direitos reservados.</p>
        <p class="footer-links">
            <a href="../pages/privacidade.php">Privacidade</a> ·
            <a href="../pages/termos.php">Termos de uso</a>
        </p>
    </div>
</footer>

<script src="/js/main.js"></script>
</body>
</html>
