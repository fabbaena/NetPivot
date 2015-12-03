-- InnoDB Backend Storage Settings
SET unique_checks=0;
SET foreign_key_checks=1;

-- SQL Mode
SET sql_mode='ALLOW_INVALID_DATES,TRADITIONAL';

-- PRIVILEGES NetPivot to demonio@localhost
REVOKE ALL PRIVILEGES ON *.* FROM 'demonio'@'localhost';
DROP USER 'demonio'@'localhost';

-- DATABASE NetPivot
DROP DATABASE IF EXISTS NetPivot;
CREATE DATABASE IF NOT EXISTS NetPivot CHARACTER SET = 'utf8' COLLATE = 'utf8_general_ci';
USE NetPivot;

-- TABLE NetPivot.users
DROP TABLE IF EXISTS NetPivot.users CASCADE;
CREATE TABLE IF NOT EXISTS NetPivot.users (
    id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(45) NOT NULL UNIQUE,
    password VARCHAR(60) NULL,
    type VARCHAR(45) NULL,
    max_files SMALLINT UNSIGNED NULL,
    max_conversions SMALLINT UNSIGNED NULL
)
ENGINE = InnoDB;

INSERT INTO users(id,name,password,type,max_files,max_conversions) VALUES (1,'admin','$2y$10$XEAw/cVMGTy4H8flaMjpLesrkZVlRo1ZVC0fm6FjHlGTWul5vh2Ae','Administrator',100,100);

-- TABLE NetPivot.files
DROP TABLE IF EXISTS NetPivot.files CASCADE;
CREATE TABLE IF NOT EXISTS NetPivot.files (
    uuid VARCHAR(36) NOT NULL UNIQUE PRIMARY KEY,
    filename VARCHAR(255) NULL,
    upload_time DATETIME(0) NULL,
    users_id SMALLINT UNSIGNED NOT NULL,
    CONSTRAINT fk_files_users
	FOREIGN KEY(users_id) REFERENCES NetPivot.users(id)
	ON DELETE CASCADE
	ON UPDATE NO ACTION
)
ENGINE = InnoDB;

-- TABLE NetPivot.conversions
DROP TABLE IF EXISTS NetPivot.conversions CASCADE;
CREATE TABLE IF NOT EXISTS NetPivot.conversions (
    id_conversions SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
    users_id SMALLINT UNSIGNED NOT NULL,
    time_conversion DATETIME(0) NOT NULL,
    files_uuid VARCHAR(36) NOT NULL,
    converted_file VARCHAR(255) NOT NULL,
    CONSTRAINT fk_conversions_users1
	FOREIGN KEY(users_id) REFERENCES NetPivot.users(id)
	ON DELETE CASCADE
	ON UPDATE NO ACTION,
    CONSTRAINT fk_conversions_files1
	FOREIGN KEY(files_uuid) REFERENCES NetPivot.files(uuid)
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
)
ENGINE = InnoDB;

-- TABLE NetPivot.settings
DROP TABLE IF EXISTS NetPivot.settings CASCADE;
CREATE TABLE IF NOT EXISTS NetPivot.settings (
    host_name TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    timezone VARCHAR(45) NULL,
    files_path VARCHAR(255) NULL
)
ENGINE = InnoDB;

-- PRIVILEGES NetPivot to demonio@localhost
CREATE USER 'demonio'@'localhost' IDENTIFIED BY 'password';
GRANT create,delete,insert,select,update ON NetPivot.* TO 'demonio'@'localhost';
-- GRANT ALL PRIVILEGES ON NetPivot.* TO 'demonio'@'localhost';

