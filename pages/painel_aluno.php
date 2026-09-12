<?php
/**
 * painel_aluno.php
 * Area logada do aluno. Acesso restrito via Auth::exigirPerfil().
 */
require_once __DIR__ . '/../includes/Auth.php';
Auth::exigirPerfil('aluno');

$usuario = Auth::usuarioAtual();
$device = 'mobile';
$pageTitle = 'Quackdro - Painel do Aluno';

include __DIR__ . '/../includes/header.php';
?>

<section class="hero">
    <h1>Olá, <?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8'); ?> 👋</h1>
    <p>Este é o seu painel de aluno.</p>
</section>

<div class="login-box">
    <h2>Painel do Aluno</h2>
    <p>Aqui entrariam seus cursos, atividades e mural.</p>

    <form action="/actions/logout.php" method="POST" style="margin-top: 1.5rem;">
        <?= Auth::csrfField(); ?>
        <button type="submit" class="btn btn-primary btn-block">Sair</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
