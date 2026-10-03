<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: 1-regiao.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedDSS — Sistema de Apoio à Decisão em Saúde</title>
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
            justify-content: space-between;
        }

        .logo {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            letter-spacing: -0.01em;
        }

        .logo span { color: var(--green-primary); }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-login {
            background: var(--green-primary);
            color: var(--white);
            border: none;
            border-radius: 6px;
            padding: 8px 20px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
        }

        .btn-login:hover { background: #247a5e; }

        /* HERO */
        .hero {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 80px 48px;
            gap: 80px;
        }

        .hero-text { max-width: 480px; }

        .hero-eyebrow {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--green-primary);
            margin-bottom: 20px;
        }

        .hero-title {
            font-size: 2.75rem;
            font-weight: 300;
            line-height: 1.15;
            letter-spacing: -0.02em;
            color: var(--text-dark);
            margin-bottom: 20px;
        }

        .hero-title strong {
            font-weight: 600;
        }

        .hero-desc {
            font-size: 1rem;
            line-height: 1.65;
            color: var(--text-mid);
            margin-bottom: 36px;
        }

        .hero-cta {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-primary {
            background: var(--green-primary);
            color: var(--white);
            border: none;
            border-radius: 6px;
            padding: 12px 28px;
            font-size: 0.9375rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
        }

        .btn-primary:hover { background: #247a5e; }

        .hero-note {
            font-size: 0.8125rem;
            color: var(--text-muted);
        }

        /* MAP VISUAL */
        .hero-visual {
            flex-shrink: 0;
        }

        .map-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 28px;
            width: 320px;
        }

        .map-card-label {
            font-size: 0.6875rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        /* Simple SVG Portugal map placeholder */
        .map-svg-wrap {
            background: var(--green-light);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 200px;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .map-svg-wrap svg {
            width: 110px;
            height: 180px;
            opacity: 0.85;
        }

        .map-regions {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .map-region-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8125rem;
            color: var(--text-mid);
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: var(--bg);
        }

        .region-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--green-mid);
            flex-shrink: 0;
        }

        /* FEATURES */
        .features {
            background: var(--white);
            border-top: 1px solid var(--border);
            padding: 56px 48px;
            display: flex;
            justify-content: center;
            gap: 0;
        }

        .feature {
            max-width: 220px;
            padding: 0 32px;
            border-right: 1px solid var(--border);
        }

        .feature:first-child { padding-left: 0; }
        .feature:last-child { border-right: none; }

        .feature-icon {
            width: 36px;
            height: 36px;
            background: var(--green-light);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            font-size: 1rem;
        }

        .feature-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .feature-desc {
            font-size: 0.8125rem;
            line-height: 1.55;
            color: var(--text-muted);
        }

        /* FOOTER */
        footer {
            border-top: 1px solid var(--border);
            padding: 20px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        @media (max-width: 900px) {
            .hero { flex-direction: column; gap: 40px; padding: 48px 24px; }
            .hero-visual { width: 100%; }
            .map-card { width: 100%; }
            .features { flex-direction: column; gap: 28px; align-items: flex-start; padding: 40px 24px; }
            .feature { border-right: none; padding: 0; max-width: none; }
            nav { padding: 0 24px; }
            footer { padding: 16px 24px; }
        }
    </style>
</head>
<body>

<nav>
    <div class="logo">Med<span>DSS</span></div>
    <div class="nav-right">
        <a href="login.php" class="btn-login">Entrar</a>
    </div>
</nav>

<section class="hero">
    <div class="hero-text">
        <div class="hero-eyebrow">Sistema de Apoio à Decisão em Saúde</div>
        <h1 class="hero-title">
            Recomendações clínicas<br>
            <strong>baseadas em dados reais</strong>
        </h1>
        <p class="hero-desc">
            Selecione a região, hospital e especialidade para obter recomendações de equipamento e protocolos baseadas em dados clínicos do Serviço Nacional de Saúde.
        </p>
        <div class="hero-cta">
            <a href="login.php" class="btn-primary">Aceder ao sistema</a>
            <span class="hero-note">Apenas para profissionais de saúde</span>
        </div>
    </div>

    <div class="hero-visual">
        <div class="map-card">
            <div class="map-card-label">Cobertura nacional</div>
            <div class="map-svg-wrap">
                <!-- Simplified Portugal silhouette -->
                <svg viewBox="0 0 110 180" xmlns="http://www.w3.org/2000/svg" fill="none">
                    <path d="M30 10 L75 8 L80 20 L85 35 L78 50 L82 65 L75 80 L80 95 L72 110 L65 125 L55 140 L45 155 L35 165 L28 158 L22 145 L25 130 L20 115 L18 100 L22 85 L18 70 L20 55 L15 40 L18 25 Z" fill="#2d8f6f" opacity="0.25"/>
                    <!-- Norte -->
                    <path d="M30 10 L75 8 L80 20 L85 35 L78 50 L55 52 L30 50 L18 40 L18 25 Z" fill="#2d8f6f" opacity="0.5"/>
                    <!-- Centro -->
                    <path d="M30 50 L55 52 L78 50 L82 65 L75 80 L55 82 L28 80 L22 65 Z" fill="#2d8f6f" opacity="0.4"/>
                    <!-- Lisboa -->
                    <path d="M28 80 L55 82 L75 80 L80 95 L72 110 L50 108 L25 105 L20 95 Z" fill="#2d8f6f" opacity="0.35"/>
                    <!-- Alentejo -->
                    <path d="M25 105 L50 108 L72 110 L65 125 L55 140 L35 138 L22 128 L25 115 Z" fill="#2d8f6f" opacity="0.3"/>
                    <!-- Algarve -->
                    <path d="M35 138 L55 140 L65 125 L60 155 L45 162 L32 158 L28 148 Z" fill="#2d8f6f" opacity="0.45"/>
                    <!-- Region labels -->
                    <text x="52" y="34" text-anchor="middle" font-size="9" fill="#1a2e25" font-family="Inter, sans-serif" font-weight="500">Norte</text>
                    <text x="53" y="68" text-anchor="middle" font-size="9" fill="#1a2e25" font-family="Inter, sans-serif" font-weight="500">Centro</text>
                    <text x="52" y="98" text-anchor="middle" font-size="8" fill="#1a2e25" font-family="Inter, sans-serif" font-weight="500">Lisboa e VT</text>
                    <text x="50" y="126" text-anchor="middle" font-size="8" fill="#1a2e25" font-family="Inter, sans-serif" font-weight="500">Alentejo</text>
                    <text x="48" y="152" text-anchor="middle" font-size="8" fill="#1a2e25" font-family="Inter, sans-serif" font-weight="500">Algarve</text>
                </svg>
            </div>
            <div class="map-regions">
                <div class="map-region-row">
                    <div class="region-dot"></div>
                    Norte
                </div>
                <div class="map-region-row">
                    <div class="region-dot"></div>
                    Centro
                </div>
            </div>
        </div>
    </div>
</section>

<section class="features">
    <div class="feature">
        <div class="feature-icon">🏥</div>
        <div class="feature-title">Por hospital</div>
        <div class="feature-desc">Filtre por hospital e serviço para resultados contextualizados.</div>
    </div>
    <div class="feature">
        <div class="feature-icon">🔬</div>
        <div class="feature-title">Por especialidade</div>
        <div class="feature-desc">Recomendações específicas para cada área clínica.</div>
    </div>
    <div class="feature">
        <div class="feature-icon">📊</div>
        <div class="feature-title">Dados clínicos reais</div>
        <div class="feature-desc">Baseado em registos do SNS, atualizado regularmente.</div>
    </div>
    <div class="feature">
        <div class="feature-icon">⚡</div>
        <div class="feature-title">Decisão rápida</div>
        <div class="feature-desc">Quatro passos para obter uma recomendação fundamentada.</div>
    </div>
</section>

<footer>
    <span>© 2025 MedDSS — Sistema de Apoio à Decisão em Saúde</span>
    <span>SNS · Portugal</span>
</footer>

</body>
</html>