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
require_once __DIR__ . '/includes/Auth.php';

Auth::start();

require_once __DIR__ . '/includes/DeviceDetector.php';
$detector = new DeviceDetector();
$device = $detector->getDeviceType();

// Se ja estiver logado, manda direto para o painel correspondente.
if (Auth::usuarioLogado()) {
    $perfil = Auth::perfilAtual();
    header('Location: ' . ($perfil === 'professor' ? '/pages/painel_professor.php' : '/pages/painel_aluno.php'));
    exit;
}

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

$erro = $_GET['erro'] ?? null;
$sucesso = $_GET['sucesso'] ?? null;
if ($erro === 'acesso_negado') {
    $erro = 'Voce precisa entrar para acessar essa pagina.';
}

$pageTitle = 'QuackMural - Início';

include __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <h1>Bem-vindo ao QuackMural</h1>
    <p>Ensine e aprenda em conjunto</p>
</section>

<section class="login-area">
    <?php if ($erro): ?>
        <div class="alert alert-erro"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <div class="alert alert-sucesso"><?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if ($device === 'desktop'): ?>
        <?php include __DIR__ . '/pages/login_professor.php'; ?>
    <?php else: ?>
        <?php include __DIR__ . '/pages/login_aluno.php'; ?>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
