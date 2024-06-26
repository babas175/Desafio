<?php

/**
 * Gera um arquivo com números aleatórios para usar como entrada
 * @param string $filename Nome do arquivo a ser gerado
 * @param int $numNumbers Quantidade de números a serem gerados
 * @param int $maxNumber Valor máximo dos números gerados
 */
function generateInputFile($filename, $numNumbers, $maxNumber) {
    $fileHandle = fopen($filename, 'w');
    for ($i = 0; $i < $numNumbers; $i++) {
        $number = rand(1, $maxNumber);
        fwrite($fileHandle, $number . PHP_EOL);
    }
    fclose($fileHandle);
}

/**
 * Função para dividir o arquivo grande em arquivos menores
 * @param string $inputFile Arquivo de entrada
 * @param int $chunkSize Tamanho de cada parte em linhas
 * @param string $tempDir Diretório temporário para armazenar partes
 * @return array Lista de arquivos de partes
 */
function splitFile($inputFile, $chunkSize, $tempDir) {
    $fileHandle = fopen($inputFile, 'r');
    $fileParts = [];
    $partNumber = 0;
    $data = [];

    while (!feof($fileHandle)) {
        $data[] = trim(fgets($fileHandle));
        if (count($data) === $chunkSize || feof($fileHandle)) {
            $partFileName = $tempDir . '/part_' . $partNumber . '.txt';
            file_put_contents($partFileName, implode(PHP_EOL, $data));
            $fileParts[] = $partFileName;
            $data = [];
            $partNumber++;
        }
    }

    fclose($fileHandle);
    return $fileParts;
}

/**
 * Função para ordenar cada arquivo pequeno utilizando Merge Sort personalizado
 * @param array $fileParts Lista de arquivos de partes
 * @param string $order Ordem de ordenação ('asc' ou 'desc')
 */
function sortChunks($fileParts, $order) {
    foreach ($fileParts as $filePart) {
        $data = file($filePart, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $data = mergeSort($data, $order);
        file_put_contents($filePart, implode(PHP_EOL, $data));
    }
}

/**
 * Implementação do Merge Sort personalizado
 * @param array $array Array a ser ordenado
 * @param string $order Ordem de ordenação ('asc' ou 'desc')
 * @return array Array ordenado
 */
function mergeSort($array, $order) {
    if (count($array) <= 1) {
        return $array;
    }

    $middle = count($array) / 2;
    $left = array_slice($array, 0, $middle);
    $right = array_slice($array, $middle);

    $left = mergeSort($left, $order);
    $right = mergeSort($right, $order);

    return merge($left, $right, $order);
}

/**
 * Função auxiliar para mesclar dois arrays no Merge Sort
 * @param array $left Array da esquerda
 * @param array $right Array da direita
 * @param string $order Ordem de ordenação ('asc' ou 'desc')
 * @return array Array mesclado
 */
function merge($left, $right, $order) {
    $result = [];
    while (count($left) > 0 && count($right) > 0) {
        if (($order === 'asc' && $left[0] <= $right[0]) || ($order === 'desc' && $left[0] >= $right[0])) {
            $result[] = array_shift($left);
        } else {
            $result[] = array_shift($right);
        }
    }

    while (count($left) > 0) {
        $result[] = array_shift($left);
    }

    while (count($right) > 0) {
        $result[] = array_shift($right);
    }

    return $result;
}

/**
 * Função para intercalar os arquivos ordenados
 * @param array $fileParts Lista de arquivos de partes
 * @param string $outputFile Arquivo de saída
 * @param string $order Ordem de ordenação ('asc' ou 'desc')
 */
function mergeFiles($fileParts, $outputFile, $order) {
    $handles = [];
    foreach ($fileParts as $filePart) {
        $handles[] = fopen($filePart, 'r');
    }

    $outHandle = fopen($outputFile, 'w');
    $heap = new SplPriorityQueue();
    $heap->setExtractFlags(SplPriorityQueue::EXTR_BOTH);

    foreach ($handles as $index => $handle) {
        if (!feof($handle)) {
            $value = (int)fgets($handle);
            $priority = $order === 'asc' ? -$value : $value;
            $heap->insert([$index, $value], $priority);
        }
    }

    while (!$heap->isEmpty()) {
        $current = $heap->extract();
        list($fileIndex, $value) = $current['data'];
        fwrite($outHandle, $value . PHP_EOL);
        if (!feof($handles[$fileIndex])) {
            $value = (int)fgets($handles[$fileIndex]);
            $priority = $order === 'asc' ? -$value : $value;
            $heap->insert([$fileIndex, $value], $priority);
        }
    }

    fclose($outHandle);

    foreach ($handles as $handle) {
        fclose($handle);
    }
}

/**
 * Função principal para ordenar um arquivo grande
 * @param string $inputFile Arquivo de entrada
 * @param string $outputFile Arquivo de saída
 * @param int $chunkSize Tamanho de cada parte em linhas
 * @param string $order Ordem de ordenação ('asc' ou 'desc')
 */
function externalSort($inputFile, $outputFile, $chunkSize = 1000, $order = 'asc') {
    $tempDir = sys_get_temp_dir() . '/external_sort';
    if (!is_dir($tempDir)) {
        mkdir($tempDir);
    }

    $fileParts = splitFile($inputFile, $chunkSize, $tempDir);
    sortChunks($fileParts, $order);
    mergeFiles($fileParts, $outputFile, $order);

    foreach ($fileParts as $filePart) {
        unlink($filePart);
    }
    rmdir($tempDir);
}


generateInputFile('grande_arquivo.txt', 1000000, 1000000);
echo "Arquivo de entrada gerado com sucesso: grande_arquivo.txt\n";


$inputFile = 'grande_arquivo.txt';
$outputFile = 'arquivo_ordenado.txt';
$order = 'asc'; // ou 'desc'
externalSort($inputFile, $outputFile, 1000, $order);

echo "Arquivo ordenado com sucesso: $outputFile\n";

?>
