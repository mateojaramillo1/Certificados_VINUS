<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class PlantillaWord
{
    public static function findAll()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM plantillas ORDER BY created_at DESC");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM plantillas WHERE id_plantilla = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getActiva()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM plantillas WHERE activa = 1 LIMIT 1");
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            INSERT INTO plantillas (nombre, descripcion, tipo_documento, ruta_archivo)
            VALUES (:nombre, :descripcion, :tipo_documento, :ruta_archivo)
        ");
        
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $tipoDoc = $data['tipo_documento'] ?? 'Certificado Laboral';
        $stmt->bindParam(':tipo_documento', $tipoDoc);
        $stmt->bindParam(':ruta_archivo', $data['archivo']);
        
        if ($stmt->execute()) {
            return self::findById($conn->lastInsertId());
        }
        
        return false;
    }

    public static function activar($id)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        // Primero desactivar todas
        $conn->exec("UPDATE plantillas SET activa = 0");
        
        // Activar la seleccionada
        $stmt = $conn->prepare("UPDATE plantillas SET activa = 1 WHERE id_plantilla = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public static function delete($id)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        // Obtener la plantilla para eliminar el archivo físico
        $plantilla = self::findById($id);
        
        if ($plantilla) {
            $rutaArchivo = __DIR__ . '/../../public/plantillas/' . $plantilla['ruta_archivo'];
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
            }
            
            $stmt = $conn->prepare("DELETE FROM plantillas WHERE id_plantilla = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        }
        
        return false;
    }

    public static function getRutaCompleta($plantilla)
    {
        if (is_array($plantilla)) {
            return __DIR__ . '/../../public/plantillas/' . $plantilla['ruta_archivo'];
        }
        return null;
    }
}
