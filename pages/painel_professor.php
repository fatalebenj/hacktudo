<?php
/**
 * painel_professor.php
 * Area logada do professor. Acesso restrito via Auth::exigirPerfil().
 */
require_once __DIR__ . '/../includes/Auth.php';
Auth::exigirPerfil('professor');

$usuario = Auth::usuarioAtual();
$device = 'desktop';
$pageTitle = 'Quackdro - Painel do Professor';

include __DIR__ . '/../includes/header.php';
?>

<section class="box" id="painel-box">
    <h1>Olá, <?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8'); ?> 👋</h1>
    <p class="subtitle" style="grid-column: 1; grid-row: 2;">Logado como <?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8'); ?></p> <br>
    <p style="grid-column: 1; grid-row: 3">Este é o seu painel de professor.</p>
    <form action="/actions/logout.php" method="POST" style="grid-column: 2; grid-row: 1;">
        <?= Auth::csrfField(); ?>
        <button type="submit" class="btn btn-primary">Sair</button>
    </form>
</section>
<section 
<?php include __DIR__ . '/../includes/footer.php'; ?>
