<?php
// Usamos un try-catch para que si falla la DB, la página NO se ponga en blanco
try {
    $dbInstance = \App\Core\Database::getInstance();
    $conn = $dbInstance->getConnection(); 
    
    $stmt = $conn->query("SELECT * FROM empresa LIMIT 1");
    $companyData = $stmt->fetch(\PDO::FETCH_ASSOC);
} catch (\Exception $e) {
    // Si falla, creamos datos por defecto para que la página cargue
    $companyData = [
        'nombre_empresa' => 'VINUS S.A.S (Error de DB)',
        'nit' => '000.000.000-0'
    ];
}

return [
    'name' => $companyData['nombre_empresa'] ?? 'VINUS S.A.S',
    'nit'  => $companyData['nit'] ?? '900.000.000-1'
];
