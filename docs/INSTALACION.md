# Instalar la aplicacion PHP desde cero

Este documento corresponde a la aplicacion independiente, no a WordPress.
Empieza en preproduccion con datos ficticios y consulta REVISION.md antes de
abrir el servicio al publico.

## 1 Preparar archivos y servidor

Descomprime el paquete. La raiz contiene README.md, config.example.php y las
carpetas public, database, tools y docs. Para GitHub sube este contenido, no un
volcado SQL ni los ficheros de tu antigua instalacion.

Necesitas PHP de 64 bits con mysqli/mysqlnd e iconv, MySQL o MariaDB y un servidor
web. Configura el DocumentRoot de Apache, o el root de Nginx con PHP-FPM, para
apuntar exclusivamente a `public/`. En Nginx debes configurar la ejecucion PHP;
no interpreta .htaccess. Usa `index.html` como indice en ambos casos.

No publiques la raiz completa del repositorio: config.local.php, database,
tools y docs deben quedar fuera de la ruta web. Si tu hosting no permite elegir
la raiz, coloca el contenido de public en su directorio publico y el archivo
config.local.php en el directorio inmediatamente superior, conservando esa
relacion. Las herramientas CLI esperan la estructura original del repositorio.

## 2 Crear la base vacia

En el panel del hosting o phpMyAdmin crea una base llamada, por ejemplo,
`canal_denuncias` y un usuario con acceso a ella. El usuario que crea las tablas
necesita CREATE. El usuario de ejecucion requiere SELECT, INSERT, UPDATE y DELETE
sobre las tablas de esta aplicacion; no uses una cuenta administrativa general.

Selecciona esa base en phpMyAdmin y ejecuta/importa `database/schema.sql`.
Alternativamente, en una terminal compatible con redireccion de entrada:

```sh
mysql -h HOST -u USUARIO -p canal_denuncias < database/schema.sql
```

La contrasena de MySQL se solicita de forma interactiva. No la escribas como
argumento. En PowerShell puedes usar phpMyAdmin o abrir el cliente mysql con
`mysql -h HOST -u USUARIO -p canal_denuncias` y ejecutar dentro de el:

```sql
SOURCE /ruta/absoluta/database/schema.sql;
SELECT COUNT(*) FROM admins;
SELECT COUNT(*) FROM tickets;
```

Ambos recuentos deben ser 0 en una base nueva. No hay usuarios de demostracion.
IF NOT EXISTS conserva cualquier tabla previa, pero no la actualiza: este
esquema no es una migracion para bases antiguas.

## 3 Completar la conexion local

Copia config.example.php a config.local.php, en la raiz, fuera de public.
Completa db_host, db_port, db_name, db_user y db_password. Ajusta timezone si
procede. Deja mail_enabled en false durante las primeras pruebas.

La configuracion se comparte entre registro, consulta, login y administracion.
No hace falta modificar cada PHP. Nunca subas config.local.php a GitHub: el
.gitignore lo excluye con Git, pero la subida manual por navegador no lo filtra.

## 4 Crear el primer administrador

Desde la raiz del repositorio ejecuta la herramienta CLI. Elige tu propio
usuario y un correo controlado; los ejemplos siguientes usan un dominio ficticio
que debes sustituir. La contrasena debe tener entre 12 y 72 bytes y llega por
entrada estandar, sin almacenarse en el historial como argumento del comando.

En PowerShell 7:

```powershell
Read-Host 'Contrasena del nuevo administrador' -AsSecureString |
    ConvertFrom-SecureString -AsPlainText |
    php tools/create-admin.php miusuario responsable@example.com
```

En Bash:

```bash
read -rs -p 'Contrasena del nuevo administrador: ' admin_password
printf '\n'
printf '%s' "$admin_password" | php tools/create-admin.php miusuario responsable@example.com
unset admin_password
```

La herramienta inserta un hash BCRYPT compatible con password_verify, no la
contrasena en claro. Rechaza nombres duplicados y no modifica cuentas existentes.
No hay pagina publica para crear administradores. Si el hosting no tiene terminal,
solicita a su responsable que ejecute esta herramienta; no la conviertas en un
instalador publico.

Despues de esta operacion admins tendra una fila y tickets seguira vacia.
Repite el comando para crear otros responsables autorizados si los necesitas.

## 5 Probar las paginas

En local puedes ejecutar `php -S 127.0.0.1:8080 -t public` desde la raiz.
Abre index.html para registrar una denuncia ficticia y conserva su ID. Entra en
admin_login.php con tu nueva cuenta; verifica listado y detalle. Cambia el estado
y consultalo mediante consult_ticket.php.

El primer administrador no se crea desde update_admin_password.php: esa pagina
solo actualiza una cuenta existente y requiere una sesion administrativa.

El esquema usa fecha_creacion como fecha de registro. El panel de esta copia
se ha ajustado a ese nombre; no depende de una columna creado_en adicional.

## 6 Probar correo

Configura el transporte que utiliza mail() en tu servidor. En config.local.php
establece mail_from con un remitente autorizado y cambia mail_enabled a true.
La aplicacion no utiliza SMTP autenticado directamente: ese cambio requeriria
otra adaptacion o la configuracion del transporte del servidor.

Registra una comunicacion ficticia y comprueba el correo con PDF en los buzones
de admins. Si el transporte falla, la denuncia ya guardada se conserva y se
registra un mensaje generico en el log de PHP. No hay estado de correo en el panel
ni reintento automatico; verifica la recepcion efectiva.

No se incluye testmail.php para evitar destinatarios antiguos ni un endpoint
publico que cualquiera pueda activar. El PDF se genera en memoria, sin /tmp fijo.

## 7 Preparar la revision antes de produccion

Revisa las tareas de REVISION.md con tu programador, especialmente controles de
acceso, CSRF, anonimato y codigos de seguimiento. Personaliza los textos de
index.html, clausula.html y protecciondedatos.html, que conservan referencias
publicas a la entidad original. Configura HTTPS y display_errors=Off; comprueba
que los errores no revelen datos ni se registren cuerpos de denuncias.

No uses el manual de WordPress para esta aplicacion: no hay shortcodes, roles
WordPress, wp_mail ni creacion automatica de tablas al activar un plugin.
