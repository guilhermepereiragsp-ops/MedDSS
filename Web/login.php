<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    header('Location: 1-regiao.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // -------------------------------------------------------
    // TODO: Replace with real database authentication
    // Example: $user = getUserFromDB($username, $password);
    // -------------------------------------------------------
    $demo_users = [
        'joao.silva' => ['password' => 'demo1234', 'name' => 'Dr. João Silva', 'role' => 'Médico'],
        'ana.ferreira' => ['password' => 'demo1234', 'name' => 'Dra. Ana Ferreira', 'role' => 'Enfermeira-Chefe'],
    ];

    if (isset($demo_users[$username]) && $demo_users[$username]['password'] === $password) {
        $_SESSION['user'] = [
            'username' => $username,
            'name'     => $demo_users[$username]['name'],
            'role'     => $demo_users[$username]['role'],
        ];
        header('Location: 1-regiao.php');
        exit;
    } else {
        $error = 'Credenciais incorretas. Verifique o utilizador e a palavra-passe.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar — MedDSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green-primary: #2d8f6f;
            --green-light: #e8f5f0;
            --green-mid: #b2ddd0;
            --text-dark: #1a2e25;
            --text-mid: #4a6358;
            --text-muted: #8aa898;
            --border: #daeae3;
            --bg: #f7faf8;
            --white: #ffffff;
            --error-bg: #fff5f5;
            --error-border: #fca5a5;
            --error-text: #b91c1c;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* NAV */
        nav {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 48px;
            height: 60px;
            display: flex;
            align-items: center;
        }

        .logo {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            letter-spacing: -0.01em;
            text-decoration: none;
        }

        .logo span { color: var(--green-primary); }

        /* LAYOUT */
        .page {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
        }

        .login-wrap {
            display: flex;
            gap: 64px;
            align-items: flex-start;
            width: 100%;
            max-width: 820px;
        }

        /* LEFT PANEL */
        .login-info {
            flex: 1;
            padding-top: 8px;
        }

        .login-info-eyebrow {
            font-size: 0.6875rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--green-primary);
            margin-bottom: 16px;
        }

        .login-info h1 {
            font-size: 1.875rem;
            font-weight: 300;
            line-height: 1.2;
            letter-spacing: -0.02em;
            color: var(--text-dark);
            margin-bottom: 16px;
        }

        .login-info h1 strong { font-weight: 600; }

        .login-info p {
            font-size: 0.875rem;
            line-height: 1.65;
            color: var(--text-mid);
            margin-bottom: 32px;
        }

        .steps {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .step {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .step-num {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--green-light);
            color: var(--green-primary);
            font-size: 0.6875rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .step-text {
            font-size: 0.8125rem;
            color: var(--text-mid);
            line-height: 1.5;
        }

        .step-text strong {
            color: var(--text-dark);
            font-weight: 500;
        }

        /* FORM CARD */
        .login-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 36px;
            width: 340px;
            flex-shrink: 0;
        }

        .login-card-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .login-card-sub {
            font-size: 0.8125rem;
            color: var(--text-muted);
            margin-bottom: 28px;
        }

        .field {
            margin-bottom: 18px;
        }

        .field label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--text-mid);
            margin-bottom: 6px;
        }

        .field input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.9375rem;
            font-family: inherit;
            background: var(--bg);
            color: var(--text-dark);
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .field input::placeholder { color: var(--text-muted); }

        .field input:focus {
            border-color: var(--green-primary);
            box-shadow: 0 0 0 3px rgba(45, 143, 111, 0.12);
            background: var(--white);
        }

        .error-msg {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 0.8125rem;
            color: var(--error-text);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .btn-submit {
            width: 100%;
            background: var(--green-primary);
            color: var(--white);
            border: none;
            border-radius: 6px;
            padding: 12px;
            font-size: 0.9375rem;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.15s;
            margin-top: 4px;
        }

        .btn-submit:hover { background: #247a5e; }

        .login-footer {
            margin-top: 20px;
            font-size: 0.75rem;
            color: var(--text-muted);
            text-align: center;
            line-height: 1.55;
        }

        .login-footer a {
            color: var(--green-primary);
            text-decoration: none;
        }

        .login-footer a:hover { text-decoration: underline; }

        /* DEMO HINT — remove in production */
        .demo-hint {
            background: var(--green-light);
            border: 1px solid var(--green-mid);
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 0.75rem;
            color: var(--text-mid);
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .demo-hint strong { color: var(--text-dark); font-weight: 500; }

        /* FOOTER */
        footer {
            border-top: 1px solid var(--border);
            padding: 18px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        @media (max-width: 720px) {
            .login-wrap { flex-direction: column; gap: 32px; }
            .login-card { width: 100%; }
            nav { padding: 0 24px; }
            footer { padding: 16px 24px; flex-direction: column; gap: 4px; text-align: center; }
        }
    </style>
</head>
<body>

<nav>
    <a href="index.php" class="logo">Med<span>DSS</span></a>
</nav>

<div class="page">
    <div class="login-wrap">

        <div class="login-info">
            <div class="login-info-eyebrow">Acesso profissional</div>
            <h1>Bem-vindo ao<br><strong>sistema de decisão</strong></h1>
            <p>Após autenticação, poderá aceder às recomendações clínicas personalizadas por região, hospital e especialidade.</p>

            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <div class="step-text"><strong>Selecione a região</strong> de saúde pretendida</div>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <div class="step-text"><strong>Escolha o hospital</strong> e serviço clínico</div>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <div class="step-text"><strong>Indique a especialidade</strong> médica em questão</div>
                </div>
                <div class="step">
                    <div class="step-num">4</div>
                    <div class="step-text"><strong>Obtenha a recomendação</strong> baseada em dados reais do SNS</div>
                </div>
            </div>
        </div>

        <div class="login-card">
            <div class="login-card-title">Entrar</div>
            <div class="login-card-sub">Use as credenciais institucionais</div>

            <?php if ($error): ?>
                <div class="error-msg"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php" novalidate>
                <div class="field">
                    <label for="username">Utilizador</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="nome.apelido"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>
                <div class="field">
                    <label for="password">Palavra-passe</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                </div>
                <button type="submit" class="btn-submit">Entrar no sistema</button>
            </form>

            <div class="login-footer">
                Problemas de acesso? <a href="mailto:suporte@meddss.pt">Contacte o suporte</a>
            </div>
        </div>

    </div>
</div>

<footer>
    <span>© 2025 MedDSS — Sistema de Apoio à Decisão em Saúde</span>
    <span>Uso restrito a profissionais de saúde · SNS Portugal</span>
</footer>

</body>
</html>