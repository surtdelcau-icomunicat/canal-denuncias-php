# Canal de Denuncias PHP independiente

Aplicacion PHP y MySQL/MariaDB basada en el proyecto original. **No es un plugin
de WordPress** y no requiere WordPress. Este paquete es una base para instalacion
y revision en preproduccion; consulta los limites conocidos en `docs/REVISION.md`.

## Contenido

- `public/`: formulario, consulta, login, panel y FPDF.
- `database/schema.sql`: CREATE TABLE de `admins` y `tickets`, sin registros.
- `config.example.php`: plantilla sin credenciales reales.
- `tools/create-admin.php`: alta del primer administrador por terminal.
- `docs/INSTALACION.md`: guia paso a paso para un entorno nuevo.
- `docs/REVISION.md`: pruebas pendientes y diferencias respecto al original.

## Instalacion rapida

1. Prepara PHP 8.1+ de 64 bits con mysqli/mysqlnd e iconv, y MySQL 8+ o MariaDB 10.6+.
   Son los requisitos objetivo de este paquete; valida tu version concreta.
2. Crea una base de datos vacia y un usuario con acceso a ella en tu hosting.
3. Ejecuta `database/schema.sql` en esa base. Ambas tablas quedan vacias.
4. Copia `config.example.php` a `config.local.php` y completa tu conexion.
5. Crea tu administrador con `tools/create-admin.php` siguiendo la guia.
6. Configura la raiz publica del servidor en `public/`.
7. Prueba el formulario y `admin_login.php` con datos ficticios.

Para una prueba local, desde la raiz del paquete:

```sh
php -S 127.0.0.1:8080 -t public
```

Abre `http://127.0.0.1:8080/index.html`. El servidor integrado es solo para pruebas.

## Base de datos sin datos precargados

El SQL contiene la estructura, no un volcado de una base existente. No incluye
INSERT, denuncias, cuentas ni contrasenas. El primer administrador se crea
expresamente despues con su contrasena hasheada.

No ejecutes este esquema como migracion sobre una instalacion antigua:
`IF NOT EXISTS` no adapta tablas existentes. Se ha reconstruido a partir de las
consultas PHP, no comprobado contra una exportacion de produccion.

## Correo

Se mantiene `mail()` de PHP y el PDF adjunto a los destinatarios de `admins`.
Por defecto, `mail_enabled` es false para las primeras pruebas. Configura el
transporte del servidor y un `mail_from` autorizado antes de activarlo.
No hay configuracion SMTP propia ni cola de reintentos en esta version.

## GitHub

Sube el contenido de esta carpeta descomprimida. No subas `config.local.php`,
bases de datos, logs o la carpeta del proyecto antiguo. `.gitignore` ayuda con
Git, pero no impide que selecciones un archivo sensible al subirlo por navegador.

La estructura del repositorio debe mostrar este README y las carpetas public,
database, tools y docs en la raiz. No mezcles este paquete con el de WordPress.

## Antes de publicar una web real

Revisa `docs/REVISION.md`, personaliza la informacion legal heredada y completa
pruebas en tu hosting. No se atribuyen a esta aplicacion las 73 pruebas del plugin
WordPress: son proyectos y esquemas diferentes.

FPDF conserva su licencia en `public/fpdf/license.txt`. Este paquete no introduce
una nueva licencia para el codigo original de la aplicacion; el titular debe
decidir y documentar sus condiciones de distribucion antes de ofrecerlo a terceros.
