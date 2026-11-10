CREATE DATABASE IF NOT EXISTS swiftdrive CHARACTER SET utf8mb4;
USE swiftdrive;
DROP TABLE IF EXISTS reviews, payments, bookings, inquiries, posts, vehicles, users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(20),
  password VARCHAR(255) NOT NULL,
  role ENUM('customer','staff','admin') DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE vehicles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  brand VARCHAR(50) NOT NULL,
  type ENUM('economy','suv','van','luxury') NOT NULL,
  transmission ENUM('Automatic','Manual') NOT NULL,
  fuel VARCHAR(20) DEFAULT 'Petrol',
  seats TINYINT NOT NULL,
  daily_rate DECIMAL(10,2) NOT NULL,
  weekly_rate DECIMAL(10,2) NOT NULL,
  location VARCHAR(60) DEFAULT 'Negombo',
  image_url VARCHAR(255),
  description TEXT,
  status ENUM('available','maintenance') DEFAULT 'available'
) ENGINE=InnoDB;

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  vehicle_id INT NOT NULL,
  pickup_date DATE NOT NULL,
  return_date DATE NOT NULL,
  pickup_location VARCHAR(60) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  deposit DECIMAL(10,2) NOT NULL,
  late_fee DECIMAL(10,2) DEFAULT 0,
  status ENUM('pending','confirmed','picked_up','returned','cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
  INDEX (vehicle_id, pickup_date, return_date)
) ENGINE=InnoDB;

CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  type ENUM('deposit','balance','penalty') NOT NULL,
  status ENUM('success','failed') NOT NULL,
  reference VARCHAR(30),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE inquiries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('new','replied') DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  vehicle_id INT NOT NULL,
  rating TINYINT NOT NULL,
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  body TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO vehicles (name,brand,type,transmission,fuel,seats,daily_rate,weekly_rate,location,description) VALUES
('Alto 800','Suzuki','economy','Manual','Petrol',4,6500,39000,'Negombo','Compact and fuel efficient, ideal for city driving.'),
('Wagon R','Suzuki','economy','Automatic','Petrol',5,7500,45000,'Negombo','Roomy hatchback with easy automatic drive.'),
('Aqua','Toyota','economy','Automatic','Hybrid',5,9000,54000,'Colombo','Hybrid economy for long, low-cost trips.'),
('Vitara','Suzuki','suv','Automatic','Petrol',5,15000,90000,'Negombo','Comfortable SUV for hill-country roads.'),
('Prado','Toyota','suv','Automatic','Diesel',7,28000,168000,'Colombo','Powerful 7-seat SUV for family adventures.'),
('KDH HiRoof','Toyota','van','Manual','Diesel',12,22000,132000,'Negombo','Spacious van for groups and airport transfers.'),
('Hiace','Toyota','van','Automatic','Diesel',10,20000,120000,'Negombo','Reliable van with air conditioning throughout.'),
('E-Class','Mercedes','luxury','Automatic','Petrol',5,45000,270000,'Colombo','Executive luxury for weddings and business.');

INSERT INTO posts (title,body) VALUES
('Top 5 Road Trips from Negombo','Start with the Cultural Triangle loop to Sigiriya and Dambulla, head south along the coast to Galle, or climb to Nuwara Eliya for tea country views. Always plan fuel stops before entering hill roads.'),
('Driving Tips for Sri Lankan Roads','Drive on the left, expect buses to overtake aggressively, keep distance on wet roads and avoid night driving in rural areas. Carry your licence and booking confirmation at all times.'),
('Best Beaches Near Negombo','Negombo beach, Waikkal and Marawila are all within an hour. Go early to avoid traffic and park only in marked areas.');
