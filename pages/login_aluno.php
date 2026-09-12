<?php
/**
 * login_aluno.php
 * Tela de login exibida quando o dispositivo detectado eh MOBILE (smartphone/tablet).
 */
require_once __DIR__ . '/../includes/Auth.php';
?>
<div class="login-box login-aluno">
    <h2>Login do Aluno</h2>
    <p class="subtitle">Entre para começar a cooperar em um mural</p>

    <form action="/actions/autenticar_aluno.php" method="POST" class="login-form">
        <?= Auth::csrfField(); ?>

        <div class="form-group">
            <label for="nome_aluno">Nome</label>
            <input type="text" id="nome_aluno" name="nome" placeholder="ex.: Linus" required>
        </div>

        <div class="form-group">
            <label for="cod_aluno">Código do mural</label>
            <input type="text" id="codigo" name="codigo" placeholder="ex.: 41639" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Entrar</button>
    </form>

    <p class="login-footer-text">
        É professor? <a href="?forcar=professor">Clique aqui para acessar como professor</a>
    </p>
</div>
