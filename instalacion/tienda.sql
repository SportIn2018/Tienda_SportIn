CREATE ROLE tiendaadmin WITH ENCRYPTED PASSWORD 'hola123';

CREATE DATABASE tienda OWNER tiendaadmin;

GRANT CONNECT ON DATABASE tienda TO tiendaadmin;
ALTER ROLE tiendaadmin WITH LOGIN;

\c tienda;

CREATE TABLE c_tipos_usuario(
	id_tipo_usuario SERIAL PRIMARY KEY,
	tipo_us_descripcion varchar(32) UNIQUE NOT NULL
);

ALTER TABLE c_tipos_usuario OWNER TO tiendaadmin;
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE c_tipos_usuario TO tiendaadmin;

INSERT INTO c_tipos_usuario(tipo_us_descripcion) VALUES('Administrador');
INSERT INTO c_tipos_usuario(tipo_us_descripcion) VALUES('Vendedor');
INSERT INTO c_tipos_usuario(tipo_us_descripcion) VALUES('Comprador');

CREATE TABLE c_tipos_pago(
	id_tipo_pago SERIAL PRIMARY KEY,
	tipo_pago_descripcion varchar(32) UNIQUE NOT NULL
);

ALTER TABLE c_tipos_pago OWNER TO tiendaadmin;
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE c_tipos_pago TO tiendaadmin;

INSERT INTO c_tipos_pago(tipo_pago_descripcion) VALUES('Tarjeta de Credito');
INSERT INTO c_tipos_pago(tipo_pago_descripcion) VALUES('Tarjeta de Debito');
INSERT INTO c_tipos_pago(tipo_pago_descripcion) VALUES('Efectivo');

CREATE TABLE c_categorias(
	id_categoria SERIAL PRIMARY KEY,
	categ_descripcion varchar(32) UNIQUE NOT NULL
);

ALTER TABLE c_categorias OWNER TO tiendaadmin;
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE c_categorias TO tiendaadmin;

CREATE TABLE c_marcas(
	id_marca SERIAL PRIMARY KEY,
	marca_descripcion varchar(32) UNIQUE NOT NULL
);

ALTER TABLE c_marcas OWNER TO tiendaadmin;
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE c_marcas TO tiendaadmin;

CREATE TABLE c_tallas(
	id_talla SERIAL PRIMARY KEY,
	talla_descripcion varchar(32) UNIQUE NOT NULL
);

ALTER TABLE c_tallas OWNER TO tiendaadmin;
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE c_tallas TO tiendaadmin;

INSERT INTO c_tallas(talla_descripcion) VALUES('XS');
INSERT INTO c_tallas(talla_descripcion) VALUES('S');
INSERT INTO c_tallas(talla_descripcion) VALUES('M');
INSERT INTO c_tallas(talla_descripcion) VALUES('L');
INSERT INTO c_tallas(talla_descripcion) VALUES('XL');

CREATE TABLE descuentos(
	id_descuento SERIAL PRIMARY KEY,
	desc_porcentaje float NOT NULL UNIQUE CHECK (desc_porcentaje >= 0)
);

ALTER TABLE descuentos OWNER TO tiendaadmin;
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE descuentos TO tiendaadmin;

INSERT INTO descuentos(desc_porcentaje) VALUES(0);
INSERT INTO descuentos(desc_porcentaje) VALUES(0.05);
INSERT INTO descuentos(desc_porcentaje) VALUES(0.1);
INSERT INTO descuentos(desc_porcentaje) VALUES(0.15);
INSERT INTO descuentos(desc_porcentaje) VALUES(0.2);
INSERT INTO descuentos(desc_porcentaje) VALUES(0.25);
INSERT INTO descuentos(desc_porcentaje) VALUES(0.5);

CREATE TABLE usuarios(
	id_usuario SERIAL PRIMARY KEY,
	id_tipo_usuario INTEGER REFERENCES c_tipos_usuario(id_tipo_usuario),
	us_nombre varchar(32) NOT NULL,
	us_apaterno varchar(32) NOT NULL,
	us_amaterno varchar(32),
	us_direccion text NOT NULL,
	us_correo varchar(64) UNIQUE NOT NULL,
	us_telefono varchar(10) NOT NULL,
	us_login varchar(16) UNIQUE NOT NULL,
	us_password varchar(32) NOT NULL
);

ALTER TABLE usuarios OWNER TO tiendaadmin;
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE usuarios TO tiendaadmin;

INSERT INTO usuarios(id_tipo_usuario,us_nombre,us_apaterno,us_direccion,us_correo,us_telefono,us_login,us_password) VALUES(1,'Administrador','Tienda','Sportin','TiendaSportIn@gmail.com','00000000','admin','9450476b384b32d8ad8b758e76c98a69');

CREATE TABLE productos(
	id_producto SERIAL PRIMARY KEY,
	id_categoria INTEGER REFERENCES c_categorias(id_categoria),
	id_marca INTEGER REFERENCES c_marcas(id_marca),
	id_talla INTEGER REFERENCES c_tallas(id_talla),
	id_descuento INTEGER REFERENCES descuentos(id_descuento) DEFAULT 1,
	prod_nombre varchar(64) NOT NULL,
	prod_descripcion text NOT NULL,
	prod_ruta_img varchar(64) DEFAULT 'img/default.jpg',
	prod_precio float NOT NULL CHECK (prod_precio > 0),
	prod_inventario INTEGER NOT NULL CHECK (prod_inventario >= 0)
);

ALTER TABLE productos OWNER TO tiendaadmin;
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE productos TO tiendaadmin;

CREATE TABLE ventas(
	id_venta SERIAL PRIMARY KEY,
	id_usuario INTEGER REFERENCES usuarios(id_usuario),
	id_tipo_pago INTEGER REFERENCES c_tipos_pago(id_tipo_pago),
	venta_fecha DATE NOT NULL DEFAULT CURRENT_DATE
);

ALTER TABLE ventas OWNER TO tiendaadmin;
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE ventas TO tiendaadmin;

CREATE TABLE venta_producto(
	id_venta INTEGER REFERENCES ventas(id_venta),
	id_producto INTEGER REFERENCES productos(id_producto),
	cantidad INTEGER NOT NULL CHECK (cantidad > 0),
	PRIMARY KEY (id_venta,id_producto)
);

ALTER TABLE venta_producto OWNER TO tiendaadmin;
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE venta_producto TO tiendaadmin;