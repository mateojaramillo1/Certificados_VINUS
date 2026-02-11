<?php
$fh = fopen(__DIR__ . '/../data/empleados.csv', 'r');
$headers = null;
while (($row = fgetcsv($fh, 0, ';')) !== false) {
    if (empty($row)) {
        continue;
    }
    $normalizedRow = array_map(function ($h) {
        $h = trim($h);
        $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);
        $h = mb_strtolower($h, 'UTF-8');
        $h = str_replace(['á','é','í','ó','ú','ñ'], ['a','e','i','o','u','n'], $h);
        $h = preg_replace('/\s+/', '_', $h);
        return $h;
    }, $row);

    if (in_array('ingreso', $normalizedRow, true) || in_array('fecha_ingreso', $normalizedRow, true)) {
        $headers = $row;
        break;
    }
}

var_export($headers);

echo "\n\nNormalized:\n";
$headerMap = [];
foreach ($headers as $idx => $h) {
    $key = trim($h);
    $key = preg_replace('/^\xEF\xBB\xBF/', '', $key);
    $key = mb_strtolower($key, 'UTF-8');
    $key = str_replace(['á','é','í','ó','ú','ñ'], ['a','e','i','o','u','n'], $key);
    $key = preg_replace('/\s+/', '_', $key);
    $headerMap[$key] = $idx;
}
var_export($headerMap);
