<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório - <?php echo $municipio_nome; ?></title>
    <style>
        :root {
            --bg-primary: #121212;
            --bg-secondary: #1e1e1e;
            --bg-card: #252525;
            --text-primary: #e0e0e0;
            --text-secondary: #a0a0a0;
            --accent: #ffc107;
            --accent-hover: #ffd54f;
            --border: #333;
            --success: #4caf50;
            --warning: #ff9800;
            --danger: #f44336;
            --info: #2196f3;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .header-hero {
            position: relative;
            height: 300px;
            overflow: hidden;
        }

        .header-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.7);
        }

        .header-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 30px;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
        }

        .municipality-name {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .municipality-state {
            font-size: 1.2rem;
            color: var(--accent);
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background-color: var(--bg-card);
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 30px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: var(--border);
        }

        /* ESTILOS MODIFICADOS PARA GRID 2x2 */
        .stats-grid-2x2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background-color: var(--bg-card);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            min-height: 180px;
            justify-content: center;
        }

        .stat-card.total-repasse {
            border-top: 4px solid var(--success);
        }

        .stat-card.total-desflorestamento {
            border-top: 4px solid var(--danger);
        }

        .stat-card.correlacao-pearson {
            border-top: 4px solid var(--info);
        }

        .stat-card.correlacao-spearman {
            border-top: 4px solid var(--warning);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 15px 0;
            width: 100%;
            word-break: break-word;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 5px;
            width: 100%;
        }

        .stat-desc {
            color: var(--text-secondary);
            font-size: 0.9rem;
            width: 100%;
        }

        .section-title {
            font-size: 1.8rem;
            margin: 40px 0 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
            color: var(--accent);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            background-color: var(--bg-card);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .data-table th {
            background-color: var(--bg-secondary);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--accent);
        }

        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border);
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover {
            background-color: rgba(255,255,255,0.05);
        }

        .charts-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        .chart-card {
            background-color: var(--bg-card);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .chart-title {
            font-size: 1.4rem;
            margin-bottom: 20px;
            color: var(--accent);
            text-align: center;
        }

        .chart-placeholder {
            height: 400px;
            background-color: var(--bg-secondary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        .analysis-section {
            background-color: var(--bg-card);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .analysis-text {
            line-height: 1.8;
            color: var(--text-primary);
        }

        .footer {
            text-align: center;
            padding: 30px 0;
            margin-top: 50px;
            border-top: 1px solid var(--border);
            color: var(--text-secondary);
        }

        @media (max-width: 768px) {
            .header-hero {
                height: 200px;
            }
            
            .municipality-name {
                font-size: 1.8rem;
            }
            
            .stats-grid-2x2 {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .stat-value {
                font-size: 1.8rem;
            }
            
            .data-table {
                font-size: 0.9rem;
            }
            
            .data-table th,
            .data-table td {
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
            
            .header-content {
                padding: 15px;
            }
            
            .municipality-name {
                font-size: 1.5rem;
            }
            
            .stat-card {
                padding: 15px;
                min-height: 150px;
            }
            
            .stat-value {
                font-size: 1.6rem;
            }
            
            .stat-label {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header-hero">
        <img src="<?php echo $imagem_capa; ?>" alt="<?php echo $municipio_nome; ?>" class="header-image">
        <div class="header-content">
            <h1 class="municipality-name"><?php echo $municipio_nome; ?></h1>
            <div class="municipality-state">Pará</div>
        </div>
    </header>

    <div class="container">
        <a href="index.php" class="back-button">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                <path d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
            </svg>
            Voltar para a lista de municípios
        </a>

        <!-- Estatísticas Principais - LAYOUT 2x2 -->
        <div class="stats-grid-2x2">
            <div class="stat-card total-repasse">
                <div class="stat-label">Total de Repasses (ICMS Verde)</div>
                <div class="stat-value" id="totalrepasses">Automação</div>
                <div class="stat-desc">Valor acumulado</div>
            </div>

            <div class="stat-card total-desflorestamento">
                <div class="stat-label">Área Desflorestada</div>
                <div class="stat-value" id="desflorestamento-acumulado"> km²</div>
                <div class="stat-desc">Total acumulado</div>
            </div>

            <div class="stat-card correlacao-pearson">
                <div class="stat-label">Correlação de Pearson</div>
                <div class="stat-value" id="pearson">Automação</div>
                <div class="stat-desc">Relação linear</div>
            </div>

            <div class="stat-card correlacao-spearman">
                <div class="stat-label">Correlação de Spearman</div>
                <div class="stat-value" id="spearman">Automação</div>
                <div class="stat-desc">Relação monotônica</div>
            </div>
        </div>

        <!-- Tabela de Dados Anuais -->
        <h2 class="section-title">Dados Anuais Detalhados</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Ano</th>
                    <th>Repasse ICMS Verde (R$)</th>
                    <th>Desflorestamento (km²)</th>
                    <th>Repasse Acumulado (R$)</th>
                    <th>Desflorestamento Acumulado (km²)</th>
                </tr>
            </thead>
            <tbody id="dados-tabelados">

            </tbody>
        </table>

        <!-- Gráficos -->
<h2 class="section-title">Visualizações e Análises</h2>
<div class="charts-container">
    <div class="chart-card">
        <h3 class="chart-title">Evolução dos Repasses vs Desflorestamento</h3>
        <div class="chart-wrapper">
            <canvas id="evolucaoChart"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <h3 class="chart-title">Correlação entre Variáveis</h3>
        <div class="chart-wrapper">
            <canvas id="correlacaoChart"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <h3 class="chart-title">Distribuição Temporal</h3>
        <div class="chart-wrapper">
            <canvas id="distribuicaoChart"></canvas>
        </div>
    </div>
</div>

<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="dados.js"></script>
<script>
const params = new URLSearchParams(window.location.search);
const id = params.get("id");



let mudar = `
    <tr>
        <td><?php echo $ano; ?></td>
        <td>R$ <?php echo number_format($dados['repasse_anual'], 2, ',', '.'); ?></td>
        <td><?php echo number_format($dados['desflorestamento_anual'], 2, ',', '.'); ?></td>
        <td>R$ <?php echo number_format($dados['repasse_acumulado'], 2, ',', '.'); ?></td>
        <td><?php echo number_format($dados['desflorestamento_acumulado'], 2, ',', '.'); ?></td>
    </tr>

`;






const anos = <?php echo json_encode(array_keys($dados_anuais)); ?>;
const repassesAnuais = <?php echo json_encode(array_column($dados_anuais, 'repasse_anual')); ?>;
const desflorestamentoAnual = <?php echo json_encode(array_column($dados_anuais, 'desflorestamento_anual')); ?>;
const repassesAcumulados = <?php echo json_encode(array_column($dados_anuais, 'repasse_acumulado')); ?>;
const desflorestamentoAcumulado = <?php echo json_encode(array_column($dados_anuais, 'desflorestamento_acumulado')); ?>;

// Dados para scatter plot (correlação)
const scatterData = [];
<?php 
foreach ($dados_anuais as $ano => $dados) {
    echo "scatterData.push({x: {$dados['repasse_anual']}, y: {$dados['desflorestamento_anual']}, ano: {$ano}});\n";
}
?>

// Gráfico 1: Evolução dos Repasses vs Desflorestamento
const evolucaoCtx = document.getElementById('evolucaoChart').getContext('2d');
new Chart(evolucaoCtx, {
    type: 'line',
    data: {
        labels: anos,
        datasets: [
            {
                label: 'Repasses ICMS Verde (R$)',
                data: repassesAnuais,
                borderColor: '#4caf50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                yAxisID: 'y',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Desflorestamento (km²)',
                data: desflorestamentoAnual,
                borderColor: '#f44336',
                backgroundColor: 'rgba(244, 67, 54, 0.1)',
                yAxisID: 'y1',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            title: {
                display: true,
                text: 'Evolução Temporal - Repasses vs Desflorestamento',
                color: '#ffc107',
                font: {
                    size: 16
                }
            },
            tooltip: {
                backgroundColor: 'rgba(30, 30, 30, 0.9)',
                titleColor: '#ffc107',
                bodyColor: '#e0e0e0',
                borderColor: '#333',
                borderWidth: 1
            },
            legend: {
                labels: {
                    color: '#e0e0e0'
                }
            }
        },
        scales: {
            x: {
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#a0a0a0'
                }
            },
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Repasses (R$)',
                    color: '#a0a0a0'
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#a0a0a0',
                    callback: function(value) {
                        return 'R$ ' + (value / 1000).toFixed(0) + ' mil';
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Desflorestamento (km²)',
                    color: '#a0a0a0'
                },
                grid: {
                    drawOnChartArea: false,
                },
                ticks: {
                    color: '#a0a0a0'
                }
            }
        }
    }
});


const correlacaoCtx = document.getElementById('correlacaoChart').getContext('2d');
new Chart(correlacaoCtx, {
    type: 'scatter',
    data: {
        datasets: [{
            label: 'Dados Anuais',
            data: scatterData,
            backgroundColor: 'rgba(33, 150, 243, 0.6)',
            borderColor: 'rgba(33, 150, 243, 1)',
            pointRadius: 8,
            pointHoverRadius: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'Correlação: Repasses vs Desflorestamento',
                color: '#ffc107',
                font: {
                    size: 16
                }
            },
            tooltip: {
                backgroundColor: 'rgba(30, 30, 30, 0.9)',
                titleColor: '#ffc107',
                bodyColor: '#e0e0e0',
                borderColor: '#333',
                borderWidth: 1,
                callbacks: {
                    label: function(context) {
                        const point = context.raw;
                        return [
                            `Ano: ${point.ano}`,
                            `Repasse: R$ ${(point.x).toLocaleString('pt-BR', {minimumFractionDigits: 2})}`,
                            `Desflorestamento: ${point.y.toFixed(2)} km²`
                        ];
                    }
                }
            },
            legend: {
                labels: {
                    color: '#e0e0e0'
                }
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Repasses ICMS Verde (R$)',
                    color: '#a0a0a0'
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#a0a0a0',
                    callback: function(value) {
                        return 'R$ ' + (value / 1000).toFixed(0) + ' mil';
                    }
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Desflorestamento (km²)',
                    color: '#a0a0a0'
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#a0a0a0'
                }
            }
        }
    }
});

// Gráfico 3: Distribuição Temporal (Barras)
const distribuicaoCtx = document.getElementById('distribuicaoChart').getContext('2d');
new Chart(distribuicaoCtx, {
    type: 'bar',
    data: {
        labels: anos,
        datasets: [
            {
                label: 'Repasses Anuais (R$)',
                data: repassesAnuais,
                backgroundColor: 'rgba(255, 193, 7, 0.8)',
                borderColor: 'rgba(255, 193, 7, 1)',
                borderWidth: 1,
                yAxisID: 'y'
            },
            {
                label: 'Desflorestamento Anual (km²)',
                data: desflorestamentoAnual,
                backgroundColor: 'rgba(244, 67, 54, 0.8)',
                borderColor: 'rgba(244, 67, 54, 1)',
                borderWidth: 1,
                yAxisID: 'y1',
                type: 'line',
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'Distribuição Anual dos Indicadores',
                color: '#ffc107',
                font: {
                    size: 16
                }
            },
            tooltip: {
                backgroundColor: 'rgba(30, 30, 30, 0.9)',
                titleColor: '#ffc107',
                bodyColor: '#e0e0e0',
                borderColor: '#333',
                borderWidth: 1
            },
            legend: {
                labels: {
                    color: '#e0e0e0'
                }
            }
        },
        scales: {
            x: {
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#a0a0a0'
                }
            },
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Repasses (R$)',
                    color: '#a0a0a0'
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#a0a0a0',
                    callback: function(value) {
                        return 'R$ ' + (value / 1000).toFixed(0) + ' mil';
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Desflorestamento (km²)',
                    color: '#a0a0a0'
                },
                grid: {
                    drawOnChartArea: false,
                },
                ticks: {
                    color: '#a0a0a0'
                }
            }
        }
    }
});
</script>

<style>
.chart-wrapper {
    height: 400px;
    position: relative;
}

.chart-card canvas {
    width: 100% !important;
    height: 100% !important;
}

/* Ajustes para modo escuro nos gráficos */
.chartjs-render-monitor {
    border-radius: 8px;
}
</style>

        <!-- Análise -->
        <div class="analysis-section">
            <h2 class="section-title">Análise Estatística</h2>
            <div class="analysis-text">
                <p>O coeficiente de correlação de Pearson de <strong><?php echo number_format($correlacao_pearson, 4, ',', '.'); ?></strong> 
                indica uma relação <?php 
                    $abs_pearson = abs($correlacao_pearson);
                    if ($abs_pearson < 0.3) echo "fraca";
                    elseif ($abs_pearson < 0.7) echo "moderada";
                    else echo "forte";
                ?> e <?php echo ($correlacao_pearson > 0) ? "positiva" : "negativa"; ?> entre os repasses de ICMS Verde e o desflorestamento no município.</p>
                
                <p>Já a correlação de Spearman de <strong><?php echo number_format($correlacao_spearman, 4, ',', '.'); ?></strong> 
                sugere uma tendência <?php 
                    $abs_spearman = abs($correlacao_spearman);
                    if ($abs_spearman < 0.3) echo "fraca";
                    elseif ($abs_spearman < 0.7) echo "moderada";
                    else echo "forte";
                ?> e <?php echo ($correlacao_spearman > 0) ? "positiva" : "negativa"; ?> na relação monotônica entre as variáveis.</p>
                
                <p>Estes resultados <?php 
                    if (($correlacao_pearson > 0 && $correlacao_spearman > 0) || ($correlacao_pearson < 0 && $correlacao_spearman < 0)) {
                        echo "sugerem uma consistência na relação observada";
                    } else {
                        echo "indicam possíveis inconsistências que merecem investigação mais aprofundada";
                    }
                ?> entre a política de repasses e os indicadores ambientais no município.</p>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>Sistema de Monitoramento - ICMS Verde Pará &copy; quietbyte <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>