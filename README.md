# ICMS Verde no Pará

Sistema de monitoramento que relaciona os repasses do ICMS Verde e os dados de desflorestamento nos **144 municípios paraenses**, com relatórios individuais, análises estatísticas e visualizações.

Publicado em [GitHub Pages](https://laboratoriocipo.github.io).

## Funcionalidades

- Lista dos 144 municípios do Pará com busca por nome.
- Relatório por município com:
  - Total de repasses do ICMS Verde (valor acumulado).
  - Área desflorestada total.
  - Correlação de Pearson e Spearman entre repasses e desflorestamento.
  - Tabela de dados anuais (repasse, desflorestamento e acumulados).
  - Gráficos (Chart.js): evolução de repasses vs. desflorestamento, correlação entre variáveis e distribuição temporal.
- Mapa do estado do Pará com destaque de municípios (`mapa_antigo.html`).
- Análise estatística gerada por página de município.

## Tecnologias

- **Front-end**: HTML5, CSS, JavaScript puro, [Chart.js](https://www.chart.js.org/) (via CDN).
- **Dados**: arquivos JSON/JS estáticos (`dados.js`, `municipios.json` em GeoJSON).
- **Back-end/Análise**: PHP 8.2 (Apache) com SQLite (`pdo_sqlite`) para correlações e geração de mapas.
- **Containerização**: Docker + Docker Compose.

## Estrutura do projeto

```
.
├── index.html          # Página principal (lista de municípios + busca)
├── municipio.html      # Relatório individual do município
├── municipio.js        # Lógica de exibição e cálculos do relatório
├── mapa_antigo.html    # Mapa do estado (versão estática)
├── mapa_para.php       # Gerador de mapa SVG do Pará a partir de GeoJSON
├── correlacoes.js      # Funções PHP de correlação (Pearson e Spearman)
├── style.css           # Estilos globais
├── dados.js            # Dados dos repasses, desflorestamento e nomes
├── municipios.json     # GeoJSON dos municípios paraenses
├── database/
│   └── icms_verde.sqlite   # Banco SQLite usado pelo backend PHP
├── fotos/              # Fotos dos municípios (originais)
├── fotos_otimizadas/   # Fotos otimizadas para a web
├── Dockerfile          # Imagem PHP 8.2 + Apache + SQLite
└── docker-compose.yml  # Serviço php-apache na porta 8080
```

### Estrutura dos dados (`dados.js`)

O arquivo expõe três objetos globais:

| Variável | Conteúdo |
| --- | --- |
| `window.Valores` | Repasses do ICMS Verde por município e ano (`codigo`, `id_mun`, `ano`, `valor`) |
| `window.Desflorestamento` | Área desflorestada por município e ano |
| `window.Nomes` | Nomes dos municípios |

## Como executar

### Opção 1 — Apenas o front-end (estático)

Como o site é 100% estático na parte visual, basta servir a pasta raiz com qualquer servidor estático:

```bash
python3 -m http.server 8000
```

Acesse `http://localhost:8000`.

### Opção 2 — Com Docker (PHP + SQLite)

```bash
docker compose up --build
```

Acesse `http://localhost:8080`.

> Observação: o `docker-compose.yml` monta `./src:/var/www/html`. Se os arquivos estiverem na raiz do repositório, ajuste o volume para `.:/var/www/html` (ou crie a pasta `src/` com o conteúdo).

## Licença

Sistema de Monitoramento — Pará &copy; quietbyte.
