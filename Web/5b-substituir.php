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
$equipAtualId  = $_GET['equipAtualId'] ?? null;

if (!$equipAtualId) {
    die('Equipamento atual não indicado.');
}

/* Equipamento atual instalado */
$stmtAtual = db()->prepare("
    SELECT
        e.id_equip,
        e.tipo,
        e.fabricante,
        e.modelo,
        e.data_install,
        e.custo_aq,
        e.custo_man,
        e.prazo_ent,
        e.t_exame,
        e.t_vida_util,
        tac.cortes AS tac_cortes,
        tac.dose_radiacao AS tac_dose_radiacao,
        rm.campo_magnetico AS rm_campo_magnetico,
        rm.tempo_aquisicao AS rm_tempo_aquisicao,
        rx.digital AS rx_digital,
        rx.dose_radiacao AS rx_dose_radiacao,
        angio.resolucao_imagem AS angio_resolucao_imagem,
        angio.dose_radiacao AS angio_dose_radiacao,
        holter.duracao_gravacao AS holter_duracao_gravacao,
        holter.canais AS holter_canais,
        ecg.canais AS ecg_canais,
        ecg.interpretacao_automatica AS ecg_interpretacao_automatica,
        mon.parametros AS monitor_parametros,
        mon.bateria_horas AS monitor_bateria_horas,
        desf.energia_max_j AS desf_energia_max_j,
        desf.modo_dea AS desf_modo_dea,
        eco.num_sondas AS eco_num_sondas,
        eco.doppler AS eco_doppler,
        ctg.gemelar AS ctg_gemelar,
        ctg.autonomia_horas AS ctg_autonomia_horas,
        bronco.diametro_mm AS bronco_diametro_mm,
        bronco.canal_trabalho_mm AS bronco_canal_trabalho_mm,
        vent.modos_ventilacao AS vent_modos_ventilacao,
        vent.autonomia_horas AS vent_autonomia_horas,
        orto.precisao_mm AS orto_precisao_mm,
        orto.portatil AS orto_portatil
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
    WHERE e.id_equip = ?
      AND e.id_hosp IS NOT NULL
");

$stmtAtual->execute([$equipAtualId]);
$equipAtual = $stmtAtual->fetch();

if (!$equipAtual) {
    die('Equipamento atual não encontrado.');
}

/* Catálogo de substituição */
$stmtCatalogo = db()->prepare("
    SELECT
        e.id_equip,
        e.tipo,
        e.fabricante,
        e.modelo,
        e.custo_aq,
        e.custo_man,
        e.prazo_ent,
        e.t_exame,
        e.t_vida_util,
        tac.cortes AS tac_cortes,
        tac.dose_radiacao AS tac_dose_radiacao,
        rm.campo_magnetico AS rm_campo_magnetico,
        rm.tempo_aquisicao AS rm_tempo_aquisicao,
        rx.digital AS rx_digital,
        rx.dose_radiacao AS rx_dose_radiacao,
        angio.resolucao_imagem AS angio_resolucao_imagem,
        angio.dose_radiacao AS angio_dose_radiacao,
        holter.duracao_gravacao AS holter_duracao_gravacao,
        holter.canais AS holter_canais,
        ecg.canais AS ecg_canais,
        ecg.interpretacao_automatica AS ecg_interpretacao_automatica,
        mon.parametros AS monitor_parametros,
        mon.bateria_horas AS monitor_bateria_horas,
        desf.energia_max_j AS desf_energia_max_j,
        desf.modo_dea AS desf_modo_dea,
        eco.num_sondas AS eco_num_sondas,
        eco.doppler AS eco_doppler,
        ctg.gemelar AS ctg_gemelar,
        ctg.autonomia_horas AS ctg_autonomia_horas,
        bronco.diametro_mm AS bronco_diametro_mm,
        bronco.canal_trabalho_mm AS bronco_canal_trabalho_mm,
        vent.modos_ventilacao AS vent_modos_ventilacao,
        vent.autonomia_horas AS vent_autonomia_horas,
        orto.precisao_mm AS orto_precisao_mm,
        orto.portatil AS orto_portatil
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
");

$stmtCatalogo->execute([$equipAtual['tipo']]);
$substitutos = $stmtCatalogo->fetchAll();

$equipAtualJson = json_encode($equipAtual, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$substitutosJson = json_encode($substitutos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MedDSS — Comparação de Equipamentos</title>
<link rel="stylesheet" href="shared.css">
<style>
  .breadcrumb-bar {
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; color: var(--muted); margin-bottom: 28px; flex-wrap: wrap;
  }
  .breadcrumb-bar a { color: var(--teal-dark); text-decoration: none; font-weight: 500; }
  .breadcrumb-bar a:hover { text-decoration: underline; }
  .breadcrumb-sep { color: var(--border); }

  .context-strip {
    display: flex; align-items: center;
    background: var(--card-bg); border: 0.5px solid var(--border);
    border-radius: 12px; overflow: hidden;
    box-shadow: var(--shadow); max-width: 860px; margin-bottom: 32px;
  }
  .ctx-item { display: flex; align-items: center; gap: 10px; padding: 14px 20px; flex: 1; }
  .ctx-item + .ctx-item { border-left: 0.5px solid var(--border); }
  .ctx-emoji { font-size: 16px; flex-shrink: 0; }
  .ctx-label { font-size: 10px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; }
  .ctx-value { font-size: 13px; font-weight: 600; color: var(--text); margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }

  /* Navigator */
  .rep-nav {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 24px; background: var(--card-bg);
    border: 0.5px solid var(--border); border-radius: 14px;
    padding: 14px 18px; box-shadow: var(--shadow); flex-wrap: wrap;
    max-width: 980px;
  }
  .rep-nav-label { font-size: 11px; font-weight: 700; color: var(--muted); white-space: nowrap; text-transform: uppercase; letter-spacing: 0.5px; }
  .rep-option {
    display: flex; align-items: center; gap: 7px;
    padding: 7px 14px; border-radius: 99px;
    border: 1.5px solid var(--border); cursor: pointer;
    transition: all 0.15s; background: var(--bg);
    font-size: 13px; font-weight: 500; color: var(--text); white-space: nowrap;
  }
  .rep-option:hover { border-color: #B5B3AB; background: var(--sand); }
  .rep-option.active { border-color: var(--teal); background: var(--teal-light); color: var(--teal-dark); font-weight: 700; }
  .rep-star { font-size: 11px; background: var(--teal); color: #fff; padding: 1px 6px; border-radius: 99px; font-weight: 700; }
  .rep-nav-arrows { margin-left: auto; display: flex; gap: 8px; flex-shrink: 0; }
  .nav-arrow-btn {
    width: 34px; height: 34px; border: 0.5px solid var(--border);
    border-radius: 8px; background: var(--bg); cursor: pointer;
    font-size: 16px; display: flex; align-items: center; justify-content: center;
    transition: all 0.15s; color: var(--text);
  }
  .nav-arrow-btn:hover { background: var(--sand); border-color: #B5B3AB; }
  .nav-arrow-btn:disabled { opacity: 0.3; cursor: default; }

  /* Key metrics banner */
  .km-banner-row { display: flex; gap: 12px; margin-bottom: 20px; max-width: 980px; flex-wrap: wrap; }
  .km-banner {
    display: flex; align-items: center; gap: 12px;
    border-radius: 10px; padding: 12px 16px; flex: 1; min-width: 260px;
  }
  .km-banner.primary   { background: var(--teal-light);  border: 1px solid rgba(29,158,117,0.25); }
  .km-banner.secondary { background: var(--sand);         border: 1px solid var(--border); }
  .km-banner.warning   { background: #FDE8E2;              border: 1px solid rgba(153,60,29,0.2); }
  .km-icon { font-size: 20px; flex-shrink: 0; }
  .km-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
  .km-banner.primary   .km-label { color: var(--teal-dark); }
  .km-banner.secondary .km-label { color: var(--muted); }
  .km-banner.warning   .km-label { color: #993C1D; }
  .km-vals { display: flex; gap: 14px; margin-top: 4px; flex-wrap: wrap; }
  .km-val-item { display: flex; flex-direction: column; }
  .km-val-label { font-size: 10px; color: var(--muted); }
  .km-val-value { font-size: 15px; font-weight: 700; }
  .km-banner.primary   .km-val-value { color: var(--teal-dark); }
  .km-banner.secondary .km-val-value { color: var(--text); }
  .km-banner.warning   .km-val-value { color: #993C1D; }

  /* Card headers */
  .compare-header-row { display: grid; grid-template-columns: 1fr 48px 1fr; gap: 0; margin-bottom: 14px; max-width: 980px; }
  .ch-card { border-radius: 14px; padding: 22px 24px; display: flex; flex-direction: column; gap: 8px; }
  .ch-card.current  { background: var(--card-bg); border: 0.5px solid var(--border); box-shadow: var(--shadow-md); }
  .ch-card.proposed { background: var(--card-bg); border: 2px solid var(--teal); box-shadow: 0 6px 24px rgba(29,158,117,0.14); }
  .ch-card.neutral  { background: var(--card-bg); border: 0.5px solid var(--border); box-shadow: var(--shadow-md); }
  .ch-type-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; }
  .current  .ch-type-label { color: var(--muted); }
  .proposed .ch-type-label { color: var(--teal-dark); }
  .neutral  .ch-type-label { color: var(--muted); }
  .ch-name { font-size: 19px; font-weight: 700; color: var(--text); letter-spacing: -0.4px; line-height: 1.2; }
  .ch-sub  { font-size: 13px; color: var(--muted); }
  .ch-vs   { display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: var(--muted); }

  /* Metrics table */
  .metrics-table {
    display: grid; grid-template-columns: 1fr 48px 1fr;
    background: var(--card-bg); border: 0.5px solid var(--border);
    border-radius: 14px; overflow: hidden; box-shadow: var(--shadow-md);
    margin-bottom: 18px; max-width: 980px;
  }
  .metrics-col { display: flex; flex-direction: column; }
  .metrics-col-header { padding: 12px 20px; background: var(--sand); font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.6px; border-bottom: 0.5px solid var(--border); }
  .metric-row { display: flex; align-items: center; gap: 10px; padding: 13px 20px; border-bottom: 0.5px solid var(--sand); font-size: 13px; min-height: 52px; }
  .metric-row:last-child { border-bottom: none; }
  .metric-icon  { font-size: 15px; flex-shrink: 0; width: 22px; text-align: center; }
  .metric-label { flex: 1; color: var(--muted); font-size: 12px; }
  .metric-value { font-weight: 700; font-size: 14px; }
  .metric-value.current-val { color: var(--text); }
  .metric-value.better { color: #0F6E56; }
  .metric-value.equal  { color: #888780; }
  .metric-value.worse  { color: #993C1D; }
  .metric-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
  .metric-dot.better { background: #1D9E75; }
  .metric-dot.equal  { background: #D3D1C7; }
  .metric-dot.worse  { background: #993C1D; }

  .metrics-vs { display: flex; flex-direction: column; align-items: center; border-left: 0.5px solid var(--border); border-right: 0.5px solid var(--border); }
  .metrics-vs-header { padding: 12px 0; background: var(--sand); width: 100%; text-align: center; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 0.5px solid var(--border); }
  .vs-cell { flex: 1; display: flex; align-items: center; justify-content: center; border-bottom: 0.5px solid var(--sand); font-size: 11px; font-weight: 700; color: var(--muted); min-height: 52px; }
  .vs-cell:last-child { border-bottom: none; }
  .vs-cell.up   { color: #0F6E56; font-size: 16px; }
  .vs-cell.same { color: #D3D1C7; font-size: 16px; }
  .vs-cell.down { color: #993C1D; font-size: 16px; }

  /* Delta */
  .delta-section { background: var(--card-bg); border: 0.5px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: var(--shadow-md); margin-bottom: 18px; max-width: 980px; }
  .delta-header { padding: 14px 22px; background: var(--sand); border-bottom: 0.5px solid var(--border); font-size: 13px; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 8px; }
  .delta-grid { display: grid; grid-template-columns: repeat(4, 1fr); }
  .delta-item { padding: 18px 20px; border-right: 0.5px solid var(--border); display: flex; flex-direction: column; gap: 4px; }
  .delta-item:last-child { border-right: none; }
  .delta-label { font-size: 12px; color: var(--muted); }
  .delta-value { font-size: 22px; font-weight: 700; letter-spacing: -0.5px; line-height: 1.1; }
  .delta-value.good    { color: var(--teal-dark); }
  .delta-value.neutral { color: var(--muted); }
  .delta-value.bad     { color: #993C1D; }
  .delta-sub { font-size: 11px; color: var(--muted); }

  /* CTA */
  .cta-section { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 20px 24px; background: var(--card-bg); border: 0.5px solid var(--border); border-radius: 14px; box-shadow: var(--shadow-md); max-width: 980px; }
  .cta-text .cta-title { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
  .cta-text .cta-sub   { font-size: 13px; color: var(--muted); }
  .cta-buttons { display: flex; gap: 10px; flex-shrink: 0; }



  /* AHP Panel */
  .ahp-panel {
    background:var(--card-bg);border:0.5px solid var(--border);
    border-radius:14px;padding:22px 24px;margin-bottom:24px;
    box-shadow:var(--shadow-md);max-width:980px;
  }
  .ahp-panel-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;cursor:pointer;user-select:none; }
  .ahp-panel-title { font-size:15px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;flex-wrap:wrap; }
  .ahp-toggle-icon { font-size:18px;color:var(--muted);transition:transform .25s; }
  .ahp-panel.open .ahp-toggle-icon { transform:rotate(90deg); }
  .ahp-panel-sub { font-size:12px;color:var(--muted);margin-bottom:16px;line-height:1.45; }
  .ahp-body { display:none; }
  .ahp-panel.open .ahp-body { display:block; }
  .rc-badge { display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;padding:4px 12px;border-radius:99px;background:var(--teal-light);color:var(--teal-dark); }
  .rc-badge.warn { background:#FEF3CD;color:#856404; }
  .weight-grid { display:grid;grid-template-columns:1fr 1fr;gap:12px 28px;margin-bottom:18px; }
  .weight-row { display:flex;flex-direction:column;gap:4px; }
  .weight-label { display:flex;justify-content:space-between;font-size:12px;gap:10px; }
  .weight-key { color:var(--muted);font-weight:500; }
  .weight-pct { font-weight:700;color:var(--teal-dark); }
  .weight-bar-wrap { height:6px;background:var(--sand);border-radius:99px;overflow:hidden; }
  .weight-bar { height:6px;border-radius:99px;transition:width .4s ease; }
  .pw-section { margin-top:16px;border-top:0.5px solid var(--sand);padding-top:16px; }
  .pw-section-label { font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px; }
  .pw-table { width:100%;border-collapse:collapse;font-size:12px; }
  .pw-table th { padding:5px 8px;text-align:left;border-bottom:0.5px solid var(--border);color:var(--muted);font-weight:600;white-space:nowrap; }
  .pw-table td { padding:5px 6px;border-bottom:0.5px solid var(--sand);white-space:nowrap; }
  .pw-table select { font-size:11px;padding:2px 4px;border-radius:4px;border:0.5px solid var(--border);background:var(--sand);color:var(--text);cursor:pointer; }
  .score-badge { display:inline-flex;align-items:center;justify-content:center;min-width:58px;height:58px;border-radius:14px;font-weight:800;font-size:20px;border:2px solid #1D9E75;background:#E1F5EE;color:#0F6E56; }
  .score-badge.mid { border-color:#C9960C;background:#FEF3CD;color:#856404; }
  .score-badge.low { border-color:#993C1D;background:#FDE8E2;color:#993C1D; }
  .ahp-rank-table { background:var(--card-bg);border:0.5px solid var(--border);border-radius:14px;box-shadow:var(--shadow-md);max-width:980px;margin-bottom:18px;overflow:hidden; }
  .ahp-rank-head { padding:14px 22px;background:var(--sand);border-bottom:0.5px solid var(--border);font-size:13px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px; }
  .ahp-rank-row { display:grid;grid-template-columns:50px 1fr 90px 130px;gap:12px;align-items:center;padding:14px 20px;border-bottom:0.5px solid var(--sand);cursor:pointer; }
  .ahp-rank-row:last-child { border-bottom:none; }
  .ahp-rank-row:hover { background:#FDFCFA; }
  .ahp-rank-row.active { background:var(--teal-light); }
  .rank-pos { font-weight:800;color:var(--teal-dark); }
  .rank-name { font-weight:700;color:var(--text); }
  .rank-sub { font-size:12px;color:var(--muted);margin-top:2px; }
  .rank-score { font-weight:800;color:var(--teal-dark);text-align:right; }
  .rank-bar-wrap { height:7px;background:var(--sand);border-radius:99px;overflow:hidden; }
  .rank-bar { height:7px;background:var(--teal);border-radius:99px; }

  /* Modal */
  .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(44,44,42,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(3px); }
  .modal-overlay.open { display: flex; animation: fadeIn 0.2s ease; }
  @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
  .modal { background: var(--card-bg); border-radius: 16px; padding: 48px 40px; width: 520px; text-align: center; box-shadow: 0 24px 80px rgba(0,0,0,0.18); animation: popIn 0.28s cubic-bezier(0.34,1.56,0.64,1); }
  @keyframes popIn { from { transform:scale(0.88);opacity:0; } to { transform:scale(1);opacity:1; } }
  .modal-icon  { font-size: 48px; margin-bottom: 16px; }
  .modal-title { font-size: 22px; font-weight: 700; color: var(--text); margin-bottom: 8px; letter-spacing: -0.4px; }
  .modal-sub   { font-size: 14px; color: var(--muted); line-height: 1.6; margin-bottom: 24px; }
  .modal-detail { background: var(--teal-light); border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; text-align: left; }
  .modal-detail-row { display: flex; justify-content: space-between; font-size: 13px; padding: 4px 0; }
  .modal-detail-key { color: var(--muted); }
  .modal-detail-val { font-weight: 600; color: var(--teal-dark); }
  .modal-actions { display: flex; gap: 12px; justify-content: center; }
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
          <div class="step-info"><div class="step-label">Equip. atual</div><div class="step-value" id="sb-atual">—</div></div>
        </div>
        <div class="step-connector done"></div>
        <div class="sidebar-step active">
          <div class="step-num">⇄</div>
          <div class="step-info"><div class="step-label">Comparação</div><div class="step-value">Substituição</div></div>
        </div>
      </div>
    </div>
    <div class="sidebar-info">
      <strong>🎨 Legenda de cores</strong>
      <div style="margin-top:8px;display:flex;flex-direction:column;gap:6px;">
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;"><div style="width:10px;height:10px;border-radius:50%;background:#1D9E75;flex-shrink:0"></div>Melhor que o atual</div>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;"><div style="width:10px;height:10px;border-radius:50%;background:#D3D1C7;flex-shrink:0"></div>Igual ou equivalente</div>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;"><div style="width:10px;height:10px;border-radius:50%;background:#993C1D;flex-shrink:0"></div>Pior que o atual</div>
      </div>
      <div style="margin-top:12px;padding-top:12px;border-top:0.5px solid var(--border);font-size:12px;color:var(--muted);line-height:1.5;" id="sb-metric-note">
        As métricas de comparação são adaptadas ao tipo de equipamento selecionado.
      </div>
    </div>
  </aside>

  <main class="main">
    <div class="breadcrumb-bar">
      <a href="1-regiao.php">Início</a><span class="breadcrumb-sep">/</span>
      <a id="bc-regiao" href="#">—</a><span class="breadcrumb-sep">/</span>
      <a id="bc-hospital" href="#">—</a><span class="breadcrumb-sep">/</span>
      <a id="bc-spec" href="#">—</a><span class="breadcrumb-sep">/</span>
      <a id="bc-inv" href="#">Inventário</a><span class="breadcrumb-sep">/</span>
      <span>Comparação</span>
    </div>

    <div class="context-strip">
      <div class="ctx-item">
        <div class="ctx-emoji">🏥</div>
        <div><div class="ctx-label">Hospital</div><div class="ctx-value" id="ctx-hospital">—</div></div>
      </div>
      <div class="ctx-item">
        <div class="ctx-emoji">📦</div>
        <div><div class="ctx-label">Equip. atual</div><div class="ctx-value" id="ctx-atual">—</div></div>
      </div>
      <div class="ctx-item">
        <div class="ctx-emoji">🩺</div>
        <div><div class="ctx-label">Especialidade</div><div class="ctx-value" id="ctx-spec">—</div></div>
      </div>
    </div>

    <div class="page-header">
      <div class="page-title">Comparação de equipamentos</div>
      <div class="page-sub" id="page-sub">Equipamento atual vs. opções de substituição</div>
    </div>

<!-- ── PRIORITY SHORTCUTS ── -->
<div class="priority-bar" id="priority-bar">
  <span class="priority-bar-label">Priorizar critério:</span>
  <div class="priority-btns">
    <button class="priority-btn" data-priority="custo"        onclick="setPriority('custo')">💰 Custo</button>
    <button class="priority-btn" data-priority="manutencao"   onclick="setPriority('manutencao')">🔧 Manutenção</button>
    <button class="priority-btn" data-priority="prazo"        onclick="setPriority('prazo')">⚡ Urgência</button>
    <button class="priority-btn" data-priority="vida_restante" onclick="setPriority('vida_restante')">🛡️ Durabilidade</button>
    <button class="priority-btn clear-btn" id="priority-clear" onclick="clearPriority()" style="display:none">✕ Limpar</button>
  </div>
</div>

    <div class="ahp-panel open" id="ahp-panel">
      <div class="ahp-panel-header" onclick="togglePanel()">
        <div class="ahp-panel-title">
          ⚖️ Matriz AHP — substituição
          <span id="rc-badge" class="rc-badge">RC = …</span>
        </div>
        <div class="ahp-toggle-icon">›</div>
      </div>
      <div class="ahp-panel-sub">
        Ajusta a importância dos critérios de 1 a 9. O ranking, o score e a melhor solução mudam automaticamente.
      </div>
      <div class="ahp-body" id="ahp-body">
        <div class="weight-grid" id="weight-bars"></div>
        <div class="pw-section">
          <div class="pw-section-label">Comparação par-a-par dos critérios</div>
          <div style="overflow-x:auto">
            <table class="pw-table" id="pw-table"></table>
          </div>
        </div>
      </div>
    </div>

    <div class="ahp-rank-table" id="ahp-rank-table"></div>

    <div class="km-banner-row" id="km-banner-row"></div>

    <div class="rep-nav" id="rep-nav">
      <span class="rep-nav-label">Opções de substituição:</span>
      <div class="rep-nav-arrows">
        <button class="nav-arrow-btn" id="btn-prev" onclick="navigate(-1)">‹</button>
        <button class="nav-arrow-btn" id="btn-next" onclick="navigate(+1)">›</button>
      </div>
    </div>

    <div class="compare-header-row" id="header-row"></div>
    <div class="metrics-table"   id="metrics-table"></div>
    <div class="delta-section"   id="delta-section"></div>

    <div class="cta-section">
      <div class="cta-text">
        <div class="cta-title">Pronto para submeter?</div>
        <div class="cta-sub">A proposta será enviada para aprovação da comissão de equipamentos.</div>
      </div>
      <div class="cta-buttons">
        <button class="cta-btn outline" onclick="exportarPDFSubstituicao()">
  📄 Exportar PDF
</button>
        <button class="cta-btn" onclick="showModal()">Confirmar substituição →</button>
      </div>
    </div>
  </main>
</div>

<div class="modal-overlay" id="modal">
  <div class="modal">
    <div class="modal-icon">✅</div>
    <div class="modal-title">Substituição confirmada</div>
    <div class="modal-sub">A proposta foi registada e enviada para aprovação.</div>
    <div class="modal-detail" id="modal-detail"></div>
    <div class="modal-actions">
      <button class="cta-btn outline" onclick="closeModal()">Fechar</button>
      <a href="1-regiao.php" class="cta-btn">Nova consulta</a>
    </div>
  </div>
</div>


<script>
const EQUIP_ATUAL = <?= $equipAtualJson ?>;
const SUBSTITUTOS = <?= $substitutosJson ?>;

const regiao = <?= json_encode($regiao, JSON_UNESCAPED_UNICODE) ?>;
const hospital = <?= json_encode($hospital, JSON_UNESCAPED_UNICODE) ?>;
const especialidade = <?= json_encode($especialidade, JSON_UNESCAPED_UNICODE) ?>;

const RI = [0, 0, 0.58, 0.90, 1.12, 1.24, 1.32, 1.41, 1.45];
const SV = [1/9,1/8,1/7,1/6,1/5,1/4,1/3,1/2,1,2,3,4,5,6,7,8,9];
const SL = ['1/9','1/8','1/7','1/6','1/5','1/4','1/3','1/2','1','2','3','4','5','6','7','8','9'];
const BAR_COLORS = ['#1D9E75','#185FA5','#854F0B','#993C1D','#534AB7','#66615A','#1D9E75','#185FA5'];

const BASE_CRITERIA = [
  { key:'idade',        label:'Idade do equipamento',    minim:true,  icon:'📅' },
  { key:'vida_restante',label:'Tempo de vida restante',  minim:false, icon:'⏳' },
  { key:'manutencao',   label:'Custo de manutenção',     minim:true,  icon:'🔧' },
  { key:'custo',        label:'Custo de aquisição',      minim:true,  icon:'💶' },
  { key:'prazo',        label:'Prazo de entrega',        minim:true,  icon:'📦' }
];

let CRITERIA = [];
let N = 0;
let pwMatrix = [];
let currentIdx = 0;
let rankedCache = [];

const equipAtualNome = EQUIP_ATUAL.fabricante + ' ' + EQUIP_ATUAL.modelo;
const equipAtualTipo = EQUIP_ATUAL.tipo;
const equipAtualAno = EQUIP_ATUAL.data_install ? new Date(EQUIP_ATUAL.data_install).getFullYear() : '—';

const r = encodeURIComponent(regiao);
const h = encodeURIComponent(hospital);
const e = encodeURIComponent(especialidade);

function num(v){ const n = Number(v || 0); return isNaN(n) ? 0 : n; }
function money(v) { return num(v).toLocaleString('pt-PT') + '€'; }
function years(v) { return num(v).toFixed(1).replace('.0','') + ' anos'; }
function percent(v) { return Math.round(num(v)) + '%'; }
function boolTxt(v) { return Number(v) === 1 ? 'Sim' : 'Não'; }
function total10y(eq) { return num(eq.custo_aq) + num(eq.custo_man) * 10; }
function ageYears(eq) {
  if (!eq.data_install) return 0;
  const installed = new Date(eq.data_install);
  if (isNaN(installed.getTime())) return 0;
  const now = new Date();
  return Math.max(0, (now - installed) / (365.25*24*60*60*1000));
}
function remainingLife(eq) { return Math.max(0, num(eq.t_vida_util) - ageYears(eq)); }

function specificSpecs(eq) {
  const t = String(eq.tipo || '').toLowerCase();
  const specs = [];
  const add = (label, value, display, minim=false) => {
    if (value !== null && value !== undefined && value !== '') specs.push({label, value:num(value), display, minim});
  };

  // Cada tipo de equipamento usa apenas 2 características específicas,
  // de acordo com a nova estrutura da base de dados.
  if (t.includes('tac')) {
    add('Cortes', eq.tac_cortes, eq.tac_cortes + ' cortes');
    add('Dose radiação', eq.tac_dose_radiacao, eq.tac_dose_radiacao + ' mSv', true);
  } else if (t === 'rm' || t.includes('resson')) {
    add('Campo magnético', eq.rm_campo_magnetico, eq.rm_campo_magnetico + ' T');
    add('Tempo aquisição', eq.rm_tempo_aquisicao, eq.rm_tempo_aquisicao + ' min', true);
  } else if (t.includes('rx')) {
    add('Digital', eq.rx_digital, boolTxt(eq.rx_digital));
    add('Dose radiação', eq.rx_dose_radiacao, eq.rx_dose_radiacao + ' mSv', true);
  } else if (t.includes('ang')) {
    add('Resolução imagem', eq.angio_resolucao_imagem, eq.angio_resolucao_imagem + ' mm', true);
    add('Dose radiação', eq.angio_dose_radiacao, eq.angio_dose_radiacao + ' mSv', true);
  } else if (t.includes('holter')) {
    add('Duração gravação', eq.holter_duracao_gravacao, eq.holter_duracao_gravacao + ' h');
    add('Canais', eq.holter_canais, eq.holter_canais + ' canais');
  } else if (t.includes('eletro') || t.includes('ecg')) {
    add('Canais', eq.ecg_canais, eq.ecg_canais + ' canais');
    add('Interpretação automática', eq.ecg_interpretacao_automatica, boolTxt(eq.ecg_interpretacao_automatica));
  } else if (t.includes('monitor')) {
    add('Parâmetros', eq.monitor_parametros, eq.monitor_parametros + ' parâmetros');
    add('Bateria', eq.monitor_bateria_horas, eq.monitor_bateria_horas + ' h');
  } else if (t.includes('desf')) {
  add('Energia máxima', eq.desf_energia_max_j, eq.desf_energia_max_j + ' J');
  add('Modo DEA', eq.desf_modo_dea, boolTxt(eq.desf_modo_dea));
} else if (t.includes('eco') || t.includes('ecó')) {
  add('Sondas', eq.eco_num_sondas, eq.eco_num_sondas + ' sondas');
  add('Doppler', eq.eco_doppler, boolTxt(eq.eco_doppler));
} else if (t.includes('cardiotoc')) {
    add('Gemelar', eq.ctg_gemelar, boolTxt(eq.ctg_gemelar));
    add('Autonomia', eq.ctg_autonomia_horas, eq.ctg_autonomia_horas + ' h');
  } else if (t.includes('bronco')) {
    add('Diâmetro', eq.bronco_diametro_mm, eq.bronco_diametro_mm + ' mm', true);
    add('Canal trabalho', eq.bronco_canal_trabalho_mm, eq.bronco_canal_trabalho_mm + ' mm');
  } else if (t.includes('ventil')) {
    add('Modos ventilação', eq.vent_modos_ventilacao, eq.vent_modos_ventilacao + ' modos');
    add('Autonomia', eq.vent_autonomia_horas, eq.vent_autonomia_horas + ' h');
  } else if (t.includes('ortop') || t.includes('serra') || t.includes('mesa cirúrgica') || t.includes('arco em c')) {
    add('Precisão', eq.orto_precisao_mm, eq.orto_precisao_mm + ' mm', true);
    add('Portátil', eq.orto_portatil, boolTxt(eq.orto_portatil));
  }
  return specs;
}


function specMetricValues(eq) {
  const specs = specificSpecs(eq);
  const values = {};
  SPEC_CRITERIA.forEach((c, i) => {
    values[c.key] = specs[i] ? num(specs[i].value) : 0;
  });
  return values;
}

const SPEC_CRITERIA = specificSpecs(EQUIP_ATUAL).slice(0, 2).map((sp, i) => ({
  key: 'spec' + i,
  label: sp.label,
  minim: !!sp.minim,
  icon: i === 0 ? '🧩' : '⚙️'
}));

CRITERIA = [...BASE_CRITERIA, ...SPEC_CRITERIA];
N = CRITERIA.length;
pwMatrix = Array.from({length:N}, () => Array.from({length:N}, () => 1));


const CURRENT = {
  id: EQUIP_ATUAL.id_equip,
  raw: EQUIP_ATUAL,
  name: equipAtualNome,
  idade: ageYears(EQUIP_ATUAL),
  vida_restante: remainingLife(EQUIP_ATUAL),
  manutencao: num(EQUIP_ATUAL.custo_man),
  custo: num(EQUIP_ATUAL.custo_aq),
  prazo: num(EQUIP_ATUAL.prazo_ent),
  ...specMetricValues(EQUIP_ATUAL),
  specDetails: specificSpecs(EQUIP_ATUAL)
};

const REPLAC = SUBSTITUTOS.map(eq => ({
  id: eq.id_equip,
  raw: eq,
  name: eq.fabricante + ' ' + eq.modelo,
  sub: eq.tipo + ' · ' + money(eq.custo_aq),
  idade: 0,
  vida_restante: num(eq.t_vida_util),
  manutencao: num(eq.custo_man),
  custo: num(eq.custo_aq),
  prazo: num(eq.prazo_ent),
  ...specMetricValues(eq),
  specDetails: specificSpecs(eq),
  financials: {
    invest: money(eq.custo_aq),
    total10y: money(total10y(eq)),
    saving: money(Math.max(0, total10y(EQUIP_ATUAL) - total10y(eq))),
    roi: '—'
  }
}));

function calcAHP() {
  const colSums = pwMatrix[0].map((_,j) => pwMatrix.reduce((s,r) => s + r[j], 0));
  const norm = pwMatrix.map(r => r.map((v,j) => v / colSums[j]));
  const w = norm.map(r => r.reduce((s,v) => s + v, 0) / N);
  const lam = w.reduce((s,wi,i) => s + colSums[i] * wi, 0);
  const CI = (lam - N) / (N - 1);
  const CR = CI / (RI[N] || 1.45);
  return {w, CR};
}

function ahpScores(items, weights) {
  const cols = CRITERIA.map(c => {
    const raw = items.map(item => num(item[c.key]));
    const adjusted = c.minim ? raw.map(v => v > 0 ? 1 / v : 0) : raw.slice();
    const sum = adjusted.reduce((s,v) => s + v, 0);
    return adjusted.map(v => sum > 0 ? v / sum : 0);
  });
  return items.map((_, ai) => weights.reduce((acc, wi, ci) => acc + wi * cols[ci][ai], 0));
}

function fmtFrac(v){
  if(v >= 1) return Number.isInteger(v) ? '' + v : v.toFixed(2);
  for(let d=2; d<=9; d++) if(Math.abs(v - 1/d) < 0.01) return '1/' + d;
  return v.toFixed(2);
}
function scoreClass(v) { return v >= 80 ? '' : (v >= 60 ? 'mid' : 'low'); }

function fillStatic() {
  document.getElementById('sb-region').textContent = regiao;
  document.getElementById('sb-hospital').textContent = hospital;
  document.getElementById('sb-spec').textContent = especialidade;
  document.getElementById('sb-atual').textContent = equipAtualNome;
  document.getElementById('ctx-hospital').textContent = hospital;
  document.getElementById('ctx-atual').textContent = equipAtualNome;
  document.getElementById('ctx-spec').textContent = especialidade;
  document.getElementById('page-sub').textContent = `${equipAtualNome} vs. opções de substituição — score calculado por AHP`;
  document.getElementById('bc-regiao').textContent = regiao;
  document.getElementById('bc-regiao').href = `2-hospital.php?regiao=${r}`;
  document.getElementById('bc-hospital').textContent = hospital;
  document.getElementById('bc-hospital').href = `3-especialidade.php?regiao=${r}&hospital=${h}`;
  document.getElementById('bc-spec').textContent = especialidade;
  document.getElementById('bc-spec').href = `4-decisao.php?regiao=${r}&hospital=${h}&especialidade=${e}`;
  document.getElementById('bc-inv').href = `4b-equipamento-atual.php?regiao=${r}&hospital=${h}&especialidade=${e}`;
}

function togglePanel(){ document.getElementById('ahp-panel').classList.toggle('open'); }

function renderWeightBars(w) {
  document.getElementById('weight-bars').innerHTML = CRITERIA.map((c,i) => `
    <div class="weight-row">
      <div class="weight-label"><span class="weight-key">${c.label}</span><span class="weight-pct">${(w[i]*100).toFixed(1)}%</span></div>
      <div class="weight-bar-wrap"><div class="weight-bar" style="width:${(w[i]*100).toFixed(1)}%;background:${BAR_COLORS[i%BAR_COLORS.length]}"></div></div>
    </div>
  `).join('');
}

function renderPW() {
  let html = '<thead><tr><th></th>' + CRITERIA.map(c => '<th>'+c.label+'</th>').join('') + '</tr></thead><tbody>';
  for (let i=0; i<N; i++) {
    html += '<tr><th>' + CRITERIA[i].label + '</th>';
    for (let j=0; j<N; j++) {
      if (i === j) html += '<td style="text-align:center;color:var(--muted)">1</td>';
      else if (j < i) html += '<td style="text-align:center;color:var(--muted);font-size:12px">' + fmtFrac(pwMatrix[i][j]) + '</td>';
      else {
        let sel = '<select data-i="'+i+'" data-j="'+j+'">';
        SV.forEach((v,k) => { sel += '<option value="'+v+'"'+(Math.abs(v-pwMatrix[i][j])<0.001?' selected':'')+'>'+SL[k]+'</option>'; });
        sel += '</select>';
        html += '<td>' + sel + '</td>';
      }
    }
    html += '</tr>';
  }
  html += '</tbody>';
  document.getElementById('pw-table').innerHTML = html;
  document.getElementById('pw-table').querySelectorAll('select').forEach(sel => {
    sel.addEventListener('change', function(){
      const i = parseInt(this.dataset.i), j = parseInt(this.dataset.j), v = parseFloat(this.value);
      pwMatrix[i][j] = v;
      pwMatrix[j][i] = 1 / v;
      renderAll(true);
    });
  });
}

function renderRankTable() {
  if (!REPLAC.length) { document.getElementById('ahp-rank-table').innerHTML = ''; return; }
  document.getElementById('ahp-rank-table').innerHTML = `
    <div class="ahp-rank-head">📊 Ranking AHP das soluções de substituição</div>
    ${rankedCache.map((item, idx) => `
      <div class="ahp-rank-row ${item.idx === currentIdx ? 'active' : ''}" onclick="currentIdx=${item.idx}; renderAll(false);">
        <div class="rank-pos">${idx === 0 ? '🥇' : (idx+1)+'.'}</div>
        <div><div class="rank-name">${item.eq.name}</div><div class="rank-sub">${item.eq.sub}</div></div>
        <div class="rank-score">${item.score100}/100</div>
        <div class="rank-bar-wrap"><div class="rank-bar" style="width:${item.score100}%"></div></div>
      </div>
    `).join('')}
  `;
}

function displayForCriterion(c, item) {
  if (c.key === 'idade') return years(item[c.key]);
  if (c.key === 'vida_restante') return years(item[c.key]);
  if (c.key === 'manutencao' || c.key === 'custo') return money(item[c.key]);
  if (c.key === 'prazo') return item[c.key] + ' dias';

  const idx = Number(String(c.key).replace('spec',''));
  const sp = item.specDetails && item.specDetails[idx] ? item.specDetails[idx] : null;
  return sp ? sp.display : '—';
}

function metricRowsFor(eq) {
  return CRITERIA.map(c => ({
    icon: c.icon || '⚙️',
    label: c.label,
    current: CURRENT[c.key],
    proposed: eq[c.key],
    curDisplay: displayForCriterion(c, CURRENT),
    propDisplay: displayForCriterion(c, eq),
    lowerIsBetter: !!c.minim
  }));
}

function compareMetric(m) {
  const threshold = Math.abs(m.current) * 0.05 || 0.1;
  if (m.lowerIsBetter) {
    if (m.proposed < m.current - threshold) return 'better';
    if (m.proposed > m.current + threshold) return 'worse';
    return 'equal';
  }
  if (m.proposed > m.current + threshold) return 'better';
  if (m.proposed < m.current - threshold) return 'worse';
  return 'equal';
}
const VS_ICONS = { better: '↑', equal: '=', worse: '↓' };
const VS_CLASSES = { better: 'up', equal: 'same', worse: 'down' };

function renderBanner(rep, score100) {
  const rc = document.getElementById('rc-badge').textContent;
  document.getElementById('km-banner-row').innerHTML = `
    <div class="km-banner primary">
      <div class="km-icon">🏆</div>
      <div style="flex:1">
        <div class="km-label">Solução selecionada</div>
        <div class="km-vals">
          <div class="km-val-item"><div class="km-val-label">Equipamento</div><div class="km-val-value">${rep.name}</div></div>
          <div class="km-val-item"><div class="km-val-label">Score AHP</div><div class="km-val-value">${score100}/100</div></div>
        </div>
      </div>
    </div>
    <div class="km-banner secondary">
      <div class="km-icon">⚖️</div>
      <div style="flex:1">
        <div class="km-label">Consistência da matriz</div>
        <div class="km-vals">
          <div class="km-val-item"><div class="km-val-label">Estado</div><div class="km-val-value">${rc}</div></div>
          <div class="km-val-item"><div class="km-val-label">Critérios específicos</div><div class="km-val-value">${SPEC_CRITERIA.length}</div></div>
        </div>
      </div>
    </div>
  `;
}

function renderComparison(rep, score100) {
  const propClass = rankedCache[0] && rankedCache[0].eq.id === rep.id ? 'proposed' : 'neutral';
  const propBadge = propClass === 'proposed'
    ? '<span style="display:inline-flex;align-items:center;gap:5px;background:var(--teal-light);color:var(--teal-dark);font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;margin-bottom:6px;">⭐ Melhor AHP</span>'
    : '';

  document.getElementById('header-row').innerHTML = `
    <div class="ch-card current">
      <div class="ch-type-label">📦 Atual</div>
      <div class="ch-name">${equipAtualNome}</div>
      <div class="ch-sub">${equipAtualTipo} · ${equipAtualAno} · instalado</div>
    </div>
    <div class="ch-vs">VS</div>
    <div class="ch-card ${propClass}">
      ${propBadge}
      <div class="ch-type-label">🔄 Proposta</div>
      <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <div><div class="ch-name">${rep.name}</div><div class="ch-sub">${rep.sub}</div></div>
        <div class="score-badge ${scoreClass(score100)}">${score100}</div>
      </div>
    </div>
  `;

  const rows = metricRowsFor(rep);
  const leftRows = rows.map(m => `
    <div class="metric-row"><div class="metric-icon">${m.icon}</div><div class="metric-label">${m.label}</div><div class="metric-value current-val">${m.curDisplay}</div></div>
  `).join('');
  const vsRows = rows.map(m => {
    const res = compareMetric(m);
    return `<div class="vs-cell ${VS_CLASSES[res]}">${VS_ICONS[res]}</div>`;
  }).join('');
  const rightRows = rows.map(m => {
    const res = compareMetric(m);
    return `<div class="metric-row"><div class="metric-dot ${res}"></div><div class="metric-label">${m.label}</div><div class="metric-value ${res}">${m.propDisplay}</div></div>`;
  }).join('');

  document.getElementById('metrics-table').innerHTML = `
    <div class="metrics-col"><div class="metrics-col-header">${equipAtualNome}</div>${leftRows}</div>
    <div class="metrics-vs"><div class="metrics-vs-header">Δ</div>${vsRows}</div>
    <div class="metrics-col"><div class="metrics-col-header">${rep.name}</div>${rightRows}</div>
  `;

  const betterCount = rows.filter(m => compareMetric(m) === 'better').length;
  const worseCount = rows.filter(m => compareMetric(m) === 'worse').length;
  const equalCount = rows.length - betterCount - worseCount;
  const specsHtml = (rep.specDetails.length ? rep.specDetails : [{label:'Sem critérios específicos', display:'—'}])
    .map(sp => `<div class="delta-sub">${sp.label}: <strong>${sp.display}</strong></div>`).join('');

  document.getElementById('delta-section').innerHTML = `
    <div class="delta-header">📊 Impacto estimado — ${rep.name}</div>
    <div class="delta-grid">
      <div class="delta-item"><div class="delta-label">Score AHP</div><div class="delta-value good">${score100}/100</div><div class="delta-sub">Ranking dinâmico</div></div>
      <div class="delta-item"><div class="delta-label">Investimento inicial</div><div class="delta-value neutral" style="font-size:18px">${rep.financials.invest}</div><div class="delta-sub">Custo de aquisição</div></div>
      <div class="delta-item"><div class="delta-label">Custo total 10 anos</div><div class="delta-value neutral" style="font-size:18px">${rep.financials.total10y}</div><div class="delta-sub">Aquisição + manutenção</div></div>
      <div class="delta-item"><div class="delta-label">Métricas melhoradas</div><div class="delta-value ${betterCount > worseCount ? 'good' : 'bad'}">${betterCount}/${rows.length}</div><div class="delta-sub">${worseCount} piores · ${equalCount} iguais</div></div>
    </div>
    <div style="padding:14px 20px;border-top:0.5px solid var(--border);display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
      ${specsHtml}
    </div>
  `;
}

function renderNav() {
  const navContainer = document.getElementById('rep-nav');
  navContainer.querySelectorAll('.rep-option').forEach(el => el.remove());
  const arrowDiv = navContainer.querySelector('.rep-nav-arrows');
  REPLAC.forEach((item) => {
    const rank = rankedCache.findIndex(r => r.eq.id === item.id) + 1;
    const pill = document.createElement('div');
    pill.className = 'rep-option' + (item.id === REPLAC[currentIdx].id ? ' active' : '');
    pill.onclick = () => { currentIdx = REPLAC.findIndex(x => x.id === item.id); renderAll(false); };
    pill.innerHTML = (rank === 1 ? '<span class="rep-star">⭐ Melhor</span> ' : '') + item.name;
    navContainer.insertBefore(pill, arrowDiv);
  });
  document.getElementById('btn-prev').disabled = currentIdx === 0;
  document.getElementById('btn-next').disabled = currentIdx === REPLAC.length - 1;
}

function renderAll(selectBest=false) {
  fillStatic();
  if (REPLAC.length === 0) {
    document.getElementById('rep-nav').innerHTML = '<span class="rep-nav-label">Sem opções de substituição no catálogo.</span>';
    document.getElementById('ahp-rank-table').innerHTML = '';
    document.getElementById('header-row').innerHTML = '';
    document.getElementById('metrics-table').innerHTML = '';
    document.getElementById('delta-section').innerHTML = '';
    return;
  }

  const ahp = calcAHP();
  const rc = Math.max(0, ahp.CR || 0);
  const rcBadge = document.getElementById('rc-badge');
  rcBadge.textContent = 'RC = ' + rc.toFixed(3) + (rc <= 0.10 ? ' OK' : ' rever');
  rcBadge.classList.toggle('warn', rc > 0.10);
  renderWeightBars(ahp.w);
  renderPW();

  const scores = ahpScores(REPLAC, ahp.w);
  const maxScore = Math.max(...scores, 0.00001);
  rankedCache = REPLAC.map((eq, idx) => ({eq, idx, ahp:scores[idx], score100:Math.round(scores[idx] / maxScore * 100)}))
    .sort((a,b) => b.ahp - a.ahp);

  if (selectBest) currentIdx = rankedCache[0].idx;
  const selected = REPLAC[currentIdx];
  const selectedRank = rankedCache.find(x => x.eq.id === selected.id);
  const score100 = selectedRank ? selectedRank.score100 : 0;

  renderRankTable();
  renderNav();
  renderBanner(selected, score100);
  renderComparison(selected, score100);
}

function navigate(dir) {
  const next = currentIdx + dir;
  if (next >= 0 && next < REPLAC.length) {
    currentIdx = next;
    renderAll(false);
  }
}

function showModal() {
  const rep = REPLAC[currentIdx];
  const rank = rankedCache.find(x => x.eq.id === rep.id);
  document.getElementById('modal-detail').innerHTML = `
    <div class="modal-detail-row"><span class="modal-detail-key">Equip. atual</span><span class="modal-detail-val">${equipAtualNome}</span></div>
    <div class="modal-detail-row"><span class="modal-detail-key">Proposta</span><span class="modal-detail-val">${rep.name}</span></div>
    <div class="modal-detail-row"><span class="modal-detail-key">Score AHP</span><span class="modal-detail-val">${rank ? rank.score100 : 0}/100</span></div>
    <div class="modal-detail-row"><span class="modal-detail-key">Investimento</span><span class="modal-detail-val">${rep.financials.invest}</span></div>
    <div class="modal-detail-row"><span class="modal-detail-key">Estado</span><span class="modal-detail-val">Aguarda aprovação</span></div>
  `;
  document.getElementById('modal').classList.add('open');
}

function closeModal() { document.getElementById('modal').classList.remove('open'); }

function exportarPDFSubstituicao() {

  const rep = REPLAC[currentIdx];
  const rank = rankedCache.find(x => x.eq.id === rep.id);

  const rows = metricRowsFor(rep);

  const betterCount = rows.filter(r => compareMetric(r) === 'better').length;
  const worseCount  = rows.filter(r => compareMetric(r) === 'worse').length;
  const equalCount  = rows.length - betterCount - worseCount;

  const ahp = calcAHP();

  const comparisonRows = rows.map(r => {

    const state = compareMetric(r);

    let icon = '⚪';
    let txt  = 'Igual';

    if (state === 'better') {
      icon = '🟢';
      txt = 'Melhor';
    }

    if (state === 'worse') {
      icon = '🔴';
      txt = 'Pior';
    }

    return `
      <tr>
        <td>${r.label}</td>
        <td>${r.curDisplay}</td>
        <td>${r.propDisplay}</td>
        <td>${icon} ${txt}</td>
      </tr>
    `;
  }).join('');

  const criteriaRows = CRITERIA.map((c,i) => `
    <tr>
      <td>${c.label}</td>
      <td>${(ahp.w[i] * 100).toFixed(1)}%</td>
    </tr>
  `).join('');

  const html = `
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">

<title>Relatório de Substituição</title>

<style>

body{
  font-family:Arial,sans-serif;
  background:#f3f4f6;
  margin:0;
  padding:40px;
  color:#111827;
}

.report{
  max-width:1000px;
  margin:auto;
  background:white;
  padding:50px;
  border-radius:18px;
}

.header{
  display:flex;
  justify-content:space-between;
  border-bottom:3px solid #0f766e;
  padding-bottom:20px;
  margin-bottom:30px;
}

.logo{
  font-size:32px;
  font-weight:800;
  color:#0f766e;
}

h1{
  margin:0;
}

h2{
  margin-top:35px;
  color:#0f766e;
  border-bottom:1px solid #ddd;
  padding-bottom:8px;
}

table{
  width:100%;
  border-collapse:collapse;
  margin-top:15px;
}

th,td{
  padding:12px;
  border-bottom:1px solid #e5e7eb;
  text-align:left;
}

th{
  background:#f3f4f6;
}

.highlight{
  background:#ecfdf5;
  border:2px solid #10b981;
  padding:25px;
  border-radius:14px;
}

.score{
  font-size:42px;
  font-weight:800;
  color:#047857;
}

.summary{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:15px;
}

.card{
  background:#f9fafb;
  border:1px solid #e5e7eb;
  padding:18px;
  border-radius:12px;
}

.label{
  font-size:12px;
  color:#6b7280;
  text-transform:uppercase;
  margin-bottom:6px;
}

.value{
  font-size:20px;
  font-weight:700;
}

.print-btn{
  position:fixed;
  top:20px;
  right:20px;
  border:none;
  background:#0f766e;
  color:white;
  padding:12px 20px;
  border-radius:10px;
  cursor:pointer;
}

@media print{
  .print-btn{display:none;}
  body{padding:0;background:white;}
}

</style>
</head>

<body>

<button class="print-btn" onclick="window.print()">
Guardar como PDF
</button>

<div class="report">

<div class="header">
  <div>
    <div class="logo">MedDSS</div>
    <div>Sistema de Apoio à Decisão em Saúde</div>
  </div>

  <div>
    ${new Date().toLocaleString('pt-PT')}
  </div>
</div>

<h1>Relatório de Substituição de Equipamento</h1>

<h2>Contexto da decisão</h2>

<table>
<tr><td>Região</td><td>${regiao}</td></tr>
<tr><td>Hospital</td><td>${hospital}</td></tr>
<tr><td>Especialidade</td><td>${especialidade}</td></tr>
<tr><td>Equipamento atual</td><td>${equipAtualNome}</td></tr>
</table>

<h2>Equipamento recomendado</h2>

<div class="highlight">

<h3>${rep.name}</h3>

<div class="score">
${rank ? rank.score100 : 0}/100
</div>

<p>
O sistema recomenda a substituição do equipamento atual por esta alternativa,
uma vez que apresenta o melhor desempenho global segundo o método AHP.
</p>

</div>

<h2>Comparação detalhada</h2>

<table>
<thead>
<tr>
<th>Critério</th>
<th>Atual</th>
<th>Proposto</th>
<th>Resultado</th>
</tr>
</thead>
<tbody>
${comparisonRows}
</tbody>
</table>

<h2>Resumo executivo</h2>

<div class="summary">

<div class="card">
<div class="label">Melhorias</div>
<div class="value">${betterCount}</div>
</div>

<div class="card">
<div class="label">Iguais</div>
<div class="value">${equalCount}</div>
</div>

<div class="card">
<div class="label">Piores</div>
<div class="value">${worseCount}</div>
</div>

</div>

<h2>Impacto financeiro</h2>

<table>
<tr><td>Investimento inicial</td><td>${rep.financials.invest}</td></tr>
<tr><td>Custo total 10 anos</td><td>${rep.financials.total10y}</td></tr>
<tr><td>Poupança estimada</td><td>${rep.financials.saving}</td></tr>
<tr><td>ROI estimado</td><td>${rep.financials.roi}</td></tr>
</table>

<h2>Pesos dos critérios AHP</h2>

<table>
<thead>
<tr>
<th>Critério</th>
<th>Peso</th>
</tr>
</thead>
<tbody>
${criteriaRows}
</tbody>
</table>

<p>
<b>RC = ${(ahp.CR*100).toFixed(1)}%</b>
</p>

<h2>Conclusão</h2>

<p>
Com base na análise multicritério realizada, o MedDSS recomenda a substituição
do equipamento <b>${equipAtualNome}</b> por <b>${rep.name}</b>.

A solução proposta apresenta <b>${betterCount}</b> melhorias,
<b>${equalCount}</b> critérios equivalentes e
<b>${worseCount}</b> critérios inferiores relativamente ao equipamento atual.
</p>

</div>

</body>
</html>
`;

  const win = window.open('', '_blank');

  win.document.open();
  win.document.write(html);
  win.document.close();

  setTimeout(() => {
    win.focus();
    win.print();
  }, 500);
}

/* ── PRIORITY (substituição) ── */
var activePriority = null;

function buildPreset(priorityIdx) {
  var m = [];
  for (var i = 0; i < N; i++) {
    m.push([]);
    for (var j = 0; j < N; j++) {
      if (i === j)               m[i].push(1);
      else if (i === priorityIdx) m[i].push(5);
      else if (j === priorityIdx) m[i].push(1/5);
      else                        m[i].push(1);
    }
  }
  return m;
}

/* Mapeia chave do botão para índice em CRITERIA */
var PRIORITY_MAP = {
  custo:         function() { return CRITERIA.findIndex(function(c){ return c.key==='custo'; }); },
  manutencao:    function() { return CRITERIA.findIndex(function(c){ return c.key==='manutencao'; }); },
  prazo:         function() { return CRITERIA.findIndex(function(c){ return c.key==='prazo'; }); },
  vida_restante: function() { return CRITERIA.findIndex(function(c){ return c.key==='vida_restante'; }); },
};

function setPriority(key) {
  if (activePriority === key) { clearPriority(); return; }
  activePriority = key;
  var idx = PRIORITY_MAP[key] ? PRIORITY_MAP[key]() : -1;
  if (idx < 0) return;
  var preset = buildPreset(idx);
  for (var i = 0; i < N; i++)
    for (var j = 0; j < N; j++)
      pwMatrix[i][j] = preset[i][j];
  _updatePriorityUI();
  renderAll(true);
}

function clearPriority() {
  activePriority = null;
  for (var i = 0; i < N; i++)
    for (var j = 0; j < N; j++)
      pwMatrix[i][j] = 1;
  _updatePriorityUI();
  renderAll(true);
}

function _updatePriorityUI() {
  document.querySelectorAll('.priority-btn[data-priority]').forEach(function(btn) {
    btn.classList.toggle('active', btn.dataset.priority === activePriority);
  });
  var cb = document.getElementById('priority-clear');
  if (cb) cb.style.display = activePriority ? '' : 'none';
}

renderAll(true);
</script>
</body>
</html>
