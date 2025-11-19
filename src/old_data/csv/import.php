<?php
// Configuração do banco
$database_path = '/var/www/database/icms_verde.sqlite';

try {
    // Conectar ao SQLite
    $pdo = new PDO("sqlite:" . $database_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Criando tabelas...\n";
    
    // Criar tabela desflorestamento
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS desflorestamento (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ano INTEGER,
            area_km REAL,
            municipio TEXT,
            geocode_ibge INTEGER,
            estado TEXT
        )
    ");
    
    // Criar tabela repasses (tratando id_mun como mes)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS repasses_municipais (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            codigo_ibge INTEGER,
            mes INTEGER,
            ano INTEGER,
            valor REAL
        )
    ");
    
    echo "Importando dados de desflorestamento...\n";
    
    // Importar desflorestamento.csv
    if (($handle = fopen("desflorestamento_prodes.csv", "r")) !== FALSE) {
        $header = fgetcsv($handle, 1000, ";");
        
        $stmt = $pdo->prepare("
            INSERT INTO desflorestamento (ano, area_km, municipio, geocode_ibge, estado) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        while (($row = fgetcsv($handle, 1000, ";")) !== FALSE) {
            // Converter vírgula para ponto no número decimal
            $area_km = str_replace(',', '.', $row[1]);
            
            $stmt->execute([
                $row[0],    // year
                $area_km,   // areakm (convertido)
                $row[2],    // municipality
                $row[3],    // geocode_ibge
                $row[4]     // state
            ]);
        }
        fclose($handle);
    }
    
    echo "Importando dados de repasses...\n";
    
    // Importar repasses_municipais.csv
    if (($handle = fopen("repasses_municipais.csv", "r")) !== FALSE) {
        $header = fgetcsv($handle, 1000, ";");
        
        $stmt = $pdo->prepare("
            INSERT INTO repasses_municipais (codigo_ibge, mes, ano, valor) 
            VALUES (?, ?, ?, ?)
        ");
        
        while (($row = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $stmt->execute([
                $row[0],    // codigo
                $row[1],    // id_mun (tratado como mes)
                $row[2],    // ano
                $row[3]     // valor
            ]);
        }
        fclose($handle);
    }
    
    echo "Importação concluída com sucesso!\n";
    
    // Mostrar estatísticas
    $count_desf = $pdo->query("SELECT COUNT(*) FROM desflorestamento")->fetchColumn();
    $count_rep = $pdo->query("SELECT COUNT(*) FROM repasses_municipais")->fetchColumn();
    
    echo "Registros importados:\n";
    echo "- Desflorestamento: $count_desf\n";
    echo "- Repasses municipais: $count_rep\n";
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>
