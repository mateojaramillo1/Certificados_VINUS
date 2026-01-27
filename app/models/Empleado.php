<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Empleado
{
    public static function authenticate($documento, $password)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $documento = trim($documento);

        $stmt = $conn->prepare("SELECT * FROM empleados WHERE numero_documento = :documento LIMIT 1");
        $stmt->bindParam(':documento', $documento, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        if (trim($password) === trim($user['numero_documento'])) {
            return $user;
        }

        return false;
    }

    public static function findById($id)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM empleados WHERE id_empleados = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByDocumento($documento)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM empleados WHERE numero_documento = :documento LIMIT 1");
        $stmt->bindParam(':documento', $documento, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("
            INSERT INTO empleados (
                id_empresa,
                numero_documento,
                nombre_completo,
                cargo,
                tipo_contrato,
                salario_basico,
                fecha_ingreso,
                estado,
                is_admin
            ) VALUES (
                :id_empresa,
                :numero_documento,
                :nombre_completo,
                :cargo,
                :tipo_contrato,
                :salario_basico,
                :fecha_ingreso,
                :estado,
                :is_admin
            )
        ");

        $stmt->bindParam(':id_empresa', $data['id_empresa'], PDO::PARAM_INT);
        $stmt->bindParam(':numero_documento', $data['numero_documento'], PDO::PARAM_STR);
        $stmt->bindParam(':nombre_completo', $data['nombre_completo'], PDO::PARAM_STR);
        $stmt->bindParam(':cargo', $data['cargo'], PDO::PARAM_STR);
        $stmt->bindParam(':tipo_contrato', $data['tipo_contrato'], PDO::PARAM_STR);
        $stmt->bindParam(':salario_basico', $data['salario_basico'], PDO::PARAM_STR);
        $stmt->bindParam(':fecha_ingreso', $data['fecha_ingreso'], PDO::PARAM_STR);
        $stmt->bindParam(':estado', $data['estado'], PDO::PARAM_STR);
        $stmt->bindParam(':is_admin', $data['is_admin'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return self::findById($conn->lastInsertId());
        }

        return false;
    }
}
