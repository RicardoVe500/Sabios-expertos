CREATE DATABASE IF NOT EXISTS tesis;
use tesis;

CREATE TABLE empresa(
empresaId       int(10) auto_increment not null,
codigoEmpresa   varchar(100) not null,
nombre          varchar(255) not null,
direccion       varchar(255),
correo          varchar(255),
telefono        char(50),
usuarioAgrega   varchar(255),
fechaAgrega     date,
usuarioModifica varchar(255),
fechaModifica   datetime,

CONSTRAINT pk_empresa PRIMARY KEY(empresaId)
);

CREATE TABLE estado(
estadoId       int(10) auto_increment not null,
estado   varchar(100) not null,
CONSTRAINT pk_estado PRIMARY KEY(estadoId)

);


CREATE TABLE tipoUsuario(
tipoUsuarioId      int(10) auto_increment not null,
codigotipo         varchar(100) not null,
nombreTipo         varchar(255),
descripcion        varchar(255),
usuarioAgrega      varchar(150),
fechaAgrega        date,
usuarioModifica   varchar(255),
fechaModifica      datetime,

CONSTRAINT pk_tipoUsuario PRIMARY KEY(tipoUsuarioId)
);

CREATE TABLE tipoPartida(
tipoPartidaId      int(10) auto_increment not null,
nombrePartida      varchar(100),
abreviacion        varchar(10),
descripcion        varchar(255),

CONSTRAINT pk_tipoPartida PRIMARY KEY(tipoPartidaId)
);

CREATE TABLE tipoComprobante(
tipoComprobanteId    int(10) auto_increment not null,
nombreComprobante    varchar(255),

CONSTRAINT pk_tipoComprobante PRIMARY KEY(tipoComprobanteId)
);

CREATE TABLE bitacora(
bitacoraId         int(10) auto_increment not null,
fecha              date,
detalle            JSON,

CONSTRAINT pk_bitacora PRIMARY KEY(bitacoraId)
);

CREATE TABLE mayorizacion(
mayorizacionId      int(10) auto_increment not null,
fecha               date,
hora                time,
detalles            varchar(255),
estado              varchar(255),
usaurioCrea         varchar(255),
cambio              varchar(255),
fechaCrea           date,
usuarioEdita        varchar(255),
fechaEdita          datetime,

CONSTRAINT pk_mayorizacion PRIMARY KEY(mayorizacionId)
);

CREATE TABLE movimientos(
movimientoId        int(10) auto_increment not null,
movimiento          varchar(255),

CONSTRAINT pk_movimientos PRIMARY KEY(movimientoId)
);

CREATE TABLE tipoDocumento(
tipoDocumentoId      int(10) auto_increment not null,
tipoDocumento        varchar(255),

CONSTRAINT pk_tipoDocumento PRIMARY KEY(tipoDocumentoId)
);

CREATE TABLE tipoContacto(
tipoContactoId       int(10) auto_increment not null,
tipoContacto         varchar(255),

CONSTRAINT pk_tipoContacto PRIMARY KEY(tipoContactoId)
);

CREATE TABLE partidas(
partidaId       int(10) auto_increment not null,
tipoPartidaId   varchar(100) not null,
estadoId        varchar(100) not null,
codigoPartida   varchar(100) not null,
concepto        varchar(100) not null,
fechacontable      datetime,
fechaActual        datetime,
usuarioAgrega      varchar(150),
fechaAgrega        datetime,
usuarioModifica    varchar(255),
fechaModifica      datetime,

CONSTRAINT pk_partidas PRIMARY KEY(partidaId)
CONSTRAINT fk_estado FOREIGN KEY(estadoId) REFERENCES estado(estadoId)
CONSTRAINT fk_tipoPartida FOREIGN KEY(tipoPartidaId) REFERENCES tipoPartida(tipoPartidaId)

);

CREATE TABLE usuarios(
usuarioId           int(10) auto_increment not null,
tipoUsuarioId       int(10) not null,
nombre              varchar(255),
apellidos           varchar(255),
email               varchar(255),
clave               varchar(255),
usuarioAgrega       varchar(255),
fechaAgrega         date,
usuariosModifica    varchar(255),
fechaModifica       varchar(255),

CONSTRAINT pk_usuarios PRIMARY KEY(usuarioId),
CONSTRAINT fk_usuario_tipo FOREIGN KEY(tipoUsuarioId) REFERENCES tipoUsuario(tipoUsuarioId)
);

CREATE TABLE encargadoSucursal(
encargadoId         int(10) auto_increment not null,
usuarioId           int(10) not null,

CONSTRAINT pk_encargadoSucursal PRIMARY KEY(encargadoId),
CONSTRAINT fk_encargado_usuarios FOREIGN KEY(usuarioId) REFERENCES usuarios(usuarioId)
);

CREATE TABLE sucursal(
sucursalId          int(10) auto_increment not null,
empresaId           int(10) not null,
encargadoId         int(10) not null,
codigoSucursal      int(100) not null,
nombre              varchar(255) not null,
direccion           varchar(255),
correo              varchar(255), /* PUEDE SER UNICO */
telefono            char(50),
usuarioAgrega       varchar(255),
fechaAgrega         date,
usuarioModifica     varchar(255),
fechaModifica       datetime,

CONSTRAINT pk_sucursal PRIMARY KEY(sucursalId),
CONSTRAINT fk_sucursal_empresa FOREIGN KEY(empresaId) REFERENCES empresa(empresaId),
CONSTRAINT fk_sucursal_encargado FOREIGN KEY(encargadoId) REFERENCES encargadoSucursal(encargadoId)
);

CREATE TABLE periodo(
periodoId           int(10) auto_increment not null,
sucursalId          int(10) not null,
anio                date,
mes                 date,
estado              varchar(50),
usuarioAgrega       varchar(255),
fechaAgrega         date,
usuarioModifica     varchar(255),
fechaModifica       datetime,

CONSTRAINT pk_periodo PRIMARY KEY(periodoId),
CONSTRAINT fk_periodo_sucursal FOREIGN KEY(sucursalId) REFERENCES sucursal(sucursalId)
);

CREATE TABLE cierre(
cierreId            int(10) auto_increment not null,
periodoId           int(10) not null,
fechaCierre         datetime,
usuarioAgrega       varchar(255),
fechaAgrega         datetime,
usuarioModifica     varchar(255),
fechaModifica       datetime,

CONSTRAINT pk_cierre PRIMARY KEY(cierreId),
CONSTRAINT fk_cierre_periodo FOREIGN KEY(periodoId) REFERENCES periodo(periodoId)
);

CREATE TABLE catalogoCuentas(
cuentaId            int(10) auto_increment not null,
movimientoId        int(10) not null,
n1                  varchar(255),
n2                  varchar(255),
n3                  varchar(255),
n4                  varchar(255),
n5                  varchar(255),
n6                  varchar(255),
n7                  varchar(255),
n8                  varchar(255),
numeroCuenta        varchar(255),
cuentaDependiente   varchar(255),
nivelCuenta         varchar(255),
nombreCuenta        varchar(255),       
usuarioAgrega       varchar(255),
fechaAgrega         datetime,
usuarioModifica     varchar(255),
fechaModifica       datetime,

CONSTRAINT pk_CatalogoCuentas PRIMARY KEY(cuentaId),
CONSTRAINT fk_Catalogo_movimiento FOREIGN KEY(movimientoId) REFERENCES movimientos(movimientoId)
);

CREATE TABLE saldo(
saldoId             int(10) auto_increment not null,
cuentaId            int(10) not null,
debe                decimal(10,2),
haber               decimal(10,2),
fecha               date,
saldo               decimal(10,2),
saldoDia            decimal(10,2),
SaldoAnterior       decimal(10,2),

CONSTRAINT pk_saldo PRIMARY KEY(saldoId),
CONSTRAINT fk_saldo_cuenta FOREIGN KEY(cuentaId) REFERENCES CatalogoCuentas(cuentaId)
);

CREATE TABLE catalogoSucursal(
catalogoSucursalId      int(10) auto_increment not null,
sucursalId              int(10) not null,
cuentaId                int(10) not null,
saldoActual             char(50),

CONSTRAINT pk_catalogoSucursal PRIMARY KEY(catalogoSucursalId),
CONSTRAINT fk_catalogo_sucursal FOREIGN KEY(sucursalId) REFERENCES sucursal(sucursalId),
CONSTRAINT fk_catalogo_cuenta FOREIGN KEY(cuentaId) REFERENCES catalogoCuentas(cuentaId)
);

CREATE TABLE encabezado(
encabezadoId            int(10) auto_increment not null,
tipoPartidaId           int(10) not null,
codigoPartida           char(50),
fechaContable           date,
fechaActual             date,
debe                    decimal(10,2),
haber                   decimal(10,2),
diferencia              char(50),
conceptoGeneral         varchar(255),
estado                  varchar(50),
usuarioAgrega           varchar(255),
fechaAgrega             datetime,
usuarioModifica         varchar(255),
fechaModifica           datetime,

CONSTRAINT pk_encabezado PRIMARY KEY(encabezadoId),
CONSTRAINT fk_encabezado_partida FOREIGN KEY(tipoPartidaId) REFERENCES tipoPartida(tipoPartidaId)
);

CREATE TABLE partidaDetalle(
partidaDetalleId        int(10) auto_increment not null,
encabezadoId             int(10) not null,
cuentaId                int(10) not null,
tipoComprobanteId       int(10) not null,
cargo                   decimal(10,2),
abono                   decimal(10,2),
saldo                   decimal(10,2),
numeroComprobante       int(100),
fechaComprobante        date,
concepto                varchar(255),
usuarioAgrega           varchar(255),
fechaAgrega             datetime,
usuarioModifica         varchar(255),
fechaModifica           datetime,

CONSTRAINT pk_partidaDetalle PRIMARY KEY(partidaDetalleId),
CONSTRAINT fk_partida_encabezado FOREIGN KEY(encabezadoId) REFERENCES encabezado(encabezadoId),
CONSTRAINT fk_partida_cuenta FOREIGN KEY(cuentaId) REFERENCES CatalogoCuentas(cuentaId),
CONSTRAINT fk_partida_comprobante FOREIGN KEY(tipoComprobanteId) REFERENCES tipoComprobante(tipoComprobanteId)
);