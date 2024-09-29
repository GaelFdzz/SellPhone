-- Creación de la base de datos sellphone
CREATE DATABASE sellphone;

USE sellphone;

-- Creación de la tabla Roles
CREATE TABLE Roles (
    Id_Rol INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(255) NOT NULL
);

-- Creación de la tabla Usuarios
CREATE TABLE Usuarios (
    Id_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(255) NOT NULL,
    Apellido VARCHAR(255) NOT NULL,
    Correo VARCHAR(320) NOT NULL UNIQUE,
    Contrasena VARCHAR(255) NOT NULL,
    Id_Rol INT,
    FOREIGN KEY (Id_Rol) REFERENCES Roles(Id_Rol)
);

-- Índices adicionales para mejorar el rendimiento en consultas
CREATE INDEX idx_usuario_nombre ON Usuarios(Nombre);
CREATE INDEX idx_usuario_apellido ON Usuarios(Apellido);

-- Creación de la tabla Categorías
CREATE TABLE Categorias (
    Id_Categoria INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(255) NOT NULL
);

-- Creación de la tabla Productos
CREATE TABLE Productos (
    Id_Producto INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(255) NOT NULL,
    Descripcion TEXT,
    Precio DECIMAL(10, 2) CHECK (Precio > 0),
    Stock INT CHECK (Stock >= 0),
    Id_Categoria INT,
    FOREIGN KEY (Id_Categoria) REFERENCES Categorias(Id_Categoria)
);

-- Índices adicionales para mejorar el rendimiento en consultas
CREATE INDEX idx_productos_nombre ON Productos(Nombre);

-- Creación de la tabla Publicaciones
CREATE TABLE Publicaciones (
    Id_Publicacion INT AUTO_INCREMENT PRIMARY KEY,
    Titulo VARCHAR(255) NOT NULL,
    Descripcion TEXT,
    Fecha_Publicacion DATE NOT NULL,
    Id_Producto INT,
    FOREIGN KEY (Id_Producto) REFERENCES Productos(Id_Producto)
);

-- Creación de la tabla Calificacion_Productos
CREATE TABLE Calificacion_Productos (
    Id_Calificacion INT AUTO_INCREMENT PRIMARY KEY,
    Puntuacion INT CHECK (Puntuacion BETWEEN 1 AND 5),
    Comentario TEXT,
    Id_Producto INT,
    Id_Usuario INT,
    FOREIGN KEY (Id_Producto) REFERENCES Productos(Id_Producto),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

-- Creación de la tabla Pagos
CREATE TABLE Pagos (
    Id_Pago INT AUTO_INCREMENT PRIMARY KEY,
    Monto DECIMAL(10, 2) NOT NULL,
    Fecha_Pago DATE NOT NULL,
    Id_Usuario INT,
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

-- Creación de la tabla Pedidos
CREATE TABLE Pedidos (
    Id_Pedido INT AUTO_INCREMENT PRIMARY KEY,
    Fecha_Pedido DATE NOT NULL,
    Estado VARCHAR(255) NOT NULL,
    Id_Usuario INT,
    Id_Pago INT,
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario),
    FOREIGN KEY (Id_Pago) REFERENCES Pagos(Id_Pago)
);

-- Creación de la tabla Detalle_Pedidos
CREATE TABLE Detalle_Pedidos (
    Id_Detalle INT AUTO_INCREMENT PRIMARY KEY,
    Cantidad INT CHECK (Cantidad > 0),
    Precio DECIMAL(10, 2) CHECK (Precio > 0),
    Id_Pedido INT,
    Id_Producto INT,
    FOREIGN KEY (Id_Pedido) REFERENCES Pedidos(Id_Pedido),
    FOREIGN KEY (Id_Producto) REFERENCES Productos(Id_Producto)
);

-- Creación de la tabla Reseñas
CREATE TABLE Resenas (
    Id_Resena INT AUTO_INCREMENT PRIMARY KEY,
    Id_Producto INT NOT NULL,
    Usuario VARCHAR(255) NOT NULL,
    Comentario TEXT NOT NULL,
    Calificacion INT CHECK (Calificacion BETWEEN 1 AND 5) NOT NULL,
    Fecha DATETIME NOT NULL DEFAULT NOW(),
    FOREIGN KEY (Id_Producto) REFERENCES Productos(Id_Producto)
);

CREATE TABLE Mensajes (
    Id_Mensaje INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(255) NOT NULL,
    Correo_Electronico VARCHAR(320) NOT NULL,
    Asunto VARCHAR(255) NOT NULL,
    Mensaje TEXT NOT NULL,
    Fecha_Recibido TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Carrito (
    Id_Carrito INT AUTO_INCREMENT PRIMARY KEY,
    Id_Usuario INT NOT NULL,
    Fecha_Creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado ENUM('activo', 'finalizado') DEFAULT 'activo',
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

CREATE TABLE Detalles_Carrito (
    Id_Detalle_Carrito INT AUTO_INCREMENT PRIMARY KEY,
    Id_Carrito INT NOT NULL,
    Id_Producto INT NOT NULL,
    Cantidad INT NOT NULL,
    Precio DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (Id_Carrito) REFERENCES Carrito(Id_Carrito),
    FOREIGN KEY (Id_Producto) REFERENCES Productos(Id_Producto)
);

show columns from pedidos;

ALTER TABLE Pedidos ADD COLUMN Total_Precio DECIMAL(10, 2);




select * from productos;
select * from resenas;

-- Crear un índice para la columna Id_Producto para mejorar el rendimiento de las consultas
CREATE INDEX idx_resenas_producto ON Resenas(Id_Producto);

-- Trigger para validar la calificación en la tabla Reseñas
DELIMITER //
CREATE TRIGGER validar_calificacion
BEFORE INSERT ON Resenas
FOR EACH ROW
BEGIN
    IF NEW.Calificacion < 1 OR NEW.Calificacion > 5 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La calificación debe estar entre 1 y 5.';
    END IF;
END; //
DELIMITER ;



-- 
-- 
-- DATOS NECESARIOS PARA INICIAR LA BASE DE DATOS
-- 
-- 

alter table productos add column Imagen varchar(255);

insert into categorias values (1, 'Celular');

use sellphone;
insert into productos values (1, 'Iphone 15', 'lorem ipsum lalala', 5000, 10, 1);
select * from productos;
delete from productos;

INSERT INTO roles (Nombre) VALUES ('Administrador');
INSERT INTO roles (Nombre) VALUES ('Soporte');
INSERT INTO roles (Nombre) VALUES ('Cliente');
INSERT INTO roles (Nombre) VALUES ('Usuario');

-- Validación del formato de correo
CREATE TRIGGER trigger_valid_email BEFORE INSERT ON Usuarios
FOR EACH ROW
BEGIN
    DECLARE email_count INT;
    SET email_count = (SELECT COUNT(*) FROM Usuarios WHERE Correo = NEW.Correo);
    IF email_count > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El correo ya está en uso.';
    END IF;
    IF NOT NEW.Correo LIKE '%_@__%.__%' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Formato de correo inválido.';
    END IF;
END;

INSERT INTO usuarios (Nombre, Apellido, Correo, Contrasena, Id_Rol)
VALUES ('admin', 'admin', 'admin@test.com', 'password', 1);