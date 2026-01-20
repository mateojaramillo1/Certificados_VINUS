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
        
        // Buscamos por numero_documento
        $stmt = $conn->prepare("SELECT * FROM empleados WHERE numero_documento = :documento LIMIT 1");
        $stmt->bindParam(':documento', $documento, PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return false;
        }
        
        // Validación: comparamos contra numero_documento (texto plano)
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
}
