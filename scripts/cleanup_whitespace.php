<?php
// Limpia espacios en blanco sin alterar la lógica:
// - Elimina espacios/tabs al final de línea.
// - Colapsa 3+ líneas en blanco a máximo 2.
// - Preserva el tipo de salto de línea original.

$root = realpath(__DIR__ . '/..');
$targets = [
    $root . '/app',
    $root . '/public',
    $root . '/scripts',
    $root . '/README.md',
    $root . '/CONFIGURACION_BD_EMPRESAS.md',
    $root . '/VARIABLES_PLANTILLAS.md',
];

$files = [];
foreach ($targets as $path) {
    if (is_dir($path)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $fileInfo) {
            $filePath = $fileInfo->getPathname();
            if (preg_match('/\.(php|md)$/i', $filePath)) {
                $files[] = $filePath;
            }
        }
    } elseif (is_file($path)) {
        $files[] = $path;
    }
}

$processed = 0;
foreach ($files as $filePath) {
    $content = file_get_contents($filePath);
    if ($content === false) {
        continue;
    }

    $eol = (strpos($content, "\r\n") !== false) ? "\r\n" : "\n";
    $endsWithNewline = (substr($content, -strlen($eol)) === $eol);

    $lines = preg_split("/\r\n|\n|\r/", $content);
    foreach ($lines as &$line) {
        $line = rtrim($line, " \t");
    }
    unset($line);

    $content = implode($eol, $lines);
    $content = preg_replace("/(?:\r?\n){3,}/", $eol . $eol, $content);

    if ($endsWithNewline && substr($content, -strlen($eol)) !== $eol) {
        $content .= $eol;
    }

    file_put_contents($filePath, $content);
    $processed++;
}

echo "Archivos procesados: {$processed}\n";
