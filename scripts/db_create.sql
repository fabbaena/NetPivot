-- PRIVILEGES NetPivot to demonio@localhost
REVOKE ALL PRIVILEGES ON *.* FROM 'demonio'@'localhost';
DROP USER 'demonio'@'localhost';

-- DATABASE NetPivot
DROP DATABASE IF EXISTS NetPivot;
CREATE DATABASE IF NOT EXISTS NetPivot CHARACTER SET = 'utf8' COLLATE = 'utf8_general_ci';
USE NetPivot;

-- TABLE NetPivot.users
DROP TABLE IF EXISTS users CASCADE;
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(45) NOT NULL UNIQUE,
    password VARCHAR(60) NOT NULL,
    type VARCHAR(45) NULL,
    max_files TINYINT UNSIGNED NULL,
    max_conversions TINYINT UNSIGNED NULL
) ENGINE = InnoDB;

INSERT INTO users(id,name,password,type,max_files,max_conversions) VALUES (1,'admin','$2y$10$G.TH1hSw9wQcQOTqZjIJNudYm1jfQIjxFthJBnbJhmSTJQrpiU2la','Administrator',100,100);

-- TABLE NetPivot.files
DROP TABLE IF EXISTS files CASCADE;
CREATE TABLE IF NOT EXISTS files (
    uuid CHAR(36) NOT NULL UNIQUE PRIMARY KEY,
    filename VARCHAR(255) NULL,
    project_name VARCHAR(64) NULL,
    upload_time DATETIME(0) NULL,
    users_id INT UNSIGNED NOT NULL,
    CONSTRAINT fk_files_users
	FOREIGN KEY(users_id) REFERENCES users(id)
	    ON DELETE CASCADE
	    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- TABLE NetPivot.settings
DROP TABLE IF EXISTS settings CASCADE;
CREATE TABLE IF NOT EXISTS settings (
    host_name TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    timezone VARCHAR(45) NOT NULL DEFAULT 'US/Eastern',
    files_path VARCHAR(255) NULL
) ENGINE = InnoDB;

-- TABLE NetPivot.conversions
DROP TABLE IF EXISTS conversions CASCADE;
CREATE TABLE IF NOT EXISTS conversions (
    id_conversions BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
    users_id INT UNSIGNED NOT NULL,
    time_conversion DATETIME(0) NOT NULL,
    files_uuid CHAR(36) NOT NULL,
    converted_file VARCHAR(255) NOT NULL,
    error_file VARCHAR(255) NULL,
    stats_file VARCHAR(255) NULL,
    CONSTRAINT fk_conversions_users1
	FOREIGN KEY(users_id) REFERENCES users(id)
	    ON DELETE CASCADE
	    ON UPDATE NO ACTION,
    CONSTRAINT fk_conversions_files1
	FOREIGN KEY(files_uuid) REFERENCES files(uuid)
	    ON DELETE NO ACTION
	    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- TABLE NetPivot.details
DROP TABLE IF EXISTS details CASCADE;
CREATE TABLE IF NOT EXISTS details (
    files_uuid CHAR(36) NOT NULL,
    module VARCHAR(16) NULL,
    obj_grp VARCHAR(32) NULL,
    obj_component VARCHAR(32) NULL,
    obj_name VARCHAR(160) NULL,
    attribute VARCHAR(128) NULL,
    converted BOOLEAN NOT NULL,
    omitted BOOLEAN NOT NULL,
    line MEDIUMINT UNSIGNED NOT NULL,
    INDEX files_uuid_idx USING BTREE (files_uuid),
    CONSTRAINT fk_details_files1
	FOREIGN KEY(files_uuid) REFERENCES files(uuid)
	    ON DELETE NO ACTION
	    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- TABLE NetPivot.modules
DROP TABLE IF EXISTS modules CASCADE;
CREATE TABLE IF NOT EXISTS modules (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    files_uuid VARCHAR(36) NOT NULL,
    INDEX files_uuid_idx USING BTREE (files_uuid),
    UNIQUE KEY files_uuid_name_idx USING BTREE (files_uuid,name),
    CONSTRAINT fk_modules_files
        FOREIGN KEY(files_uuid) REFERENCES files(uuid)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE = InnoDB;

-- TABLE NetPivot.obj_grps
DROP TABLE IF EXISTS obj_grps CASCADE;
CREATE TABLE IF NOT EXISTS obj_grps (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    obj_component VARCHAR(255) NULL,
    module_id INT UNSIGNED NOT NULL,
    INDEX module_id_idx USING BTREE (module_id),
    UNIQUE KEY name_module_id_idx USING BTREE (name,module_id),
    CONSTRAINT fk_obj_grps_modules
        FOREIGN KEY(module_id) REFERENCES modules(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE = InnoDB;

-- TABLE NetPivot.obj_names
DROP TABLE IF EXISTS obj_names CASCADE;
CREATE TABLE IF NOT EXISTS obj_names (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    line INT UNSIGNED NOT NULL,
    obj_grp_id INT UNSIGNED NOT NULL,
    INDEX obj_grp_id_idx USING BTREE (obj_grp_id),
    UNIQUE KEY name_obj_grp_id USING BTREE (name,obj_grp_id),
    CONSTRAINT fk_obj_names_obj_grps
        FOREIGN KEY(obj_grp_id) REFERENCES obj_grps(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE = InnoDB;

-- TABLE NetPivot.attributes
DROP TABLE IF EXISTS attributes CASCADE;
CREATE TABLE IF NOT EXISTS attributes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    converted BOOLEAN NOT NULL,
    omitted BOOLEAN NOT NULL,
    line MEDIUMINT UNSIGNED NULL,
    obj_name_id INT UNSIGNED NOT NULL,
    INDEX obj_name_id_idx USING BTREE (obj_name_id),
    INDEX name_obj_name_idx USING BTREE (name,obj_name_id),
    CONSTRAINT fk_attributes_obj_names
	FOREIGN KEY(obj_name_id) REFERENCES obj_names(id)
	    ON DELETE CASCADE
	    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- VIEW NetPivot.obj_names_view
DROP VIEW IF EXISTS obj_names_view CASCADE;
CREATE VIEW obj_names_view AS
    SELECT
        obj_names.obj_grp_id AS obj_grp_id,
        obj_names.id AS id,
        obj_names.name AS name,
        obj_names.line AS line,
        (SELECT COUNT(*) FROM attributes WHERE obj_name_id = obj_names.id) AS attribute_count,
        (SELECT SUM(converted) FROM attributes WHERE obj_name_id = obj_names.id) AS attribute_converted,
        (SELECT SUM(omitted) FROM attributes WHERE obj_name_id = obj_names.id) AS attribute_omitted
    FROM
        obj_names;

-- VIEW NetPivot.obj_grps_view
DROP VIEW IF EXISTS obj_grps_view CASCADE;
CREATE VIEW obj_grps_view AS
    SELECT
        obj_grps.module_id AS module_id,
        obj_grps.id AS id,
        obj_grps.name AS name,
        (SELECT COUNT(*) FROM obj_names WHERE obj_grp_id = obj_grps.id) AS object_count,
        (SELECT SUM(attribute_count) FROM obj_names_view WHERE obj_grp_id = obj_grps.id) AS attribute_count,
        (SELECT SUM(attribute_converted) FROM obj_names_view WHERE obj_grp_id = obj_grps.id) AS attribute_converted,
        (SELECT SUM(attribute_omitted) FROM obj_names_view WHERE obj_grp_id = obj_grps.id) AS attribute_omitted
    FROM
        obj_grps;

-- VIEW NetPivot.modules_view
DROP VIEW IF EXISTS modules_view CASCADE;
CREATE VIEW modules_view AS
    SELECT
	modules.files_uuid AS files_uuid,
	modules.id AS id,
	modules.name AS name,
	(SELECT COUNT(*) FROM obj_grps_view WHERE module_id = modules.id) AS objgrp_count,
	(SELECT SUM(object_count) FROM obj_grps_view WHERE module_id = modules.id) AS object_count,
	(SELECT SUM(attribute_count) FROM obj_grps_view WHERE module_id = modules.id) AS attribute_count,
	(SELECT SUM(attribute_converted) FROM obj_grps_view WHERE module_id = modules.id) AS attribute_converted,
	(SELECT SUM(attribute_omitted) FROM obj_grps_view WHERE module_id = modules.id) AS attribute_omitted
    FROM
	modules;

-- TRIGGER NetPivot.new_detail_record
DROP TRIGGER IF EXISTS new_detail_record;
DELIMITER $$
CREATE TRIGGER new_detail_record BEFORE INSERT ON details FOR EACH ROW
BEGIN
    SET @module_id = (SELECT id FROM modules WHERE name = NEW.module AND files_uuid = NEW.files_uuid);
    IF (@module_id IS NULL) THEN
	INSERT INTO modules(name,files_uuid) VALUES (NEW.module,NEW.files_uuid);
	SET @module_id = (SELECT id FROM modules WHERE name = NEW.module AND files_uuid = NEW.files_uuid);
    END IF;
    SET @obj_grp_id = (SELECT id FROM obj_grps WHERE name = NEW.obj_grp AND module_id = @module_id);
    IF (@obj_grp_id IS NULL) THEN
	INSERT INTO obj_grps(name,obj_component,module_id) VALUES (NEW.obj_grp,NEW.obj_component,@module_id);
	SET @obj_grp_id = (SELECT id FROM obj_grps WHERE name = NEW.obj_grp AND module_id = @module_id);
    END IF;
    IF ((NEW.attribute IS NOT NULL OR NEW.attribute <> "") AND (NEW.obj_name IS NULL OR NEW.obj_name = "")) THEN
	SET NEW.obj_name = '---';
    END IF;
    SET @obj_name_id = (SELECT id FROM obj_names WHERE name=NEW.obj_name AND obj_grp_id = @obj_grp_id);
    IF (@obj_name_id IS NULL) THEN
	INSERT INTO obj_names(name,line,obj_grp_id) VALUES (NEW.obj_name,NEW.line,@obj_grp_id);
	SET @obj_name_id = (SELECT id FROM obj_names WHERE name = NEW.obj_name AND obj_grp_id = @obj_grp_id);
    END IF;
    IF (NEW.attribute IS NOT NULL AND NEW.attribute <> "") THEN
	INSERT INTO attributes(name,converted,omitted,line,obj_name_id) VALUES (NEW.attribute,NEW.converted,NEW.omitted,NEW.line,@obj_name_id);
    END IF;
END $$
DELIMITER ;

-- PRIVILEGES NetPivot to demonio@localhost
CREATE USER 'demonio'@'localhost' IDENTIFIED BY 's3cur3s0c';
GRANT file,reload ON *.* TO 'demonio'@'localhost';
GRANT ALL PRIVILEGES ON NetPivot.* TO 'demonio'@'localhost';
-- GRANT create,delete,insert,select,update ON NetPivot.* TO 'demonio'@'localhost';

