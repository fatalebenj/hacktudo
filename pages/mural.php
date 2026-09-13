<?php
/**
 * mural.php
 * Pagina em comum do mural: acessada tanto pelo professor dono quanto
 * pelos alunos que entraram com o codigo. O acesso e controlado por:
 *  - professor: precisa ser o dono do mural (professor_id bate com a sessao)
 *  - aluno: precisa ter entrado nesse mural especifico (mural_id na sessao)
 *
 * Visibilidade dos nomes:
 *  - professor: ve o nome de TODOS os alunos e de quem publicou cada post-it
 *  - aluno: ve apenas o proprio nome e o nome do professor dono do mural
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
$erro = $_GET['erro'] ?? null;

$itens = Mural::listarItens((int) $mural['id']);
$nomeProfessor = Mural::nomeProfessor((int) $mural['professor_id']);

$cores = ['#f5f2e9', '#a3e6c1', '#8ecae6', '#f9e26b', '#f5a3a3', '#d6b3f5'];

include __DIR__ . '/../includes/header.php';
?>

<section class="box mural-topo" id="mural-box">
    <div class="mural-topo-info">
        <h1><?= htmlspecialchars($mural['titulo'], ENT_QUOTES, 'UTF-8'); ?></h1>

        <?php if ($perfil === 'professor'): ?>
            <p class="subtitle">
                Compartilhe o código abaixo com os alunos para que eles entrem neste mural.
            </p>
            <p class="mural-codigo"><?= htmlspecialchars($mural['codigo'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php else: ?>
            <p class="subtitle">
                Você está como <strong><?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8'); ?></strong>
                <?php if ($nomeProfessor): ?>
                    · turma de <strong><?= htmlspecialchars($nomeProfessor, ENT_QUOTES, 'UTF-8'); ?></strong>
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="mural-topo-acoes">
        <?php if ($perfil === 'professor'): ?>
            <a href="/pages/painel_professor.php" class="btn btn-secundario">&larr; Painel</a>
        <?php else: ?>
            <form action="/actions/logout.php" method="POST">
                <?= Auth::csrfField(); ?>
                <button type="submit" class="btn btn-secundario">Sair</button>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php if ($erro): ?>
    <div class="alert alert-erro"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<section class="box mural-novo-item">
    <form action="/actions/postar_item.php" method="POST" class="form-post-it">
        <?= Auth::csrfField(); ?>
        <input type="hidden" name="codigo" value="<?= htmlspecialchars($mural['codigo'], ENT_QUOTES, 'UTF-8'); ?>">

        <div class="form-group">
            <label for="conteudo">Novo post-it</label>
            <textarea id="conteudo" name="conteudo" rows="3" maxlength="500" placeholder="Escreva sua ideia, dúvida ou anotação..." required></textarea>
        </div>

        <div class="form-post-it-rodape">
            <div class="seletor-cor">
                <?php foreach ($cores as $i => $cor): ?>
                    <label class="cor-opcao" style="background: <?= htmlspecialchars($cor, ENT_QUOTES, 'UTF-8'); ?>;">
                        <input type="radio" name="cor" value="<?= htmlspecialchars($cor, ENT_QUOTES, 'UTF-8'); ?>" <?= $i === 0 ? 'checked' : ''; ?>>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn btn-primary">Publicar no mural</button>
        </div>
    </form>
</section>

<section class="quadro-mural">
    <?php if (empty($itens)): ?>
        <p class="quadro-vazio">Ainda não há nada aqui. Seja o primeiro a publicar um post-it!</p>
    <?php else: ?>
        <div class="post-its">
            <?php foreach ($itens as $i => $item): ?>
                <?php
                    // Nome do autor so aparece para o professor (ve todos) ou
                    // para o proprio autor / aluno vendo um post do professor.
                    $mostrarNomeAutor = true;
                    if ($perfil === 'aluno' && $item['autor_tipo'] === 'aluno' && (int) $item['autor_id'] !== (int) $usuario['id']) {
                        $mostrarNomeAutor = false;
                    }

                    $podeRemover = ($perfil === 'professor')
                        || ($perfil === 'aluno' && $item['autor_tipo'] === 'aluno' && (int) $item['autor_id'] === (int) $usuario['id']);

                    $rotacao = (($i % 5) - 2) * 1.5; // leve variacao de angulo, -3 a +3 graus
                ?>
                <article class="post-it" style="--post-it-cor: <?= htmlspecialchars($item['cor'], ENT_QUOTES, 'UTF-8'); ?>; --post-it-rot: <?= $rotacao; ?>deg;">
                    <p class="post-it-texto"><?= nl2br(htmlspecialchars($item['conteudo'], ENT_QUOTES, 'UTF-8')); ?></p>
                    <div class="post-it-rodape">
                        <span class="post-it-autor">
                            <?php if ($mostrarNomeAutor): ?>
                                <?= htmlspecialchars($item['autor_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                <?= $item['autor_tipo'] === 'professor' ? ' (professor)' : ''; ?>
                            <?php else: ?>
                                Colega da turma
                            <?php endif; ?>
                        </span>

                        <?php if ($podeRemover): ?>
                            <form action="/actions/remover_item.php" method="POST" class="post-it-remover">
                                <?= Auth::csrfField(); ?>
                                <input type="hidden" name="codigo" value="<?= htmlspecialchars($mural['codigo'], ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="item_id" value="<?= (int) $item['id']; ?>">
                                <button type="submit" title="Remover" aria-label="Remover post-it">&times;</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
