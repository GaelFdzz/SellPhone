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
    Contraseña_Hash VARCHAR(255) NOT NULL,
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
    Id_Usuario INT,
    Id_Categoria INT,
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario),
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
    Fecha_Calificacion DATE NOT NULL,
    Id_Producto INT,
    FOREIGN KEY (Id_Producto) REFERENCES Productos(Id_Producto)
);

-- Creación de la tabla Pagos
CREATE TABLE Pagos (
    Id_Pago INT AUTO_INCREMENT PRIMARY KEY,
    Fecha_Pago DATE NOT NULL,
    Monto DECIMAL(10, 2) CHECK (Monto > 0),
    Metodo_Pago VARCHAR(255) NOT NULL,
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

-- Auditoría para cambios en la tabla Usuarios
CREATE TABLE Usuarios_Auditoria (
    Id_Auditoria INT AUTO_INCREMENT PRIMARY KEY,
    Id_Usuario INT,
    Cambio VARCHAR(255),
    Fecha_Cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

-- Trigger para registrar auditoría de modificaciones en Usuarios
CREATE TRIGGER trigger_usuarios_audit BEFORE UPDATE ON Usuarios
FOR EACH ROW
BEGIN
    INSERT INTO Usuarios_Auditoria (Id_Usuario, Cambio)
    VALUES (OLD.Id_Usuario, 'Modificación');
END;

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


-- INSERTAR DATOS VALORES INICIALES A LA BASE DE DATOS
-- Roles
INSERT INTO roles (Nombre) VALUES ('Administrador');
INSERT INTO roles (Nombre) VALUES ('Soporte');
INSERT INTO roles (Nombre) VALUES ('Cliente');
INSERT INTO roles (Nombre) VALUES ('Usuario');