<?php
// Array com os diretórios permitidos onde os arquivos estão localizados
$directories = [
    '/arquivos/', //adicione varios diretorios
];

if (isset($_GET['file'])) {
    $file = $_GET['file']; // Pega o caminho completo do arquivo
    $filePath = realpath($file); // Resolve o caminho real

    // Função para verificar se o arquivo está dentro de um dos diretórios permitidos
    function isFileInAllowedDirectories($filePath, $directories) {
        foreach ($directories as $directory) {
            if (strpos($filePath, realpath($directory)) === 0) {
                return true; // Arquivo está dentro de um dos diretórios permitidos
            }
        }
        return false;
    }

    // Verifica se o arquivo existe e está em um dos diretórios permitidos
    if ($filePath && isFileInAllowedDirectories($filePath, $directories) && file_exists($filePath)) {
        // Configura os cabeçalhos para download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        flush(); // Limpa os buffers do sistema
        readfile($filePath); // Envia o arquivo para o cliente
        exit;
    } else {
        echo "<h2>Arquivo não encontrado ou acesso não permitido.</h2>";
    }
}
?>