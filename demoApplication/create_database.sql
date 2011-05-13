-- creating schema

CREATE DATABASE `demoApplication`
DEFAULT CHARACTER SET utf8 COLLATE utf8_hungarian_ci;

USE `demoApplication`;

-- schema policy

GRANT SELECT, INSERT, UPDATE, DELETE
ON `demoApplication`.*
TO 'demoUser'@'localhost'
IDENTIFIED BY 'demoPassword';

-- schema structure

CREATE TABLE `user`
(
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` CHAR(50) NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_hungarian_ci;

ALTER TABLE `user` ADD UNIQUE (`name`);
ALTER TABLE `user` ADD UNIQUE (`email`);

CREATE TABLE `task`
(
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `start` DATE NOT NULL DEFAULT 0,
  `finish` DATE NOT NULL DEFAULT 0,
  `priority` ENUM('low', 'normal', 'high') NOT NULL DEFAULT 'normal',
  `is_public` BOOLEAN NOT NULL DEFAULT '0',
  INDEX (`user_id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_hungarian_ci;

ALTER TABLE `task`
ADD FOREIGN KEY (`user_id`)
REFERENCES `user`(`id`)
ON DELETE RESTRICT ON UPDATE CASCADE;
