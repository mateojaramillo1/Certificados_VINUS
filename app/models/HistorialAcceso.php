<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class HistorialAcceso
{
    private static function ensureTable(): void
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $conn->exec("
            CREATE TABLE IF NOT EXISTS historial_accesos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_empleado INT NOT NULL,
                nombre_completo VARCHAR(255) NOT NULL,
                numero_documento VARCHAR(50) NOT NULL,
                ip VARCHAR(45) NOT NULL,
                user_agent VARCHAR(255) NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_historial_accesos_empleado (id_empleado),
                INDEX idx_historial_accesos_fecha (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    public static function crear(array $data): bool
    {
        self::ensureTable();
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("
            INSERT INTO historial_accesos (
                id_empleado,
                nombre_completo,
                numero_documento,
                ip,
                user_agent
            ) VALUES (
                :id_empleado,
                :nombre_completo,
                :numero_documento,
                :ip,
                :user_agent
            )
        ");

        $stmt->bindValue(':id_empleado', $data['id_empleado'], PDO::PARAM_INT);
        $stmt->bindValue(':nombre_completo', $data['nombre_completo'], PDO::PARAM_STR);
        $stmt->bindValue(':numero_documento', $data['numero_documento'], PDO::PARAM_STR);
        $stmt->bindValue(':ip', $data['ip'], PDO::PARAM_STR);
        $stmt->bindValue(':user_agent', $data['user_agent'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function ultimos(int $limit = 15): array
    {
        self::ensureTable();
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM historial_accesos ORDER BY created_at DESC LIMIT :limit");
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
            FROM historial_accesos
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at) ASC
        ");
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
