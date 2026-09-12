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

## Próximos passos sugeridos

- Conectar `actions/autenticar_professor.php` e `actions/autenticar_aluno.php`
  a um banco de dados real, com senhas com hash (`password_hash` / `password_verify`).
- Criar `pages/painel_professor.php` e `pages/painel_aluno.php` (áreas logadas).
- Adicionar proteção CSRF nos formulários de login.
- Adicionar testes para os principais user agents (desktop, Android, iOS).
