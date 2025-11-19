<?php
function gerarMapaParaFromGeoJSON($codigoIBGEDestacado = null, $arquivoGeoJSON = 'municipios.json') {
    // Carrega o arquivo GeoJSON
    if (!file_exists($arquivoGeoJSON)) {
        return '<p style="color: red;">Arquivo GeoJSON não encontrado: ' . $arquivoGeoJSON . '</p>';
    }
    
    $geoJSON = json_decode(file_get_contents($arquivoGeoJSON), true);
    
    if (!$geoJSON || !isset($geoJSON['features'])) {
        return '<p style="color: red;">Erro ao carregar GeoJSON</p>';
    }
    
    // Encontra os limites do mapa para calcular a viewBox
    $minX = $minY = INF;
    $maxX = $maxY = -INF;
    
    foreach ($geoJSON['features'] as $feature) {
        $coordinates = $feature['geometry']['coordinates'][0];
        foreach ($coordinates as $coord) {
            $minX = min($minX, $coord[0]);
            $minY = min($minY, $coord[1]);
            $maxX = max($maxX, $coord[0]);
            $maxY = max($maxY, $coord[1]);
        }
    }
    
    // Adiciona uma margem menor para melhor visualização
    $marginX = ($maxX - $minX) * 0.02;
    $marginY = ($maxY - $minY) * 0.02;
    
    $minX -= $marginX;
    $minY -= $marginY;
    $maxX += $marginX;
    $maxY += $marginY;
    
    // Calcula dimensões
    $width = $maxX - $minX;
    $height = $maxY - $minY;
    
    // Gera o SVG
    $svg = '<svg width="800" height="600" viewBox="' . $minX . ' ' . $minY . ' ' . $width . ' ' . $height . '" xmlns="http://www.w3.org/2000/svg">';
    $svg .= '
    <style>
        .municipio { 
            fill: #2d3748; 
            stroke: #718096; 
            stroke-width: 0.08;
            transition: all 0.3s ease;
        }
        .municipio:hover { 
            fill: #4a5568; 
            cursor: pointer;
            stroke-width: 0.12;
        }
        .municipio-destacado { 
            fill: #800020 !important; 
            stroke: #ff4444;
            stroke-width: 0.15;
            filter: drop-shadow(0 0 3px #800020);
        }
        .tooltip {
            position: absolute;
            background: rgba(0,0,0,0.85);
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-family: Arial, sans-serif;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 1000;
            border: 1px solid #4a5568;
        }
    </style>
    ';
    
    // Adiciona cada município
    foreach ($geoJSON['features'] as $feature) {
        $codigoIBGE = $feature['properties']['id'];
        $nome = $feature['properties']['name'];
        $coordinates = $feature['geometry']['coordinates'][0];
        
        // Converte coordenadas para string de path
        $pathData = 'M';
        foreach ($coordinates as $index => $coord) {
            if ($index > 0) $pathData .= ' L';
            $pathData .= number_format($coord[0], 6) . ',' . number_format($coord[1], 6);
        }
        $pathData .= ' Z';
        
        $classe = 'municipio';
        if ($codigoIBGE == $codigoIBGEDestacado) {
            $classe .= ' municipio-destacado';
        }
        
        $svg .= '<path id="' . $codigoIBGE . '" class="' . $classe . '" d="' . $pathData . '" data-nome="' . htmlspecialchars($nome) . '" />';
    }
    
    $svg .= '</svg>';
    
    // JavaScript para interatividade
    $js = '
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tooltip = document.createElement("div");
            tooltip.className = "tooltip";
            document.body.appendChild(tooltip);
            
            const municipios = document.querySelectorAll(".municipio");
            
            municipios.forEach(municipio => {
                municipio.addEventListener("mouseenter", function(e) {
                    const nome = this.getAttribute("data-nome");
                    const codigo = this.id;
                    tooltip.textContent = nome + " (" + codigo + ")";
                    tooltip.style.opacity = "1";
                });
                
                municipio.addEventListener("mousemove", function(e) {
                    tooltip.style.left = (e.clientX + 15) + "px";
                    tooltip.style.top = (e.clientY + 15) + "px";
                });
                
                municipio.addEventListener("mouseleave", function() {
                    tooltip.style.opacity = "0";
                });
                
                municipio.addEventListener("click", function() {
                    const codigoIBGE = this.id;
                    window.location.href = "?ibge=" + codigoIBGE;
                });
            });
        });
    </script>
    ';
    
    return '
    <div style="background-color: #1a1a1a; padding: 25px; border-radius: 12px; display: inline-block; position: relative; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
        ' . $svg . '
        ' . $js . '
    </div>';
}

// Função para buscar nome do município pelo código IBGE
function getNomeMunicipio($codigoIBGE, $arquivoGeoJSON = 'municipios.json') {
    if (!file_exists($arquivoGeoJSON)) return null;
    
    $geoJSON = json_decode(file_get_contents($arquivoGeoJSON), true);
    if (!$geoJSON) return null;
    
    foreach ($geoJSON['features'] as $feature) {
        if ($feature['properties']['id'] == $codigoIBGE) {
            return $feature['properties']['name'];
        }
    }
    return null;
}

// Função para obter lista de municípios para o select
function getListaMunicipios($arquivoGeoJSON = 'municipios.json') {
    if (!file_exists($arquivoGeoJSON)) return [];
    
    $geoJSON = json_decode(file_get_contents($arquivoGeoJSON), true);
    if (!$geoJSON) return [];
    
    $municipios = [];
    foreach ($geoJSON['features'] as $feature) {
        $municipios[$feature['properties']['id']] = $feature['properties']['name'];
    }
    
    asort($municipios); // Ordena por nome
    return $municipios;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa do Pará - IBGE</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #0c0c0c 0%, #1a1a1a 100%);
            color: #e2e8f0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #800020, #ff4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .header p {
            font-size: 1.1rem;
            opacity: 0.8;
        }
        
        .controls {
            background: rgba(45, 55, 72, 0.6);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid #4a5568;
        }
        
        .map-container {
            text-align: center;
            margin: 25px 0;
            padding: 10px;
        }
        
        .info-box {
            background: rgba(45, 55, 72, 0.6);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 12px;
            margin-top: 25px;
            border: 1px solid #4a5568;
        }
        
        .form-group {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
            margin: 15px 0;
        }
        
        label {
            font-weight: 600;
            font-size: 1rem;
        }
        
        input, select, button {
            padding: 10px 16px;
            border: 1px solid #4a5568;
            border-radius: 8px;
            background: #2d3748;
            color: #e2e8f0;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #800020;
            box-shadow: 0 0 0 2px rgba(128, 0, 32, 0.2);
        }
        
        button {
            background: linear-gradient(45deg, #800020, #a52a2a);
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
        
        button:hover {
            background: linear-gradient(45deg, #a52a2a, #c53030);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(128, 0, 32, 0.3);
        }
        
        .clear-btn {
            background: #4a5568;
            margin-left: 10px;
        }
        
        .clear-btn:hover {
            background: #718096;
            transform: translateY(-1px);
        }
        
        .municipio-info {
            background: linear-gradient(45deg, #800020, #a52a2a);
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .municipio-info h3 {
            margin-bottom: 5px;
            font-size: 1.3rem;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .stat-item {
            background: rgba(74, 85, 104, 0.3);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #800020;
            margin-bottom: 5px;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .form-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            input, select, button {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗺️ Mapa do Pará</h1>
            <p>Visualize e destaque municípios por código IBGE</p>
        </div>

        <div class="controls">
            <?php
            $codigoIBGE = $_GET['ibge'] ?? null;
            $nomeMunicipio = $codigoIBGE ? getNomeMunicipio($codigoIBGE) : null;
            $listaMunicipios = getListaMunicipios();
            
            if ($codigoIBGE && $nomeMunicipio) {
                echo '<div class="municipio-info">';
                echo '<h3>📍 Município Destacado</h3>';
                echo '<p><strong>' . $nomeMunicipio . '</strong> (Código IBGE: ' . $codigoIBGE . ')</p>';
                echo '</div>';
            }
            ?>
            
            <form method="GET">
                <div class="form-group">
                    <label for="ibge">Selecionar Município:</label>
                    <select id="ibge" name="ibge" onchange="this.form.submit()">
                        <option value="">-- Selecione um município --</option>
                        <?php foreach ($listaMunicipios as $codigo => $nome): ?>
                            <option value="<?php echo $codigo; ?>" <?php echo $codigoIBGE == $codigo ? 'selected' : ''; ?>>
                                <?php echo $nome . ' (' . $codigo . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <input type="text" 
                           id="ibge_search" 
                           name="ibge" 
                           value="<?php echo htmlspecialchars($codigoIBGE ?? ''); ?>" 
                           placeholder="Ou digite o código IBGE"
                           style="flex: 1; max-width: 200px;">
                    
                    <button type="submit">Destacar Município</button>
                    
                    <?php if ($codigoIBGE): ?>
                        <a href="?" class="clear-btn" style="padding: 10px 16px; background: #4a5568; color: white; text-decoration: none; border-radius: 8px; display: inline-block;">
                            Limpar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="map-container">
            <?php
            echo gerarMapaParaFromGeoJSON($codigoIBGE);
            ?>
        </div>

        <div class="info-box">
            <h3>📊 Informações do Mapa</h3>
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-value"><?php echo count($listaMunicipios); ?></div>
                    <div>Total de Municípios</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $codigoIBGE ? '1' : '0'; ?></div>
                    <div>Município Destacado</div>
                </div>
            </div>
            
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #4a5568;">
                <h4>🎯 Como usar:</h4>
                <ul style="list-style: none; margin-top: 10px;">
                    <li style="margin-bottom: 8px;">• <strong>Clique</strong> em qualquer município no mapa para destacá-lo</li>
                    <li style="margin-bottom: 8px;">• <strong>Selecione</strong> um município no dropdown acima</li>
                    <li style="margin-bottom: 8px;">• <strong>Digite</strong> o código IBGE diretamente no campo de texto</li>
                    <li style="margin-bottom: 8px;">• <strong>Passe o mouse</strong> sobre os municípios para ver informações</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Foco no campo de busca quando a página carrega
        document.addEventListener('DOMContentLoaded', function() {
            const searchField = document.getElementById('ibge_search');
            if (searchField) {
                searchField.focus();
            }
        });
    </script>
</body>
</html>