# MedDSS — Sistema de Apoio à Decisão em Saúde

Base de dados do projeto MedDSS (Medical Decision Support System), desenvolvido na UC de Métodos de Apoio à Decisão em Sistemas de Saúde (MADSS), 1.º ano do Mestrado em Engenharia Biomédica, no ISEP.

A proposta foi desenvolvida para apoiar hospitais públicos portugueses na decisão de aquisição e substituição de equipamento médico, com recomendações baseadas em critérios técnicos, financeiros e epidemiológicos (método AHP).

## Autores

Guilherme Pereira, Mariana Sá  
Departamento de Física, ISEP, Porto, Portugal

## Ficheiro

`base_dados.sql` — dump completo da base de dados `dss_hospitalar` (estrutura + dados), exportado via HeidiSQL a partir de MySQL 8.4.

## Instalação e ligação à base de dados

A aplicação web liga-se à base de dados através do [Laragon](https://laragon.org/), o ambiente local que fornece o servidor web (Apache), o PHP e o MySQL. Para tudo funcionar:

1. **Instalar o Laragon** e iniciar os serviços (botão *Start All*), garantindo que o Apache e o MySQL estão a correr.
2. **Colocar a pasta do projeto dentro da pasta `www` do Laragon**, por omissão:

   ```
   C:\laragon\www\<nome_da_pasta_do_projeto>
   ```

   Fora desta pasta, o servidor não serve o projeto.
3. **Importar a base de dados:** abrir o HeidiSQL (incluído no Laragon), ligar ao MySQL local, criar a base de dados `dss_hospitalar` e importar o ficheiro `base_dados.sql`.
4. **Confirmar as credenciais de ligação** à base de dados no código do projeto (host, utilizador, password e nome da base de dados `dss_hospitalar`). No Laragon, por omissão, o utilizador é `root` com password vazia, no host `localhost`.
5. **Abrir no navegador:**

   ```
   http://localhost/<nome_da_pasta_do_projeto>
   ```

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
