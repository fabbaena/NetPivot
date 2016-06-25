-- This UPDATES the Layout of an already deployed DB with Data.
-- USE WITH CARE, please backup DB before running this.

\c netpivot

CREATE TABLE IF NOT EXISTS roles (
    id SMALLSERIAL NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    starturl VARCHAR(255) NOT NULL
);
ALTER TABLE IF EXISTS roles OWNER TO demonio;
ALTER SEQUENCE IF EXISTS roles_id_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 32767 START WITH 1 CYCLE OWNER BY roles_id;
DROP INDEX IF EXISTS roles_name_idx CASCADE;
CREATE INDEX IF NOT EXISTS roles_name_idx ON roles USING BTREE (name);
COPY roles (id, name, starturl) FROM STDIN WITH FORMAT CSV;
1,'System Admin','admin/'
2,'Sales','sales/'
3,'Engineer','dashboard/'
\.

CREATE TABLE IF NOT EXISTS user_role (
    user_id SMALLINT NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE NO ACTION,
    role_id SMALLINT NOT NULL REFERENCES roles(id) ON DELETE CASCADE ON UPDATE NO ACTION,
);
ALTER TABLE IF EXISTS user_role OWNER TO demonio;
DROP INDEX IF EXISTS user_role_user_id_idx CASCADE;
DROP INDEX IF EXISTS user_role_role_id_idx CASCADE;
DROP INDEX IF EXISTS user_role_user_id_role_id_idx CASCADE;
CREATE INDEX IF NOT EXISTS user_role_user_id_idx ON user_role USING BTREE (user_id);
CREATE INDEX IF NOT EXISTS user_role_role_id_idx ON user_role USING BTREE (role_id);
CREATE UNIQUE INDEX IF NOT EXISTS user_role_user_id_role_id_idx ON user_role USING BTREE (user_id,role_id);
COPY user_role (user_id, role_id) FROM STDIN WITH FORMAT CSV;
1,1
1,2
1,3
\.

CREATE TABLE IF NOT EXISTS f5_monitor_json (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE NO ACTION,
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

CREATE TABLE IF NOT EXISTS f5_node_json (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE NO ACTION,
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

CREATE TABLE IF NOT EXISTS f5_persistence_json (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE NO ACTION,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    adminpart VARCHAR(255) NOT NULL,
    attributes JSONB NOT NULL,
);
ALTER TABLE IF EXISTS f5_persistence_json OWNER TO demonio;
ALTER SEQUENCE IF EXISTS f5_persistence_json_id_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START WITH 1 CYCLE OWNED BY f5_persistence_json.id;
DROP INDEX IF EXISTS f5_persistence_json_files_uuid_idx CASCADE;
DROP INDEX IF EXISTS f5_persistence_json_name_idx CASCADE;
DROP INDEX IF EXISTS f5_persistence_json_name_files_uuid_idx CASCADE;
CREATE INDEX IF NOT EXISTS f5_persistence_json_files_uuid_idx ON f5_persistence_json USING HASH (files_uuid);
CREATE INDEX IF NOT EXISTS f5_persistence_json_name_idx ON f5_persistence_json USING BTREE (name);
CREATE UNIQUE INDEX IF NOT EXISTS f5_persistence_json_name_files_uuid_idx ON f5_persistence_json USING BTREE (name,files_uuid);

CREATE TABLE IF NOT EXISTS f5_pool_json (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE NO ACTION,
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

CREATE TABLE IF NOT EXISTS f5_profile_json (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE NO ACTION,
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

CREATE TABLE IF NOT EXISTS f5_virtualaddress_json (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE NO ACTION,
    name VARCHAR(255) NOT NULL,
    adminpart VARCHAR(255) NOT NULL,
    attributes JSONB NOT NULL
);
ALTER TABLE IF EXISTS f5_virtualaddress_json OWNER TO demonio;
ALTER SEQUENCE IF EXISTS f5_virtualaddress_json_id_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START WITH 1 CYCLE OWNED BY f5_virtualaddress_json.id;
DROP INDEX IF EXISTS f5_virtualaddress_json_files_uuid_idx CASCADE;
DROP INDEX IF EXISTS f5_virtualaddress_json_name_idx CASCADE;
DROP INDEX IF EXISTS f5_virtualaddress_json_name_files_uuid_idx CASCADE;
CREATE INDEX IF NOT EXISTS f5_virtualaddress_json_files_uuid_idx ON f5_virtualaddress_json USING HASH (files_uuid);
CREATE INDEX IF NOT EXISTS f5_virtualaddress_json_name_idx ON f5_virtualaddress_json USING BTREE (name);
CREATE UNIQUE INDEX IF NOT EXISTS f5_virtualaddress_json_name_files_uuid_idx ON f5_virtualaddress_json USING BTREE (name,files_uuid);

CREATE TABLE IF NOT EXISTS f5_virtual_json (
    id BIGSERIAL NOT NULL PRIMARY KEY,
    files_uuid UUID NOT NULL REFERENCES files(uuid) ON DELETE CASCADE ON UPDATE NO ACTION,
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

