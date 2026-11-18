CREATE DATABASE IF NOT EXISTS doctor_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE doctor_booking;
CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS doctors (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 specialty VARCHAR(100) NOT NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS appointments (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL,
 doctor_id INT UNSIGNED NOT NULL,
 appointment_date DATE NOT NULL,
 appointment_time TIME NOT NULL,
 reason VARCHAR(500) NOT NULL DEFAULT '',
 status ENUM('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_appointments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_appointments_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id),
 CONSTRAINT uq_doctor_slot UNIQUE (doctor_id, appointment_date, appointment_time)
) ENGINE=InnoDB;
INSERT INTO doctors (name,specialty)
SELECT 'Айгүл Әбдірахманова','Терапевт' WHERE NOT EXISTS (SELECT 1 FROM doctors WHERE name='Айгүл Әбдірахманова');
INSERT INTO doctors (name,specialty)
SELECT 'Ерлан Сейітов','Кардиолог' WHERE NOT EXISTS (SELECT 1 FROM doctors WHERE name='Ерлан Сейітов');
INSERT INTO doctors (name,specialty)
SELECT 'Мадина Омарова','Педиатр' WHERE NOT EXISTS (SELECT 1 FROM doctors WHERE name='Мадина Омарова');
INSERT INTO doctors (name,specialty)
SELECT 'Данияр Нұрланұлы','Невропатолог' WHERE NOT EXISTS (SELECT 1 FROM doctors WHERE name='Данияр Нұрланұлы');
