<?php
/**
 * mural.php
 * Pagina em comum do mural: acessada tanto pelo professor dono quanto
 * pelos alunos que entraram com o codigo. O acesso e controlado por:
 *  - professor: precisa ser o dono do mural (professor_id bate com a sessao)
 *  - aluno: precisa ter entrado nesse mural especifico (mural_id na sessao)
 */
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Mural.php';

Auth::start();

if (!Auth::usuarioLogado()) {
    header('Location: /index.php?erro=acesso_negado');
    exit;
}

$codigo = $_GET['codigo'] ?? '';
$mural = $codigo !== '' ? Mural::buscarPorCodigo($codigo) : null;

if (!$mural) {
    header('Location: /index.php?erro=' . urlencode('Mural nao encontrado.'));
    exit;
}

$usuario = Auth::usuarioAtual();
$perfil = Auth::perfilAtual();

$acessoPermitido = false;
if ($perfil === 'professor' && (int) $mural['professor_id'] === (int) $usuario['id']) {
    $acessoPermitido = true;
} elseif ($perfil === 'aluno' && Auth::muralIdAtual() === (int) $mural['id']) {
    $acessoPermitido = true;
}

if (!$acessoPermitido) {
    header('Location: /index.php?erro=acesso_negado');
    exit;
}

$device = $perfil === 'professor' ? 'desktop' : 'mobile';
$pageTitle = 'Quackdro - ' . $mural['titulo'];

include __DIR__ . '/../includes/header.php';
?>

<section class="box" id="mural-box">
    <h1><?= htmlspecialchars($mural['titulo'], ENT_QUOTES, 'UTF-8'); ?></h1>

    <?php if ($perfil === 'professor'): ?>
        <p class="subtitle">
            Compartilhe o código abaixo com os alunos para que eles entrem neste mural.
        </p>
        <p class="mural-codigo" style="font-size: 2rem; font-weight: bold; letter-spacing: 0.2em;">
            <?= htmlspecialchars($mural['codigo'], ENT_QUOTES, 'UTF-8'); ?>
        </p>
    <?php else: ?>
        <p class="subtitle">Você entrou como <?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8'); ?>.</p>
    <?php endif; ?>

    <div class="mural-conteudo" style="margin-top: 1.5rem;">
        <p>Aqui é o espaço em comum do mural — professor e alunos veem o mesmo conteúdo.</p>
        <!-- TODO: listar/postar itens do mural (posts, atividades, etc.) -->
    </div>

    <p style="margin-top: 2rem;">
        <a href="<?= $perfil === 'professor' ? '/pages/painel_professor.php' : '/pages/painel_aluno.php'; ?>">
            &larr; Voltar ao painel
        </a>
    </p>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
