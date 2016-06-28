-- ROLE demonio
DROP DATABASE IF EXISTS netpivot;
DROP ROLE IF EXISTS demonio;
CREATE ROLE demonio ENCRYPTED PASSWORD 'md5f888bd8c43bc60c06e326e6cad9e4c5e' NOSUPERUSER NOCREATEDB NOCREATEROLE INHERIT LOGIN;

-- DATABASE netpivot
CREATE DATABASE netpivot WITH OWNER demonio ENCODING UTF8;
\c netpivot

-- DATA TYPE = boolint, it accepts 0 or 1
DROP DOMAIN IF EXISTS boolint CASCADE;
CREATE DOMAIN boolint AS SMALLINT DEFAULT 0 CHECK(VALUE >=0 AND VALUE < 2);
ALTER DOMAIN boolint OWNER TO demonio;

-- TABLE users
DROP TABLE IF EXISTS users CASCADE;
CREATE TABLE IF NOT EXISTS users (
    id SMALLSERIAL NOT NULL PRIMARY KEY,
    name VARCHAR(45) NOT NULL UNIQUE,
    password VARCHAR(60) NOT NULL,
    type VARCHAR(45) NULL,
    max_files SMALLINT NULL,
    max_conversions SMALLINT NULL
);
ALTER TABLE IF EXISTS users OWNER TO demonio;
ALTER SEQUENCE IF EXISTS users_id_seq INCREMENT BY 1 MINVALUE 10 MAXVALUE 32767 START WITH 10 CYCLE OWNED BY users.id;
INSERT INTO users(id,name,password,type,max_files,max_conversions) VALUES (1,'admin','$2y$10$G.TH1hSw9wQcQOTqZjIJNudYm1jfQIjxFthJBnbJhmSTJQrpiU2la','Administrator',100,100);

-- TABLE roles
DROP TABLE IF EXISTS roles CASCADE;
CREATE TABLE IF NOT EXISTS roles (
    id SMALLSERIAL NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    starturl VARCHAR(255) NOT NULL
);
ALTER TABLE IF EXISTS roles OWNER TO demonio;
ALTER SEQUENCE IF EXISTS roles_id_seq INCREMENT BY 1 MINVALUE 10 MAXVALUE 32767 START WITH 10 CYCLE OWNED BY roles.id;
DROP INDEX IF EXISTS roles_name_idx CASCADE;
CREATE INDEX IF NOT EXISTS roles_name_idx ON roles USING BTREE (name);
BEGIN;
INSERT INTO roles(id,name,starturl) VALUES (1,'System Admin','admin/');
INSERT INTO roles(id,name,starturl) VALUES (2,'Sales','sales/');
INSERT INTO roles(id,name,starturl) VALUES (3,'Engineer','dashboard/');
COMMIT;

-- TABLE user_role
DROP TABLE IF EXISTS user_role CASCADE;
CREATE TABLE IF NOT EXISTS user_role (
    user_id SMALLINT NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    role_id SMALLINT NOT NULL REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE
);
ALTER TABLE IF EXISTS user_role OWNER TO demonio;
DROP INDEX IF EXISTS user_role_user_id_idx CASCADE;
DROP INDEX IF EXISTS user_role_role_id_idx CASCADE;
DROP INDEX IF EXISTS user_role_user_id_role_id_idx CASCADE;
CREATE INDEX IF NOT EXISTS user_role_user_id_idx ON user_role USING BTREE (user_id);
CREATE INDEX IF NOT EXISTS user_role_role_id_idx ON user_role USING BTREE (role_id);
CREATE UNIQUE INDEX IF NOT EXISTS user_role_user_id_role_id_idx ON user_role USING BTREE (user_id,role_id);
BEGIN;
INSERT INTO user_role(user_id,role_id) VALUES (1,1);
INSERT INTO user_role(user_id,role_id) VALUES (1,2);
INSERT INTO user_role(user_id,role_id) VALUES (1,3);
COMMIT;

-- TABLE files
DROP TABLE IF EXISTS files CASCADE;
CREATE TABLE IF NOT EXISTS files (
    uuid UUID NOT NULL UNIQUE PRIMARY KEY,
    filename VARCHAR(255) NULL,
    project_name VARCHAR(64) NULL,
    upload_time TIMESTAMP(0) WITH TIME ZONE NULL,
    users_id SMALLINT NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
);
ALTER TABLE IF EXISTS files OWNER TO demonio;

-- TABLE settings
DROP TABLE IF EXISTS settings CASCADE;
CREATE TABLE IF NOT EXISTS settings (
    host_name SMALLINT NOT NULL PRIMARY KEY,
    timezone VARCHAR(45) NOT NULL DEFAULT 'US/Eastern',
    files_path VARCHAR(255) NULL
);
ALTER TABLE IF EXISTS settings OWNER TO demonio;

-- TABLE conversions
DROP TABLE IF EXISTS conversions CASCADE;
CREATE TABLE IF NOT EXISTS conversions (
    id_conversions BIGSERIAL NOT NULL UNIQUE PRIMARY KEY,
    users_id SMALLINT NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    time_conversion TIMESTAMP(0) WITH TIME ZONE NOT NULL,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE CASCADE,
    converted_file VARCHAR(255) NOT NULL,
    error_file VARCHAR(255) NULL,
    stats_file VARCHAR(255) NULL
);
ALTER TABLE IF EXISTS conversions OWNER TO demonio;
ALTER SEQUENCE IF EXISTS conversions_id_conversions_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START WITH 1 CYCLE OWNED BY conversions.id_conversions;

-- TABLE details
DROP TABLE IF EXISTS details CASCADE;
CREATE TABLE IF NOT EXISTS details (
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE CASCADE,
    module VARCHAR(16) NULL,
    obj_grp VARCHAR(32) NULL,
    obj_component VARCHAR(32) NULL,
    obj_name VARCHAR(160) NULL,
    attribute VARCHAR(128) NULL,
    converted boolint NOT NULL,
    omitted boolint NOT NULL,
    line INTEGER NOT NULL
);
ALTER TABLE IF EXISTS details OWNER TO demonio;
DROP INDEX IF EXISTS details_files_uuid_idx CASCADE;
CREATE INDEX IF NOT EXISTS details_files_uuid_idx ON details USING HASH (files_uuid);

-- TABLE modules
DROP TABLE IF EXISTS modules CASCADE;
CREATE TABLE IF NOT EXISTS modules (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE CASCADE
);
ALTER TABLE IF EXISTS modules OWNER TO demonio;
ALTER SEQUENCE IF EXISTS modules_id_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START WITH 1 CYCLE OWNED BY modules.id;
DROP INDEX IF EXISTS modules_files_uuid_idx CASCADE;
DROP INDEX IF EXISTS modules_files_uuid_name_idx CASCADE;
CREATE INDEX IF NOT EXISTS modules_files_uuid_idx ON modules USING HASH (files_uuid);
CREATE UNIQUE INDEX IF NOT EXISTS modules_files_uuid_name_idx ON modules USING BTREE (files_uuid,name);

-- TABLE obj_grps
DROP TABLE IF EXISTS obj_grps CASCADE;
CREATE TABLE IF NOT EXISTS obj_grps (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    obj_component VARCHAR(255) NULL,
    module_id BIGINT NOT NULL REFERENCES modules(id) ON DELETE CASCADE ON UPDATE CASCADE
);
ALTER TABLE IF EXISTS obj_grps OWNER TO demonio;
ALTER SEQUENCE IF EXISTS obj_grps_id_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START WITH 1 CYCLE OWNED BY obj_grps.id;
DROP INDEX IF EXISTS obj_grps_module_id_idx CASCADE;
DROP INDEX IF EXISTS obj_grps_name_module_id_idx CASCADE;
CREATE INDEX IF NOT EXISTS obj_grps_module_id_idx ON obj_grps USING BTREE (module_id);
CREATE UNIQUE INDEX IF NOT EXISTS obj_grps_name_module_id_idx ON obj_grps USING BTREE (name,module_id);

-- TABLE obj_names
DROP TABLE IF EXISTS obj_names CASCADE;
CREATE TABLE IF NOT EXISTS obj_names (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    line INTEGER NOT NULL,
    obj_grp_id BIGINT NOT NULL REFERENCES obj_grps(id) ON DELETE CASCADE ON UPDATE CASCADE
);
ALTER TABLE IF EXISTS obj_names OWNER TO demonio;
ALTER SEQUENCE IF EXISTS obj_names_id_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START WITH 1 CYCLE OWNED BY obj_names.id;
DROP INDEX IF EXISTS obj_names_obj_grp_id_idx CASCADE;
DROP INDEX IF EXISTS obj_names_name_obj_grp_id_idx CASCADE;
CREATE INDEX IF NOT EXISTS obj_names_obj_grp_id_idx ON obj_names USING BTREE (obj_grp_id);
CREATE UNIQUE INDEX IF NOT EXISTS obj_names_name_obj_grp_id_idx ON obj_names USING BTREE (name,obj_grp_id);

-- TABLE attributes
DROP TABLE IF EXISTS attributes CASCADE;
CREATE TABLE IF NOT EXISTS attributes (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    converted boolint NOT NULL,
    omitted boolint NOT NULL,
    line INTEGER NULL,
    obj_name_id BIGINT NOT NULL REFERENCES obj_names(id) ON DELETE CASCADE ON UPDATE CASCADE
);
ALTER TABLE IF EXISTS attributes OWNER TO demonio;
ALTER SEQUENCE IF EXISTS attributes_id_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START WITH 1 CYCLE OWNED BY attributes.id;
DROP INDEX IF EXISTS attributes_obj_name_id_idx CASCADE;
DROP INDEX IF EXISTS attributes_name_obj_name_id_idx CASCADE;
CREATE INDEX IF NOT EXISTS attributes_obj_name_id_idx ON attributes USING BTREE (obj_name_id);
CREATE INDEX IF NOT EXISTS attributes_name_obj_name_id_idx ON attributes USING BTREE (name,obj_name_id);

-- TABLE f5_monitor_json
DROP TABLE IF EXISTS f5_monitor_json CASCADE;
CREATE TABLE IF NOT EXISTS f5_monitor_json (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE CASCADE,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    adminpart VARCHAR(255) NOT NULL,
    attributes JSONB NOT NULL
);
ALTER TABLE IF EXISTS f5_monitor_json OWNER TO demonio;
ALTER SEQUENCE IF EXISTS f5_monitor_json_id_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START WITH 1 CYCLE OWNED BY f5_monitor_json.id;
DROP INDEX IF EXISTS f5_monitor_json_files_uuid_idx CASCADE;
DROP INDEX IF EXISTS f5_monitor_json_name_idx CASCADE;
DROP INDEX IF EXISTS f5_monitor_json_name_files_uuid_idx CASCADE;
CREATE INDEX IF NOT EXISTS f5_monitor_json_files_uuid_idx ON f5_monitor_json USING HASH (files_uuid);
CREATE INDEX IF NOT EXISTS f5_monitor_json_files_name_idx ON f5_monitor_json USING BTREE (name);
CREATE UNIQUE INDEX IF NOT EXISTS f5_monitor_json_name_files_uuid_idx ON f5_monitor_json USING BTREE (name,files_uuid);

-- TABLE f5_node_json
DROP TABLE IF EXISTS f5_node_json CASCADE;
CREATE TABLE IF NOT EXISTS f5_node_json (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE CASCADE,
    name VARCHAR(255) NOT NULL,
    adminpart VARCHAR(255) NOT NULL,
    attributes JSONB NOT NULL
);
ALTER TABLE IF EXISTS f5_node_json OWNER TO demonio;
ALTER SEQUENCE IF EXISTS f5_node_json_id_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START WITH 1 CYCLE OWNED BY f5_node_json.id;
DROP INDEX IF EXISTS f5_node_json_files_uuid_idx CASCADE;
DROP INDEX IF EXISTS f5_node_json_name_idx CASCADE;
DROP INDEX IF EXISTS f5_node_json_name_files_uuid_idx CASCADE;
CREATE INDEX IF NOT EXISTS f5_node_json_files_uuid_idx ON f5_node_json USING HASH (files_uuid);
CREATE INDEX IF NOT EXISTS f5_node_json_name_idx ON f5_node_json USING BTREE (name);
CREATE UNIQUE INDEX IF NOT EXISTS f5_node_json_name_files_uuid_idx ON f5_node_json USING BTREE (name,files_uuid);

-- TABLE f5_persistence_json
DROP TABLE IF EXISTS f5_persistence_json CASCADE;
CREATE TABLE IF NOT EXISTS f5_persistence_json (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE CASCADE,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    adminpart VARCHAR(255) NOT NULL,
    attributes JSONB NOT NULL
);
ALTER TABLE IF EXISTS f5_persistence_json OWNER TO demonio;
ALTER SEQUENCE IF EXISTS f5_persistence_json_id_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START WITH 1 CYCLE OWNED BY f5_persistence_json.id;
DROP INDEX IF EXISTS f5_persistence_json_files_uuid_idx CASCADE;
DROP INDEX IF EXISTS f5_persistence_json_name_idx CASCADE;
DROP INDEX IF EXISTS f5_persistence_json_name_files_uuid_idx CASCADE;
CREATE INDEX IF NOT EXISTS f5_persistence_json_files_uuid_idx ON f5_persistence_json USING HASH (files_uuid);
CREATE INDEX IF NOT EXISTS f5_persistence_json_name_idx ON f5_persistence_json USING BTREE (name);
CREATE UNIQUE INDEX IF NOT EXISTS f5_persistence_json_name_files_uuid_idx ON f5_persistence_json USING BTREE (name,files_uuid);

-- TABLE f5_pool_json
DROP TABLE IF EXISTS f5_pool_json CASCADE;
CREATE TABLE IF NOT EXISTS f5_pool_json (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE CASCADE,
    name VARCHAR(255) NOT NULL,
    adminpart VARCHAR(255) NOT NULL,
    attributes JSONB NOT NULL
);
ALTER TABLE IF EXISTS f5_pool_json OWNER TO demonio;
ALTER SEQUENCE IF EXISTS f5_pool_json_id_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START WITH 1 CYCLE OWNED BY f5_pool_json.id;
DROP INDEX IF EXISTS f5_pool_json_files_uuid_idx CASCADE;
DROP INDEX IF EXISTS f5_pool_json_name_idx CASCADE;
DROP INDEX IF EXISTS f5_pool_json_name_files_uuid_idx CASCADE;
CREATE INDEX IF NOT EXISTS f5_pool_json_files_uuid_idx ON f5_pool_json USING HASH (files_uuid);
CREATE INDEX IF NOT EXISTS f5_pool_json_name_idx ON f5_pool_json USING BTREE (name);
CREATE UNIQUE INDEX IF NOT EXISTS f5_pool_json_name_files_uuid_idx ON f5_pool_json USING BTREE(name,files_uuid);

-- TABLE f5_profile_json
DROP TABLE IF EXISTS f5_profile_json CASCADE;
CREATE TABLE IF NOT EXISTS f5_profile_json (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE CASCADE,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    adminpart VARCHAR(255) NOT NULL,
    attributes JSONB NOT NULL
);
ALTER TABLE IF EXISTS f5_profile_json OWNER TO demonio;
ALTER SEQUENCE IF EXISTS f5_profile_json_id_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START WITH 1 CYCLE OWNED BY f5_profile_json.id;
DROP INDEX IF EXISTS f5_profile_json_files_uuid_idx CASCADE;
DROP INDEX IF EXISTS f5_profile_json_name_idx CASCADE;
DROP INDEX IF EXISTS f5_profile_json_name_files_uuid_idx CASCADE;
CREATE INDEX IF NOT EXISTS f5_profile_json_files_uuid_idx ON f5_profile_json USING HASH (files_uuid);
CREATE INDEX IF NOT EXISTS f5_profile_json_name_idx ON f5_profile_json USING BTREE (name);
CREATE UNIQUE INDEX IF NOT EXISTS f5_profile_json_name_files_uuid_idx ON f5_profile_json USING BTREE (name,files_uuid);

-- TABLE f5_virtual_address_json
DROP TABLE IF EXISTS f5_virtual_address_json CASCADE;
CREATE TABLE IF NOT EXISTS f5_virtual_address_json (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE CASCADE,
    name VARCHAR(255) NOT NULL,
    adminpart VARCHAR(255) NOT NULL,
    attributes JSONB NOT NULL
);
ALTER TABLE IF EXISTS f5_virtual_address_json OWNER TO demonio;
ALTER SEQUENCE IF EXISTS f5_virtual_address_json_id_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START WITH 1 CYCLE OWNED BY f5_virtualaddress_json.id;
DROP INDEX IF EXISTS f5_virtual_address_json_files_uuid_idx CASCADE;
DROP INDEX IF EXISTS f5_virtual_address_json_name_idx CASCADE;
DROP INDEX IF EXISTS f5_virtual_address_json_name_files_uuid_idx CASCADE;
CREATE INDEX IF NOT EXISTS f5_virtual_address_json_files_uuid_idx ON f5_virtual_address_json USING HASH (files_uuid);
CREATE INDEX IF NOT EXISTS f5_virtual_address_json_name_idx ON f5_virtual_address_json USING BTREE (name);
CREATE UNIQUE INDEX IF NOT EXISTS f5_virtual_address_json_name_files_uuid_idx ON f5_virtual_address_json USING BTREE (name,files_uuid);

-- TABLE f5_virtual_json
DROP TABLE IF EXISTS f5_virtual_json CASCADE;
CREATE TABLE IF NOT EXISTS f5_virtual_json (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE CASCADE,
    name VARCHAR(255) NOT NULL,
    adminpart VARCHAR(255) NOT NULL,
    attributes JSONB NOT NULL
);
ALTER TABLE IF EXISTS f5_virtual_json OWNER TO demonio;
ALTER SEQUENCE IF EXISTS f5_virtual_json_id_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START WITH 1 CYCLE OWNED BY f5_virtual_json.id;
DROP INDEX IF EXISTS f5_virtual_json_files_uuid_idx CASCADE;
DROP INDEX IF EXISTS f5_virtual_json_name_idx CASCADE;
DROP INDEX IF EXISTS f5_virtual_json_name_files_uuid_idx CASCADE;
CREATE INDEX IF NOT EXISTS f5_virtual_json_files_uuid ON f5_virtual_json USING HASH (files_uuid);
CREATE INDEX IF NOT EXISTS f5_virtual_json_name_idx ON f5_virtual_json USING BTREE (name);
CREATE UNIQUE INDEX IF NOT EXISTS f5_virtual_json_name_files_uuid_idx ON f5_virtual_json USING BTREE (files_uuid,name);

-- VIEW user_role_view
CREATE OR REPLACE VIEW user_role_view AS
    SELECT users.name AS username,
	users.id AS userid,
	roles.name AS rolename,
	roles.id AS roleid,
	roles.starturl
    FROM users, roles, user_role
    WHERE ((users.id = user_role.user_id) AND (roles.id = user_role.role_id));
ALTER VIEW IF EXISTS user_role_view OWNER TO demonio;

-- VIEW obj_names_view
CREATE OR REPLACE VIEW obj_names_view AS
    SELECT
        obj_grp_id,
        id,
        name,
        line,
        (SELECT COUNT(*) FROM attributes WHERE obj_name_id = obj_names.id) AS attribute_count,
        (SELECT SUM(converted) FROM attributes WHERE obj_name_id = obj_names.id) AS attribute_converted,
        (SELECT SUM(omitted) FROM attributes WHERE obj_name_id = obj_names.id) AS attribute_omitted
    FROM
        obj_names;
ALTER VIEW IF EXISTS obj_names_view OWNER TO demonio;

-- VIEW obj_grps_view
CREATE OR REPLACE VIEW obj_grps_view AS
    SELECT
        module_id,
        id,
        name,
        (SELECT COUNT(*) FROM obj_names WHERE obj_grp_id = obj_grps.id) AS object_count,
        (SELECT SUM(attribute_count) FROM obj_names_view WHERE obj_grp_id = obj_grps.id) AS attribute_count,
        (SELECT SUM(attribute_converted) FROM obj_names_view WHERE obj_grp_id = obj_grps.id) AS attribute_converted,
        (SELECT SUM(attribute_omitted) FROM obj_names_view WHERE obj_grp_id = obj_grps.id) AS attribute_omitted
    FROM
        obj_grps;
ALTER VIEW IF EXISTS obj_grps_view OWNER TO demonio;

-- VIEW modules_view
CREATE OR REPLACE VIEW modules_view AS
    SELECT
	files_uuid,
	id,
	name,
	(SELECT COUNT(*) FROM obj_grps_view WHERE module_id = modules.id) AS objgrp_count,
	(SELECT SUM(object_count) FROM obj_grps_view WHERE module_id = modules.id) AS object_count,
	(SELECT SUM(attribute_count) FROM obj_grps_view WHERE module_id = modules.id) AS attribute_count,
	(SELECT SUM(attribute_converted) FROM obj_grps_view WHERE module_id = modules.id) AS attribute_converted,
	(SELECT SUM(attribute_omitted) FROM obj_grps_view WHERE module_id = modules.id) AS attribute_omitted
    FROM
	modules;
ALTER VIEW IF EXISTS modules_view OWNER TO demonio;

-- TRIGGER new_detail_record()
CREATE OR REPLACE FUNCTION new_detail_record() RETURNS TRIGGER AS $new_detail_record$
DECLARE
    var_module_id modules.id%TYPE;
    var_obj_grp_id obj_grps.id%TYPE;
    var_obj_name_id obj_names.id%TYPE;
    
BEGIN
    EXECUTE 'SELECT id FROM modules WHERE name = $1 AND files_uuid = $2' INTO var_module_id USING NEW.module, NEW.files_uuid;
    IF var_module_id IS NULL THEN
	INSERT INTO modules(name,files_uuid) VALUES (NEW.module,NEW.files_uuid);
	EXECUTE 'SELECT id FROM modules WHERE name = $1 AND files_uuid = $2' INTO var_module_id USING NEW.module, NEW.files_uuid;
    END IF;

    EXECUTE 'SELECT id FROM obj_grps WHERE name = $1 AND module_id = $2' INTO var_obj_grp_id USING NEW.obj_grp, var_module_id;
    IF var_obj_grp_id IS NULL THEN
	INSERT INTO obj_grps(name,obj_component,module_id) VALUES (NEW.obj_grp,NEW.obj_component,var_module_id);
	EXECUTE 'SELECT id FROM obj_grps WHERE name = $1 AND module_id = $2' INTO var_obj_grp_id USING NEW.obj_grp, var_module_id;
    END IF;

    IF ((NEW.attribute IS NOT NULL OR NEW.attribute <> '') AND (NEW.obj_name IS NULL OR NEW.obj_name = '')) THEN
	NEW.obj_name := '---';
    END IF;

    EXECUTE 'SELECT id FROM obj_names WHERE name = $1 AND obj_grp_id = $2' INTO var_obj_name_id USING NEW.obj_name, var_obj_grp_id;
    IF var_obj_name_id IS NULL THEN
	INSERT INTO obj_names(name,line,obj_grp_id) VALUES (NEW.obj_name,NEW.line,var_obj_grp_id);
	EXECUTE 'SELECT id FROM obj_names WHERE name = $1 AND obj_grp_id = $2' INTO var_obj_name_id USING NEW.obj_name, var_obj_grp_id;
    END IF;

    IF NEW.attribute IS NOT NULL AND NEW.attribute <> '' THEN
	INSERT INTO attributes(name,converted,omitted,line,obj_name_id) VALUES (NEW.attribute,NEW.converted,NEW.omitted,NEW.line,var_obj_name_id);
    END IF;

    RETURN NEW;
END;
$new_detail_record$ LANGUAGE plpgsql;
ALTER FUNCTION new_detail_record() OWNER TO demonio;
DROP TRIGGER IF EXISTS new_detail_record ON details CASCADE;
CREATE TRIGGER new_detail_record BEFORE INSERT ON details FOR EACH ROW EXECUTE PROCEDURE new_detail_record();

