<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/api/config.php';

$regiao = $_GET['regiao'] ?? 'Norte';
$hospital = $_GET['hospital'] ?? '—';

// ← ADICIONAR AQUI
$especialidades = db()->query("
    SELECT id_esp, nome
    FROM especialidades
    ORDER BY nome
")->fetchAll();

$indicadores = [];
$stmtInd = db()->prepare("
    SELECT
        esp.nome,
        i.prev,
        i.mort,
        i.ind_env
    FROM indicadores_especialidade_regiao i
    JOIN especialidades esp ON esp.id_esp = i.id_esp
    JOIN regioes r ON r.id_regiao = i.id_reg
    WHERE r.nome_regiao = ?
");
$stmtInd->execute([$regiao]);
foreach ($stmtInd->fetchAll() as $row) {
    $indicadores[$row['nome']] = $row;
}

function iconEspecialidade($nome) {
    return match ($nome) {
        'Cardiologia' => '❤️',
        'Imagiologia', 'Imagiologia e Radiologia' => '🔬',
        'Pneumologia' => '🫁',
        'Ginecologia / Obstetrícia' => '🤰',
        'Ortopedia' => '🦴',
        default => '🩺'
    };
}

function descEspecialidade($nome) {
    return match ($nome) {
        'Cardiologia' => 'Diagnóstico e tratamento de doenças cardiovasculares',
        'Imagiologia', 'Imagiologia e Radiologia' => 'Diagnóstico por imagem — TC, RM, Raio-X e Ecografia',
        'Pneumologia' => 'Diagnóstico e acompanhamento de doenças respiratórias',
        'Ginecologia / Obstetrícia' => 'Saúde materna, obstetrícia e acompanhamento ginecológico',
        'Ortopedia' => 'Cirurgia musculoesquelética e reabilitação',
        default => 'Área clínica hospitalar'
    };
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MedDSS — Especialidade Médica</title>
<link rel="stylesheet" href="shared.css">
<style>
  .specialty-card {
    background: var(--card-bg);
    border: 0.5px solid var(--border);
    border-radius: var(--radius-modal);
    padding: 28px 24px;
    cursor: pointer;
    transition: all 0.18s;
    box-shadow: var(--shadow);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    text-decoration: none;
    color: inherit;
    position: relative;
    overflow: hidden;
  }
  .specialty-card::after {
    content: '›';
    position: absolute;
    right: 18px; bottom: 20px;
    font-size: 20px;
    color: var(--border);
    transition: color 0.15s, right 0.15s;
  }
  .specialty-card:hover {
    border-color: var(--teal);
    box-shadow: var(--shadow-md);
    transform: translateY(-3px);
  }
  .specialty-card:hover::after { color: var(--teal); right: 14px; }

  .specialty-icon-wrap {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: var(--teal-light);
    display: flex; align-items: center; justify-content: center;
    font-size: 26px;
    transition: background 0.15s;
    flex-shrink: 0;
  }
  .specialty-card:hover .specialty-icon-wrap { background: #C6EDE0; }

  .specialty-name { font-size: 16px; font-weight: 700; color: var(--text); line-height: 1.2; }
  .specialty-desc { font-size: 12px; color: var(--muted); line-height: 1.4; }
  .specialty-equip-count {
    font-size: 11px;
    font-weight: 600;
    color: var(--teal-dark);
    background: var(--teal-light);
    padding: 3px 10px;
    border-radius: 99px;
  }

  /* Breadcrumb */
  .breadcrumb-bar {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 24px;
    flex-wrap: wrap;
  }
  .breadcrumb-bar a { color: var(--teal-dark); text-decoration: none; font-weight: 500; }
  .breadcrumb-bar a:hover { text-decoration: underline; }
  .breadcrumb-sep { color: var(--border); }

  /* Context card */
  .context-card {
    background: var(--card-bg);
    border: 0.5px solid var(--border);
    border-radius: 12px;
    padding: 18px 22px;
    margin-bottom: 32px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: var(--shadow);
  }
  .context-icon { font-size: 28px; flex-shrink: 0; }
  .context-info { flex: 1; }
  .context-label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; }
  .context-value { font-size: 15px; font-weight: 600; color: var(--text); margin-top: 2px; }
  .context-sep { width: 0.5px; height: 36px; background: var(--border); }

  /* Indicadores no card */
.spec-indicators {
  display: flex;
  gap: 10px;
  margin-top: 4px;
  flex-wrap: wrap;
}
.spec-ind-item {
  display: flex;
  flex-direction: column;
  gap: 1px;
}
.spec-ind-label {
  font-size: 9px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .5px;
  color: var(--muted);
}
.spec-ind-value {
  font-size: 13px;
  font-weight: 700;
  color: var(--text);
}
.spec-ind-value.high { color: #993C1D; }
.spec-ind-value.mid  { color: #856404; }
.spec-ind-value.low  { color: #0F6E56; }

/* Separador subtil antes dos indicadores */
.spec-ind-sep {
  width: 100%;
  height: 0.5px;
  background: var(--border);
  margin: 6px 0 2px;
}

/* Badge de destaque (quando mortalidade alta) */
.spec-alert-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 99px;
  background: #FDE8E2;
  color: #993C1D;
  margin-top: 2px;
}
</style>
</head>
<body>

<header class="topbar">
  <a class="logo" href="1-regiao.php">Med<span>DSS</span></a>
  <div class="topbar-divider"></div>
  <span class="topbar-sub">Sistema de Apoio à Decisão em Saúde</span>
  <div class="topbar-right">
    <div class="topbar-user">
      <div class="topbar-user">
    <div class="user-avatar">
        <?= strtoupper(substr($_SESSION['user']['name'], 0, 1)) ?>
    </div>

    <span class="user-name">
        <?= htmlspecialchars($_SESSION['user']['name']) ?>
    </span>

    <a href="logout.php" class="logout-btn">Sair</a>
</div>
  </div>
</header>

<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-section">
      <div class="sidebar-section-label">Progresso</div>
      <div class="sidebar-steps">
        <a href="1-regiao.php" class="sidebar-step done">
          <div class="step-num">✓</div>
          <div class="step-info">
            <div class="step-label">Região</div>
            <div class="step-value" id="sb-region">—</div>
          </div>
        </a>
        <div class="step-connector done"></div>
        <div class="sidebar-step done">
          <div class="step-num">✓</div>
          <div class="step-info">
            <div class="step-label">Hospital</div>
            <div class="step-value" id="sb-hospital">—</div>
          </div>
        </div>
        <div class="step-connector done"></div>
        <div class="sidebar-step active">
          <div class="step-num">3</div>
          <div class="step-info">
            <div class="step-label">Especialidade</div>
            <div class="step-value">Não selecionado</div>
          </div>
        </div>
        <div class="step-connector"></div>
        <div class="sidebar-step locked">
          <div class="step-num">4</div>
          <div class="step-info">
            <div class="step-label">Tipo de decisão</div>
            <div class="step-value">—</div>
          </div>
        </div>
      </div>
    </div>
    <div class="sidebar-info">
      <strong>🩺 Especialidades clínicas</strong>
      As recomendações são ajustadas à especialidade e ao perfil de utilização do serviço.
    </div>
  </aside>

  <main class="main">
    <div class="breadcrumb-bar">
      <a href="1-regiao.php">Início</a>
      <span class="breadcrumb-sep">/</span>
      <a id="bc-regiao" href="#">—</a>
      <span class="breadcrumb-sep">/</span>
      <a id="bc-hospital" href="#">—</a>
    </div>

    <div class="context-card">
      <div class="context-icon">🏥</div>
      <div class="context-info">
        <div class="context-label">Hospital</div>
        <div class="context-value" id="ctx-hospital">—</div>
      </div>
      <div class="context-sep"></div>
      <div class="context-icon">📍</div>
      <div class="context-info">
        <div class="context-label">Região</div>
        <div class="context-value" id="ctx-region">—</div>
      </div>
    </div>

    <div class="page-header">
      <div class="page-title">Especialidade médica</div>
      <div class="page-sub">Selecione a área clínica para obter recomendações específicas</div>
    </div>

    <div class="card-grid card-grid-3" id="spec-grid">

  <?php foreach ($especialidades as $esp): 
  $ind = $indicadores[$esp['nome']] ?? null;
  
  /* Classes de cor baseadas nos valores */
  $mortClass = '';
  $prevClass = '';
  $alertBadge = '';
  
  if ($ind) {
    $mortClass = $ind['mort'] >= 3.5 ? 'high' : ($ind['mort'] >= 2.0 ? 'mid' : 'low');
    $prevClass = $ind['prev'] >= 12.0 ? 'high' : ($ind['prev'] >= 8.0 ? 'mid' : 'low');
    
    if ($ind['mort'] >= 3.5) {
      $alertBadge = '<div class="spec-alert-badge">⚠ Mortalidade elevada na região</div>';
    }
  }
?>
  <div class="specialty-card" data-spec="<?= htmlspecialchars($esp['nome']) ?>">
    <div class="specialty-icon-wrap">
      <?= iconEspecialidade($esp['nome']) ?>
    </div>

    <div class="specialty-name"><?= htmlspecialchars($esp['nome']) ?></div>
    <div class="specialty-desc"><?= descEspecialidade($esp['nome']) ?></div>

    <?php if ($ind): ?>
    <div class="spec-ind-sep"></div>
    <div class="spec-indicators">
      <div class="spec-ind-item">
        <div class="spec-ind-label">Prevalência</div>
        <div class="spec-ind-value <?= $prevClass ?>"><?= number_format($ind['prev'], 1) ?>%</div>
      </div>
      <div class="spec-ind-item">
        <div class="spec-ind-label">Mortalidade</div>
        <div class="spec-ind-value <?= $mortClass ?>"><?= number_format($ind['mort'], 1) ?>%</div>
      </div>
      <div class="spec-ind-item">
        <div class="spec-ind-label">Índice de envelhecimento</div>
        <div class="spec-ind-value"><?= number_format($ind['ind_env'], 0) ?></div>
      </div>
    </div>
    <?php if ($alertBadge): ?>
      <?= $alertBadge ?>
    <?php endif; ?>
    <?php endif; ?>

    <div class="specialty-equip-count">Ver equipamentos</div>
  </div>
<?php endforeach; ?>

</div>
  </main>
</div>

<script>
  const regiao = <?= json_encode($regiao, JSON_UNESCAPED_UNICODE) ?>;
  const hospital = <?= json_encode($hospital, JSON_UNESCAPED_UNICODE) ?>;

  document.getElementById('sb-region').textContent = regiao;
  document.getElementById('sb-hospital').textContent = hospital;
  document.getElementById('ctx-hospital').textContent = hospital;
  document.getElementById('ctx-region').textContent = regiao;

  const bcRegiao = document.getElementById('bc-regiao');
  bcRegiao.textContent = regiao;
  bcRegiao.href = `2-hospital.php?regiao=${encodeURIComponent(regiao)}`;

  document.getElementById('bc-hospital').textContent = hospital;

  function goSpec(name) {
    const r = encodeURIComponent(regiao);
    const h = encodeURIComponent(hospital);
    const s = encodeURIComponent(name);
    window.location.href = `4-decisao.php?regiao=${r}&hospital=${h}&especialidade=${s}`;
  }

  document.querySelectorAll('.specialty-card').forEach(card => {
    card.addEventListener('click', function () {
      goSpec(this.dataset.spec);
    });
  });
</script>
</body>
</html>
