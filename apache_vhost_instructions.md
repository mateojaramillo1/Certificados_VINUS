# Instrucciones: VirtualHost para XAMPP (Windows)

Estas instrucciones crean un VirtualHost `certificados.local` para servir la aplicación en:
`C:\xampp\htdocs\Certificados\public`

1) Edita el archivo de VirtualHosts de XAMPP:
- Abre con privilegios de administrador:
  `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
- Añade al final (ejemplo):

```
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot "C:/xampp/htdocs/Certificados/public"
    ServerName certificados.local

    <Directory "C:/xampp/htdocs/Certificados/public">
        Require all granted
        AllowOverride All
        Options Indexes FollowSymLinks
    </Directory>

    ErrorLog "logs/certificados.local-error.log"
    CustomLog "logs/certificados.local-access.log" combined
</VirtualHost>
```

2) Asegúrate de que `httpd-vhosts.conf` está incluido en `httpd.conf` (línea `Include conf/extra/httpd-vhosts.conf` no comentada).

3) Añade la entrada al archivo `hosts` de Windows (necesitas abrir el editor como administrador):
- Archivo: `C:\Windows\System32\drivers\etc\hosts`
- Agrega la línea:
```
127.0.0.1 certificados.local
```
- Alternativa (PowerShell como Administrador):
```powershell
Add-Content -Path "C:\Windows\System32\drivers\etc\hosts" -Value "127.0.0.1 certificados.local"
```

4) Reinicia Apache desde el XAMPP Control Panel.

5) Accede en el navegador:
- http://certificados.local/

Notas:
- Si usas HTTPS necesitarás configurar certificados y VirtualHost en el puerto 443.
- No olvides ejecutar el `crear_certificados.sql` antes de probar (phpMyAdmin o `mysql -u root -p < crear_certificados.sql`).
- Si tienes problemas, revisa los logs de Apache en `C:\xampp\apache\logs`.

Si quieres, puedo generar automáticamente el bloque y un archivo `certificados.vhost.conf` listo para copiar.
