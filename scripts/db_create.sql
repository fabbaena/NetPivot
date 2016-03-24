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
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(45) NOT NULL UNIQUE,
    password VARCHAR(60) NOT NULL,
    type VARCHAR(45) NULL,
    max_files TINYINT UNSIGNED NULL,
    max_conversions TINYINT UNSIGNED NULL
) ENGINE = InnoDB;

INSERT INTO NetPivot.users(id,name,password,type,max_files,max_conversions) VALUES (1,'admin','$2y$10$G.TH1hSw9wQcQOTqZjIJNudYm1jfQIjxFthJBnbJhmSTJQrpiU2la','Administrator',100,100);

-- TABLE NetPivot.files
DROP TABLE IF EXISTS NetPivot.files CASCADE;
CREATE TABLE IF NOT EXISTS NetPivot.files (
    uuid CHAR(36) NOT NULL UNIQUE PRIMARY KEY,
    filename VARCHAR(255) NULL,
    project_name VARCHAR(64) NULL,
    upload_time DATETIME(0) NULL,
    users_id INT UNSIGNED NOT NULL,
    CONSTRAINT fk_files_users
	FOREIGN KEY(users_id) REFERENCES NetPivot.users(id)
	ON DELETE CASCADE
	ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- TABLE NetPivot.settings
DROP TABLE IF EXISTS NetPivot.settings CASCADE;
CREATE TABLE IF NOT EXISTS NetPivot.settings (
    host_name TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    timezone VARCHAR(45) NOT NULL DEFAULT 'US/Eastern',
    files_path VARCHAR(255) NULL
) ENGINE = InnoDB;

-- TABLE NetPivot.conversions
DROP TABLE IF EXISTS NetPivot.conversions CASCADE;
CREATE TABLE IF NOT EXISTS NetPivot.conversions (
    id_conversions BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
    users_id INT UNSIGNED NOT NULL,
    time_conversion DATETIME(0) NOT NULL,
    files_uuid CHAR(36) NOT NULL,
    converted_file VARCHAR(255) NOT NULL,
    error_file VARCHAR(255) NULL,
    stats_file VARCHAR(255) NULL,
    CONSTRAINT fk_conversions_users1
	FOREIGN KEY(users_id) REFERENCES NetPivot.users(id)
	ON DELETE CASCADE
	ON UPDATE NO ACTION,
    CONSTRAINT fk_conversions_files1
	FOREIGN KEY(files_uuid) REFERENCES NetPivot.files(uuid)
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- TABLE NetPivot.details
DROP TABLE IF EXISTS NetPivot.details CASCADE;
CREATE TABLE IF NOT EXISTS NetPivot.details (
    files_uuid CHAR(36) NOT NULL,
    module VARCHAR(16) NULL,
    obj_grp VARCHAR(32) NULL,
    obj_component VARCHAR(32) NULL,
    obj_name VARCHAR(160) NULL,
    attribute VARCHAR(128) NULL,
    converted BOOLEAN NULL,
    omitted BOOLEAN NULL,
    line SMALLINT UNSIGNED NULL,
    INDEX files_uuid_idx USING BTREE (files_uuid),
    CONSTRAINT fk_details_files1
	FOREIGN KEY(files_uuid) REFERENCES NetPivot.files(uuid)
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- PRIVILEGES NetPivot to demonio@localhost
CREATE USER 'demonio'@'localhost' IDENTIFIED BY 's3cur3s0c';
GRANT create,delete,insert,select,update ON NetPivot.* TO 'demonio'@'localhost';
-- GRANT ALL PRIVILEGES ON NetPivot.* TO 'demonio'@'localhost';

