CREATE TABLE `amanities` (
  `amanity` varchar(50) NOT NULL,
  PRIMARY KEY (`amanity`)
);

INSERT INTO amanities
VALUES ("Arcade"),
("Conference rooms"),
("Convenience store"),
("Free WiFi"),
("Gym"),
("Hotel bar"),
("Laundry service"),
("Parking"),
("Pool"),
("Restaurant"),
("Snack bar"),
("Spa"),
("Terrace");

CREATE TABLE `accounts` (
  `username` varchar(100) NOT NULL,
  `mypass` varchar(100) NOT NULL,
  `acc_type` tinyint(1) NOT NULL,
  PRIMARY KEY (`username`)
);

CREATE TABLE business_locations (
  `address` varchar(120) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `name` varchar(70) NOT NULL,
  `about` varchar(500),
  `phone` int unsigned,
  `chargePerPerson` float,
  `image` varchar(100),
  PRIMARY KEY (address),
  FOREIGN KEY (user_id) REFERENCES accounts(username)
);

CREATE TABLE ama_offered (
  `offer_id` int NOT NULL AUTO_INCREMENT,
  `address_id` varchar(120) NOT NULL,
  `ama_id` varchar(50) NOT NULL,
  PRIMARY KEY (offer_id),
  FOREIGN KEY (address_id) REFERENCES business_locations(`address`),
  FOREIGN KEY (ama_id) REFERENCES amanities(amanity)
);

CREATE TABLE rooms (
  `room_id` int NOT NULL AUTO_INCREMENT,
  `address_id` varchar(120) NOT NULL,
  `room_num` int unsigned NOT NULL,
  `base_price` int unsigned NOT NULL,
  `max_occup` int unsigned NOT NULL,
  PRIMARY KEY (room_id),
  FOREIGN KEY (address_id) REFERENCES business_locations(`address`)
);

CREATE TABLE ama_to_room (
  `offer_id`int NOT NULL,
  `room_id`int NOT NULL,
  `distance` int unsigned DEFAULT NULL,
  CONSTRAINT PK_ama_to_room PRIMARY KEY (offer_id,room_id),
  FOREIGN KEY (offer_id) REFERENCES ama_offered(`offer_id`),
  FOREIGN KEY (room_id) REFERENCES rooms(room_id)
);

CREATE TABLE bookings (
  booking_id int NOT NULL AUTO_INCREMENT,
  customer_username varchar(100) NOT NULL,
  address_id varchar(120) NOT NULL,
  room_num int NOT NULL,
  from_date date NOT NULL,
  to_date date NOT NULL,
  PRIMARY KEY (booking_id)
);

SELECT ar.* 
FROM ama_to_room AS ar, rooms AS r 
WHERE r.address_id = '$address' AND r.room_id = ar.room_id;


INSERT INTO accounts
VALUES ('neel@mail.com', md5('neel'), 1),
('rutu@mail.com', md5('rutu'), 0),
('uofs@mail.com', md5('uofs'), 1),
('tester@mail.com', md5('tester'), 1);

INSERT INTO business_locations (`address`, `user_id`, `name`)
VALUES ('800 Linden St., Scranton, PA 18510', 'neel@mail.com', 'Comfort'),
('123 Mulbery St., wilkes-barre, PA 18510', 'uofs@mail.com', 'Comfortable Inn'),
('5252 Fight St., Scranton, PA 18510', 'tester@mail.com', 'Fairfield Inn Scranton');

INSERT INTO ama_offered (`address_id`, `ama_id`)
VALUES ('800 Linden St., Scranton, PA 18510', 'Conference rooms'),
('800 Linden St., Scranton, PA 18510', 'Free WiFi'),
('800 Linden St., Scranton, PA 18510', 'Hotel bar'),
('800 Linden St., Scranton, PA 18510', 'Restaurant'),
('800 Linden St., Scranton, PA 18510', 'Snack bar'),
('800 Linden St., Scranton, PA 18510', 'Terrace'),
('123 Mulbery St., wilkes-barre, PA 18510', 'Arcade'),
('123 Mulbery St., wilkes-barre, PA 18510', 'Free WiFi'),
('123 Mulbery St., wilkes-barre, PA 18510', 'Spa'),
('5252 Fight St., Scranton, PA 18510', 'Convenience store'),
('5252 Fight St., Scranton, PA 18510', 'Free WiFi'),
('5252 Fight St., Scranton, PA 18510', 'Parking'),
('5252 Fight St., Scranton, PA 18510', 'Snack bar'),
('5252 Fight St., Scranton, PA 18510', 'Spa');

SELECT ao.ama_id, ar.distance, ar.room_id, ar.offer_id, ao.address_id
FROM ama_to_room AS ar
CROSS JOIN ama_offered AS ao
WHERE ar.offer_id = ao.offer_id AND ar.room_id = '$todo';

SELECT * FROM rooms
WHERE address_id LIKE '%scranton%' 
AND room_num NOT IN (SELECT room_num FROM bookings 
                     WHERE from_date BETWEEN '2023-05-17' AND '2023-05-19' OR 
                     to_date BETWEEN '2023-05-17' AND '2023-05-19' OR 
                     (from_date < '2023-05-17' AND to_date >'2023-05-19') );

SELECT r.address_id, r.room_num, r.base_price, r.max_occup, ar.room_id, ar.offer_id, ar.distance 
FROM rooms AS r
CROSS JOIN ama_to_room AS ar
WHERE r.room_id = ar.room_id AND (r.address_id LIKE '%scranton%' 
AND r.room_num NOT IN (SELECT room_num FROM bookings 
                     WHERE from_date BETWEEN '2023-05-17' AND '2023-05-19' OR 
                     to_date BETWEEN '2023-05-17' AND '2023-05-19' OR 
                     (from_date < '2023-05-17' AND to_date >'2023-05-19') ) );