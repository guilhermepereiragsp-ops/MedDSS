/**
 * MedDSS — Equipment Data Library
 * Keyed by: EQUIP_DB[especialidade][tipoEquipamento]
 *
 * Each entry has:
 *   - primaryMetricLabel  : what "Cortes / Canais / MHz / etc." is called for this device type
 *   - radiationLabel      : what the radiation / energy field is called (null if N/A)
 *   - recommended         : the best option (main card)
 *   - alternatives        : array of other options
 *
 * Each equipment object has:
 *   - name, sub, score, tags[]
 *   - scores[]            : { label, value }
 *   - specs[]             : { k, v, highlight? }  — highlight = show in teal
 *   - financials          : { invest, total10y, saving, roi }
 */

const EQUIP_DB = {

  /* ══════════════════════════════════════════════
     IMAGIOLOGIA
  ══════════════════════════════════════════════ */
  'Imagiologia': {

    'Tomografia Computorizada': {
      primaryMetricLabel: 'Cortes / Detetor',
      radiationLabel: 'Dose de radiação (DLP)',
      recommended: {
        name: 'Siemens SOMATOM X.cite',
        sub:  'TC 128 cortes · Dose ultra-baixa · €680.000',
        score: 92,
        tags: ['128 cortes', 'Dose ultra-baixa', 'IA integrada', 'DICOM 3.0'],
        highlightTags: ['128 cortes', 'Dose ultra-baixa'],
        scores: [
          { label: 'Score global',         value: 92 },
          { label: 'Custo-benefício',      value: 88 },
          { label: 'Adequação clínica',    value: 95 },
          { label: 'Manutenção e suporte', value: 84 },
        ],
        specs: [
          { k: 'Fabricante',              v: 'Siemens Healthineers' },
          { k: 'Cortes / Detetor',        v: '128 cortes',      hi: true },
          { k: 'Dose de radiação (DLP)',   v: 'Ultra-baixa',     hi: true },
          { k: 'Tempo de exame',           v: '4 minutos',       hi: true },
          { k: 'Vida útil estimada',       v: '15 anos' },
          { k: 'Manutenção/ano',           v: '€18.000' },
          { k: 'Disponibilidade',          v: '99%',             hi: true },
          { k: 'Preço estimado',           v: '€680.000' },
          { k: 'Prazo de entrega',         v: '12 semanas' },
        ],
        financials: { invest: '€680.000', total10y: '€860.000', saving: '€240.000', roi: '4,2 anos' },
      },
      alternatives: [
        {
          name: 'GE Revolution EVO', score: 86,
          detail: 'Menor custo inicial · 64 cortes · €490.000',
          scores: [
            { label: 'Score global',         value: 86 },
            { label: 'Custo-benefício',      value: 91 },
            { label: 'Adequação clínica',    value: 78 },
            { label: 'Manutenção e suporte', value: 80 },
          ],
          specs: [
            { k: 'Fabricante',            v: 'GE Healthcare' },
            { k: 'Cortes / Detetor',      v: '64 cortes' },
            { k: 'Dose de radiação (DLP)', v: 'Baixa' },
            { k: 'Tempo de exame',         v: '6 minutos' },
            { k: 'Vida útil',              v: '12 anos' },
            { k: 'Manutenção/ano',         v: '€22.000' },
            { k: 'Preço estimado',         v: '€490.000' },
            { k: 'Prazo de entrega',       v: '10 semanas' },
          ],
        },
        {
          name: 'Philips Incisive CT', score: 81,
          detail: 'Melhor ergonomia · prazo longo · €620.000',
          scores: [
            { label: 'Score global',         value: 81 },
            { label: 'Custo-benefício',      value: 79 },
            { label: 'Adequação clínica',    value: 83 },
            { label: 'Manutenção e suporte', value: 72 },
          ],
          specs: [
            { k: 'Fabricante',            v: 'Philips Healthcare' },
            { k: 'Cortes / Detetor',      v: '128 cortes' },
            { k: 'Dose de radiação (DLP)', v: 'Baixa' },
            { k: 'Tempo de exame',         v: '5 minutos' },
            { k: 'Vida útil',              v: '12 anos' },
            { k: 'Manutenção/ano',         v: '€26.000' },
            { k: 'Preço estimado',         v: '€620.000' },
            { k: 'Prazo de entrega',       v: '20 semanas' },
          ],
        },
        {
          name: 'Canon Aquilion ONE Genesis', score: 67,
          detail: 'Alta resolução volumétrica · manutenção elevada · €740.000',
          scores: [
            { label: 'Score global',         value: 67 },
            { label: 'Custo-benefício',      value: 58 },
            { label: 'Adequação clínica',    value: 80 },
            { label: 'Manutenção e suporte', value: 62 },
          ],
          specs: [
            { k: 'Fabricante',            v: 'Canon Medical' },
            { k: 'Cortes / Detetor',      v: '320 cortes' },
            { k: 'Dose de radiação (DLP)', v: 'Ultra-baixa' },
            { k: 'Tempo de exame',         v: '3 minutos' },
            { k: 'Vida útil',              v: '15 anos' },
            { k: 'Manutenção/ano',         v: '€48.000' },
            { k: 'Preço estimado',         v: '€740.000' },
            { k: 'Prazo de entrega',       v: '16 semanas' },
          ],
        },
      ],
    },

    'Ressonância Magnética': {
      primaryMetricLabel: 'Campo magnético (Tesla)',
      radiationLabel: null,   // RM has no ionising radiation
      recommended: {
        name: 'Siemens MAGNETOM Vida 3T',
        sub:  'RM 3 Tesla · Wide-bore 70 cm · €1.800.000',
        score: 91,
        tags: ['3 Tesla', 'Wide-bore 70cm', 'Sem radiação', 'NeuroQ IA'],
        highlightTags: ['128 cortes', '3 Tesla'],
        scores: [
          { label: 'Score global',         value: 91 },
          { label: 'Custo-benefício',      value: 83 },
          { label: 'Adequação clínica',    value: 96 },
          { label: 'Manutenção e suporte', value: 87 },
        ],
        specs: [
          { k: 'Fabricante',              v: 'Siemens Healthineers' },
          { k: 'Campo magnético',         v: '3,0 Tesla',    hi: true },
          { k: 'Cortes / Detetor',        v: '128 cortes',   hi: true },
          { k: 'Diâmetro bore',           v: '70 cm' },
          { k: 'Tempo de exame médio',    v: '25 minutos',   hi: true },
          { k: 'Vida útil estimada',       v: '15 anos' },
          { k: 'Manutenção/ano',           v: '€45.000' },
          { k: 'Disponibilidade',          v: '98%',         hi: true },
          { k: 'Preço estimado',           v: '€1.800.000' },
          { k: 'Prazo de entrega',         v: '20 semanas' },
        ],
        financials: { invest: '€1.800.000', total10y: '€2.250.000', saving: '€180.000', roi: '6,8 anos' },
      },
      alternatives: [
        {
          name: 'GE SIGNA Artist 1.5T', score: 84,
          detail: '1,5 Tesla · Menor custo · Excelente SNR · €900.000',
          scores: [
            { label: 'Score global',         value: 84 },
            { label: 'Custo-benefício',      value: 92 },
            { label: 'Adequação clínica',    value: 80 },
            { label: 'Manutenção e suporte', value: 86 },
          ],
          specs: [
            { k: 'Fabricante',         v: 'GE Healthcare' },
            { k: 'Campo magnético',    v: '1,5 Tesla' },
            { k: 'Cortes / Detetor',   v: '320 cortes' },
            { k: 'Diâmetro bore',      v: '60 cm' },
            { k: 'Tempo de exame',     v: '30 minutos' },
            { k: 'Manutenção/ano',     v: '€38.000' },
            { k: 'Preço estimado',     v: '€900.000' },
            { k: 'Prazo de entrega',   v: '16 semanas' },
          ],
        },
        {
          name: 'Philips Ingenia Elition 3T', score: 78,
          detail: '3 Tesla · dStream coils · Entrega prolongada · €1.950.000',
          scores: [
            { label: 'Score global',         value: 78 },
            { label: 'Custo-benefício',      value: 71 },
            { label: 'Adequação clínica',    value: 88 },
            { label: 'Manutenção e suporte', value: 79 },
          ],
          specs: [
            { k: 'Fabricante',         v: 'Philips Healthcare' },
            { k: 'Campo magnético',    v: '3,0 Tesla' },
            { k: 'Cortes / Detetor',   v: '256 cortes' },
            { k: 'Diâmetro bore',      v: '70 cm' },
            { k: 'Tempo de exame',     v: '22 minutos' },
            { k: 'Manutenção/ano',     v: '€52.000' },
            { k: 'Preço estimado',     v: '€1.950.000' },
            { k: 'Prazo de entrega',   v: '28 semanas' },
          ],
        },
      ],
    },

    'Ecografia': {
      primaryMetricLabel: 'Frequência de sonda (MHz)',
      radiationLabel: null,
      recommended: {
        name: 'GE LOGIQ E10',
        sub:  'Ecógrafo premium · Sondas 1–15 MHz · €95.000',
        score: 89,
        tags: ['1–15 MHz', 'Sem radiação', 'IA AutoSCAN', 'Portátil opcional'],
        highlightTags: ['1–15 MHz', 'Sem radiação'],
        scores: [
          { label: 'Score global',         value: 89 },
          { label: 'Custo-benefício',      value: 85 },
          { label: 'Adequação clínica',    value: 92 },
          { label: 'Manutenção e suporte', value: 88 },
        ],
        specs: [
          { k: 'Fabricante',              v: 'GE Healthcare' },
          { k: 'Frequência de sonda',     v: '1–15 MHz',   hi: true },
          { k: 'Modos disponíveis',       v: 'B, M, Doppler, 3D/4D', hi: true },
          { k: 'Tempo de exame médio',    v: '15 minutos' },
          { k: 'Vida útil estimada',      v: '10 anos' },
          { k: 'Manutenção/ano',          v: '€6.500' },
          { k: 'Disponibilidade',         v: '99%',        hi: true },
          { k: 'Preço estimado',          v: '€95.000' },
          { k: 'Prazo de entrega',        v: '6 semanas' },
        ],
        financials: { invest: '€95.000', total10y: '€160.000', saving: '€45.000', roi: '2,8 anos' },
      },
      alternatives: [
        {
          name: 'Philips EPIQ Elite', score: 85,
          detail: 'nSIGHT imaging · 1–18 MHz · excelente resolução · €110.000',
          scores: [
            { label: 'Score global',         value: 85 },
            { label: 'Custo-benefício',      value: 80 },
            { label: 'Adequação clínica',    value: 91 },
            { label: 'Manutenção e suporte', value: 83 },
          ],
          specs: [
            { k: 'Fabricante',          v: 'Philips Healthcare' },
            { k: 'Frequência de sonda', v: '1–18 MHz' },
            { k: 'Modos disponíveis',   v: 'B, M, Doppler, Elastografia, 3D' },
            { k: 'Manutenção/ano',      v: '€8.000' },
            { k: 'Preço estimado',      v: '€110.000' },
            { k: 'Prazo de entrega',    v: '8 semanas' },
          ],
        },
        {
          name: 'Samsung RS85 Prestige', score: 72,
          detail: 'Crystal Architecture · menor preço · €65.000',
          scores: [
            { label: 'Score global',         value: 72 },
            { label: 'Custo-benefício',      value: 90 },
            { label: 'Adequação clínica',    value: 70 },
            { label: 'Manutenção e suporte', value: 74 },
          ],
          specs: [
            { k: 'Fabricante',          v: 'Samsung Medison' },
            { k: 'Frequência de sonda', v: '1–15 MHz' },
            { k: 'Modos disponíveis',   v: 'Elastografia, 3D' },
            { k: 'Manutenção/ano',      v: '€4.500' },
            { k: 'Preço estimado',      v: '€65.000' },
            { k: 'Prazo de entrega',    v: '8 semanas' },
          ],
        },
      ],
    },

    'PET-CT': {
      primaryMetricLabel: 'Cortes TC / Resolução PET',
      radiationLabel: 'Dose de radiação (mSv)',
      recommended: {
        name: 'Siemens Biograph Vision 600',
        sub:  'PET-CT 128 cortes · Resolução 3,3 mm FWHM · €2.200.000',
        score: 94,
        tags: ['128 cortes TC', 'PET 3,3mm FWHM', 'ToF 214ps', 'IA integrada'],
        highlightTags: ['128 cortes TC', 'PET 3,3mm FWHM'],
        scores: [
          { label: 'Score global',         value: 94 },
          { label: 'Custo-benefício',      value: 86 },
          { label: 'Adequação clínica',    value: 98 },
          { label: 'Manutenção e suporte', value: 89 },
        ],
        specs: [
          { k: 'Fabricante',                    v: 'Siemens Healthineers' },
          { k: 'Cortes TC / Resolução PET',     v: '128 cort. / 3,3mm FWHM', hi: true },
          { k: 'Dose de radiação (mSv)',         v: '3–8 mSv (protocolo)',    hi: true },
          { k: 'Time-of-Flight',                 v: '214 ps',                 hi: true },
          { k: 'Tempo de exame (corpo inteiro)', v: '10–15 minutos' },
          { k: 'Vida útil estimada',             v: '15 anos' },
          { k: 'Manutenção/ano',                 v: '€90.000' },
          { k: 'Disponibilidade',                v: '97%',                    hi: true },
          { k: 'Preço estimado',                 v: '€2.200.000' },
          { k: 'Prazo de entrega',               v: '24 semanas' },
        ],
        financials: { invest: '€2.200.000', total10y: '€3.100.000', saving: '€320.000', roi: '7,4 anos' },
      },
      alternatives: [
        {
          name: 'GE Discovery MI DR', score: 88,
          detail: '64 cortes TC · SiPM ToF · menor custo · €1.800.000',
          scores: [
            { label: 'Score global',         value: 88 },
            { label: 'Custo-benefício',      value: 91 },
            { label: 'Adequação clínica',    value: 88 },
            { label: 'Manutenção e suporte', value: 85 },
          ],
          specs: [
            { k: 'Fabricante',                 v: 'GE Healthcare' },
            { k: 'Cortes TC / Resolução PET',  v: '64 cort. / 4,2mm FWHM' },
            { k: 'Dose de radiação (mSv)',      v: '4–9 mSv (protocolo)' },
            { k: 'Time-of-Flight',              v: '375 ps' },
            { k: 'Manutenção/ano',             v: '€75.000' },
            { k: 'Preço estimado',             v: '€1.800.000' },
            { k: 'Prazo de entrega',           v: '20 semanas' },
          ],
        },
        {
          name: 'Philips Vereos Digital PET/CT', score: 76,
          detail: 'PET digital · 64 cortes TC · entrega longa · €1.950.000',
          scores: [
            { label: 'Score global',         value: 76 },
            { label: 'Custo-benefício',      value: 74 },
            { label: 'Adequação clínica',    value: 82 },
            { label: 'Manutenção e suporte', value: 71 },
          ],
          specs: [
            { k: 'Fabricante',                 v: 'Philips Healthcare' },
            { k: 'Cortes TC / Resolução PET',  v: '64 cort. / 4,0mm FWHM' },
            { k: 'Dose de radiação (mSv)',      v: '4–10 mSv (protocolo)' },
            { k: 'Time-of-Flight',              v: '310 ps' },
            { k: 'Manutenção/ano',             v: '€82.000' },
            { k: 'Preço estimado',             v: '€1.950.000' },
            { k: 'Prazo de entrega',           v: '28 semanas' },
          ],
        },
      ],
    },

    'Raio-X Digital': {
      primaryMetricLabel: 'Resolução do detetor (lp/mm)',
      radiationLabel: 'Dose de entrada (µGy)',
      recommended: {
        name: 'Agfa DR 800',
        sub:  'Raio-X digital · 3,4 lp/mm · Dose mínima · €120.000',
        score: 88,
        tags: ['3,4 lp/mm', 'Dose mínima', 'Wireless DR', 'MUSICA AI'],
        highlightTags: ['3,4 lp/mm', 'Dose mínima'],
        scores: [
          { label: 'Score global',         value: 88 },
          { label: 'Custo-benefício',      value: 87 },
          { label: 'Adequação clínica',    value: 90 },
          { label: 'Manutenção e suporte', value: 85 },
        ],
        specs: [
          { k: 'Fabricante',               v: 'Agfa HealthCare' },
          { k: 'Resolução do detetor',     v: '3,4 lp/mm',      hi: true },
          { k: 'Dose de entrada (µGy)',    v: '0,9–1,2 µGy',    hi: true },
          { k: 'Tempo de exame médio',     v: '< 2 minutos',     hi: true },
          { k: 'Vida útil estimada',       v: '10 anos' },
          { k: 'Manutenção/ano',           v: '€4.000' },
          { k: 'Disponibilidade',          v: '99%',             hi: true },
          { k: 'Preço estimado',           v: '€120.000' },
          { k: 'Prazo de entrega',         v: '8 semanas' },
        ],
        financials: { invest: '€120.000', total10y: '€160.000', saving: '€32.000', roi: '3,1 anos' },
      },
      alternatives: [
        {
          name: 'Carestream DRX-Evolution Plus', score: 83,
          detail: 'Detetor wireless · 3,1 lp/mm · €95.000',
          scores: [
            { label: 'Score global',         value: 83 },
            { label: 'Custo-benefício',      value: 90 },
            { label: 'Adequação clínica',    value: 80 },
            { label: 'Manutenção e suporte', value: 80 },
          ],
          specs: [
            { k: 'Fabricante',            v: 'Carestream Health' },
            { k: 'Resolução do detetor',  v: '3,1 lp/mm' },
            { k: 'Dose de entrada (µGy)', v: '1,2–1,8 µGy' },
            { k: 'Manutenção/ano',        v: '€3.500' },
            { k: 'Preço estimado',        v: '€95.000' },
            { k: 'Prazo de entrega',      v: '10 semanas' },
          ],
        },
        {
          name: 'Shimadzu RADspeed Pro', score: 70,
          detail: 'Alta capacidade de carga · 2,8 lp/mm · €80.000',
          scores: [
            { label: 'Score global',         value: 70 },
            { label: 'Custo-benefício',      value: 85 },
            { label: 'Adequação clínica',    value: 66 },
            { label: 'Manutenção e suporte', value: 72 },
          ],
          specs: [
            { k: 'Fabricante',            v: 'Shimadzu Medical' },
            { k: 'Resolução do detetor',  v: '2,8 lp/mm' },
            { k: 'Dose de entrada (µGy)', v: '1,5–2,2 µGy' },
            { k: 'Manutenção/ano',        v: '€3.200' },
            { k: 'Preço estimado',        v: '€80.000' },
            { k: 'Prazo de entrega',      v: '12 semanas' },
          ],
        },
      ],
    },

    'Angiografia': {
      primaryMetricLabel: 'Resolução espacial (lp/mm)',
      radiationLabel: 'Débito de dose (µGy/frame)',
      recommended: {
        name: 'Siemens ARTIS icono biplane',
        sub:  'Angiografia biplanar · 4,5 lp/mm · €1.600.000',
        score: 93,
        tags: ['Biplanar', '4,5 lp/mm', 'Dose < 0,1 µGy/f', 'syngo IA'],
        highlightTags: ['Biplanar', '4,5 lp/mm'],
        scores: [
          { label: 'Score global',         value: 93 },
          { label: 'Custo-benefício',      value: 85 },
          { label: 'Adequação clínica',    value: 97 },
          { label: 'Manutenção e suporte', value: 88 },
        ],
        specs: [
          { k: 'Fabricante',                v: 'Siemens Healthineers' },
          { k: 'Resolução espacial',        v: '4,5 lp/mm',           hi: true },
          { k: 'Débito de dose',            v: '< 0,1 µGy/frame',     hi: true },
          { k: 'Configuração',              v: 'Biplanar',             hi: true },
          { k: 'Tempo de proc. médio',      v: '45 minutos' },
          { k: 'Vida útil estimada',        v: '15 anos' },
          { k: 'Manutenção/ano',            v: '€65.000' },
          { k: 'Disponibilidade',           v: '98%',                  hi: true },
          { k: 'Preço estimado',            v: '€1.600.000' },
          { k: 'Prazo de entrega',          v: '20 semanas' },
        ],
        financials: { invest: '€1.600.000', total10y: '€2.250.000', saving: '€280.000', roi: '6,1 anos' },
      },
      alternatives: [
        {
          name: 'Philips Azurion 7 B20/15', score: 87,
          detail: 'FlexArm · 4,0 lp/mm · sala única · €1.200.000',
          scores: [
            { label: 'Score global',         value: 87 },
            { label: 'Custo-benefício',      value: 89 },
            { label: 'Adequação clínica',    value: 88 },
            { label: 'Manutenção e suporte', value: 84 },
          ],
          specs: [
            { k: 'Fabricante',         v: 'Philips Healthcare' },
            { k: 'Resolução espacial', v: '4,0 lp/mm' },
            { k: 'Débito de dose',     v: '0,12 µGy/frame' },
            { k: 'Configuração',       v: 'Monoplanar (FlexArm)' },
            { k: 'Manutenção/ano',     v: '€55.000' },
            { k: 'Preço estimado',     v: '€1.200.000' },
            { k: 'Prazo de entrega',   v: '18 semanas' },
          ],
        },
        {
          name: 'GE Allia IGS 7', score: 73,
          detail: 'Monoplanar · 3,6 lp/mm · custo baixo · €950.000',
          scores: [
            { label: 'Score global',         value: 73 },
            { label: 'Custo-benefício',      value: 88 },
            { label: 'Adequação clínica',    value: 71 },
            { label: 'Manutenção e suporte', value: 76 },
          ],
          specs: [
            { k: 'Fabricante',         v: 'GE Healthcare' },
            { k: 'Resolução espacial', v: '3,6 lp/mm' },
            { k: 'Débito de dose',     v: '0,15 µGy/frame' },
            { k: 'Configuração',       v: 'Monoplanar' },
            { k: 'Manutenção/ano',     v: '€48.000' },
            { k: 'Preço estimado',     v: '€950.000' },
            { k: 'Prazo de entrega',   v: '16 semanas' },
          ],
        },
      ],
    },
  },

  /* ══════════════════════════════════════════════
     CARDIOLOGIA
  ══════════════════════════════════════════════ */
  'Cardiologia': {

    'Ecocardiógrafo': {
      primaryMetricLabel: 'Frequência de sonda (MHz)',
      radiationLabel: null,
      recommended: {
        name: 'Philips EPIQ CVx',
        sub:  'Ecocardiógrafo premium · 1–12 MHz · €180.000',
        score: 91,
        tags: ['1–12 MHz', 'Sem radiação', '4D em tempo real', 'HeartModel IA'],
        highlightTags: ['1–12 MHz', 'Sem radiação'],
        scores: [
          { label: 'Score global',         value: 91 },
          { label: 'Custo-benefício',      value: 84 },
          { label: 'Adequação clínica',    value: 96 },
          { label: 'Manutenção e suporte', value: 88 },
        ],
        specs: [
          { k: 'Fabricante',          v: 'Philips Healthcare' },
          { k: 'Frequência de sonda', v: '1–12 MHz',         hi: true },
          { k: 'Modos disponíveis',   v: 'B, M, Doppler CW/PW, 3D/4D, Strain', hi: true },
          { k: 'Tempo de exame',      v: '20–30 minutos' },
          { k: 'Vida útil estimada',  v: '10 anos' },
          { k: 'Manutenção/ano',      v: '€12.000' },
          { k: 'Disponibilidade',     v: '99%',              hi: true },
          { k: 'Preço estimado',      v: '€180.000' },
          { k: 'Prazo de entrega',    v: '10 semanas' },
        ],
        financials: { invest: '€180.000', total10y: '€300.000', saving: '€85.000', roi: '3,5 anos' },
      },
      alternatives: [
        {
          name: 'GE Vivid E95', score: 87,
          detail: 'TVI · Strain · 2–8 MHz · €155.000',
          scores: [
            { label: 'Score global',         value: 87 },
            { label: 'Custo-benefício',      value: 89 },
            { label: 'Adequação clínica',    value: 88 },
            { label: 'Manutenção e suporte', value: 84 },
          ],
          specs: [
            { k: 'Fabricante',          v: 'GE Healthcare' },
            { k: 'Frequência de sonda', v: '2–8 MHz' },
            { k: 'Modos disponíveis',   v: 'B, M, Doppler, 3D, Strain' },
            { k: 'Manutenção/ano',      v: '€10.500' },
            { k: 'Preço estimado',      v: '€155.000' },
            { k: 'Prazo de entrega',    v: '8 semanas' },
          ],
        },
        {
          name: 'Siemens ACUSON SC2000', score: 74,
          detail: 'Volumétrico · menor versatilidade · €130.000',
          scores: [
            { label: 'Score global',         value: 74 },
            { label: 'Custo-benefício',      value: 82 },
            { label: 'Adequação clínica',    value: 73 },
            { label: 'Manutenção e suporte', value: 76 },
          ],
          specs: [
            { k: 'Fabricante',          v: 'Siemens Healthineers' },
            { k: 'Frequência de sonda', v: '1–6 MHz' },
            { k: 'Manutenção/ano',      v: '€9.000' },
            { k: 'Preço estimado',      v: '€130.000' },
            { k: 'Prazo de entrega',    v: '12 semanas' },
          ],
        },
      ],
    },

    'Monitor ECG': {
      primaryMetricLabel: 'Canais de ECG',
      radiationLabel: null,
      recommended: {
        name: 'GE MAC 5500 HD',
        sub:  'Holter / ECG 12 canais · Análise IA · €8.500',
        score: 90,
        tags: ['12 canais', 'Sem radiação', 'IA Marquette', 'Wireless'],
        highlightTags: ['12 canais', 'Sem radiação'],
        scores: [
          { label: 'Score global',         value: 90 },
          { label: 'Custo-benefício',      value: 93 },
          { label: 'Adequação clínica',    value: 91 },
          { label: 'Manutenção e suporte', value: 88 },
        ],
        specs: [
          { k: 'Fabricante',          v: 'GE Healthcare' },
          { k: 'Canais de ECG',       v: '12 canais',     hi: true },
          { k: 'Interpretação IA',    v: 'Marquette 12SL',hi: true },
          { k: 'Conectividade',       v: 'Wi-Fi / LAN' },
          { k: 'Vida útil estimada',  v: '8 anos' },
          { k: 'Manutenção/ano',      v: '€400' },
          { k: 'Disponibilidade',     v: '99,5%',         hi: true },
          { k: 'Preço estimado',      v: '€8.500' },
          { k: 'Prazo de entrega',    v: '4 semanas' },
        ],
        financials: { invest: '€8.500', total10y: '€12.500', saving: '€4.000', roi: '1,8 anos' },
      },
      alternatives: [
        {
          name: 'Philips PageWriter TC70', score: 85,
          detail: 'TouchScreen · 12 canais · €9.200',
          scores: [
            { label: 'Score global',         value: 85 },
            { label: 'Custo-benefício',      value: 88 },
            { label: 'Adequação clínica',    value: 86 },
            { label: 'Manutenção e suporte', value: 82 },
          ],
          specs: [
            { k: 'Fabricante',         v: 'Philips Healthcare' },
            { k: 'Canais de ECG',      v: '12 canais' },
            { k: 'Interpretação IA',   v: 'Philips DXL' },
            { k: 'Manutenção/ano',     v: '€450' },
            { k: 'Preço estimado',     v: '€9.200' },
            { k: 'Prazo de entrega',   v: '4 semanas' },
          ],
        },
        {
          name: 'Schiller Cardiovit AT-102', score: 68,
          detail: 'Básico · 6 canais simultâneos · €4.200',
          scores: [
            { label: 'Score global',         value: 68 },
            { label: 'Custo-benefício',      value: 88 },
            { label: 'Adequação clínica',    value: 62 },
            { label: 'Manutenção e suporte', value: 74 },
          ],
          specs: [
            { k: 'Fabricante',         v: 'Schiller' },
            { k: 'Canais de ECG',      v: '6 canais simultâneos' },
            { k: 'Interpretação IA',   v: 'Básica' },
            { k: 'Manutenção/ano',     v: '€200' },
            { k: 'Preço estimado',     v: '€4.200' },
            { k: 'Prazo de entrega',   v: '3 semanas' },
          ],
        },
      ],
    },

    'Desfibrilhador': {
      primaryMetricLabel: 'Energia máxima (Joules)',
      radiationLabel: null,
      recommended: {
        name: 'Zoll R Series ALS',
        sub:  'Desfibrilhador bifásico · 360 J · Monitor integrado · €14.500',
        score: 92,
        tags: ['360 J bifásico', 'Sem radiação', 'CPR-D Padz', 'Monitor ECG'],
        highlightTags: ['360 J bifásico', 'Sem radiação'],
        scores: [
          { label: 'Score global',         value: 92 },
          { label: 'Custo-benefício',      value: 88 },
          { label: 'Adequação clínica',    value: 94 },
          { label: 'Manutenção e suporte', value: 90 },
        ],
        specs: [
          { k: 'Fabricante',          v: 'Zoll Medical' },
          { k: 'Energia máxima',      v: '360 Joules',    hi: true },
          { k: 'Forma de onda',       v: 'Bifásica RBFT', hi: true },
          { k: 'Monitor integrado',   v: 'ECG 12 derivações' },
          { k: 'Vida útil estimada',  v: '10 anos' },
          { k: 'Manutenção/ano',      v: '€800' },
          { k: 'Disponibilidade',     v: '99,9%',         hi: true },
          { k: 'Preço estimado',      v: '€14.500' },
          { k: 'Prazo de entrega',    v: '4 semanas' },
        ],
        financials: { invest: '€14.500', total10y: '€22.500', saving: '€8.000', roi: '2,2 anos' },
      },
      alternatives: [
        {
          name: 'Philips HeartStart XL+', score: 86,
          detail: 'Compacto · 200 J bifásico · €11.000',
          scores: [
            { label: 'Score global',         value: 86 },
            { label: 'Custo-benefício',      value: 90 },
            { label: 'Adequação clínica',    value: 85 },
            { label: 'Manutenção e suporte', value: 84 },
          ],
          specs: [
            { k: 'Fabricante',         v: 'Philips Healthcare' },
            { k: 'Energia máxima',     v: '200 Joules' },
            { k: 'Forma de onda',      v: 'Bifásica truncada' },
            { k: 'Manutenção/ano',     v: '€600' },
            { k: 'Preço estimado',     v: '€11.000' },
            { k: 'Prazo de entrega',   v: '3 semanas' },
          ],
        },
        {
          name: 'Stryker LIFEPAK 20e', score: 71,
          detail: 'Robusto · 360 J · menor conectividade · €10.500',
          scores: [
            { label: 'Score global',         value: 71 },
            { label: 'Custo-benefício',      value: 82 },
            { label: 'Adequação clínica',    value: 72 },
            { label: 'Manutenção e suporte', value: 78 },
          ],
          specs: [
            { k: 'Fabricante',         v: 'Stryker / Physio-Control' },
            { k: 'Energia máxima',     v: '360 Joules' },
            { k: 'Forma de onda',      v: 'Bifásica adaptativa' },
            { k: 'Manutenção/ano',     v: '€700' },
            { k: 'Preço estimado',     v: '€10.500' },
            { k: 'Prazo de entrega',   v: '5 semanas' },
          ],
        },
      ],
    },
  },

  /* ══════════════════════════════════════════════
     ONCOLOGIA
  ══════════════════════════════════════════════ */
  'Oncologia': {

    'Acelerador Linear': {
      primaryMetricLabel: 'Energia do feixe (MV)',
      radiationLabel: 'Dose/fração (Gy) — uso terapêutico',
      recommended: {
        name: 'Varian TrueBeam STx',
        sub:  'LINAC 6–15 MV · IMRT/VMAT/SBRT · €2.800.000',
        score: 95,
        tags: ['6–15 MV', 'Dose terapêutica', 'SBRT/IGRT', 'IA RapidPlan'],
        highlightTags: ['6–15 MV', 'Dose terapêutica'],
        scores: [
          { label: 'Score global',         value: 95 },
          { label: 'Custo-benefício',      value: 87 },
          { label: 'Adequação clínica',    value: 98 },
          { label: 'Manutenção e suporte', value: 91 },
        ],
        specs: [
          { k: 'Fabricante',                v: 'Varian Medical Systems' },
          { k: 'Energia do feixe',          v: '6, 10, 15 MV',           hi: true },
          { k: 'Dose/fração (terapêutica)', v: '1,8–20 Gy (protocolo)',  hi: true },
          { k: 'Técnicas suportadas',       v: 'IMRT, VMAT, SBRT, SRS', hi: true },
          { k: 'IGRT integrado',            v: 'kV + MV CBCT' },
          { k: 'Vida útil estimada',        v: '15 anos' },
          { k: 'Manutenção/ano',            v: '€140.000' },
          { k: 'Disponibilidade',           v: '96%',                    hi: true },
          { k: 'Preço estimado',            v: '€2.800.000' },
          { k: 'Prazo de entrega',          v: '36 semanas' },
        ],
        financials: { invest: '€2.800.000', total10y: '€4.200.000', saving: '€520.000', roi: '8,1 anos' },
      },
      alternatives: [
        {
          name: 'Elekta Versa HD', score: 89,
          detail: '6–10 MV · Agility MLC · €2.400.000',
          scores: [
            { label: 'Score global',         value: 89 },
            { label: 'Custo-benefício',      value: 88 },
            { label: 'Adequação clínica',    value: 91 },
            { label: 'Manutenção e suporte', value: 86 },
          ],
          specs: [
            { k: 'Fabricante',                v: 'Elekta' },
            { k: 'Energia do feixe',          v: '6, 10 MV' },
            { k: 'Dose/fração (terapêutica)', v: '1,8–18 Gy (protocolo)' },
            { k: 'Técnicas suportadas',       v: 'IMRT, VMAT, SBRT' },
            { k: 'Manutenção/ano',            v: '€120.000' },
            { k: 'Preço estimado',            v: '€2.400.000' },
            { k: 'Prazo de entrega',          v: '32 semanas' },
          ],
        },
        {
          name: 'Accuray TomoTherapy H Series', score: 74,
          detail: 'Tomotherapy rotacional · menor flexibilidade · €2.100.000',
          scores: [
            { label: 'Score global',         value: 74 },
            { label: 'Custo-benefício',      value: 76 },
            { label: 'Adequação clínica',    value: 80 },
            { label: 'Manutenção e suporte', value: 70 },
          ],
          specs: [
            { k: 'Fabricante',                v: 'Accuray' },
            { k: 'Energia do feixe',          v: '6 MV (fixo)' },
            { k: 'Dose/fração (terapêutica)', v: '1,8–12 Gy (protocolo)' },
            { k: 'Técnicas suportadas',       v: 'TomoTherapy, IMRT' },
            { k: 'Manutenção/ano',            v: '€105.000' },
            { k: 'Preço estimado',            v: '€2.100.000' },
            { k: 'Prazo de entrega',          v: '40 semanas' },
          ],
        },
      ],
    },

    'Braquiterapia': {
      primaryMetricLabel: 'Débito de dose (Gy/h)',
      radiationLabel: 'Dose total prescrita (Gy)',
      recommended: {
        name: 'Elekta Flexitron HDR',
        sub:  'Braquiterapia HDR · Ir-192 · €420.000',
        score: 90,
        tags: ['HDR Ir-192', 'Dose terapêutica', 'Stepping source', 'DICOM RT'],
        highlightTags: ['HDR Ir-192', 'Dose terapêutica'],
        scores: [
          { label: 'Score global',         value: 90 },
          { label: 'Custo-benefício',      value: 86 },
          { label: 'Adequação clínica',    value: 93 },
          { label: 'Manutenção e suporte', value: 88 },
        ],
        specs: [
          { k: 'Fabricante',              v: 'Elekta' },
          { k: 'Débito de dose',          v: '> 12 Gy/h (HDR)',     hi: true },
          { k: 'Dose total prescrita',    v: '7–45 Gy (protocolo)', hi: true },
          { k: 'Fonte radioativa',        v: 'Ir-192 stepping',     hi: true },
          { k: 'Canais',                  v: '40 canais' },
          { k: 'Vida útil estimada',      v: '15 anos' },
          { k: 'Manutenção/ano',          v: '€28.000' },
          { k: 'Disponibilidade',         v: '97%',                 hi: true },
          { k: 'Preço estimado',          v: '€420.000' },
          { k: 'Prazo de entrega',        v: '18 semanas' },
        ],
        financials: { invest: '€420.000', total10y: '€700.000', saving: '€140.000', roi: '5,2 anos' },
      },
      alternatives: [
        {
          name: 'Varian GammaMedplus iX', score: 82,
          detail: 'HDR · 20 canais · menor custo · €340.000',
          scores: [
            { label: 'Score global',         value: 82 },
            { label: 'Custo-benefício',      value: 90 },
            { label: 'Adequação clínica',    value: 80 },
            { label: 'Manutenção e suporte', value: 82 },
          ],
          specs: [
            { k: 'Fabricante',           v: 'Varian / Isodose Control' },
            { k: 'Débito de dose',       v: '> 12 Gy/h (HDR)' },
            { k: 'Dose total prescrita', v: '7–40 Gy (protocolo)' },
            { k: 'Canais',               v: '20 canais' },
            { k: 'Manutenção/ano',       v: '€22.000' },
            { k: 'Preço estimado',       v: '€340.000' },
            { k: 'Prazo de entrega',     v: '16 semanas' },
          ],
        },
      ],
    },
  },

  /* ══════════════════════════════════════════════
     NEUROLOGIA
  ══════════════════════════════════════════════ */
  'Neurologia': {

    'Ressonância Magnética': {
      primaryMetricLabel: 'Campo magnético (Tesla)',
      radiationLabel: null,
      recommended: {
        name: 'Siemens MAGNETOM Prisma 3T',
        sub:  'RM neurológica 3 Tesla · 80 mT/m gradientes · €2.100.000',
        score: 94,
        tags: ['3 Tesla', 'Sem radiação', '80 mT/m', 'NeuroQ IA'],
        highlightTags: ['3 Tesla', 'Sem radiação'],
        scores: [
          { label: 'Score global',         value: 94 },
          { label: 'Custo-benefício',      value: 84 },
          { label: 'Adequação clínica',    value: 98 },
          { label: 'Manutenção e suporte', value: 90 },
        ],
        specs: [
          { k: 'Fabricante',           v: 'Siemens Healthineers' },
          { k: 'Campo magnético',      v: '3,0 Tesla',    hi: true },
          { k: 'Gradientes',           v: '80 mT/m',      hi: true },
          { k: 'Slew rate',            v: '200 T/m/s' },
          { k: 'Vida útil estimada',   v: '15 anos' },
          { k: 'Manutenção/ano',       v: '€65.000' },
          { k: 'Disponibilidade',      v: '98%',          hi: true },
          { k: 'Preço estimado',       v: '€2.100.000' },
          { k: 'Prazo de entrega',     v: '24 semanas' },
        ],
        financials: { invest: '€2.100.000', total10y: '€2.750.000', saving: '€210.000', roi: '7,2 anos' },
      },
      alternatives: [
        {
          name: 'GE SIGNA Pioneer 3T', score: 86,
          detail: '3 Tesla · 70 mT/m · €1.800.000',
          scores: [
            { label: 'Score global',         value: 86 },
            { label: 'Custo-benefício',      value: 88 },
            { label: 'Adequação clínica',    value: 88 },
            { label: 'Manutenção e suporte', value: 82 },
          ],
          specs: [
            { k: 'Fabricante',         v: 'GE Healthcare' },
            { k: 'Campo magnético',    v: '3,0 Tesla' },
            { k: 'Gradientes',         v: '70 mT/m' },
            { k: 'Manutenção/ano',     v: '€58.000' },
            { k: 'Preço estimado',     v: '€1.800.000' },
            { k: 'Prazo de entrega',   v: '22 semanas' },
          ],
        },
      ],
    },

    'EEG Digital': {
      primaryMetricLabel: 'Canais de EEG',
      radiationLabel: null,
      recommended: {
        name: 'Natus Quantum Amplifier',
        sub:  'EEG digital 256 canais · Video integrado · €48.000',
        score: 89,
        tags: ['256 canais', 'Sem radiação', 'Video-EEG', 'AURA LTM'],
        highlightTags: ['256 canais', 'Sem radiação'],
        scores: [
          { label: 'Score global',         value: 89 },
          { label: 'Custo-benefício',      value: 85 },
          { label: 'Adequação clínica',    value: 92 },
          { label: 'Manutenção e suporte', value: 87 },
        ],
        specs: [
          { k: 'Fabricante',         v: 'Natus Medical' },
          { k: 'Canais de EEG',      v: '256 canais',    hi: true },
          { k: 'Video integrado',    v: 'Sim (4K)',       hi: true },
          { k: 'Modo LTM',          v: 'Sim (72h+)' },
          { k: 'Vida útil estimada', v: '10 anos' },
          { k: 'Manutenção/ano',     v: '€2.800' },
          { k: 'Disponibilidade',    v: '99%',            hi: true },
          { k: 'Preço estimado',     v: '€48.000' },
          { k: 'Prazo de entrega',   v: '8 semanas' },
        ],
        financials: { invest: '€48.000', total10y: '€76.000', saving: '€22.000', roi: '3,2 anos' },
      },
      alternatives: [
        {
          name: 'Nihon Kohden EEG-1200', score: 82,
          detail: '128 canais · robusto · €35.000',
          scores: [
            { label: 'Score global',         value: 82 },
            { label: 'Custo-benefício',      value: 90 },
            { label: 'Adequação clínica',    value: 80 },
            { label: 'Manutenção e suporte', value: 82 },
          ],
          specs: [
            { k: 'Fabricante',         v: 'Nihon Kohden' },
            { k: 'Canais de EEG',      v: '128 canais' },
            { k: 'Video integrado',    v: 'Opcional' },
            { k: 'Manutenção/ano',     v: '€2.200' },
            { k: 'Preço estimado',     v: '€35.000' },
            { k: 'Prazo de entrega',   v: '6 semanas' },
          ],
        },
      ],
    },
  },

  /* ══════════════════════════════════════════════
     ORTOPEDIA
  ══════════════════════════════════════════════ */
  'Ortopedia': {

    'Raio-X Ortopédico': {
      primaryMetricLabel: 'Resolução do detetor (lp/mm)',
      radiationLabel: 'Dose de entrada (µGy)',
      recommended: {
        name: 'Siemens YSIO Max',
        sub:  'Raio-X ortopédico · 3,4 lp/mm · CARE Dose4D · €160.000',
        score: 90,
        tags: ['3,4 lp/mm', 'CARE Dose4D', 'Tecto + Chão', 'DICOM 3.0'],
        highlightTags: ['3,4 lp/mm', 'CARE Dose4D'],
        scores: [
          { label: 'Score global',         value: 90 },
          { label: 'Custo-benefício',      value: 85 },
          { label: 'Adequação clínica',    value: 93 },
          { label: 'Manutenção e suporte', value: 88 },
        ],
        specs: [
          { k: 'Fabricante',               v: 'Siemens Healthineers' },
          { k: 'Resolução do detetor',     v: '3,4 lp/mm',      hi: true },
          { k: 'Dose de entrada (µGy)',    v: '0,8–1,0 µGy',    hi: true },
          { k: 'Configuração',             v: 'Tecto + coluna' },
          { k: 'Peso máx. doente',         v: '300 kg',         hi: true },
          { k: 'Vida útil estimada',       v: '12 anos' },
          { k: 'Manutenção/ano',           v: '€5.500' },
          { k: 'Disponibilidade',          v: '99%',            hi: true },
          { k: 'Preço estimado',           v: '€160.000' },
          { k: 'Prazo de entrega',         v: '10 semanas' },
        ],
        financials: { invest: '€160.000', total10y: '€215.000', saving: '€42.000', roi: '3,6 anos' },
      },
      alternatives: [
        {
          name: 'Agfa DR 600', score: 84,
          detail: '3,1 lp/mm · Wireless DR · €110.000',
          scores: [
            { label: 'Score global',         value: 84 },
            { label: 'Custo-benefício',      value: 91 },
            { label: 'Adequação clínica',    value: 82 },
            { label: 'Manutenção e suporte', value: 82 },
          ],
          specs: [
            { k: 'Fabricante',            v: 'Agfa HealthCare' },
            { k: 'Resolução do detetor',  v: '3,1 lp/mm' },
            { k: 'Dose de entrada (µGy)', v: '1,1–1,4 µGy' },
            { k: 'Configuração',          v: 'Coluna fixa' },
            { k: 'Manutenção/ano',        v: '€4.200' },
            { k: 'Preço estimado',        v: '€110.000' },
            { k: 'Prazo de entrega',      v: '8 semanas' },
          ],
        },
      ],
    },

    'Densitómetro Ósseo': {
      primaryMetricLabel: 'Precisão de medição (CV%)',
      radiationLabel: 'Dose efetiva (µSv/exame)',
      recommended: {
        name: 'Hologic Discovery A',
        sub:  'DEXA · CV 0,5% · Dose ultra-baixa · €85.000',
        score: 91,
        tags: ['CV 0,5%', 'Dose 1–6 µSv', 'Corpo inteiro', 'Morphometry VFA'],
        highlightTags: ['CV 0,5%', 'Dose 1–6 µSv'],
        scores: [
          { label: 'Score global',         value: 91 },
          { label: 'Custo-benefício',      value: 87 },
          { label: 'Adequação clínica',    value: 94 },
          { label: 'Manutenção e suporte', value: 88 },
        ],
        specs: [
          { k: 'Fabricante',              v: 'Hologic' },
          { k: 'Precisão de medição',     v: 'CV 0,5%',      hi: true },
          { k: 'Dose efetiva (µSv/exam)', v: '1–6 µSv',      hi: true },
          { k: 'Áreas de medição',        v: 'Coluna, anca, corpo inteiro', hi: true },
          { k: 'Tempo de exame (anca)',    v: '< 1 minuto' },
          { k: 'Vida útil estimada',      v: '12 anos' },
          { k: 'Manutenção/ano',          v: '€4.800' },
          { k: 'Disponibilidade',         v: '99%',           hi: true },
          { k: 'Preço estimado',          v: '€85.000' },
          { k: 'Prazo de entrega',        v: '8 semanas' },
        ],
        financials: { invest: '€85.000', total10y: '€133.000', saving: '€28.000', roi: '3,8 anos' },
      },
      alternatives: [
        {
          name: 'GE Lunar iDXA', score: 86,
          detail: 'CV 0,6% · Composição corporal · €78.000',
          scores: [
            { label: 'Score global',         value: 86 },
            { label: 'Custo-benefício',      value: 89 },
            { label: 'Adequação clínica',    value: 87 },
            { label: 'Manutenção e suporte', value: 84 },
          ],
          specs: [
            { k: 'Fabricante',              v: 'GE Healthcare' },
            { k: 'Precisão de medição',     v: 'CV 0,6%' },
            { k: 'Dose efetiva (µSv/exam)', v: '1–8 µSv' },
            { k: 'Áreas de medição',        v: 'Coluna, anca, corpo inteiro' },
            { k: 'Manutenção/ano',          v: '€4.200' },
            { k: 'Preço estimado',          v: '€78.000' },
            { k: 'Prazo de entrega',        v: '8 semanas' },
          ],
        },
      ],
    },
  },

  /* ══════════════════════════════════════════════
     URGÊNCIA
  ══════════════════════════════════════════════ */
  'Urgência': {

    'TC Emergência': {
      primaryMetricLabel: 'Cortes / Detetor',
      radiationLabel: 'Dose de radiação (DLP)',
      recommended: {
        name: 'Siemens SOMATOM go.Top',
        sub:  'TC Emergência 128 cortes · Protocolo stroke < 5 min · €720.000',
        score: 93,
        tags: ['128 cortes', 'DLP reduzido', 'Stroke < 5 min', 'IA syngo'],
        highlightTags: ['128 cortes', 'DLP reduzido'],
        scores: [
          { label: 'Score global',         value: 93 },
          { label: 'Custo-benefício',      value: 87 },
          { label: 'Adequação clínica',    value: 96 },
          { label: 'Manutenção e suporte', value: 88 },
        ],
        specs: [
          { k: 'Fabricante',              v: 'Siemens Healthineers' },
          { k: 'Cortes / Detetor',        v: '128 cortes',      hi: true },
          { k: 'Dose de radiação (DLP)',   v: 'Ultra-baixa',     hi: true },
          { k: 'Protocolo stroke',         v: '< 5 minutos',     hi: true },
          { k: 'Protocolo trauma',         v: '< 3 minutos' },
          { k: 'Vida útil estimada',       v: '15 anos' },
          { k: 'Manutenção/ano',           v: '€20.000' },
          { k: 'Disponibilidade',          v: '99%',             hi: true },
          { k: 'Preço estimado',           v: '€720.000' },
          { k: 'Prazo de entrega',         v: '14 semanas' },
        ],
        financials: { invest: '€720.000', total10y: '€920.000', saving: '€260.000', roi: '4,8 anos' },
      },
      alternatives: [
        {
          name: 'GE Revolution EVO', score: 85,
          detail: '64 cortes · protocolo stroke 7 min · €490.000',
          scores: [
            { label: 'Score global',         value: 85 },
            { label: 'Custo-benefício',      value: 92 },
            { label: 'Adequação clínica',    value: 82 },
            { label: 'Manutenção e suporte', value: 80 },
          ],
          specs: [
            { k: 'Fabricante',            v: 'GE Healthcare' },
            { k: 'Cortes / Detetor',      v: '64 cortes' },
            { k: 'Dose de radiação (DLP)', v: 'Baixa' },
            { k: 'Protocolo stroke',       v: '7 minutos' },
            { k: 'Manutenção/ano',         v: '€22.000' },
            { k: 'Preço estimado',         v: '€490.000' },
            { k: 'Prazo de entrega',       v: '12 semanas' },
          ],
        },
      ],
    },

    'Ventilador UCI': {
      primaryMetricLabel: 'Volume corrente (mL)',
      radiationLabel: null,
      recommended: {
        name: 'Dräger Evita V500',
        sub:  'Ventilador UCI · 2–2000 mL · Adulto/Pediátrico · €42.000',
        score: 92,
        tags: ['2–2000 mL', 'Sem radiação', 'NIV integrado', 'SmartCare IA'],
        highlightTags: ['2–2000 mL', 'Sem radiação'],
        scores: [
          { label: 'Score global',         value: 92 },
          { label: 'Custo-benefício',      value: 87 },
          { label: 'Adequação clínica',    value: 95 },
          { label: 'Manutenção e suporte', value: 90 },
        ],
        specs: [
          { k: 'Fabricante',         v: 'Dräger' },
          { k: 'Volume corrente',    v: '2–2000 mL',    hi: true },
          { k: 'Modos ventilatórios',v: 'VC, PC, SIMV, APRV, NIV, CPAP', hi: true },
          { k: 'FiO₂ range',         v: '21–100%' },
          { k: 'Vida útil estimada', v: '10 anos' },
          { k: 'Manutenção/ano',     v: '€2.500' },
          { k: 'Disponibilidade',    v: '99,5%',        hi: true },
          { k: 'Preço estimado',     v: '€42.000' },
          { k: 'Prazo de entrega',   v: '6 semanas' },
        ],
        financials: { invest: '€42.000', total10y: '€67.000', saving: '€18.000', roi: '3,1 anos' },
      },
      alternatives: [
        {
          name: 'Hamilton Medical C6', score: 86,
          detail: 'INTELLiVENT-ASV · 10–2200 mL · €38.000',
          scores: [
            { label: 'Score global',         value: 86 },
            { label: 'Custo-benefício',      value: 90 },
            { label: 'Adequação clínica',    value: 88 },
            { label: 'Manutenção e suporte', value: 82 },
          ],
          specs: [
            { k: 'Fabricante',          v: 'Hamilton Medical' },
            { k: 'Volume corrente',     v: '10–2200 mL' },
            { k: 'Modos ventilatórios', v: 'ASV, VC, PC, NIV, CPAP' },
            { k: 'Manutenção/ano',      v: '€2.200' },
            { k: 'Preço estimado',      v: '€38.000' },
            { k: 'Prazo de entrega',    v: '5 semanas' },
          ],
        },
        {
          name: 'Philips Trilogy Evo Universal', score: 71,
          detail: 'Portátil · UCI + domicílio · menor potência · €22.000',
          scores: [
            { label: 'Score global',         value: 71 },
            { label: 'Custo-benefício',      value: 85 },
            { label: 'Adequação clínica',    value: 68 },
            { label: 'Manutenção e suporte', value: 72 },
          ],
          specs: [
            { k: 'Fabricante',          v: 'Philips Healthcare' },
            { k: 'Volume corrente',     v: '25–1500 mL' },
            { k: 'Modos ventilatórios', v: 'VC, PC, NIV, CPAP' },
            { k: 'Manutenção/ano',      v: '€1.400' },
            { k: 'Preço estimado',      v: '€22.000' },
            { k: 'Prazo de entrega',    v: '4 semanas' },
          ],
        },
      ],
    },

    'Monitor Sinais Vitais': {
      primaryMetricLabel: 'Parâmetros monitorizados',
      radiationLabel: null,
      recommended: {
        name: 'Philips IntelliVue MX800',
        sub:  'Monitor UCI · 9 parâmetros · Tela 19" · €18.500',
        score: 91,
        tags: ['9 parâmetros', 'Sem radiação', 'Telemetria', 'IntelliSpace'],
        highlightTags: ['9 parâmetros', 'Sem radiação'],
        scores: [
          { label: 'Score global',         value: 91 },
          { label: 'Custo-benefício',      value: 86 },
          { label: 'Adequação clínica',    value: 93 },
          { label: 'Manutenção e suporte', value: 90 },
        ],
        specs: [
          { k: 'Fabricante',              v: 'Philips Healthcare' },
          { k: 'Parâmetros monitorizados',v: 'ECG, SpO₂, PNI, PIC, CO₂, Temp, Débito, PA invasiva + 1', hi: true },
          { k: 'Ecrã',                    v: '19" touch',     hi: true },
          { k: 'Alarmes',                 v: 'Inteligentes com IA' },
          { k: 'Vida útil estimada',      v: '10 anos' },
          { k: 'Manutenção/ano',          v: '€1.200' },
          { k: 'Disponibilidade',         v: '99,8%',         hi: true },
          { k: 'Preço estimado',          v: '€18.500' },
          { k: 'Prazo de entrega',        v: '5 semanas' },
        ],
        financials: { invest: '€18.500', total10y: '€30.500', saving: '€8.500', roi: '2,6 anos' },
      },
      alternatives: [
        {
          name: 'GE CARESCAPE B850', score: 85,
          detail: '7 parâmetros · modular · €15.000',
          scores: [
            { label: 'Score global',         value: 85 },
            { label: 'Custo-benefício',      value: 89 },
            { label: 'Adequação clínica',    value: 86 },
            { label: 'Manutenção e suporte', value: 82 },
          ],
          specs: [
            { k: 'Fabricante',               v: 'GE Healthcare' },
            { k: 'Parâmetros monitorizados', v: 'ECG, SpO₂, PNI, CO₂, Temp, PA invasiva, Débito' },
            { k: 'Ecrã',                     v: '15" touch' },
            { k: 'Manutenção/ano',           v: '€1.000' },
            { k: 'Preço estimado',           v: '€15.000' },
            { k: 'Prazo de entrega',         v: '4 semanas' },
          ],
        },
      ],
    },
  },
};

/**
 * Lookup helper: returns the entry for a given (specialty, equipment type),
 * falling back to a generic TC entry if not found.
 */
function getEquipData(especialidade, tipo) {
  return (EQUIP_DB[especialidade] && EQUIP_DB[especialidade][tipo])
    || EQUIP_DB['Imagiologia']['Tomografia Computorizada'];
}
