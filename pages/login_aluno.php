<?php
/**
 * login_aluno.php
 * Tela de login exibida quando o dispositivo detectado eh MOBILE (smartphone/tablet).
 */
?>
<div class="login-box login-aluno">
    <h2>Login do Aluno</h2>
    <p class="subtitle">Entre para acessar seus cursos e atividades.</p>

    <form action="/actions/autenticar_aluno.php" method="POST" class="login-form">
        <div class="form-group">
            <label for="email_aluno">E-mail</label>
            <input type="email" id="email_aluno" name="email" placeholder="seuemail@exemplo.com" required>
        </div>

        <div class="form-group">
            <label for="senha_aluno">Senha</label>
            <input type="password" id="senha_aluno" name="senha" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Entrar</button>
    </form>

    <p class="login-footer-text">
        Ainda não tem conta? <a href="/pages/cadastro_aluno.php">Cadastre-se</a>
    </p>
    <p class="login-footer-text">
        É professor? <a href="?forcar=professor">Clique aqui para acessar como professor</a>
    </p>
</div>
