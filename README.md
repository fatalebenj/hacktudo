# EduPlataforma — Carcaça de Site de Educação

Estrutura inicial de um site de educação em PHP que detecta o tipo de
dispositivo do visitante (usando a biblioteca **foroco/browser-detection**)
e exibe:

- **Desktop** → tela de login do **Professor**
- **Mobile (smartphone/tablet)** → tela de login do **Aluno**

## Estrutura de pastas

```
site-educacao/
├── actions/
│   ├── autenticar_professor.php   # processa o login do professor (placeholder)
│   └── autenticar_aluno.php       # processa o login do aluno (placeholder)
├── css/
│   └── style.css
├── js/
│   └── main.js
├── includes/
│   ├── DeviceDetector.php         # wrapper da lib foroco
│   ├── header.php
│   └── footer.php
├── pages/
│   ├── login_professor.php
│   ├── login_aluno.php
│   ├── cursos.php
│   ├── sobre.php
│   └── contato.php
├── composer.json
└── index.php                      # ponto de entrada com a lógica de detecção
```

## Instalação

1. Certifique-se de ter o PHP >= 7.4 e o Composer instalados.
2. Na raiz do projeto, rode:

   ```bash
   composer require foroco/browser-detection
   ```

   Isso vai criar a pasta `vendor/` com a biblioteca e o autoload,
   que já é referenciado em `includes/DeviceDetector.php`:

   ```php
   require_once __DIR__ . '/../vendor/autoload.php';
   use foroco\BrowserDetection;
   ```

3. Suba um servidor local para testar:

   ```bash
   php -S localhost:8000
   ```

4. Acesse `http://localhost:8000/`.

## Como funciona a detecção

O arquivo `includes/DeviceDetector.php` encapsula exatamente a lógica que
você pediu:

```php
$Browser   = new foroco\BrowserDetection();
$useragent = $_SERVER['HTTP_USER_AGENT'];
$result    = $Browser->getOS($useragent);
```

E a partir do campo `device_type` retornado (`desktop`, `smartphone`,
`tablet`, etc.), decide qual tela mostrar em `index.php`:

```php
$detector = new DeviceDetector();
$device   = $detector->getDeviceType(); // 'desktop' ou 'mobile'

if ($device === 'desktop') {
    include 'pages/login_professor.php';
} else {
    include 'pages/login_aluno.php';
}
```

## Testando sem trocar de dispositivo

Para forçar a visualização durante o desenvolvimento, use o parâmetro
`?forcar=`:

- `index.php?forcar=professor` → força a tela do professor
- `index.php?forcar=aluno` → força a tela do aluno

## Sistema de login (funcional)

O projeto agora tem autenticação real, com banco de dados **SQLite**
(criado automaticamente em `data/eduplataforma.sqlite` na primeira
execução — não precisa configurar nada).

### Recursos implementados

- Cadastro e login de **professores** (por e-mail) e **alunos** (por nome + código do mural).
- Senhas com hash `bcrypt` (`password_hash` / `password_verify`), nunca em texto puro.
- Proteção **CSRF** em todos os formulários (login, cadastro, logout).
- Sessões seguras: cookie `httponly`, id de sessão regenerado no login.
- Bloqueio temporário (5 minutos) após 5 tentativas de login erradas.
- Páginas protegidas (`pages/painel_professor.php`, `pages/painel_aluno.php`)
  que só abrem para quem estiver logado com o perfil correto.
- Botão de sair (`actions/logout.php`) que encerra a sessão.

### Como testar

```bash
composer install          # se a pasta vendor/ não estiver presente
php -S localhost:8000
```

Acesse `http://localhost:8000/`, use `?forcar=professor` ou `?forcar=aluno`
para alternar a tela sem trocar de dispositivo, cadastre uma conta e faça login.

### Estrutura adicionada

```
includes/
├── Database.php     # conexão SQLite + criação das tabelas
└── Auth.php          # cadastro, login, logout, CSRF, rate limiting
actions/
├── criar_professor.php
├── criar_aluno.php
└── logout.php
pages/
├── cadastro_aluno.php
├── painel_professor.php
└── painel_aluno.php
data/                  # criado automaticamente (ignorado pelo git)
```

## Próximos passos sugeridos

- Adicionar recuperação de senha por e-mail.
- Adicionar testes automatizados para os principais user agents (desktop, Android, iOS).
- Migrar de SQLite para MySQL/PostgreSQL em produção, se necessário.
