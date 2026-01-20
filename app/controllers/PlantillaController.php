<?php

namespace App\Controllers;

use App\Models\PlantillaWord;

class PlantillaController
{
    private function checkAdmin()
    {
        // Centralizamos la validación de administrador
        if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
            $_SESSION['error'] = 'No tiene permisos para acceder a esta sección';
            header('Location: index.php?controller=auth&action=dashboard');
            exit;
        }
    }

    public function index()
    {
        $this->checkAdmin();
        $plantillas = PlantillaWord::findAll();
        require __DIR__ . '/../views/plantillas/index.php';
    }

    public function crear()
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $activa = isset($_POST['activa']) ? 1 : 0;

            if (empty($nombre)) {
                $errors[] = 'El nombre de la plantilla es requerido';
            }

            // Validación de archivo
            if (empty($_FILES['archivo']['name'])) {
                $errors[] = 'Debe seleccionar un archivo Word';
            } else {
                $archivo = $_FILES['archivo'];
                $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                
                if (!in_array($extension, ['doc', 'docx'])) {
                    $errors[] = 'Solo se permiten archivos Word (.doc o .docx)';
                }
            }

            if (empty($errors)) {
                $nombreBase = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $nombre);
                $nombreArchivo = $nombreBase . '.' . $extension;
                $rutaDestino = __DIR__ . '/../../public/plantillas/' . $nombreArchivo;

                // Asegurar que la carpeta existe
                if (!is_dir(dirname($rutaDestino))) {
                    mkdir(dirname($rutaDestino), 0777, true);
                }

                if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                    // Conversión automática de .doc a .docx
                    if ($extension === 'doc') {
                        $rutaConvertida = $this->convertirDocADocx($rutaDestino, $nombreBase);
                        if ($rutaConvertida) {
                            unlink($rutaDestino); // Borrar .doc viejo
                            $nombreArchivo = basename($rutaConvertida);
                        }
                    }
                    
                    // Guardar en la DB
                    PlantillaWord::create([
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'archivo' => $nombreArchivo,
                        'activa' => $activa
                    ]);

                    $_SESSION['mensaje'] = 'Plantilla guardada correctamente';
                    header('Location: index.php?controller=plantilla&action=index');
                    exit;
                } else {
                    $errors[] = 'Error al mover el archivo al servidor';
                }
            }
            require __DIR__ . '/../views/plantillas/crear.php';
            return;
        }
        require __DIR__ . '/../views/plantillas/crear.php';
    }

    public function activar()
    {
        $this->checkAdmin();
        $id = $_GET['id'] ?? null;
        
        if ($id && PlantillaWord::activar($id)) {
            $_SESSION['mensaje'] = 'Plantilla principal actualizada';
        } else {
            $_SESSION['error'] = 'No se pudo activar la plantilla';
        }
        
        header('Location: index.php?controller=plantilla&action=index');
    }

    private function convertirDocADocx($rutaDoc, $nombreBase)
    {
        $dirPlantillas = realpath(__DIR__ . '/../../public/plantillas/');
        $rutaDocx = $dirPlantillas . DIRECTORY_SEPARATOR . $nombreBase . '.docx';
        
        // Comando para sistemas Linux/Windows con LibreOffice
        // --headless ejecuta sin interfaz gráfica
        $comando = "libreoffice --headless --convert-to docx --outdir " . escapeshellarg($dirPlantillas) . " " . escapeshellarg($rutaDoc);
        
        exec($comando);

        return file_exists($rutaDocx) ? $rutaDocx : false;
    }
}
