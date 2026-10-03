# MedDSS — Sistema de Apoio à Decisão em Saúde

Base de dados do projeto MedDSS (Medical Decision Support System), desenvolvido na UC de M]etodos de Apoio à Decisão em Sistemas de Saúde (MADSS) 1ºano de Mestrado em Engenharia Biomédica pelo ISEP. 

A proposta foi desenvolvida para apoiar hospitais públicos portugueses na decisão de aquisição e substituição de equipamento médico, com recomendações baseadas em critérios técnicos, financeiros e epidemiológicos (método AHP).

## Ficheiro

`base_dados.sql` — dump completo da base de dados `dss_hospitalar` (estrutura + dados), exportado via HeidiSQL a partir de MySQL 8.4.

## Como testar o sistema

### 1. Login

```
Utilizador: joao.silva
Password:   demo1234
```

### 2. Fluxo de navegação

Login → Região → Hospital → Especialidade (com recomendação) → Aquisição ou Substituição → Configuração AHP → Resultado/Score final

Na página de **Especialidade**, o sistema já apresenta a especialidade mais crítica para a região e hospital selecionados, com base nos indicadores demográficos e epidemiológicos (prevalência, mortalidade, índice de envelhecimento). As restantes especialidades continuam visíveis para escolha livre do utilizador.

Em **Aquisição** ou **Substituição**, é possível ajustar os pesos dos critérios (matriz AHP) e o score de cada equipamento atualiza-se automaticamente.

### 3. Exemplos sugeridos para teste

| Hospital | Especialidade | Equipamento a testar |
|---|---|---|
| Centro Hospitalar do Porto | Cardiologia | Holter (substituição — várias opções no catálogo) |
| Centro Hospitalar do Porto | Cardiologia | Desfibrilhador (aquisição) |
| Hospital São João | Ginecologia / Obstetrícia | Ecógrafo (substituição) |

Estes três equipamentos foram escolhidos por terem maior variedade de alternativas no catálogo, o que permite ver melhor o efeito da matriz AHP no ranking final.

## Estrutura da base de dados

- `regioes`, `hospitais`, `especialidades` — dados de contexto
- `indicadores_especialidade_regiao` — dados demográficos/epidemiológicos usados no cálculo da especialidade mais crítica
- `equipamento` — inventário instalado nos hospitais (`id_hosp` preenchido) e catálogo de mercado (`id_hosp` = NULL)
- `eq_tac`, `eq_rm`, `eq_rx`, `eq_angio`, `eq_holter`, `eq_ecg`, `eq_monitor`, `eq_desfibri`, `eq_eco`, `eq_cardiotoc`, `eq_bronco`, `eq_ventilad`, `eq_ortopedia` — atributos técnicos específicos por tipo de equipamento, usados como critérios no AHP
