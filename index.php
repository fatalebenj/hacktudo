<?php
/**
 * index.php
 *
 * Pagina inicial do site de educacao.
 *
 * Regra de negocio:
 *  - Se o dispositivo for DESKTOP  -> mostra a tela de login do PROFESSOR
 *  - Se o dispositivo for MOBILE   -> mostra a tela de login do ALUNO
 *
 * A deteccao eh feita com a biblioteca foroco/browser-detection.
 */
require_once __DIR__ . '/vendor/autoload.php';

use foroco\BrowserDetection;

$Browser = new BrowserDetection();
$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// Get OS data (array):
$result = $Browser->getOS($useragent);
$device = $result['os_type'];


// Permite forcar manualmente a visualizacao (util para testes),
// ex: index.php?forcar=aluno  ou  index.php?forcar=professor
if (isset($_GET['forcar'])) {
    $forcar = $_GET['forcar'];
    if ($forcar === 'aluno') {
        $device = 'mobile';
    } elseif ($forcar === 'professor') {
        $device = 'desktop';
    }
}

$pageTitle = 'EduPlataforma - Início';

include __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <h1>Bem-vindo à EduPlataforma</h1>
    <p>Ensine e aprenda em conjunto</p>
</section>

<section class="login-area">
    <?php if ($device === 'desktop'): ?>
        <?php include __DIR__ . '/pages/login_professor.php'; ?>
    <?php else: ?>
        <?php include __DIR__ . '/pages/login_aluno.php'; ?>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
