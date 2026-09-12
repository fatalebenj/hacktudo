<?php
/**
 * cadastro_prof.php
 * Tela de cadastro de uma nova conta de professor.
 */
require_once __DIR__ . '/../includes/Auth.php';
Auth::start();

$device = 'desktop';
$pageTitle = 'Quackdro - Cadastro de Professor';
$erro = $_GET['erro'] ?? null;

include __DIR__ . '/../includes/header.php';
?>

<section class="hero">
    <h1>Crie sua conta de Professor</h1>
    <p>Comece a ensinar na Quackdro</p>
</section>

<div class="login-box login-professor">
    <h2>Cadastro do Professor</h2>
    <p class="subtitle">Crie sua conta e comece a ensinar</p>

    <?php if ($erro): ?>
        <div class="alert alert-erro"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form action="/actions/criar_professor.php" method="POST" class="login-form">
        <?= Auth::csrfField(); ?>

        <div class="form-group">
            <label for="nome_professor">Nome</label>
            <input type="text" id="nome_professor" name="nome" placeholder="Seu nome" required>
        </div>

        <div class="form-group">
            <label for="email_professor">E-mail</label>
            <input type="email" id="email_professor" name="email" placeholder="professor@escola.com" required>
        </div>

        <div class="form-group">
            <label for="senha_professor">Senha</label>
            <input type="password" id="senha_professor" name="senha" placeholder="••••••••" minlength="6" required>
        </div>

        <div class="form-group">
            <label for="confirmar_senha_professor">Confirmar senha</label>
            <input type="password" id="confirmar_senha_professor" name="confirmar_senha" placeholder="••••••••" minlength="6" required>
        </div>

        <button type="submit" class="btn btn-primary">Criar conta</button>
    </form>

    <p class="login-footer-text">
        Já tem conta? <a href="/index.php?forcar=professor">Clique aqui para acessar sua conta de professor</a>
    </p>
    <p class="login-footer-text">
        É aluno? <a href="/index.php?forcar=aluno">Clique aqui para acessar como aluno</a>
    </p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
