<?php
/**
 * painel_professor.php
 * Area logada do professor. Acesso restrito via Auth::exigirPerfil().
 */
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Mural.php';
Auth::exigirPerfil('professor');

$usuario = Auth::usuarioAtual();
$device = 'desktop';
$pageTitle = 'Quackdro - Painel do Professor';
$erro = $_GET['erro'] ?? null;

$murais = Mural::listarPorProfessor((int) $usuario['id']);

include __DIR__ . '/../includes/header.php';
?>

<section class="box" id="painel-box">
    <h1>Olá, <?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
    <p class="subtitle" style="grid-column: 1; grid-row: 2;">Logado como <?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8'); ?></p> <br>
    <p style="grid-column: 1; grid-row: 3">Este é o seu painel de professor.</p>

    <button type="button" class="btn btn-primary" style="grid-column: 2; grid-row: 1; margin-right: 10px;" onclick="document.getElementById('form-novo-mural').classList.toggle('hidden')">
        + Novo Mural
    </button>

    <form action="/actions/logout.php" method="POST" style="grid-column: 3; grid-row: 1; height: 18px;">
        <?= Auth::csrfField(); ?>
        <button type="submit" class="btn btn-primary">Sair</button>
    </form>
</section>

<?php if ($erro): ?>
    <div class="alert alert-erro"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<section class="box hidden" id="form-novo-mural">
    <h2>Criar novo mural</h2>
    <form action="/actions/criar_mural.php" method="POST" class="login-form">
        <?= Auth::csrfField(); ?>
        <div class="form-group">
            <label for="titulo_mural">Título do mural</label>
            <input type="text" id="titulo_mural" name="titulo" placeholder="ex.: Turma 3A - Matemática" required maxlength="100">
        </div>
        <button type="submit" class="btn btn-primary">Criar mural</button>
    </form>
</section>

<section class="box" id="lista-murais">
    <h2>Seus murais</h2>
    <?php if (empty($murais)): ?>
        <p>Você ainda não criou nenhum mural.</p>
    <?php else: ?>
        <ul class="mural-lista">
            <?php foreach ($murais as $mural): ?>
                <li>
                    <a href="/pages/mural.php?codigo=<?= urlencode($mural['codigo']); ?>">
                        <?= htmlspecialchars($mural['titulo'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    — código: <strong><?= htmlspecialchars($mural['codigo'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    (<?= Mural::contarAlunos((int) $mural['id']); ?> aluno(s))
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
