<?php
/**
 * login_professor.php
 * Tela de login exibida quando o dispositivo detectado eh DESKTOP.
 */
require_once __DIR__ . '/../includes/Auth.php';
?>
<div class="login-box login-professor">
    <h2>Login do Professor</h2>
    <p class="subtitle">Acesse o painel para gerenciar suas turmas e conteúdos.</p>

    <form action="/actions/autenticar_professor.php" method="POST" class="login-form">
        <?= Auth::csrfField(); ?>

        <div class="form-group">
            <label for="email_professor">E-mail</label>
            <input type="email" id="email_professor" name="email" placeholder="professor@escola.com" required>
        </div>

        <div class="form-group">
            <label for="senha_professor">Senha</label>
            <input type="password" id="senha_professor" name="senha" placeholder="••••••••" required>
        </div>

        <div class="form-group form-options">
            <label><input type="checkbox" name="lembrar"> Lembrar-me</label>
        </div>

        <button type="submit" class="btn btn-primary">Entrar como Professor</button>
    </form>

    <p class="login-footer-text">
        Ainda não tem conta? <a href="/pages/cadastro_prof.php">Cadastre-se</a>
    </p>

    <p class="login-footer-text">
        É aluno? <a href="?forcar=aluno">Clique aqui para acessar como aluno</a>
    </p>
</div>
