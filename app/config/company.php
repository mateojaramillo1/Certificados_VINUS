<?php
// Usamos un try-catch para que si falla la DB, la página NO se ponga en blanco
$fallback = [
    'name' => 'VINUS S.A.S',
    'nit'  => '900.000.000-1',
    'city' => 'Medellín',
    'address' => 'Dirección no registrada',
    'email' => 'info@vinus.com.co',
    'phone' => '0000000',
    'representative' => 'Representante Legal',
    'responsible_name' => 'Representante Legal',
    'responsible_title' => 'Representante Legal'
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
        'address' => $companyData['direccion'] ?? $fallback['address'],
        'email' => $companyData['email'] ?? $fallback['email'],
        'phone' => $companyData['telefono'] ?? $fallback['phone'],
        'representative' => $companyData['representante_legal'] ?? $fallback['representative'],
        'responsible_name' => $companyData['representante_legal'] ?? $fallback['responsible_name'],
        'responsible_title' => $fallback['responsible_title']
    ];
} catch (\Exception $e) {
    error_log('Company config DB error: ' . $e->getMessage());
    return $fallback;
}
