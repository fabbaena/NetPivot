-- This UPDATES the Layout of an already deployed DB with Data.
-- USE WITH CARE, please backup DB before running this.

LOCK TABLES
    NetPivot.details WRITE;

ALTER TABLE NetPivot.details
    MODIFY converted BOOLEAN NOT NULL AFTER attribute,
    MODIFY omitted BOOLEAN NOT NULL AFTER converted,
    MODIFY line MEDIUMINT UNSIGNED NOT NULL AFTER omitted;

UNLOCK TABLES;

CREATE TABLE IF NOT EXISTS NetPivot.modules (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    files_uuid VARCHAR(36) NOT NULL,
    INDEX files_uuid_idx USING BTREE (files_uuid),
    UNIQUE KEY files_uuid_name_idx USING BTREE (files_uuid,name),
    CONSTRAINT fk_modules_files
        FOREIGN KEY(files_uuid) REFERENCES NetPivot.files(uuid)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS NetPivot.obj_grps (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    obj_component VARCHAR(255) NULL,
    module_id INT UNSIGNED NOT NULL,
    INDEX module_id_idx USING BTREE (module_id),
    UNIQUE KEY name_module_id_idx USING BTREE (name,module_id),
    CONSTRAINT fk_obj_grps_modules
        FOREIGN KEY(module_id) REFERENCES NetPivot.modules(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS NetPivot.obj_names (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    line INT UNSIGNED NOT NULL,
    obj_grp_id INT UNSIGNED NOT NULL,
    INDEX obj_grp_id_idx USING BTREE (obj_grp_id),
    UNIQUE KEY name_obj_grp_id USING BTREE (name,obj_grp_id),
    CONSTRAINT fk_obj_names_obj_grps
        FOREIGN KEY(obj_grp_id) REFERENCES NetPivot.obj_grps(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS NetPivot.attributes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    converted BOOLEAN NOT NULL,
    omitted BOOLEAN NOT NULL,
    line MEDIUMINT UNSIGNED NULL,
    obj_name_id INT UNSIGNED NOT NULL,
    INDEX obj_name_id_idx USING BTREE (obj_name_id),
    INDEX name_obj_name_idx USING BTREE (name,obj_name_id),
    CONSTRAINT fk_attributes_obj_names
       FOREIGN KEY(obj_name_id) REFERENCES NetPivot.obj_names(id)
           ON DELETE CASCADE
           ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE VIEW NetPivot.obj_names_view AS
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

CREATE VIEW NetPivot.obj_grps_view AS
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

CREATE VIEW NetPivot.modules_view AS
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

DELIMITER $$
CREATE TRIGGER NetPivot.new_detail_record BEFORE INSERT ON NetPivot.details FOR EACH ROW
BEGIN
    SET @module_id = (SELECT id FROM NetPivot.modules WHERE name = NEW.module AND files_uuid = NEW.files_uuid);
    IF (@module_id IS NULL) THEN
       INSERT INTO NetPivot.modules(name,files_uuid) VALUES (NEW.module,NEW.files_uuid);
       SET @module_id = (SELECT id FROM NetPivot.modules WHERE name = NEW.module AND files_uuid = NEW.files_uuid);
    END IF;
    SET @obj_grp_id = (SELECT id FROM NetPivot.obj_grps WHERE name = NEW.obj_grp AND module_id = @module_id);
    IF (@obj_grp_id IS NULL) THEN
       INSERT INTO NetPivot.obj_grps(name,obj_component,module_id) VALUES (NEW.obj_grp,NEW.obj_component,@module_id);
       SET @obj_grp_id = (SELECT id FROM NetPivot.obj_grps WHERE name = NEW.obj_grp AND module_id = @module_id);
    END IF;
    IF ((NEW.attribute IS NOT NULL OR NEW.attribute <> "") AND (NEW.obj_name IS NULL OR NEW.obj_name = "")) THEN
       SET NEW.obj_name = '---';
    END IF;
    SET @obj_name_id = (SELECT id FROM NetPivot.obj_names WHERE name=NEW.obj_name AND obj_grp_id = @obj_grp_id);
    IF (@obj_name_id IS NULL) THEN
       INSERT INTO NetPivot.obj_names(name,line,obj_grp_id) VALUES (NEW.obj_name,NEW.line,@obj_grp_id);
       SET @obj_name_id = (SELECT id FROM NetPivot.obj_names WHERE name = NEW.obj_name AND obj_grp_id = @obj_grp_id);
    END IF;
    IF (NEW.attribute IS NOT NULL AND NEW.attribute <> "") THEN
       INSERT INTO NetPivot.attributes(name,converted,omitted,line,obj_name_id) VALUES (NEW.attribute,NEW.converted,NEW.omitted,NEW.line,@obj_name_id);
    END IF;
END $$
DELIMITER ;

REVOKE ALL PRIVILEGES ON *.* FROM 'demonio'@'localhost';
GRANT file,reload ON *.* TO 'demonio'@'localhost';
GRANT ALL PRIVILEGES ON NetPivot.* TO 'demonio'@'localhost';

