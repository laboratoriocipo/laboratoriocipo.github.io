// municipio.js

// Funções de correlação
function calcularPearson(x, y) {
    const n = x.length;
    let sumX = 0, sumY = 0, sumXY = 0, sumX2 = 0, sumY2 = 0;
    
    for (let i = 0; i < n; i++) {
        sumX += x[i];
        sumY += y[i];
        sumXY += x[i] * y[i];
        sumX2 += x[i] * x[i];
        sumY2 += y[i] * y[i];
    }
    
    const numerador = n * sumXY - sumX * sumY;
    const denominador = Math.sqrt((n * sumX2 - sumX * sumX) * (n * sumY2 - sumY * sumY));
    
    return denominador === 0 ? 0 : numerador / denominador;
}

function calcularSpearman(x, y) {
    const n = x.length;
    
    // Função para calcular ranks
    function calcularRank(array) {
        const sorted = [...array].sort((a, b) => a - b);
        const ranks = array.map(val => sorted.indexOf(val) + 1);
        
        // Lidar com empates
        const uniqueValues = [...new Set(array)];
        uniqueValues.forEach(val => {
            const indices = array.map((v, i) => v === val ? i : -1).filter(i => i !== -1);
            if (indices.length > 1) {
                const avgRank = indices.reduce((sum, i) => sum + ranks[i], 0) / indices.length;
                indices.forEach(i => ranks[i] = avgRank);
            }
        });
        
        return ranks;
    }
    
    const rankX = calcularRank(x);
    const rankY = calcularRank(y);
    
    // Calcular diferenças dos ranks
    let sumD2 = 0;
    for (let i = 0; i < n; i++) {
        const d = rankX[i] - rankY[i];
        sumD2 += d * d;
    }
    
    return 1 - (6 * sumD2) / (n * (n * n - 1));
}

// Função para converter string com vírgula para número
function parseBrasileiro(numeroStr) {
    return parseFloat(numeroStr.replace(/\./g, '').replace(',', '.'));
}

// Função principal para carregar e processar dados
async function carregarDadosMunicipio() {
    // Obter código do município da URL
    const urlParams = new URLSearchParams(window.location.search);
    const codigoIbge = urlParams.get('id');
    
    if (!codigoIbge) {
        window.location.href = 'index.html';
        return;
    }
    
    // Filtrar dados do município
    const repasses = window.Valores.filter(item => item.codigo == codigoIbge);
    const desflorestamentos = window.Desflorestamento.filter(item => item.geocode_ibge == codigoIbge);
    
    if (repasses.length === 0 || desflorestamentos.length === 0) {
        document.getElementById('municipio-nome').textContent = 'Município não encontrado';
        return;
    }
    
    // Combinar dados por ano (após 2013)
    const dadosCombinados = [];
    const anosDisponiveis = new Set();
    
    // Adicionar anos de repasse
    repasses.forEach(repasse => {
        if (repasse.ano > 2013) {
            anosDisponiveis.add(repasse.ano);
        }
    });
    
    // Adicionar anos de desflorestamento
    desflorestamentos.forEach(desf => {
        if (desf.year > 2013) {
            anosDisponiveis.add(desf.year);
        }
    });
    
    // Ordenar anos
    const anos = Array.from(anosDisponiveis).sort();
    
    // Processar cada ano
    let repasseAcumulado = 0;
    let desflorestamentoAcumulado = 0;
    const dadosAnuais = [];
    const repassesAnuais = [];
    const desflorestamentosAnuais = [];
    
    anos.forEach(ano => {
        // Encontrar repasse do ano
        const repasseAnual = repasses.find(r => r.ano === ano)?.valor || 0;
        
        // Encontrar desflorestamento do ano
        const desfAnual = desflorestamentos.find(d => d.year === ano);
        const areaDesflorestamento = desfAnual ? parseBrasileiro(desfAnual.areakm) : 0;
        
        // Acumular
        repasseAcumulado += repasseAnual;
        desflorestamentoAcumulado += areaDesflorestamento;
        
        // Armazenar dados do ano
        dadosAnuais.push({
            ano,
            repasseAnual,
            desflorestamentoAnual: areaDesflorestamento,
            repasseAcumulado,
            desflorestamentoAcumulado
        });
        
        // Arrays para correlação
        repassesAnuais.push(repasseAnual);
        desflorestamentosAnuais.push(areaDesflorestamento);
    });
    
    // Informações do município
    const primeiroDesf = desflorestamentos[0];
    const municipioNome = primeiroDesf?.municipality || 'Município';
    const estado = primeiroDesf?.state || 'Pará';
    
    // Calcular totais
    const totalRepasse = repasseAcumulado;
    const totalDesflorestamento = desflorestamentoAcumulado;
    
    // Calcular correlações
    const correlacaoPearson = calcularPearson(repassesAnuais, desflorestamentosAnuais);
    const correlacaoSpearman = calcularSpearman(repassesAnuais, desflorestamentosAnuais);
    
    // Atualizar interface
    atualizarInterface(municipioNome, estado, codigoIbge, dadosAnuais, totalRepasse, totalDesflorestamento, correlacaoPearson, correlacaoSpearman);
    
    // Criar gráficos
    criarGraficos(dadosAnuais, correlacaoPearson, correlacaoSpearman);
}

function atualizarInterface(municipioNome, estado, codigoIbge, dadosAnuais, totalRepasse, totalDesflorestamento, pearson, spearman) {
    // Atualizar cabeçalho
    document.getElementById('municipio-nome').textContent = municipioNome;
    document.getElementById('municipio-estado').textContent = estado;
    
    // Tentar carregar imagem
    const imgElement = document.getElementById('municipio-imagem');
    imgElement.src = `fotos/${codigoIbge}.jpg`;
    imgElement.alt = municipioNome;
    imgElement.onerror = function() {
        this.src = 'fotos/padrao.jpg';
    };
    
    // Atualizar estatísticas
    document.getElementById('total-repasse').textContent = 
        `R$ ${totalRepasse.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;
    document.getElementById('total-desflorestamento').textContent = 
        `${totalDesflorestamento.toLocaleString('pt-BR', {minimumFractionDigits: 2})} km²`;
    document.getElementById('correlacao-pearson').textContent = 
        pearson.toFixed(4);
    document.getElementById('correlacao-spearman').textContent = 
        spearman.toFixed(4);
    
    // Atualizar tabela
    const tabelaBody = document.getElementById('tabela-dados');
    tabelaBody.innerHTML = '';
    
    dadosAnuais.forEach(dado => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${dado.ano}</td>
            <td>R$ ${dado.repasseAnual.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
            <td>${dado.desflorestamentoAnual.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
            <td>R$ ${dado.repasseAcumulado.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
            <td>${dado.desflorestamentoAcumulado.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
        `;
        tabelaBody.appendChild(row);
    });
    
    // Atualizar análise
    const absPearson = Math.abs(pearson);
    const absSpearman = Math.abs(spearman);
    
    const forcaPearson = absPearson < 0.3 ? 'fraca' : absPearson < 0.7 ? 'moderada' : 'forte';
    const forcaSpearman = absSpearman < 0.3 ? 'fraca' : absSpearman < 0.7 ? 'moderada' : 'forte';
    
    const direcaoPearson = pearson > 0 ? 'positiva' : 'negativa';
    const direcaoSpearman = spearman > 0 ? 'positiva' : 'negativa';
    
    const consistencia = (pearson > 0 && spearman > 0) || (pearson < 0 && spearman < 0) 
        ? 'sugerem uma consistência na relação observada' 
        : 'indicam possíveis inconsistências que merecem investigação mais aprofundada';
    
    document.getElementById('analise-texto').innerHTML = `
        <p>O coeficiente de correlação de Pearson de <strong>${pearson.toFixed(4)}</strong> 
        indica uma relação ${forcaPearson} e ${direcaoPearson} entre os repasses de ICMS Verde e o desflorestamento no município.</p>
        
        <p>Já a correlação de Spearman de <strong>${spearman.toFixed(4)}</strong> 
        sugere uma tendência ${forcaSpearman} e ${direcaoSpearman} na relação monotônica entre as variáveis.</p>
        
        <p>Estes resultados ${consistencia} entre a política de repasses e os indicadores ambientais no município.</p>
    `;
    
    // Atualizar ano no footer
    document.getElementById('ano-atual').textContent = new Date().getFullYear();
}

function criarGraficos(dadosAnuais, pearson, spearman) {
    const anos = dadosAnuais.map(d => d.ano);
    const repassesAnuais = dadosAnuais.map(d => d.repasseAnual);
    const desflorestamentoAnual = dadosAnuais.map(d => d.desflorestamentoAnual);
    const repassesAcumulados = dadosAnuais.map(d => d.repasseAcumulado);
    const desflorestamentoAcumulado = dadosAnuais.map(d => d.desflorestamentoAcumulado);
    
    // Dados para scatter plot
    const scatterData = dadosAnuais.map(d => ({
        x: d.repasseAnual,
        y: d.desflorestamentoAnual,
        ano: d.ano
    }));
    
    // Gráfico 1: Evolução
    new Chart(document.getElementById('evolucaoChart'), {
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
            plugins: {
                title: {
                    display: true,
                    text: 'Evolução Temporal - Repasses vs Desflorestamento',
                    color: '#ffc107',
                    font: { size: 16 }
                }
            }
        }
    });
    
    // Gráfico 2: Correlação (Scatter)
    new Chart(document.getElementById('correlacaoChart'), {
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
                    text: `Correlação: Pearson=${pearson.toFixed(4)}, Spearman=${spearman.toFixed(4)}`,
                    color: '#ffc107',
                    font: { size: 16 }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const point = context.raw;
                            return [
                                `Ano: ${point.ano}`,
                                `Repasse: R$ ${point.x.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`,
                                `Desflorestamento: ${point.y.toFixed(2)} km²`
                            ];
                        }
                    }
                }
            }
        }
    });
    
    // Gráfico 3: Distribuição
    new Chart(document.getElementById('distribuicaoChart'), {
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
                    font: { size: 16 }
                }
            }
        }
    });
}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', carregarDadosMunicipio);