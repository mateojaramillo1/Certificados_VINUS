Para completar la funcionalidad de edición en VINUS S.A.S, necesitas el método correspondiente en tu controlador. Este código se encarga de cargar los datos actuales de la base de datos y procesar la actualización.

1. El Controlador de Edición (PlantillaController.php)
Este método debe integrarse en la clase PlantillaController. Su función es buscar la plantilla por su ID, mostrar la vista y guardar los cambios.

PHP

<?php

namespace App\Controllers;

use App\Models\PlantillaWord;

class PlantillaController
{
    /**
     * Acción para editar una plantilla existente
     */
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?c=plantilla&a=index');
            exit;
        }

        // 1. Buscar la plantilla en la base de datos
        $plantilla = PlantillaWord::findById($id);

        if (!$plantilla) {
            die("La plantilla no existe en la base de datos.");
        }

        $errors = [];

        // 2. Procesar el formulario cuando se envía (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $activa = isset($_POST['activa']) ? 1 : 0;

            if (empty($nombre)) {
                $errors[] = "El nombre de la plantilla es obligatorio.";
            }

            if (empty($errors)) {
                try {
                    // 3. Actualizar el modelo
                    $plantilla->nombre = $nombre;
                    $plantilla->descripcion = $descripcion;
                    $plantilla->activa = $activa;

                    // 4. Guardar cambios (Si es activa, el modelo debe desactivar las otras)
                    $plantilla->update();

                    header('Location: index.php?c=plantilla&a=index&msg=updated');
                    exit;
                } catch (\Exception $e) {
                    $errors[] = "Error al actualizar: " . $e->getMessage();
                }
            }
        }

        // 5. Cargar la vista de edición (el HTML que proporcionaste)
        require __DIR__ . '/../Views/plantillas/editar.php';
    }
}