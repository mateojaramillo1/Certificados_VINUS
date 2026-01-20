<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Empresa
{
    public static function getAll()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT id_empresa, nombre_empresa, nit FROM empresas ORDER BY nombre_empresa ASC");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM empresas WHERE id_empresa = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByNit($nit)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM empresas WHERE nit = :nit LIMIT 1");
        $stmt->bindParam(':nit', $nit, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            INSERT INTO empresas (nombre_empresa, nit, direccion, telefono, email, representante_legal)
            VALUES (:nombre, :nit, :direccion, :telefono, :email, :representante)
        ");
        
        $stmt->bindParam(':nombre', $data['nombre_empresa']);
        $stmt->bindParam(':nit', $data['nit']);
        $stmt->bindParam(':direccion', $data['direccion']);
        $stmt->bindParam(':telefono', $data['telefono']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':representante', $data['representante_legal']);
        
        if ($stmt->execute()) {
            return self::findById($conn->lastInsertId());
        }
        
        return false;
    }
}
