<?php

function spearman($array1, $array2) {
    // Verifica se os arrays têm o mesmo tamanho
    if (count($array1) != count($array2)) {
        throw new InvalidArgumentException('Os arrays devem ter o mesmo tamanho');
    }
    
    $n = count($array1);
    
    // Função melhorada para calcular ranks com tratamento de empates
    function getRanks($array) {
        $n = count($array);
        
        // Cria array com valores e índices originais
        $indexedArray = [];
        for ($i = 0; $i < $n; $i++) {
            $indexedArray[$i] = $array[$i];
        }
        
        // Ordena mantendo os índices
        asort($indexedArray);
        
        $ranks = array_fill(0, $n, 0);
        $currentRank = 1;
        
        while (!empty($indexedArray)) {
            // Pega o menor valor atual
            $currentValue = current($indexedArray);
            $currentIndex = key($indexedArray);
            
            // Encontra todos os elementos com o mesmo valor (empates)
            $tieIndexes = [];
            $tieValues = [];
            
            foreach ($indexedArray as $idx => $val) {
                if ($val == $currentValue) {
                    $tieIndexes[] = $idx;
                    $tieValues[] = $val;
                }
            }
            
            // Calcula o rank médio para os valores empatados
            $tieCount = count($tieIndexes);
            $averageRank = $currentRank + ($tieCount - 1) / 2;
            
            // Atribui o rank médio a todos os valores empatados
            foreach ($tieIndexes as $idx) {
                $ranks[$idx] = $averageRank;
                unset($indexedArray[$idx]);
            }
            
            $currentRank += $tieCount;
        }
        
        return $ranks;
    }

    $ranks1 = getRanks($array1);
    $ranks2 = getRanks($array2);
    
    // Calcula as diferenças entre os ranks
    $d = [];
    for ($i = 0; $i < $n; $i++) {
        $d[] = $ranks1[$i] - $ranks2[$i];
    }
    
    // Calcula a soma dos quadrados das diferenças
    $d2 = array_sum(array_map(function($x) { return $x * $x; }, $d));
    
    // Calcula o coeficiente de Spearman
    if ($n > 1) {
        return 1 - (6 * $d2) / ($n * ($n * $n - 1));
    } else {
        return 0;
    }
}

function pearson($array1, $array2) {
    // Verifica se os arrays têm o mesmo tamanho
    if (count($array1) != count($array2)) {
        throw new InvalidArgumentException('Os arrays devem ter o mesmo tamanho');
    }
    
    $n = count($array1);
    
    // Se n for 1, retorna um valor padrão
    if ($n <= 1) {
        return 0;
    }
    
    $sum_x = array_sum($array1);
    $sum_y = array_sum($array2);
    $sum_xy = 0;
    $sum_x2 = 0;
    $sum_y2 = 0;
    
    for ($i = 0; $i < $n; $i++) {
        $sum_xy += $array1[$i] * $array2[$i];
        $sum_x2 += $array1[$i] ** 2;
        $sum_y2 += $array2[$i] ** 2;
    }
    
    // Calcula o coeficiente de Pearson
    $numerator = $n * $sum_xy - $sum_x * $sum_y;
    $denominator_x = $n * $sum_x2 - $sum_x ** 2;
    $denominator_y = $n * $sum_y2 - $sum_y ** 2;
    
    if ($denominator_x <= 0 || $denominator_y <= 0) {
        return 0;
    }
    
    return $numerator / sqrt($denominator_x * $denominator_y);
}

// Exemplo de uso com dados mais realistas:
/*
$x = [1.5, 2.3, 3.7, 4.1, 5.8, 6.2, 7.4];
$y = [2.1, 3.9, 5.8, 7.2, 9.1, 10.5, 12.3];

echo "Dados X: " . implode(", ", $x) . "\n";
echo "Dados Y: " . implode(", ", $y) . "\n";
echo "Coeficiente de Spearman: " . number_format(spearman($x, $y), 4) . "\n";
echo "Coeficiente de Pearson: " . number_format(pearson($x, $y), 4) . "\n";

// Teste com arrays vazios ou pequenos
echo "\nTestes especiais:\n";
echo "Arrays vazios - Spearman: " . spearman([], []) . "\n";
echo "Arrays com 1 elemento - Pearson: " . pearson([5], [3]) . "\n";
*/
?>