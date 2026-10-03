<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/api/config.php';

$regiao        = $_GET['regiao'] ?? '—';
$hospital      = $_GET['hospital'] ?? '—';
$especialidade = $_GET['especialidade'] ?? '—';
$tipo          = $_GET['tipo'] ?? '—';

$sql = "
    SELECT
        e.id_equip, e.tipo, e.fabricante, e.modelo,
        e.custo_aq, e.custo_man, e.prazo_ent, e.t_exame, e.t_vida_util,
        tac.cortes AS tac_cortes, tac.dose_radiacao AS tac_dose_radiacao,
        rm.campo_magnetico AS rm_campo_magnetico, rm.tempo_aquisicao AS rm_tempo_aquisicao,
        rx.digital AS rx_digital, rx.dose_radiacao AS rx_dose_radiacao,
        angio.resolucao_imagem AS angio_resolucao_imagem, angio.dose_radiacao AS angio_dose_radiacao,
        holter.duracao_gravacao AS holter_duracao_gravacao, holter.canais AS holter_canais,
        ecg.canais AS ecg_canais, ecg.interpretacao_automatica AS ecg_interpretacao_automatica,
        mon.parametros AS monitor_parametros, mon.bateria_horas AS monitor_bateria_horas,
        desf.energia_max_j AS desf_energia_max_j, desf.modo_dea AS desf_modo_dea,
        eco.num_sondas AS eco_num_sondas, eco.doppler AS eco_doppler,
        ctg.gemelar AS ctg_gemelar, ctg.autonomia_horas AS ctg_autonomia_horas,
        bronco.diametro_mm AS bronco_diametro_mm, bronco.canal_trabalho_mm AS bronco_canal_trabalho_mm,
        vent.modos_ventilacao AS vent_modos_ventilacao, vent.autonomia_horas AS vent_autonomia_horas,
        orto.precisao_mm AS orto_precisao_mm, orto.portatil AS orto_portatil
    FROM equipamento e
    LEFT JOIN eq_tac tac ON tac.id_equip = e.id_equip
    LEFT JOIN eq_rm rm ON rm.id_equip = e.id_equip
    LEFT JOIN eq_rx rx ON rx.id_equip = e.id_equip
    LEFT JOIN eq_angio angio ON angio.id_equip = e.id_equip
    LEFT JOIN eq_holter holter ON holter.id_equip = e.id_equip
    LEFT JOIN eq_ecg ecg ON ecg.id_equip = e.id_equip
    LEFT JOIN eq_monitor mon ON mon.id_equip = e.id_equip
    LEFT JOIN eq_desfibri desf ON desf.id_equip = e.id_equip
    LEFT JOIN eq_eco eco ON eco.id_equip = e.id_equip
    LEFT JOIN eq_cardiotoc ctg ON ctg.id_equip = e.id_equip
    LEFT JOIN eq_bronco bronco ON bronco.id_equip = e.id_equip
    LEFT JOIN eq_ventilad vent ON vent.id_equip = e.id_equip
    LEFT JOIN eq_ortopedia orto ON orto.id_equip = e.id_equip
    WHERE e.id_hosp IS NULL
      AND e.tipo = ?
    ORDER BY e.custo_aq ASC
";

$stmt = db()->prepare($sql);
$stmt->execute([$tipo]);

$catalogo = $stmt->fetchAll();

$catalogoJson = json_encode($catalogo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MedDSS — Recomendação AHP</title>
<link rel="stylesheet" href="shared.css">
<style>
  .breadcrumb-bar { display:flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);margin-bottom:28px;flex-wrap:wrap; }
  .breadcrumb-bar a { color:var(--teal-dark);text-decoration:none;font-weight:500; }
  .breadcrumb-bar a:hover { text-decoration:underline; }
  .breadcrumb-sep { color:var(--border); }

  .context-strip { display:flex;align-items:center;background:var(--card-bg);border:0.5px solid var(--border);border-radius:12px;overflow:hidden;box-shadow:var(--shadow);max-width:860px;margin-bottom:32px; }
  .ctx-item { display:flex;align-items:center;gap:10px;padding:14px 20px;flex:1; }
  .ctx-item+.ctx-item { border-left:0.5px solid var(--border); }
  .ctx-emoji { font-size:16px;flex-shrink:0; }
  .ctx-label { font-size:10px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px; }
  .ctx-value { font-size:13px;font-weight:600;color:var(--text);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px; }

  /* ── AHP Panel ── */
  .ahp-panel {
    background:var(--card-bg);border:0.5px solid var(--border);
    border-radius:14px;padding:22px 24px;margin-bottom:24px;
    box-shadow:var(--shadow-md);max-width:980px;
  }
  .ahp-panel-header {
    display:flex;align-items:center;justify-content:space-between;
    margin-bottom:4px;cursor:pointer;user-select:none;
  }
  .ahp-panel-title { font-size:15px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px; }
  .ahp-toggle-icon { font-size:18px;color:var(--muted);transition:transform .25s; }
  .ahp-panel.open .ahp-toggle-icon { transform:rotate(90deg); }
  .ahp-panel-sub { font-size:12px;color:var(--muted);margin-bottom:16px; }

  .ahp-body { display:none; }
  .ahp-panel.open .ahp-body { display:block; }

  /* RC badge */
  .rc-badge {
    display:inline-flex;align-items:center;gap:6px;
    font-size:12px;font-weight:600;padding:4px 12px;border-radius:99px;
    background:var(--teal-light);color:var(--teal-dark);
  }
  .rc-badge.warn { background:#FEF3CD;color:#856404; }

  /* Weight sliders */
  .weight-grid { display:grid;grid-template-columns:1fr 1fr;gap:12px 28px;margin-bottom:18px; }
  .weight-row { display:flex;flex-direction:column;gap:4px; }
  .weight-label { display:flex;justify-content:space-between;font-size:12px; }
  .weight-key { color:var(--muted);font-weight:500; }
  .weight-pct { font-weight:700;color:var(--teal-dark); }
  .weight-bar-wrap { height:6px;background:var(--sand);border-radius:99px;overflow:hidden; }
  .weight-bar { height:6px;border-radius:99px;transition:width .4s ease; }

  /* Pairwise matrix */
  .pw-section { margin-top:16px;border-top:0.5px solid var(--sand);padding-top:16px; }
  .pw-section-label { font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px; }
  .pw-table { width:100%;border-collapse:collapse;font-size:12px;overflow-x:auto;display:block; }
  .pw-table th { padding:5px 8px;text-align:left;border-bottom:0.5px solid var(--border);color:var(--muted);font-weight:600;white-space:nowrap; }
  .pw-table td { padding:5px 6px;border-bottom:0.5px solid var(--sand);white-space:nowrap; }
  .pw-table select { font-size:11px;padding:2px 4px;border-radius:4px;border:0.5px solid var(--border);background:var(--sand);color:var(--text);cursor:pointer; }

  /* ── Ranking cards ── */
  .rec-layout { display:grid;grid-template-columns:1fr 360px;gap:28px;align-items:start;max-width:980px; }

  .rank-badge {
    display:inline-flex;align-items:center;gap:6px;
    font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;
    background:var(--teal-light);color:var(--teal-dark);margin-bottom:10px;
  }

  .rec-card {
    background:var(--card-bg);border:2px solid var(--teal);
    border-radius:16px;padding:24px 28px;
    box-shadow:0 6px 24px rgba(29,158,117,.13);margin-bottom:12px;
  }
  .rec-card.alt-card-wrap {
    border:0.5px solid var(--border);box-shadow:var(--shadow);
    padding:0;border-radius:12px;overflow:hidden;margin-bottom:10px;
  }

  .rc-header { display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:16px; }
  .rc-name { font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.4px;line-height:1.2; }
  .rc-sub  { font-size:13px;color:var(--muted);margin-top:4px; }
  .rc-tags { display:flex;flex-wrap:wrap;gap:6px;margin-top:10px; }
  .rc-tag  { font-size:12px;font-weight:500;padding:3px 10px;border-radius:99px;background:var(--sand);color:var(--muted); }
  .rc-tag.hl { background:var(--teal-light);color:var(--teal-dark); }

  .score-circle {
    width:76px;height:76px;border-radius:50%;
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    flex-shrink:0;border:2.5px solid;
  }
  .score-circle.sg { background:#E1F5EE;border-color:#1D9E75; }
  .score-circle.sy { background:#FEF3CD;border-color:#C9960C; }
  .score-circle.sr { background:#FDE8E2;border-color:#993C1D; }
  .score-circle .num { font-size:24px;font-weight:700;line-height:1; }
  .score-circle.sg .num { color:#0F6E56; }
  .score-circle.sy .num { color:#856404; }
  .score-circle.sr .num { color:#993C1D; }
  .score-circle .lbl { font-size:9px;color:var(--muted);margin-top:1px; }

  .ahp-score-row { display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:0.5px solid var(--sand); }
  .ahp-score-row:last-child { border-bottom:none; }
  .ahp-score-label { font-size:12px;color:var(--muted);width:140px;flex-shrink:0; }
  .ahp-score-bar-wrap { flex:1;height:7px;background:var(--sand);border-radius:99px;overflow:hidden; }
  .ahp-score-bar { height:7px;border-radius:99px;background:var(--teal);transition:width .7s cubic-bezier(.22,1,.36,1); }
  .ahp-score-val { font-size:12px;font-weight:700;color:var(--teal-dark);width:40px;text-align:right;flex-shrink:0; }

  /* Alt accordion */
  .alt-header { display:flex;align-items:center;gap:12px;padding:14px 18px;cursor:pointer;transition:background .15s;user-select:none; }
  .alt-header:hover { background:#FDFCFA; }
  .alt-open .alt-header { background:var(--sand); }
  .alt-expand { font-size:16px;color:var(--muted);margin-left:auto;transition:transform .2s; }
  .alt-open .alt-expand { transform:rotate(90deg);color:var(--teal-dark); }
  .alt-body-inner { display:none;padding:0 18px 18px;border-top:0.5px solid var(--border); }
  .alt-open .alt-body-inner { display:block; }

  .alt-score-pill {
    width:48px;height:48px;border-radius:10px;
    display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;
  }
  .alt-score-pill.sg { background:#E1F5EE; }
  .alt-score-pill.sy { background:#FEF3CD; }
  .alt-score-pill.sr { background:#FDE8E2; }
  .alt-score-pill .n { font-size:16px;font-weight:700; }
  .alt-score-pill.sg .n { color:#0F6E56; }
  .alt-score-pill.sy .n { color:#856404; }
  .alt-score-pill.sr .n { color:#993C1D; }
  .alt-score-pill .l { font-size:9px;color:var(--muted); }

  /* Rank medal */
  .rank-medal { font-size:22px;min-width:30px;text-align:center;flex-shrink:0; }

  /* Right panel */
  .right-panel {
    display:flex;flex-direction:column;gap:14px;
    position:sticky;top:24px;
    max-height:calc(100vh - 48px);
    overflow-y:auto;
    padding-right:4px;
  }
  .right-panel::-webkit-scrollbar { width:6px; }
  .right-panel::-webkit-scrollbar-track { background:transparent; }
  .right-panel::-webkit-scrollbar-thumb { background:var(--border);border-radius:99px; }
  .right-panel::-webkit-scrollbar-thumb:hover { background:var(--muted); }
  .info-card { background:var(--card-bg);border:0.5px solid var(--border);border-radius:12px;padding:20px;box-shadow:var(--shadow); }
  .info-card-title { font-size:13px;font-weight:700;color:var(--text);margin-bottom:12px; }
  .detail-row { display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:0.5px solid var(--sand);font-size:13px; }
  .detail-row:last-child { border-bottom:none; }
  .detail-key { color:var(--muted); }
  .detail-val { font-weight:600;color:var(--text); }
  .detail-val.teal { color:var(--teal-dark); }

  .action-panel { background:var(--teal-light);border:1px solid rgba(29,158,117,.2);border-radius:12px;padding:20px; }
  .action-panel-title { font-size:13px;font-weight:700;color:var(--teal-dark);margin-bottom:12px; }

  /* AHP ranking bar in right panel */
  .ahp-mini-row { display:flex;align-items:center;gap:8px;padding:5px 0; }
  .ahp-mini-label { font-size:11px;color:var(--muted);min-width:80px; }
  .ahp-mini-bar-wrap { flex:1;height:5px;background:var(--sand);border-radius:99px;overflow:hidden; }
  .ahp-mini-bar { height:5px;border-radius:99px;background:var(--teal); }
  .ahp-mini-pct { font-size:11px;font-weight:700;color:var(--teal-dark);min-width:32px;text-align:right; }

  /* Modal */
  .modal-overlay { display:none;position:fixed;inset:0;background:rgba(44,44,42,.5);z-index:100;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(3px); }
  .modal-overlay.open { display:flex;animation:fadeIn .2s ease; }
  @keyframes fadeIn { from{opacity:0}to{opacity:1} }
  .modal { background:var(--card-bg);border-radius:16px;padding:48px 40px;width:480px;text-align:center;box-shadow:0 24px 80px rgba(0,0,0,.18);animation:popIn .28s cubic-bezier(.34,1.56,.64,1); }
  @keyframes popIn { from{transform:scale(.88);opacity:0}to{transform:scale(1);opacity:1} }
  .modal-icon { font-size:48px;margin-bottom:16px; }
  .modal-title { font-size:22px;font-weight:700;color:var(--text);margin-bottom:8px;letter-spacing:-.4px; }
  .modal-sub { font-size:14px;color:var(--muted);line-height:1.6;margin-bottom:28px; }
  .modal-actions { display:flex;gap:12px;justify-content:center; }

  /* ── Priority Bar ── */
.priority-bar {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  max-width: 980px;
  margin-bottom: 20px;
  padding: 16px 20px;
  background: var(--card-bg);
  border: 0.5px solid var(--border);
  border-radius: 14px;
  box-shadow: var(--shadow-md);
}

.priority-bar-label {
  font-size: 11px;
  font-weight: 700;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: .7px;
  white-space: nowrap;
  flex-shrink: 0;
  padding-right: 4px;
  border-right: 1.5px solid var(--border);
  margin-right: 4px;
  line-height: 2;
}

.priority-btns {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  align-items: center;
}

.priority-btn {
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  font-weight: 600;
  color: var(--muted);
  background: var(--sand);
  border: 1.5px solid transparent;
  border-radius: 10px;
  padding: 8px 18px;
  cursor: pointer;
  transition: all .18s cubic-bezier(.22,1,.36,1);
  white-space: nowrap;
  line-height: 1;
  letter-spacing: -.1px;
}

.priority-btn:hover {
  background: var(--teal-light);
  border-color: var(--teal);
  color: var(--teal-dark);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(29,158,117,.18);
}

.priority-btn.active {
  background: var(--teal);
  border-color: var(--teal-dark);
  color: #fff;
  box-shadow: 0 4px 14px rgba(29,158,117,.35);
  transform: translateY(-2px);
}

.priority-btn.active:hover {
  background: var(--teal-dark);
}

/* Botão Limpar — separado visualmente */
.priority-btn.clear-btn {
  background: transparent;
  border-color: #D07060;
  color: #993C1D;
  padding: 7px 14px;
  font-size: 12px;
  margin-left: 4px;
}
.priority-btn.clear-btn:hover {
  background: #FDE8E2;
  border-color: #993C1D;
  transform: translateY(-1px);
  box-shadow: none;
  color: #993C1D;
}

/* Highlight nas barras de peso */
.weight-row.priority-active .weight-key {
  color: var(--teal-dark);
  font-weight: 700;
}
.weight-row.priority-active .weight-pct {
  font-size: 13px;
  color: var(--teal-dark);
}
.weight-row.priority-active .weight-bar {
  box-shadow: 0 0 0 2px var(--teal-light), 0 0 0 3.5px var(--teal);
}

/* Highlight na matriz pairwise */
.pw-table th.pw-priority {
  background: var(--teal-light);
  color: var(--teal-dark);
  font-weight: 700;
}
.pw-table td.pw-priority {
  background: #F5FCFA;
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
          <div class="step-info"><div class="step-label">Região</div><div class="step-value" id="sb-region">—</div></div>
        </a>
        <div class="step-connector done"></div>
        <div class="sidebar-step done">
          <div class="step-num">✓</div>
          <div class="step-info"><div class="step-label">Hospital</div><div class="step-value" id="sb-hospital">—</div></div>
        </div>
        <div class="step-connector done"></div>
        <div class="sidebar-step done">
          <div class="step-num">✓</div>
          <div class="step-info"><div class="step-label">Especialidade</div><div class="step-value" id="sb-spec">—</div></div>
        </div>
        <div class="step-connector done"></div>
        <div class="sidebar-step done">
          <div class="step-num">✓</div>
          <div class="step-info"><div class="step-label">Tipo</div><div class="step-value" id="sb-tipo">—</div></div>
        </div>
        <div class="step-connector done"></div>
        <div class="sidebar-step active">
          <div class="step-num">✦</div>
          <div class="step-info"><div class="step-label">Ranking AHP</div><div class="step-value">Adquirir</div></div>
        </div>
      </div>
    </div>
    <div class="sidebar-info">
      <strong>⚖️ Método AHP</strong><br>
      O ranking é calculado pela <em>Analytic Hierarchy Process</em> com base nos pesos que define para cada critério.
      <br><br>
      <span style="color:#0F6E56;font-weight:700">🟢 ≥ 85</span> Excelente<br>
      <span style="color:#856404;font-weight:700">🟡 70–84</span> Bom<br>
      <span style="color:#993C1D;font-weight:700">🔴 &lt; 70</span> Fraco<br><br>
      <span style="font-size:11px">RC &lt; 10% garante consistência lógica da matriz de pesos.</span>
    </div>
  </aside>

  <main class="main">
    <div class="breadcrumb-bar">
      <a href="1-regiao.php">Início</a><span class="breadcrumb-sep">/</span>
      <a id="bc-regiao" href="#">—</a><span class="breadcrumb-sep">/</span>
      <a id="bc-hospital" href="#">—</a><span class="breadcrumb-sep">/</span>
      <a id="bc-spec" href="#">—</a><span class="breadcrumb-sep">/</span>
      <a id="bc-tipo" href="#">—</a><span class="breadcrumb-sep">/</span>
      <span>Ranking AHP</span>
    </div>

    <div class="context-strip">
      <div class="ctx-item">
        <div class="ctx-emoji">🏥</div>
        <div><div class="ctx-label">Hospital</div><div class="ctx-value" id="ctx-hospital">—</div></div>
      </div>
      <div class="ctx-item">
        <div class="ctx-emoji">🩺</div>
        <div><div class="ctx-label">Especialidade</div><div class="ctx-value" id="ctx-spec">—</div></div>
      </div>
      <div class="ctx-item">
        <div class="ctx-emoji">🖥️</div>
        <div><div class="ctx-label">Tipo de equipamento</div><div class="ctx-value" id="ctx-tipo">—</div></div>
      </div>
    </div>

    <div class="page-header">
      <div class="page-title">Ranking AHP de equipamentos</div>
      <div class="page-sub" id="page-sub">Configure os pesos dos critérios e obtenha o ranking</div>
    </div>

  <!-- ── PRIORITY SHORTCUTS ── -->
<div class="priority-bar" id="priority-bar">
  <span class="priority-bar-label">Priorizar critério</span>
  <div class="priority-btns">
    <button class="priority-btn" data-priority="custo"      onclick="setPriority('custo')">💰 Custo</button>
    <button class="priority-btn" data-priority="manutencao" onclick="setPriority('manutencao')">🔧 Manutenção</button>
    <button class="priority-btn" data-priority="prazo"      onclick="setPriority('prazo')">⚡ Urgência</button>
    <button class="priority-btn" data-priority="vida"       onclick="setPriority('vida')">🛡️ Durabilidade</button>
    <button class="priority-btn clear-btn" id="priority-clear" onclick="clearPriority()" style="display:none">✕ Limpar</button>
  </div>
</div>

    <!-- ── AHP CONFIGURATION PANEL ── -->
    <div class="ahp-panel open" id="ahp-panel" style="max-width:980px">
      <div class="ahp-panel-header" onclick="togglePanel()">
        <div class="ahp-panel-title">
          ⚖️ Configuração AHP — pesos dos critérios
          <span id="rc-badge" class="rc-badge">RC = …</span>
        </div>
        <div class="ahp-toggle-icon">›</div>
      </div>
      <div class="ahp-panel-sub">Ajuste a importância relativa entre critérios. O ranking atualiza automaticamente.</div>

      <div class="ahp-body" id="ahp-body">
        <!-- Weight bars (read-only visual) -->
        <div class="weight-grid" id="weight-bars"></div>

        <!-- Pairwise matrix -->
        <div class="pw-section">
          <div class="pw-section-label">Matriz de comparação par-a-par (escala Saaty 1–9)</div>
          <div style="overflow-x:auto">
            <table class="pw-table" id="pw-table"></table>
          </div>
        </div>
      </div>
    </div>

    <!-- ── RANKING OUTPUT ── -->
    <div class="rec-layout" id="rec-layout"></div>
  </main>
</div>

<!-- Modal -->
<div class="modal-overlay" id="modal">
  <div class="modal">
    <div class="modal-icon">✅</div>
    <div class="modal-title">Pedido submetido</div>
    <div class="modal-sub" id="modal-sub">O pedido de aquisição foi registado e enviado para aprovação da direção clínica.</div>
    <div class="modal-actions">
      <button class="cta-btn outline" onclick="closeModal()">Fechar</button>
      <a href="1-regiao.php" class="cta-btn">Nova consulta</a>
    </div>
  </div>
</div>

<script>
/* ═══════════════════════════════════════════════════════
   AHP ENGINE
═══════════════════════════════════════════════════════ */
var RI = [0, 0, 0.58, 0.90, 1.12, 1.24, 1.32, 1.41, 1.45];
var SV = [1/9,1/8,1/7,1/6,1/5,1/4,1/3,1/2,1,2,3,4,5,6,7,8,9];
var SL = ['1/9','1/8','1/7','1/6','1/5','1/4','1/3','1/2','1','2','3','4','5','6','7','8','9'];
var BAR_COLORS = ['#1D9E75','#185FA5','#854F0B','#993C1D','#534AB7'];

/* ── Critérios base (sem score global) ── */
var BASE_CRITERIA = [
  { key:'custo',      label:'Custo de aquisição', minim:true,  icon:'💶' },
  { key:'manutencao', label:'Manutenção/ano',      minim:true,  icon:'🔧' },
  { key:'prazo',      label:'Prazo de entrega',    minim:true,  icon:'📦' },
  { key:'vida',       label:'Vida útil',           minim:false, icon:'⏳' },
];

/* ── Extrai critérios específicos do tipo de equipamento ── */
function specificSpecs(eq) {
  var t = String(eq.tipo || '').toLowerCase();
  var specs = [];
  function add(label, value, display, minim) {
    if (value !== null && value !== undefined && value !== '')
      specs.push({ label: label, value: Number(value) || 0, display: display, minim: !!minim });
  }
  if (t.includes('tac')) {
    add('Cortes', eq.tac_cortes, eq.tac_cortes + ' cortes', false);
    add('Dose radiação', eq.tac_dose_radiacao, eq.tac_dose_radiacao + ' mSv', true);
  } else if (t === 'rm' || t.includes('resson')) {
    add('Campo magnético', eq.rm_campo_magnetico, eq.rm_campo_magnetico + ' T', false);
    add('Tempo aquisição', eq.rm_tempo_aquisicao, eq.rm_tempo_aquisicao + ' min', true);
  } else if (t.includes('rx')) {
    add('Dose radiação', eq.rx_dose_radiacao, eq.rx_dose_radiacao + ' mSv', true);
  } else if (t.includes('ang')) {
    add('Resolução imagem', eq.angio_resolucao_imagem, eq.angio_resolucao_imagem + ' mm', true);
    add('Dose radiação', eq.angio_dose_radiacao, eq.angio_dose_radiacao + ' mSv', true);
  } else if (t.includes('holter')) {
    add('Duração gravação', eq.holter_duracao_gravacao, eq.holter_duracao_gravacao + ' h', false);
    add('Canais', eq.holter_canais, eq.holter_canais + ' canais', false);
  } else if (t.includes('eletro') || t.includes('ecg')) {
    add('Canais', eq.ecg_canais, eq.ecg_canais + ' canais', false);
    add('Interpretação automática', eq.ecg_interpretacao_automatica,
        Number(eq.ecg_interpretacao_automatica) === 1 ? 'Sim' : 'Não', false);
  } else if (t.includes('monitor')) {
    add('Parâmetros', eq.monitor_parametros, eq.monitor_parametros + ' parâmetros', false);
    add('Bateria', eq.monitor_bateria_horas, eq.monitor_bateria_horas + ' h', false);
  } else if (t.includes('desf')) {
    add('Energia máxima', eq.desf_energia_max_j, eq.desf_energia_max_j + ' J', false);
    add('Modo DEA', eq.desf_modo_dea, Number(eq.desf_modo_dea) === 1 ? 'Sim' : 'Não', false);
  } else if (t.includes('eco') || t.includes('ecó')) {
    add('Sondas', eq.eco_num_sondas, eq.eco_num_sondas + ' sondas', false);
    add('Doppler', eq.eco_doppler, Number(eq.eco_doppler) === 1 ? 'Sim' : 'Não', false);
  } else if (t.includes('cardiotoc')) {
    add('Gemelar', eq.ctg_gemelar, Number(eq.ctg_gemelar) === 1 ? 'Sim' : 'Não', false);
    add('Autonomia', eq.ctg_autonomia_horas, eq.ctg_autonomia_horas + ' h', false);
  } else if (t.includes('bronco')) {
    add('Diâmetro', eq.bronco_diametro_mm, eq.bronco_diametro_mm + ' mm', true);
    add('Canal trabalho', eq.bronco_canal_trabalho_mm, eq.bronco_canal_trabalho_mm + ' mm', false);
  } else if (t.includes('ventil')) {
    add('Modos ventilação', eq.vent_modos_ventilacao, eq.vent_modos_ventilacao + ' modos', false);
    add('Autonomia', eq.vent_autonomia_horas, eq.vent_autonomia_horas + ' h', false);
  } else if (t.includes('ortop') || t.includes('serra') || t.includes('mesa') || t.includes('arco')) {
    add('Precisão', eq.orto_precisao_mm, eq.orto_precisao_mm + ' mm', true);
    add('Portátil', eq.orto_portatil, Number(eq.orto_portatil) === 1 ? 'Sim' : 'Não', false);
  }
  return specs;
}

var catalogo = <?= $catalogoJson ?>;

/* Detecta critérios específicos a partir do primeiro equipamento do catálogo */
var SPEC_CRITERIA = (function() {

  for (var i = 0; i < catalogo.length; i++) {

    var specs = specificSpecs(catalogo[i]);

    if (specs.length > 0) {

      return specs.slice(0, 2).map(function(sp, j) {

        return { key: 'spec' + j, label: sp.label, minim: sp.minim, icon: j === 0 ? '🧩' : '⚙️' };

      });

    }

  }

  return [];

})();

var CRITERIA = BASE_CRITERIA.concat(SPEC_CRITERIA);
var N = CRITERIA.length;
var pwMatrix = [];
for (var i = 0; i < N; i++) { pwMatrix.push([]); for (var j = 0; j < N; j++) pwMatrix[i].push(1); }

function calcAHP() {
  var colSums = pwMatrix[0].map(function(_,j){return pwMatrix.reduce(function(s,r){return s+r[j];},0);});
  var norm    = pwMatrix.map(function(r){return r.map(function(v,j){return v/colSums[j];});});
  var w       = norm.map(function(r){return r.reduce(function(s,v){return s+v;},0)/N;});
  var lam     = w.reduce(function(s,wi,i){return s+colSums[i]*wi;},0);
  var CI      = (lam-N)/(N-1);
  var CR      = CI / (RI[N]||1.45);
  return {w:w, CR:CR};
}

/* ═══════════════════════════════════════════════════════
   EXTRACT NUMERIC VALUES FROM EQUIP DATA
═══════════════════════════════════════════════════════ */
function parseNum(str) {
  if (!str) return 0;
  var s = String(str).replace(/[€.]/g,'').replace(',','.').replace(/[^0-9.]/g,'');
  var n = parseFloat(s);
  return isNaN(n) ? 0 : n;
}

function getSpecVal(eq, keyHint) {
  var spec = (eq.specs||[]).find(function(s){
    var k = s.k.toLowerCase();
    var h = keyHint.toLowerCase();
    return k.includes(h)||h.includes(k.split('(')[0].trim());
  });
  return spec ? parseNum(spec.v) : 0;
}

function getCriterionVal(eq, crit) {
  if (crit.key === 'custo')      return eq.custo      || 0;
  if (crit.key === 'manutencao') return eq.manutencao || 0;
  if (crit.key === 'prazo')      return eq.prazo      || 0;
  if (crit.key === 'vida')       return eq.vida       || 0;
  if (crit.key === 'spec0')      return eq.spec0      || 0;
  if (crit.key === 'spec1')      return eq.spec1      || 0;
  return 0;
}

/* ═══════════════════════════════════════════════════════
   NORMALIZE & SCORE
═══════════════════════════════════════════════════════ */
function ahpScores(equips, weights) {
  var normCols = CRITERIA.map(function(c, ci) {
    var raw = equips.map(function(e){ return getCriterionVal(e,c); });
    var adj = c.minim ? raw.map(function(v){ return v>0 ? 1/v : 0; }) : raw.slice();
    var sum = adj.reduce(function(s,v){return s+v;},0);
    return adj.map(function(v){ return sum>0 ? v/sum : 0; });
  });
  return equips.map(function(_, ai) {
    return weights.reduce(function(acc, wi, ci) { return acc + wi * normCols[ci][ai]; }, 0);
  });
}

/* ═══════════════════════════════════════════════════════
   URL PARAMS
═══════════════════════════════════════════════════════ */
var params        = new URLSearchParams(window.location.search);
var regiao        = params.get('regiao')        || '—';
var hospital      = params.get('hospital')      || '—';
var especialidade = params.get('especialidade') || 'Imagiologia';
var tipo          = decodeURIComponent(params.get('tipo') || 'Tomografia Computorizada');

document.getElementById('sb-region').textContent    = regiao;
document.getElementById('sb-hospital').textContent  = hospital;
document.getElementById('sb-spec').textContent      = especialidade;
document.getElementById('sb-tipo').textContent      = tipo;
document.getElementById('ctx-hospital').textContent = hospital;
document.getElementById('ctx-spec').textContent     = especialidade;
document.getElementById('ctx-tipo').textContent     = tipo;
document.getElementById('page-sub').textContent     = tipo+' · '+especialidade+' — ranking calculado por AHP';

var r = encodeURIComponent(regiao), h = encodeURIComponent(hospital), e = encodeURIComponent(especialidade);
var bc = function(id, txt, href){ var el=document.getElementById(id); el.textContent=txt; el.href=href; };
bc('bc-regiao',  regiao,       '2-hospital.php?regiao='+r);
bc('bc-hospital',hospital,     '3-especialidade.php?regiao='+r+'&hospital='+h);
bc('bc-spec',    especialidade,'4-decisao.php?regiao='+r+'&hospital='+h+'&especialidade='+e);
bc('bc-tipo',    tipo,         '4a-tipo-equipamento.php?regiao='+r+'&hospital='+h+'&especialidade='+e);

/* ═══════════════════════════════════════════════════════
   LOAD CATALOGUE
═══════════════════════════════════════════════════════ */

var equips = catalogo.map(function(e) {
  var specs = specificSpecs(e);
  var specVals = {};
  SPEC_CRITERIA.forEach(function(c, i) {
    specVals[c.key] = specs[i] ? (Number(specs[i].value) || 0) : 0;
  });

  return {
    id: e.id_equip,
    name: e.fabricante + ' ' + e.modelo,
    sub: e.tipo + ' · ' + Number(e.custo_aq).toLocaleString('pt-PT') + '€',
    detail: e.fabricante + ' · ' + e.modelo,
    /* critérios base */
    custo:      Number(e.custo_aq)    || 0,
    manutencao: Number(e.custo_man)   || 0,
    prazo:      Number(e.prazo_ent)   || 0,
    vida:       Number(e.t_vida_util) || 0,
    /* critérios específicos */
    spec0: specVals.spec0 || 0,
    spec1: specVals.spec1 || 0,
    specDetails: specs,
    tags: [ e.tipo, e.fabricante, 'Prazo ' + e.prazo_ent + ' dias' ],
    highlightTags: [ e.tipo ],
    specs: [
      { k: 'Fabricante',      v: e.fabricante },
      { k: 'Modelo',          v: e.modelo },
      { k: 'Tipo',            v: e.tipo },
      { k: 'Preço estimado',  v: Number(e.custo_aq).toLocaleString('pt-PT') + '€', hi: true },
      { k: 'Manutenção/ano',  v: Number(e.custo_man).toLocaleString('pt-PT') + '€' },
      { k: 'Prazo de entrega',v: e.prazo_ent + ' dias' },
      { k: 'Tempo de exame',  v: e.t_exame + ' min' },
      { k: 'Vida útil',       v: e.t_vida_util + ' anos', hi: true }
    ].concat(specs.map(function(s) { return { k: s.label, v: s.display }; })),
    financials: {
      invest:   Number(e.custo_aq).toLocaleString('pt-PT') + '€',
      total10y: Number(Number(e.custo_aq) + Number(e.custo_man) * 10).toLocaleString('pt-PT') + '€',
      saving: '—', roi: '—'
    }
  };
});

if (equips.length === 0) {
  document.getElementById('rec-layout').innerHTML =
    '<div class="info-card">Não existem equipamentos no catálogo para este tipo.</div>';
}

/* ═══════════════════════════════════════════════════════
   SCORE CLASS
═══════════════════════════════════════════════════════ */
function sc(v) { return v>=85?'sg':v>=70?'sy':'sr'; }
function fmtFrac(v){
  if(v>=1) return v%1===0?''+v:v.toFixed(2);
  for(var d=2;d<=9;d++) if(Math.abs(v-1/d)<0.01) return '1/'+d;
  return v.toFixed(2);
}

/* ═══════════════════════════════════════════════════════
   RENDER WEIGHT BARS
═══════════════════════════════════════════════════════ */
function renderWeightBars(w) {
  var html = CRITERIA.map(function(c, i) {
    var isActive = activePriority && c.key === activePriority;
    return '<div class="weight-row' + (isActive ? ' priority-active' : '') + '">'
      + '<div class="weight-label"><span class="weight-key">' + c.label + '</span>'
      + '<span class="weight-pct">' + (w[i] * 100).toFixed(1) + '%</span></div>'
      + '<div class="weight-bar-wrap"><div class="weight-bar" style="width:' + (w[i] * 100).toFixed(1)
      + '%;background:' + BAR_COLORS[i % BAR_COLORS.length] + '"></div></div>'
      + '</div>';
  }).join('');
  document.getElementById('weight-bars').innerHTML = html;
}

/* ═══════════════════════════════════════════════════════
   RENDER PAIRWISE MATRIX
═══════════════════════════════════════════════════════ */
function renderPW() {
  var html = '<thead><tr><th></th>'
    + CRITERIA.map(function(c, i) {
        var isPri = activePriority && c.key === activePriority;
        return '<th class="' + (isPri ? 'pw-priority' : '') + '">' + c.label + '</th>';
      }).join('')
    + '</tr></thead><tbody>';

  for (var i = 0; i < N; i++) {
    var rowPri = activePriority && CRITERIA[i].key === activePriority;
    html += '<tr><th class="' + (rowPri ? 'pw-priority' : '') + '">' + CRITERIA[i].label + '</th>';
    for (var j = 0; j < N; j++) {
      var colPri = activePriority && CRITERIA[j].key === activePriority;
      var cellCls = (rowPri || colPri) ? ' class="pw-priority"' : '';
      if (i === j) {
        html += '<td' + cellCls + ' style="text-align:center;color:var(--muted)">1</td>';
      } else if (j < i) {
        html += '<td' + cellCls + ' style="text-align:center;color:var(--muted);font-size:12px">' + fmtFrac(pwMatrix[i][j]) + '</td>';
      } else {
        var cur = pwMatrix[i][j];
        var sel = '<select data-i="' + i + '" data-j="' + j + '">';
        SV.forEach(function(v, k) {
          sel += '<option value="' + v + '"' + (Math.abs(v - cur) < 0.001 ? ' selected' : '') + '>' + SL[k] + '</option>';
        });
        sel += '</select>';
        html += '<td' + cellCls + '>' + sel + '</td>';
      }
    }
    html += '</tr>';
  }
  html += '</tbody>';
  document.getElementById('pw-table').innerHTML = html;

  document.getElementById('pw-table').querySelectorAll('select').forEach(function(sel) {
    sel.addEventListener('change', function() {
      var i = parseInt(this.dataset.i), j = parseInt(this.dataset.j), v = parseFloat(this.value);
      pwMatrix[i][j] = v; pwMatrix[j][i] = 1 / v;
      renderAll();
    });
  });
}

/* ═══════════════════════════════════════════════════════
   RENDER RANKING
═══════════════════════════════════════════════════════ */
var MEDALS = ['🥇','🥈','🥉','4.','5.','6.','7.'];

function renderRanking(w) {
  var rawScores = ahpScores(equips, w);
  var ranked = equips.map(function(eq,i){ return {eq:eq, ahp:rawScores[i]}; })
    .sort(function(a,b){return b.ahp-a.ahp;});
  var maxAhp = ranked[0].ahp;

  var best = ranked[0].eq;
  var restRanked = ranked.slice(1);

  /* AHP score as 0–100 */
  function ahpTo100(v){ return Math.round(v/maxAhp*100); }

  /* ── Specs for best ── */
  function specsHTML(specs){
    return (specs||[]).map(function(s){
      return '<div class="detail-row"><span class="detail-key">'+s.k+'</span>'
        +'<span class="detail-val'+(s.hi?' teal':'')+'">'+s.v+'</span></div>';
    }).join('');
  }

  /* ── Criterion breakdown for a given eq index ── */
  function criterionBreakdown(eq, wts) {
    return CRITERIA.map(function(c,ci){
      var rawArr = equips.map(function(e){return getCriterionVal(e,c);});
      var adj    = c.minim ? rawArr.map(function(v){return v>0?1/v:0;}) : rawArr.slice();
      var sum    = adj.reduce(function(s,v){return s+v;},0);
      var norm   = adj.map(function(v){return sum>0?v/sum:0;});
      var idx    = equips.indexOf(eq);
      var contrib= (wts[ci] * (norm[idx]||0));
      return {label:c.label, pct:(contrib/wts.reduce(function(s,v){return s+v;},0)*100).toFixed(1)};
    });
  }

  /* ── Left column ── */
  var bestScore100 = ahpTo100(ranked[0].ahp);
  var bestCls = sc(bestScore100);

  var leftHTML = '<div>'
    +'<div class="rank-badge">'+MEDALS[0]+' Melhor opção — ranking AHP</div>'
    +'<div class="rec-card">'
    +'<div class="rc-header">'
    +'<div><div class="rc-name">'+best.name+'</div>'
    +'<div class="rc-sub">'+best.sub+'</div>'
    +'<div class="rc-tags">'+(best.tags||[]).map(function(t){
      return '<span class="rc-tag'+(( (best.highlightTags||[]).includes(t))?' hl':'')+'">'+t+'</span>';
    }).join('')+'</div></div>'
    +'<div class="score-circle '+bestCls+'"><div class="num">'+bestScore100+'</div><div class="lbl">/ 100</div></div>'
    +'</div>'
    +'<div style="margin-top:12px">'
    +criterionBreakdown(best,w).map(function(c){
      return '<div class="ahp-score-row">'
        +'<div class="ahp-score-label">'+c.label+'</div>'
        +'<div class="ahp-score-bar-wrap"><div class="ahp-score-bar" style="width:'+Math.min(c.pct*4,100)+'%"></div></div>'
        +'<div class="ahp-score-val">'+c.pct+'%</div>'
        +'</div>';
    }).join('')
    +'</div></div>';

  /* Alternatives */
  leftHTML += '<div style="margin-top:20px">'
    +'<div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.7px;margin-bottom:10px">Alternativas — clique para expandir</div>';

  restRanked.forEach(function(item,idx){
    var eq  = item.eq;
    var s100= ahpTo100(item.ahp);
    var cls = sc(s100);
    var bd  = criterionBreakdown(eq,w);
    leftHTML+='<div class="alt-card-wrap rec-card" id="alt-'+idx+'">'
      +'<div class="alt-header" onclick="toggleAlt('+idx+')">'
      +'<div class="rank-medal">'+MEDALS[idx+1]+'</div>'
      +'<div class="alt-score-pill '+cls+'"><div class="n">'+s100+'</div><div class="l">AHP</div></div>'
      +'<div style="flex:1"><div style="font-size:14px;font-weight:600;color:var(--text)">'+eq.name+'</div>'
      +'<div style="font-size:12px;color:var(--muted);margin-top:2px">'+(eq.detail||eq.sub||'')+'</div></div>'
      +'<div class="alt-expand">›</div></div>'
      +'<div class="alt-body-inner">'
      +'<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:14px">'
      +'<div><div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Contribuição por critério</div>'
      +bd.map(function(c){
        return '<div class="ahp-mini-row"><div class="ahp-mini-label">'+c.label+'</div>'
          +'<div class="ahp-mini-bar-wrap"><div class="ahp-mini-bar" style="width:'+Math.min(parseFloat(c.pct)*4,100)+'%"></div></div>'
          +'<div class="ahp-mini-pct">'+c.pct+'%</div></div>';
      }).join('')
      +'</div>'
      +'<div><div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Especificações</div>'
      +(eq.specs||[]).map(function(s){
        return '<div class="detail-row"><span class="detail-key">'+s.k+'</span>'
          +'<span class="detail-val'+(s.hi?' teal':'')+'">'+s.v+'</span></div>';
      }).join('')
      +'</div></div></div></div>';
  });

  leftHTML += '</div></div>';

  /* ── Right column ── */
  var fin = best.financials||{invest:'—',total10y:'—',saving:'—',roi:'—'};

  var rankSummaryHTML = '<div class="info-card"><div class="info-card-title">📊 Resumo do ranking AHP</div>'
    +ranked.map(function(item,i){
      var s = ahpTo100(item.ahp);
      var cls2 = sc(s);
      var colors2 = {sg:'#1D9E75',sy:'#C9960C',sr:'#993C1D'};
      return '<div style="display:flex;align-items:center;gap:10px;padding:7px 0;border-bottom:0.5px solid var(--sand)">'
        +'<span style="font-size:16px;min-width:24px;text-align:center">'+MEDALS[i]+'</span>'
        +'<div style="flex:1;font-size:13px;font-weight:500;color:var(--text)">'+item.eq.name+'</div>'
        +'<div style="min-width:72px"><div style="height:6px;background:var(--sand);border-radius:99px;overflow:hidden">'
        +'<div style="height:6px;border-radius:99px;background:'+colors2[cls2]+';width:'+(item.ahp/maxAhp*100).toFixed(1)+'%"></div></div></div>'
        +'<span style="font-size:12px;font-weight:700;color:'+colors2[cls2]+';min-width:30px;text-align:right">'+s+'</span>'
        +'</div>';
    }).join('')+'</div>';

  var rightHTML = '<div class="right-panel">'
    + rankSummaryHTML
    +'<div class="info-card"><div class="info-card-title">📋 Especificações — '+best.name+'</div>'
    + specsHTML(best.specs)
    +'</div>'
    +'<div class="action-panel"><div class="action-panel-title">⚡ Ações</div>'
    +'<div style="display:flex;flex-direction:column;gap:8px">'
    +'<button class="cta-btn full" onclick="showModal()">Submeter pedido de aquisição →</button>'
    +'<button type="button" onclick="exportarRelatorioPDF()" class="cta-btn outline full" style="margin-top:2px">📄 Exportar relatório PDF</button>'
    +'</div></div>'
    +'<div class="info-card"><div class="info-card-title">💰 Análise financeira — 1.º classificado</div>'
    +'<div class="detail-row"><span class="detail-key">Investimento inicial</span><span class="detail-val">'+fin.invest+'</span></div>'
    +'<div class="detail-row"><span class="detail-key">Custo total 10 anos</span><span class="detail-val">'+fin.total10y+'</span></div>'
    +'<div class="detail-row"><span class="detail-key">Poupança estimada</span><span class="detail-val teal">'+fin.saving+'</span></div>'
    +'<div class="detail-row"><span class="detail-key">ROI estimado</span><span class="detail-val teal">'+fin.roi+'</span></div>'
    +'</div></div>';

  document.getElementById('rec-layout').innerHTML = leftHTML + rightHTML;

  document.getElementById('modal-sub').innerHTML =
    'O pedido de aquisição do <strong>'+best.name+'</strong> (1.º no ranking AHP) foi registado e enviado para aprovação da direção clínica.';
}

/* ═══════════════════════════════════════════════════════
   MAIN RENDER
═══════════════════════════════════════════════════════ */
function renderAll() {
  var res = calcAHP();
  var w   = res.w;
  var cr  = res.CR;

  var badge = document.getElementById('rc-badge');
  badge.textContent = 'RC = '+(cr*100).toFixed(1)+'%'+(cr<0.1?' ✓':'  ⚠');
  badge.className   = 'rc-badge'+(cr<0.1?'':' warn');

  renderWeightBars(w);
  renderPW();
  renderRanking(w);
}

function togglePanel() {
  document.getElementById('ahp-panel').classList.toggle('open');
}

function toggleAlt(idx) {
  var el = document.getElementById('alt-'+idx);
  el.classList.toggle('alt-open');
}

function showModal()  { document.getElementById('modal').classList.add('open'); }
function closeModal() { document.getElementById('modal').classList.remove('open'); }

function escapeHTML(v) {
  return String(v ?? '—')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function ahpTo100(value) {
  if (!isFinite(value) || value <= 0) return 0;

  var res = calcAHP();
  var rawScores = ahpScores(equips, res.w);
  var maxScore = Math.max(...rawScores);

  if (!isFinite(maxScore) || maxScore <= 0) return 0;

  return Math.round((value / maxScore) * 100);
}

function exportarRelatorioPDF() {
  var res = calcAHP();
  var w = res.w;
  var cr = res.CR;

  var rawScores = ahpScores(equips, w);
  var ranked = equips.map(function(eq, i) {
    return { eq: eq, ahp: rawScores[i] };
  }).sort(function(a, b) {
    return b.ahp - a.ahp;
  });

  var maxAhp = ranked[0].ahp || 1;
  var best = ranked[0].eq;
  var bestScore = ahpTo100(ranked[0].ahp);

  var rankingRows = ranked.map(function(item, i) {
    var score = ahpTo100(item.ahp);
    return `
      <tr>
        <td>${i + 1}.º</td>
        <td>${escapeHTML(item.eq.name)}</td>
        <td>${escapeHTML(item.eq.specs?.[0]?.v || '—')}</td>
        <td><strong>${score}/100</strong></td>
      </tr>
    `;
  }).join('');

  var criteriosRows = CRITERIA.map(function(c, i) {
    return `
      <tr>
        <td>${escapeHTML(c.label)}</td>
        <td>${(w[i] * 100).toFixed(1)}%</td>
      </tr>
    `;
  }).join('');

  var specsRows = (best.specs || []).map(function(s) {
    return `
      <tr>
        <td>${escapeHTML(s.k)}</td>
        <td><strong>${escapeHTML(s.v)}</strong></td>
      </tr>
    `;
  }).join('');

  var data = new Date().toLocaleString('pt-PT');

  var html = `
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Relatório de Decisão — MedDSS</title>

<style>
  body {
    font-family: Arial, sans-serif;
    background: #f3f4f6;
    color: #111827;
    margin: 0;
    padding: 40px;
  }

  .report {
    max-width: 900px;
    margin: auto;
    background: white;
    padding: 48px;
    border-radius: 18px;
    box-shadow: 0 20px 50px rgba(0,0,0,.10);
  }

  .header {
    display: flex;
    justify-content: space-between;
    border-bottom: 3px solid #0f766e;
    padding-bottom: 20px;
    margin-bottom: 32px;
  }

  .logo {
    font-size: 30px;
    font-weight: 800;
    color: #0f766e;
  }

  .muted {
    color: #6b7280;
    font-size: 13px;
  }

  h1 {
    margin: 0 0 10px;
    font-size: 28px;
  }

  h2 {
    margin-top: 34px;
    font-size: 18px;
    color: #0f766e;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 8px;
  }

  .grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-top: 18px;
  }

  .box {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 14px;
  }

  .label {
    text-transform: uppercase;
    font-size: 11px;
    color: #6b7280;
    font-weight: 700;
    margin-bottom: 6px;
  }

  .value {
    font-weight: 700;
  }

  .recommendation {
    margin-top: 20px;
    padding: 24px;
    background: #ecfdf5;
    border: 2px solid #10b981;
    border-radius: 16px;
  }

  .score {
    font-size: 44px;
    font-weight: 800;
    color: #047857;
    margin: 10px 0;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 14px;
  }

  th, td {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
  }

  th {
    background: #f3f4f6;
    font-size: 13px;
  }

  .footer {
    margin-top: 40px;
    text-align: center;
    font-size: 12px;
    color: #6b7280;
  }

  .print-btn {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #0f766e;
    color: white;
    border: none;
    padding: 12px 18px;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
  }

  @media print {
    body {
      background: white;
      padding: 0;
    }

    .report {
      box-shadow: none;
      border-radius: 0;
    }

    .print-btn {
      display: none;
    }
  }
</style>
</head>

<body>

<button class="print-btn" onclick="window.print()">Guardar como PDF</button>

<div class="report">

  <div class="header">
    <div>
      <div class="logo">MedDSS</div>
      <div class="muted">Sistema de Apoio à Decisão em Saúde</div>
    </div>
    <div class="muted">
      <strong>Relatório de Decisão</strong><br>
      ${escapeHTML(data)}
    </div>
  </div>

  <h1>Relatório de Recomendação AHP</h1>

  <p>
    Este relatório apresenta a recomendação gerada pelo MedDSS com base no método
    <strong>Analytic Hierarchy Process</strong>, considerando os critérios definidos
    para avaliação das alternativas de equipamento.
  </p>

  <h2>1. Contexto da decisão</h2>

  <div class="grid">
    <div class="box">
      <div class="label">Região</div>
      <div class="value">${escapeHTML(regiao)}</div>
    </div>
    <div class="box">
      <div class="label">Hospital</div>
      <div class="value">${escapeHTML(hospital)}</div>
    </div>
    <div class="box">
      <div class="label">Especialidade</div>
      <div class="value">${escapeHTML(especialidade)}</div>
    </div>
    <div class="box">
      <div class="label">Tipo de equipamento</div>
      <div class="value">${escapeHTML(tipo)}</div>
    </div>
  </div>

  <h2>2. Melhor alternativa recomendada</h2>

  <div class="recommendation">
    <div class="label">Equipamento recomendado</div>
    <h1>${escapeHTML(best.name)}</h1>
    <div class="score">${bestScore}/100</div>
    <p>
      A alternativa <strong>${escapeHTML(best.name)}</strong> foi recomendada por obter
      o melhor resultado global no ranking AHP, considerando os pesos atribuídos aos
      critérios de decisão.
    </p>
  </div>

  <h2>3. Ranking das alternativas</h2>

  <table>
    <thead>
      <tr>
        <th>Posição</th>
        <th>Equipamento</th>
        <th>Fabricante</th>
        <th>Score AHP</th>
      </tr>
    </thead>
    <tbody>
      ${rankingRows}
    </tbody>
  </table>

  <h2>4. Pesos dos critérios AHP</h2>

  <table>
    <thead>
      <tr>
        <th>Critério</th>
        <th>Peso calculado</th>
      </tr>
    </thead>
    <tbody>
      ${criteriosRows}
    </tbody>
  </table>

  <p>
    Índice de consistência da matriz: <strong>RC = ${(cr * 100).toFixed(1)}%</strong>.
    ${cr < 0.1 ? 'A matriz apresenta consistência aceitável.' : 'A matriz deve ser revista, pois apresenta inconsistência elevada.'}
  </p>

  <h2>5. Especificações do equipamento recomendado</h2>

  <table>
    <tbody>
      ${specsRows}
    </tbody>
  </table>

  <h2>6. Conclusão</h2>

  <p>
    Com base na análise multicritério realizada, o sistema recomenda a aquisição de
    <strong>${escapeHTML(best.name)}</strong>. Esta recomendação deve ser interpretada
    como apoio à decisão, sendo a decisão final da responsabilidade do decisor clínico
    ou administrativo.
  </p>

  <div class="footer">
    Relatório gerado automaticamente pelo MedDSS.
  </div>

</div>

</body>
</html>
`;

  var win = window.open('', '_blank');
  win.document.open();
  win.document.write(html);
  win.document.close();

  setTimeout(function() {
    win.focus();
    win.print();
  }, 500);
}

/* ═══════════════════════════════════════════════════════
   PRIORITY SHORTCUTS
   Mapeia cada botão para uma preset da matriz pairwise.
   Não altera calcAHP() nem ahpScores() — apenas preenche
   pwMatrix com valores Saaty pré-definidos e chama renderAll().
═══════════════════════════════════════════════════════ */

function buildPreset(priorityIdx) {
  /* Constrói uma matriz NxN onde a linha/coluna priorityIdx domina */
  var m = [];
  for (var i = 0; i < N; i++) {
    m.push([]);
    for (var j = 0; j < N; j++) {
      if (i === j)              m[i].push(1);
      else if (i === priorityIdx) m[i].push(5);   /* linha prioritária: 5x mais */
      else if (j === priorityIdx) m[i].push(1/5); /* coluna prioritária */
      else                        m[i].push(1);   /* restantes: iguais entre si */
    }
  }
  return m;
}

var PRIORITY_PRESETS = {
  custo:      function() { return buildPreset(0); },
  manutencao: function() { return buildPreset(1); },
  prazo:      function() { return buildPreset(2); },
  vida:       function() { return buildPreset(3); },
};

var activePriority = null; /* chave activa, ou null */

function setPriority(key) {
  if (activePriority === key) { clearPriority(); return; }
  activePriority = key;
  var preset = PRIORITY_PRESETS[key]();  /* <-- invocar como função */
  for (var i = 0; i < N; i++)
    for (var j = 0; j < N; j++)
      pwMatrix[i][j] = preset[i][j];
  _updatePriorityUI();
  renderAll();
}
function clearPriority() {
  activePriority = null;

  /* Repõe a matriz identidade (todos 1) */
  for (var i = 0; i < N; i++) {
    for (var j = 0; j < N; j++) {
      pwMatrix[i][j] = 1;
    }
  }

  _updatePriorityUI();
  renderAll();
}

function _updatePriorityUI() {
  /* Actualiza classe dos botões */
  document.querySelectorAll('.priority-btn[data-priority]').forEach(function(btn) {
    btn.classList.toggle('active', btn.dataset.priority === activePriority);
  });

  /* Mostra/oculta botão "Limpar" */
  var clearBtn = document.getElementById('priority-clear');
  if (clearBtn) clearBtn.style.display = activePriority ? '' : 'none';
}

/* ── Bootstrap ── */
renderAll();
</script>
</body>
</html>