<?php
// Usamos un try-catch para que si falla la DB, la página NO se ponga en blanco
$fallback = [
    'name' => 'VINUS S.A.S',
    'nit'  => '900.000.000-1',
    'city' => 'Medellín',
    'representative' => 'Representante Legal'
];

try {
    $dbInstance = \App\Core\Database::getInstance();
    $conn = $dbInstance->getConnection();

    $stmt = $conn->query("SELECT * FROM empresas LIMIT 1");
    $companyData = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$companyData) {
        return $fallback;
    }

    return [
        'name' => $companyData['nombre_empresa'] ?? $fallback['name'],
        'nit'  => $companyData['nit'] ?? $fallback['nit'],
        'city' => $companyData['ciudad'] ?? $fallback['city'],
        'representative' => $companyData['representante_legal'] ?? $fallback['representative']
    ];
} catch (\Exception $e) {
    error_log('Company config DB error: ' . $e->getMessage());
    return $fallback;
}
