<?php
/**
 * painel_professor.php
 * Area logada do professor. Acesso restrito via Auth::exigirPerfil().
 */
require_once __DIR__ . '/../includes/Auth.php';
Auth::exigirPerfil('professor');

$usuario = Auth::usuarioAtual();
$device = 'desktop';
$pageTitle = 'EduPlataforma - Painel do Professor';

include __DIR__ . '/../includes/header.php';
?>

<section class="hero">
    <h1>Olá, <?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8'); ?> 👋</h1>
    <p>Este é o seu painel de professor.</p>
</section>

<div class="login-box">
    <h2>Painel do Professor</h2>
    <p class="subtitle">Logado como <?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Aqui entrariam as ferramentas de gerenciamento de turmas e conteúdos.</p>

    <form action="/actions/logout.php" method="POST" style="margin-top: 1.5rem;">
        <?= Auth::csrfField(); ?>
        <button type="submit" class="btn btn-primary">Sair</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
