# Ordenação Externa de Arquivos

Este projeto contém um script PHP que implementa um algoritmo de ordenação externa para ordenar grandes conjuntos de dados contidos em arquivos externos. O script é capaz de lidar com arquivos de entrada de tamanho arbitrário, potencialmente maiores do que a memória disponível no servidor, utilizando um algoritmo de ordenação próprio (Merge Sort).

## Requisitos:

PHP 7.0 ou superior
Permissões de leitura e escrita no sistema de arquivos

## Estrutura do Projeto:
script.php: Script principal que gera o arquivo de entrada, divide o arquivo em partes menores, ordena as partes e interage as partes ordenadas.
README.md: Arquivo de documentação (este arquivo).

## Como Usar
- Clonar o Repositório
- Rodar o codigo com uma extensao do php no vs code
## Executar a Ordenação
- Após gerar o arquivo de entrada, o script automaticamente procederá para dividir o arquivo, ordenar as partes, e intercalar as partes ordenadas em um único arquivo de saída chamado arquivo_ordenado.txt.

##  Verificar o Resultado
- Verifique o arquivo de saída arquivo_ordenado.txt para confirmar que os dados foram ordenados corretamente.

## Personalização
Você pode ajustar os seguintes parâmetros no script.php:

- generateInputFile('grande_arquivo.txt', 1000000, 1000000): Gera um arquivo de entrada com 1.000.000 de números aleatórios entre 1 e 1.000.000. Você pode ajustar esses números conforme necessário.
- externalSort($inputFile, $outputFile, 1000, $order): Função principal para ordenar o arquivo. Os parâmetros são:
- $inputFile: Arquivo de entrada.
- $outputFile: Arquivo de saída.
- $chunkSize: Tamanho de cada parte em linhas.
- $order: Ordem de ordenação ('asc' para ascendente, 'desc' para descendente).
