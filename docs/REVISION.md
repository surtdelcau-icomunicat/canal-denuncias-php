# Alcance del paquete PHP y revision pendiente

## Ajustes incluidos para distribuir e instalar

- Codigo original separado bajo public, sin modificar los archivos originales
  del proyecto ni el paquete WordPress.
- Conexion compartida y credenciales reales sustituidas por una plantilla.
- CREATE TABLE de admins y tickets reconstruido de las consultas del codigo,
  sin registros ni contrasenas iniciales.
- Herramienta CLI para crear administradores con password_hash.
- Lectura del campo fecha_creacion unificada en el panel y el detalle.
- Enlaces de navegacion a la portada convertidos en rutas relativas.
- Regeneracion del ID de sesion al iniciar sesion y retirada de la cookie
  admin_logged_in, que no se utilizaba para autorizar el acceso.
- Correo desactivado inicialmente y remitente configurable.
- PDF generado en memoria; se elimina la ruta temporal fija y utf8_decode.
- Excluidos los diagnosticos testmail.php y check_admin_emails.php y los modulos
  de administracion/consulta duplicados no enlazados por el flujo principal.
- FPDF incluido con sus fuentes y licencia; PHPMailer omitido porque no se utiliza.

## Problemas heredados que bloquean una publicacion sin revision

Este empaquetado no convierte la aplicacion original en un sistema auditado.
Antes de produccion, el programador debe resolver y probar al menos:

1. El ID se sigue generando con time(): es predecible y dos altas en un mismo
   segundo pueden colisionar. Sustituirlo por un ID interno y un codigo privado
   aleatorio de seguimiento, sin perder las referencias que se decida migrar.
2. Hay borrado por GET en el panel y no hay tokens CSRF en sus acciones ni en el
   cambio de contrasena. Usar POST, autorizacion y validacion CSRF coherentes.
3. La validacion de entrada es insuficiente; ocultar campos en JavaScript no
   garantiza anonimato. Descartar identificadores en el servidor cuando la
   denuncia sea anonima y validar longitudes, opciones y tipos.
4. No hay limitacion de intentos de acceso, consulta ni envio, ni un flujo de
   cierre de sesion incorporado. Revisar cookies, duracion de sesion y logout.
5. Los datos no tienen cifrado adicional en reposo; el panel carga todos los
   tickets sin paginacion. Revisar permisos, acceso a backups y volumen esperado.
6. El envio sigue siendo sincrono con mail(). No hay seguimiento de entrega ni
   reintentos; un fallo puede interrumpir los destinatarios restantes.
7. Los textos de privacidad y los recursos CDN son los heredados. Revisar su
   adecuacion al despliegue, dependencias, accesibilidad y contenido legal.

Los textos opcionales del detalle (centro_trabajo, direccion, localidad,
provincia y cp) estan definidos vacios en el esquema por compatibilidad con su
vista; el formulario actual no los solicita.

## Comprobaciones realizadas al preparar el paquete

Se ha comprobado la sintaxis de sus PHP, que las columnas insertadas existen
en el CREATE TABLE, la presencia de los archivos necesarios y que el ZIP no
contiene las credenciales originales, configuracion local, WordPress o dumps.

No se ha ejecutado este esquema en un servidor MySQL/MariaDB ni probado el envio
real de correo en esta preparacion. Los checks de WordPress documentados en otro
paquete no prueban esta aplicacion. Completar en el hosting de pruebas:

- Importacion del esquema en base nueva: ambos COUNT(*) a cero.
- Alta de administrador y login; rechazo de credenciales incorrectas.
- Registro, confirmacion, detalle y consulta del mismo expediente.
- Cambio de estado y cambio de contrasena de una cuenta de prueba.
- Correo activado/desactivado y manejo de fallo de transporte.
- Caracteres espanoles y PDF; concurrencia de altas y errores de base de datos.
- Pruebas de seguridad despues de corregir los puntos anteriores.

El esquema sirve para una instalacion nueva. La migracion de datos antiguos
requiere contrastar el esquema real antes de modificarlo.
