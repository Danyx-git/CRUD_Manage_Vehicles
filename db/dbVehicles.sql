DROP DATABASE IF EXISTS db_vehicles;
CREATE DATABASE db_vehicles;
USE db_vehicles;
CREATE TABLE IF NOT EXISTS vehicles(
	id INT AUTO_INCREMENT PRIMARY KEY,
	clientName VARCHAR(150) NOT NULL,
    brand VARCHAR(150) NOT NULL,
    plate VARCHAR(8) NOT NULL UNIQUE,
    combustion VARCHAR(50) NOT NULL,
    warranty BOOLEAN NOT NULL,
    cleanCar BOOLEAN NOT NULL,
    oilChange BOOLEAN NOT NULL,
    brakeCheck BOOLEAN NOT NULL,
    alignment BOOLEAN NOT NULL,
    image VARCHAR(500)
);