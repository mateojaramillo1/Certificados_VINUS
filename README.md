# Certificados — Instrucciones para ejecutar con XAMPP

Sigue estos pasos para poner en marcha la aplicación (suponiendo que el proyecto está en `C:\xampp\htdocs\Certificados`).

1. Iniciar servicios XAMPP
- Abre el XAMPP Control Panel y arranca **Apache** y **MySQL**.

2. Importar la base de datos
- Opción GUI (phpMyAdmin):
  - Abre: http://localhost/phpmyadmin
  - Haz clic en **Importar**, selecciona `crear_certificados.sql` y ejecuta.

- Opción terminal (PowerShell):
```powershell
mysql -u root -p < "C:\xampp\htdocs\Certificados\crear_certificados.sql"
```

3. Instalar dependencias PHP (Composer)
- Si no tienes Composer instalado, descárgalo desde https://getcomposer.org/
- En PowerShell, desde la raíz del proyecto:
```powershell
cd C:\xampp\htdocs\Certificados
composer install
```

4. Configurar variables de entorno
- Copia el ejemplo y edítalo si quieres cambiar credenciales:
```powershell
copy .env.example .env
# luego edita .env con tu editor favorito
```
- Alternativa: configura las variables de entorno `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` en Apache/PHP.

5. Acceder a la aplicación
- URL base: http://localhost/Certificados/public/
- phpMyAdmin: http://localhost/phpmyadmin
- Ejemplo de ruta: http://localhost/Certificados/public/index.php?controller=certificado&action=search&q=Mar%C3%ADa

6. Notas de seguridad y buenas prácticas
- No comites `.env` con contraseñas al repositorio.
- En producción, no uses `root` ni contraseñas por defecto; crea usuarios con permisos mínimos.
- Considera asegurar el acceso a `public/` como root del sitio en Apache (VirtualHost).

Si quieres, puedo:
- Crear un `VirtualHost` de Apache para servir el proyecto en `http://certificados.local`.
- Generar un `.gitignore` que excluya `.env`.
