Para completar esta funcionalidad de administración en VINUS S.A.S, necesitas el controlador que procese la subida del archivo físico (.docx) y guarde el registro en la base de datos vinculándolo con el modelo PlantillaWord que vimos al inicio.

Aquí tienes el código del controlador para gestionar la creación de plantillas:

1. El Controlador de Plantillas (PlantillaController.php)
Este controlador maneja la validación del archivo, lo mueve a la carpeta permanente y actualiza la base de datos.

PHP

<?php

namespace App\Controllers;

use App\Models\PlantillaWord;

class PlantillaController
{
    public function crear()
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $activa = isset($_POST['activa']) ? 1 : 0;
            $archivo = $_FILES['archivo'] ?? null;

            // 1. Validaciones básicas
            if (empty($nombre)) $errors[] = "El nombre es obligatorio.";
            
            if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Debe subir un archivo Word válido.";
            } else {
                // 2. Validar extensión
                $ext = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                if (!in_array(strtolower($ext), ['doc', 'docx'])) {
                    $errors[] = "Solo se permiten archivos .doc o .docx";
                }
            }

            if (empty($errors)) {
                // 3. Preparar nombre de archivo único
                $nombreArchivo = time() . '_' . basename($archivo['name']);
                $rutaDestino = __DIR__ . '/../../public/plantillas/' . $nombreArchivo;

                // Crear carpeta si no existe
                if (!is_dir(dirname($rutaDestino))) {
                    mkdir(dirname($rutaDestino), 0777, true);
                }

                // 4. Mover archivo y guardar en BD
                if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                    PlantillaWord::create([
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'archivo' => $nombreArchivo,
                        'activa' => $activa
                    ]);

                    header('Location: index.php?c=plantilla&a=index&success=1');
                    exit;
                } else {
                    $errors[] = "Error al mover el archivo al servidor.";
                }
            }
        }

        // Carga la vista que me proporcionaste
        require __DIR__ . '/../Views/plantillas/crear.php';
    }
}
