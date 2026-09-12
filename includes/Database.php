<?php
/**
 * Database.php
 *
 * Conexao central com o banco SQLite e criacao automatica das tabelas
 * necessarias para o sistema de login (professores e alunos).
 *
 * SQLite foi escolhido para o hackathon por nao exigir nenhum servidor
 * de banco de dados externo: o arquivo fica em /data/quackdro.sqlite
 * e e criado automaticamente na primeira execucao.
 */

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dbDir = __DIR__ . '/../data';
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0775, true);
            }

            $dbPath = $dbDir . '/quackdro.sqlite';
            $pdo = new PDO('sqlite:' . $dbPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->exec('PRAGMA foreign_keys = ON');

            self::$instance = $pdo;
            self::migrate($pdo);
        }

        return self::$instance;
    }

    private static function migrate(PDO $pdo): void
    {
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS professores (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nome TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                senha_hash TEXT NOT NULL,
                criado_em TEXT NOT NULL DEFAULT (datetime(\'now\'))
            )
        ');

        // Contador simples de tentativas de login para mitigar forca bruta.
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS login_attempts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                identificador TEXT NOT NULL,
                perfil TEXT NOT NULL,
                tentativas INTEGER NOT NULL DEFAULT 0,
                bloqueado_ate TEXT,
                atualizado_em TEXT NOT NULL DEFAULT (datetime(\'now\')),
                UNIQUE(identificador, perfil)
            )
        ');

        // Murais criados pelos professores. Cada mural tem um codigo
        // alfanumerico unico usado pelos alunos para entrar.
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS murais (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                professor_id INTEGER NOT NULL,
                titulo TEXT NOT NULL,
                codigo TEXT NOT NULL UNIQUE,
                criado_em TEXT NOT NULL DEFAULT (datetime(\'now\')),
                FOREIGN KEY (professor_id) REFERENCES professores(id) ON DELETE CASCADE
            )
        ');

        // Alunos entram em um mural especifico usando nome + codigo do mural.
        // Nao ha senha: o codigo do mural funciona como convite/autenticacao.
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS alunos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                mural_id INTEGER NOT NULL,
                nome TEXT NOT NULL,
                codigo_mural TEXT NOT NULL,
                criado_em TEXT NOT NULL DEFAULT (datetime(\'now\')),
                FOREIGN KEY (mural_id) REFERENCES murais(id) ON DELETE CASCADE
            )
        ');
    }
}
