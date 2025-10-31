USE db_vehicles;

INSERT INTO vehicles (clientName, brand, plate, combustion, warranty, cleanCar, oilChange, brakeCheck, alignment, image)
VALUES
('Carlos Pérez', 'Toyota Corolla', '1234ABC', 'Gasolina', TRUE, TRUE, TRUE, TRUE, TRUE, '../images/imagenEjemplo.jpg'),
('María López', 'Tesla Model 3', '5678XYZ', 'Eléctrico', TRUE, FALSE, FALSE, TRUE, TRUE, '../images/imagenEjemplo.jpg'),
('Luis García', 'Ford Focus', '9123DFG', 'Diésel', FALSE, TRUE, TRUE, FALSE, TRUE, '../images/imagenEjemplo.jpg'),
('Ana Torres', 'Hyundai Tucson', '3456LMN', 'Híbrido', TRUE, TRUE, TRUE, TRUE, FALSE, '../images/imagenEjemplo.jpg'),
('Javier Martín', 'Volkswagen Golf', '7890JKL', 'Gasolina', FALSE, FALSE, TRUE, TRUE, TRUE, '../images/imagenEjemplo.jpg');
