-- Fresh standalone PHP installation, not the WordPress plugin.
-- Select an existing empty database before executing this script.
-- Reconstructed from application queries; not an export of production data.
-- No users, passwords or reports are seeded. Existing tables are not modified.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS admins (
    id int unsigned NOT NULL AUTO_INCREMENT,
    usuario varchar(100) NOT NULL,
    `contraseña` varchar(255) NOT NULL,
    email varchar(254) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY admins_usuario (usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tickets (
    id bigint unsigned NOT NULL,
    lugar_incidente text NOT NULL,
    relacion text NOT NULL,
    tipo_denuncia varchar(30) NOT NULL,
    nombre varchar(255) NOT NULL DEFAULT '',
    apellidos varchar(255) NOT NULL DEFAULT '',
    telefono varchar(100) NOT NULL DEFAULT '',
    email varchar(254) NOT NULL DEFAULT '',
    puesto_trabajo varchar(255) NOT NULL DEFAULT '',
    razon_anonimato text NOT NULL,
    tipo_delito varchar(100) NOT NULL,
    otros_delito text NOT NULL,
    descripcion longtext NOT NULL,
    fecha_hechos text NOT NULL,
    afectados text NOT NULL,
    identidad_conocida varchar(30) NOT NULL DEFAULT '',
    nombre_cargo text NOT NULL,
    documento_acreditativo varchar(30) NOT NULL DEFAULT '',
    descripcion_documento text NOT NULL,
    centro_trabajo varchar(255) NOT NULL DEFAULT '',
    direccion varchar(255) NOT NULL DEFAULT '',
    localidad varchar(255) NOT NULL DEFAULT '',
    provincia varchar(255) NOT NULL DEFAULT '',
    cp varchar(30) NOT NULL DEFAULT '',
    fecha_creacion datetime NOT NULL,
    estado enum('pendiente','en_proceso','resuelto') NOT NULL DEFAULT 'pendiente',
    PRIMARY KEY (id),
    KEY tickets_estado (estado),
    KEY tickets_fecha_creacion (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
