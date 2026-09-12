<?php
/**
 * header.php
 * Cabecalho comum de todas as paginas do site.
 * Espera que a variavel $pageTitle e $device ja estejam definidas.
 */
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'EduPlataforma'); ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="device-<?= htmlspecialchars($device ?? 'desktop'); ?>">

<header class="site-header">
    <div class="container header-inner">
        <a href="/index.php" class="logo">📚 EduPlataforma</a>

        <nav class="main-nav">
            <ul>
                <li><a href="/index.php">Início</a></li>
                <li><a href="/pages/cursos.php">Cursos</a></li>
                <li><a href="/pages/sobre.php">Sobre</a></li>
                <li><a href="/pages/contato.php">Contato</a></li>
            </ul>
        </nav>

        <div class="device-badge" title="Dispositivo detectado pela biblioteca foroco">
            <?= $device === 'mobile' ? '📱 Mobile' : '🖥️ Desktop'; ?>
        </div>
    </div>
</header>

<main class="container main-content">
