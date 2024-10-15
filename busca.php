<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador de Arquivos SMB</title>
    <!-- Link para os ícones do Font Awesome compatíveis com IE -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        /* Estilo para o formulário de busca */
        .search-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #f2f2f2;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            text-align: center;
            box-sizing: border-box;
        }

        .search-bar form {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Campo de input maior, no estilo do Google */
        .search-bar input[type="text"] {
            width: 50%;
            padding: 12px;
            font-size: 18px;
            border: 1px solid #dfe1e5;
            border-radius: 24px 0 0 24px;
            outline: none;
            box-sizing: border-box;
        }

        .search-bar input[type="submit"] {
            padding: 12px 20px;
            font-size: 18px;
            background-color: #4285f4;
            color: white;
            border: none;
            border-radius: 0 24px 24px 0;
            cursor: pointer;
            box-sizing: border-box;
        }

        .search-bar input[type="submit"]:hover {
            background-color: #357ae8;
        }

        /* Estilo do conteúdo para não ser sobreposto */
        .content {
            margin-top: 100px;
            text-align: left;
            padding: 20px;
        }

        /* Estilo para os resultados de arquivos */
        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin: 10px 0;
            overflow: hidden; /* Para evitar que o float cause problemas de layout */
        }

        /* Ícone ao lado esquerdo do arquivo */
        .file-icon {
            float: left;
            margin-right: 10px;
            color: #1a73e8;
        }

        /* Estilo para o link */
        a {
            text-decoration: none;
            color: #1a73e8;
            font-size: 18px;
            line-height: 24px;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .search-bar input[type="text"] {
                width: 70%;
                font-size: 16px;
            }

            .search-bar input[type="submit"] {
                padding: 10px 15px;
                font-size: 16px;
            }
        }

        /* Fallback para Internet Explorer */
        @media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {
            .search-bar form {
                display: block;
            }

            .search-bar input[type="text"], .search-bar input[type="submit"] {
                width: 100%;
                margin-bottom: 10px;
                font-size: 16px;
            }

            .search-bar {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<!-- Formulário de busca fixo ao topo -->
<div class="search-bar">
    <form method="GET">
        <input type="text" name="query" placeholder="Digite o nome do arquivo" required>
        <input type="submit" value="Buscar">
    </form>
</div>

<!-- Conteúdo da página -->
<div class="content">
    <?php
// Array com os diretórios onde deseja realizar a busca
$directories = [
    '/arquivos/', //adicione varios diretorios
];

// Função recursiva para buscar arquivos em múltiplos diretórios e suas subpastas
function searchFiles($directories, $query) {
    $matchingFiles = [];

    foreach ($directories as $directory) {
        // Verifica se o diretório existe
        if (is_dir($directory)) {
            // Utiliza iteradores recursivos para buscar em subpastas
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                // Verifica se o item é um arquivo e se o nome corresponde à busca
                if ($file->isFile() && stripos($file->getFilename(), $query) !== false) {
                    $matchingFiles[] = $file->getPathname(); // Armazena o caminho completo do arquivo
                }
            }
        }
    }

    return $matchingFiles;
}

// Verifica se foi enviada uma consulta de busca
if (isset($_GET['query'])) {
    $query = $_GET['query'];

    // Chama a função para buscar os arquivos em todos os diretórios
    $matchingFiles = searchFiles($directories, $query);

    if (count($matchingFiles) > 0) {
        echo "<h2>Resultados encontrados para '$query':</h2>";
        echo "<ul>";
        foreach ($matchingFiles as $filePath) {
            $fileName = basename($filePath); // Extrai o nome do arquivo
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION); // Pega a extensão do arquivo
            $downloadLink = "download.php?file=" . urlencode($filePath);

            // Determina o ícone com base no tipo de arquivo
            $iconClass = 'fa-file-alt'; // Ícone padrão
            if (in_array($fileExtension, ['jpg', 'png', 'gif', 'bmp'])) {
                $iconClass = 'fa-file-image';
            } elseif (in_array($fileExtension, ['pdf'])) {
                $iconClass = 'fa-file-pdf';
            } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                $iconClass = 'fa-file-word';
            } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                $iconClass = 'fa-file-excel';
            } elseif (in_array($fileExtension, ['ppt', 'pptx'])) {
                $iconClass = 'fa-file-powerpoint';
            } elseif (in_array($fileExtension, ['zip', 'rar'])) {
                $iconClass = 'fa-file-archive';
            } elseif (in_array($fileExtension, ['mp3', 'wav'])) {
                $iconClass = 'fa-file-audio';
            } elseif (in_array($fileExtension, ['mp4', 'avi', 'mkv'])) {
                $iconClass = 'fa-file-video';
            }

            // Exibe o arquivo com ícone
            echo "<li><i class='fas $iconClass file-icon'></i><a href='$downloadLink'>$fileName</a></li>";
        }
        echo "</ul>";
    } else {
        echo "<h2>Nenhum arquivo encontrado.</h2>";
    }
}
?>

</div>

</body>
</html>