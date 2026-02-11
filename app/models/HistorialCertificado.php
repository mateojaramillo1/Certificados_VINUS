<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class HistorialCertificado
{
    private static function ensureTable(): void
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $conn->exec("
            CREATE TABLE IF NOT EXISTS historial_certificados (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_empleado INT NOT NULL,
                nombre_completo VARCHAR(255) NOT NULL,
                numero_documento VARCHAR(50) NOT NULL,
                incluir_salario TINYINT(1) NOT NULL DEFAULT 0,
                tipo VARCHAR(20) NOT NULL DEFAULT 'pdf',
                generado_por INT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_historial_cert_empleado (id_empleado),
                INDEX idx_historial_cert_fecha (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    public static function crear(array $data): bool
    {
        self::ensureTable();
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("
            INSERT INTO historial_certificados (
                id_empleado,
                nombre_completo,
                numero_documento,
                incluir_salario,
                tipo,
                generado_por
            ) VALUES (
                :id_empleado,
                :nombre_completo,
                :numero_documento,
                :incluir_salario,
                :tipo,
                :generado_por
            )
        ");

        $stmt->bindValue(':id_empleado', $data['id_empleado'], PDO::PARAM_INT);
        $stmt->bindValue(':nombre_completo', $data['nombre_completo'], PDO::PARAM_STR);
        $stmt->bindValue(':numero_documento', $data['numero_documento'], PDO::PARAM_STR);
        $stmt->bindValue(':incluir_salario', (int)$data['incluir_salario'], PDO::PARAM_INT);
        $stmt->bindValue(':tipo', $data['tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':generado_por', $data['generado_por'] ?? null, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function ultimos(int $limit = 15): array
    {
        self::ensureTable();
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM historial_certificados ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function ultimosPorEmpleado(int $idEmpleado, int $limit = 10): array
    {
        self::ensureTable();
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM historial_certificados WHERE id_empleado = :id ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':id', $idEmpleado, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function conteoPorDia(int $days = 7): array
    {
        self::ensureTable();
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("
            SELECT DATE(created_at) as fecha, COUNT(*) as total
            FROM historial_certificados
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at) ASC
        ");
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function conteoPorMes(int $months = 6): array
    {
        self::ensureTable();
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as periodo, COUNT(*) as total
            FROM historial_certificados
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY DATE_FORMAT(created_at, '%Y-%m') ASC
        ");
        $stmt->bindValue(':months', $months, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
