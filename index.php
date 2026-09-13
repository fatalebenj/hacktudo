<?php

require_once __DIR__ . '/includes/Auth.php';
require_once __DIR__ . '/includes/Mural.php';

Auth::start();

require_once __DIR__ . '/includes/DeviceDetector.php';
$detector = new DeviceDetector();
$device = $detector->getDeviceType();

// Se ja estiver logado, manda direto para onde ele pertence.
// Professor -> painel dele. Aluno -> nao existe "painel do aluno":
// ele pertence ao mural em que entrou, entao volta direto pra la.
if (Auth::usuarioLogado()) {
    $perfil = Auth::perfilAtual();

    if ($perfil === 'professor') {
        header('Location: /pages/painel_professor.php');
        exit;
    }

    $muralId = Auth::muralIdAtual();
    $mural = $muralId ? Mural::buscarPorId($muralId) : null;

    if ($mural) {
        header('Location: /pages/mural.php?codigo=' . urlencode($mural['codigo']));
        exit;
    }

    // Sessao de aluno sem mural valido (situacao inconsistente): desloga
    // e manda de volta pro login em vez de tentar um painel que nao existe.
    Auth::logout();
    header('Location: /index.php');
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

$pageTitle = 'Quackdro - Início';

include __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <h1>Bem-vindo ao <span class="cursivo">Quackdro</span></h1>
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
