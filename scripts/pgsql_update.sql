-- This UPDATES the Layout of an already deployed DB with Data.
-- USE WITH CARE, please backup DB before running this.
\echo '>>> Create f5_attributes_json table'
CREATE TABLE IF NOT EXISTS f5_attributes_json (
    id integer NOT NULL,
    files_uuid uuid NOT NULL,
    objecttype character varying NOT NULL,
    feature character varying NOT NULL,
    module character varying NOT NULL,
    type character varying,
    name character varying,
    adminpart character varying,
    converted boolean,
    line numeric,
    attributes jsonb,
    conv_attr numeric,
    total_attr numeric,
    lineend numeric
);

\echo '>>> Change owner of f5_attributes_json to demonio'
ALTER TABLE f5_attributes_json OWNER TO demonio;

\echo '>>> Create f5_attributes_json_id_seq sequence'
\echo '>>> Change owner of f5_attributes_json_id_seq to demonio'
\echo '>>> Assign sequence f5_attributes_json_id_seq to column id of table f5_attributes_json'
DO $$ 
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_class c 
        JOIN pg_namespace n ON n.oid = c.relnamespace 
        WHERE c.relname='f5_attributes_json_id_seq' AND n.nspname = 'public') 
    THEN 
        CREATE SEQUENCE f5_attributes_json_id_seq 
            START WITH 1 
            INCREMENT BY 1 
            NO MINVALUE 
            NO MAXVALUE 
            CACHE 1; 
        ALTER TABLE f5_attributes_json_id_seq OWNER TO demonio;
        ALTER SEQUENCE f5_attributes_json_id_seq OWNED BY f5_attributes_json.id;
        ALTER TABLE ONLY f5_attributes_json ALTER COLUMN id SET DEFAULT nextval('f5_attributes_json_id_seq'::regclass);
    END IF;
END$$;


\echo '>>> Create f5_json_pkey primary key for table f5_attributes_json'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM information_schema.constraint_column_usage 
           WHERE table_name = 'f5_attributes_json'  
           AND constraint_name = 'f5_json_pkey') 
    THEN 
        ALTER TABLE ONLY f5_attributes_json
            ADD CONSTRAINT f5_json_pkey PRIMARY KEY (id);
    END IF;
END$$;

\echo '>>> Create f5_attributes_files_uuid_feature_module index on table f5_attributes_json'
CREATE INDEX IF NOT EXISTS f5_attributes_files_uuid_feature_module ON f5_attributes_json USING btree (files_uuid, feature, module);

\echo '>>> Create foreign key f5_json_files_fkey on table f5_attributes_json'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'f5_json_files_fkey' )
    THEN 
        ALTER TABLE ONLY f5_attributes_json
            ADD CONSTRAINT f5_json_files_fkey 
            FOREIGN KEY (files_uuid) 
            REFERENCES files(uuid) ON DELETE CASCADE;
    END IF;
END$$;


\echo '>>> Create f5_stats_features table'
CREATE TABLE IF NOT EXISTS f5_stats_features (
    id integer NOT NULL,
    files_uuid uuid NOT NULL,
    name character varying NOT NULL,
    objects numeric NOT NULL,
    attributes numeric NOT NULL,
    converted numeric NOT NULL,
    modules numeric NOT NULL
);

\echo '>>> Change owner of f5_stats_features to demonio'
ALTER TABLE f5_stats_features OWNER TO demonio;

\echo '>>> Create f5_stats_features_id_seq sequence'
\echo '>>> Change owner of f5_stats_features_id_seq to demonio'
\echo '>>> Assign sequence f5_stats_features_id_seq to column id of table f5_stats_features'
DO $$ 
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_class c 
        JOIN pg_namespace n ON n.oid = c.relnamespace 
        WHERE c.relname='f5_stats_features_id_seq' AND n.nspname = 'public') 
    THEN 
        CREATE SEQUENCE f5_stats_features_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;
            END IF;
        ALTER TABLE f5_stats_features_id_seq OWNER TO demonio;
        ALTER SEQUENCE f5_stats_features_id_seq OWNED BY f5_stats_features.id;
        ALTER TABLE ONLY f5_stats_features ALTER COLUMN id SET DEFAULT nextval('f5_stats_features_id_seq'::regclass);
END$$;



\echo '>>> Create f5_stats_features_files_uuid_feature unique constraint of table f5_stats_features'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'f5_stats_features_files_uuid_feature' )
    THEN 
        ALTER TABLE ONLY f5_stats_features
            ADD CONSTRAINT f5_stats_features_files_uuid_feature UNIQUE (files_uuid, name);
    END IF;
END$$;

\echo '>>> Create f5_stats_features_pkey primary key for table f5_stats_features'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM information_schema.constraint_column_usage 
           WHERE table_name = 'f5_stats_features'  
           AND constraint_name = 'f5_stats_features_pkey') 
    THEN 
        ALTER TABLE ONLY f5_stats_features
            ADD CONSTRAINT f5_stats_features_pkey PRIMARY KEY (id);
    END IF;
END$$;

\echo '>>> Create foreign key f5_stats_features_files_uuid_fkey on table f5_stats_features'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'f5_stats_features_files_uuid_fkey' )
    THEN 
        ALTER TABLE ONLY f5_stats_features
            ADD CONSTRAINT f5_stats_features_files_uuid_fkey FOREIGN KEY (files_uuid) REFERENCES files(uuid) ON DELETE CASCADE;
    END IF;
END$$;

\echo '>>> Create f5_stats_modules table'
CREATE TABLE IF NOT EXISTS f5_stats_modules (
    id integer NOT NULL,
    files_uuid uuid NOT NULL,
    feature_id integer NOT NULL,
    name character varying NOT NULL,
    objects numeric NOT NULL,
    attributes numeric NOT NULL,
    converted numeric NOT NULL
);

\echo '>>> Change owner of f5_stats_features to demonio'
ALTER TABLE f5_stats_modules OWNER TO demonio;

\echo '>>> Create f5_stats_modules_id_seq sequence'
\echo '>>> Change owner of f5_stats_modules_id_seq to demonio'
\echo '>>> Assign sequence f5_stats_modules_id_seq to column id of table f5_stats_modules'
DO $$ 
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_class c 
        JOIN pg_namespace n ON n.oid = c.relnamespace 
        WHERE c.relname='f5_stats_modules_id_seq' AND n.nspname = 'public') 
    THEN 
        CREATE SEQUENCE f5_stats_modules_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;
            END IF;
        ALTER TABLE f5_stats_modules_id_seq OWNER TO demonio;
        ALTER SEQUENCE f5_stats_modules_id_seq OWNED BY f5_stats_modules.id;
        ALTER TABLE ONLY f5_stats_modules ALTER COLUMN id SET DEFAULT nextval('f5_stats_modules_id_seq'::regclass);
END$$;


\echo '>>> Create f5_stats_modules_pkey primary key for table f5_stats_modules'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM information_schema.constraint_column_usage 
           WHERE table_name = 'f5_stats_modules'  
           AND constraint_name = 'f5_stats_modules_pkey') 
    THEN 
        ALTER TABLE ONLY f5_stats_modules
            ADD CONSTRAINT f5_stats_modules_pkey PRIMARY KEY (id);
    END IF;
END$$;

\echo '>>> Create f5_stats_modules_ukey unique constraint of table f5_stats_modules'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'f5_stats_modules_ukey' )
    THEN 
        ALTER TABLE ONLY f5_stats_modules
            ADD CONSTRAINT f5_stats_modules_ukey UNIQUE (files_uuid, feature_id, name);
    END IF;
END$$;

\echo '>>> Create foreign key f5_stats_modules_feature_id_fkey on table f5_stats_modules'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'f5_stats_modules_feature_id_fkey' )
    THEN 
        ALTER TABLE ONLY f5_stats_modules
            ADD CONSTRAINT f5_stats_modules_feature_id_fkey FOREIGN KEY (feature_id) REFERENCES f5_stats_features(id) ON DELETE CASCADE;
    END IF;
END$$;

\echo '>>> Create foreign key f5_stats_modules_files_uuid_fkey on table f5_stats_modules'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'f5_stats_modules_files_uuid_fkey' )
    THEN 
        ALTER TABLE ONLY f5_stats_modules
            ADD CONSTRAINT f5_stats_modules_files_uuid_fkey FOREIGN KEY (files_uuid) REFERENCES files(uuid) ON DELETE CASCADE;
    END IF;
END$$;

\echo '>> Create table domains'
CREATE TABLE IF NOT EXISTS domains (
    id integer NOT NULL,
    name character varying
);

\echo '>>> Change owner of domains to demonio'
ALTER TABLE domains OWNER TO demonio;

\echo '>>> Create domains_id_seq sequence'
\echo '>>> Change owner of domains_id_seq to demonio'
\echo '>>> Assign sequence domains_id_seq to column id of table domains'
DO $$ 
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_class c 
        JOIN pg_namespace n ON n.oid = c.relnamespace 
        WHERE c.relname='domains_id_seq' AND n.nspname = 'public') 
    THEN 
        CREATE SEQUENCE domains_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;
            END IF;
        ALTER TABLE user_domains_id_seq OWNER TO demonio;
        ALTER SEQUENCE domains_id_seq OWNED BY domains.id;
        ALTER TABLE ONLY domains ALTER COLUMN id SET DEFAULT nextval('domains_id_seq'::regclass);
END$$;


\echo '>>> Create domains_name_key unique constraint of table domain'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'domains_name_key' )
    THEN 
        ALTER TABLE ONLY domains
            ADD CONSTRAINT domains_name_key UNIQUE (name);
    END IF;
END$$;

\echo '>> Add email column to table users'
DO $$
BEGIN
    IF NOT EXISTS ( 
    	select 1 from information_schema.columns where table_name='users' and column_name='email')
    THEN 
        ALTER TABLE users 
	        ADD COLUMN email character varying;
    END IF;
END$$;

\echo '>> Add validation_string column to table users'
DO $$
BEGIN
    IF NOT EXISTS ( 
    	select 1 from information_schema.columns where table_name='users' and column_name='validation_string')
    THEN 
        ALTER TABLE users 
	        ADD COLUMN validation_string character varying;
    END IF;
END$$;

\echo '>> Add position column to table users'
DO $$
BEGIN
    IF NOT EXISTS ( 
    	select 1 from information_schema.columns where table_name='users' and column_name='position')
    THEN 
        ALTER TABLE users 
	        ADD COLUMN "position" character varying;
    END IF;
END$$;

\echo '>> Add company column to table users'
DO $$
BEGIN
    IF NOT EXISTS ( 
    	select 1 from information_schema.columns where table_name='users' and column_name='company')
    THEN 
        ALTER TABLE users 
	        ADD COLUMN company character varying;
    END IF;
END$$;

\echo '>> Add firstname column to table users'
DO $$
BEGIN
    IF NOT EXISTS ( 
    	select 1 from information_schema.columns where table_name='users' and column_name='firstname')
    THEN 
        ALTER TABLE users 
	        ADD COLUMN firstname character varying;
    END IF;
END$$;

\echo '>> Add lastname column to table users'
DO $$
BEGIN
    IF NOT EXISTS ( 
    	select 1 from information_schema.columns where table_name='users' and column_name='lastname')
    THEN 
        ALTER TABLE users 
	        ADD COLUMN lastname character varying;
    END IF;
END$$;

-- Added 20160930
\echo '>> rename column time_conversion to conversion_time on table conversions'
DO $$
BEGIN
    IF EXISTS ( 
        select 1 from information_schema.columns where table_name='conversions' and column_name='time_conversion')
    THEN 
        ALTER TABLE conversions 
            RENAME COLUMN time_conversion TO conversion_time ;
    END IF;
END$$;

\echo '>> rename column id_conversions to id on table conversions'
DO $$
BEGIN
    IF EXISTS ( 
        select 1 from information_schema.columns where table_name='conversions' and column_name='id_conversions')
    THEN 
        ALTER TABLE conversions 
            RENAME COLUMN id_conversions TO id ;
    END IF;
END$$;

\echo '>> Add json_file column to table conversions'
DO $$
BEGIN
    IF NOT EXISTS ( 
        select 1 from information_schema.columns where table_name='conversions' and column_name='json_file')
    THEN 
        ALTER TABLE conversions 
            ADD COLUMN json_file character varying;
    END IF;
END$$;

\echo '>> Add module_id column to table f5_attributes_json'
DO $$
BEGIN
    IF NOT EXISTS ( 
        select 1 from information_schema.columns where table_name='f5_attributes_json' and column_name='module_id')
    THEN 
        ALTER TABLE f5_attributes_json 
            ADD COLUMN module_id integer;
    END IF;
END$$;

\echo '>>> Create foreign key f5_attribute_module_id_fkey on table f5_attributes_json'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'f5_attribute_module_id_fkey' )
    THEN 
        ALTER TABLE ONLY f5_attributes_json
            ADD CONSTRAINT f5_attribute_module_id_fkey FOREIGN KEY (module_id) REFERENCES f5_stats_modules(id) ON UPDATE CASCADE ON DELETE CASCADE;
    END IF;
END$$;


\echo '>> Add conversion_id column to table f5_stats_features'
DO $$
BEGIN
    IF NOT EXISTS ( 
        select 1 from information_schema.columns where table_name='f5_stats_features' and column_name='conversion_id')
    THEN 
        ALTER TABLE f5_stats_features 
            ADD COLUMN conversion_id bigint;
    END IF;
END$$;

\echo '>>> Create foreign key f5_stats_features_conversion_id_fkey on table f5_stats_features'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'f5_stats_features_conversion_id_fkey' )
    THEN 
        ALTER TABLE ONLY f5_stats_features
            ADD CONSTRAINT f5_stats_features_conversion_id_fkey FOREIGN KEY (conversion_id) REFERENCES conversions(id) ON DELETE CASCADE;
    END IF;
END$$;


ALTER TABLE ONLY f5_stats_features
    ADD CONSTRAINT f5_stats_features_conversion_id FOREIGN KEY (conversion_id) REFERENCES conversions(id) ON DELETE CASCADE;


\echo '>>> Create users_email_ukey unique constraint of table users'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'users_email_ukey' )
    THEN 
        ALTER TABLE ONLY users
            ADD CONSTRAINT users_email_ukey UNIQUE (email);
    END IF;
END$$;

\echo '>>> DROP IF EXISTS view modules_view'
DO $$
BEGIN
    IF EXISTS (select 1 from information_schema.views where table_name='modules_view') 
    THEN
        RAISE NOTICE 'DROP VIEW modules_view';
        DROP VIEW modules_view;
    END IF;
END$$;



--- Remove old tables, sequences and views
\echo '>>> DROP IF EXISTS view obj_grps_view'
DO $$
BEGIN
    IF EXISTS (select 1 from information_schema.views where table_name='obj_grps_view') 
    THEN
        RAISE NOTICE 'DROP VIEW obj_grps_view';
        DROP VIEW obj_grps_view;
    END IF;
END$$;

\echo '>>> DROP IF EXISTS view obj_names_view'
DO $$
BEGIN
    IF EXISTS (select 1 from information_schema.views where table_name='obj_names_view') 
    THEN
        RAISE NOTICE 'DROP VIEW obj_names_view';
        DROP VIEW obj_names_view;
    END IF;
END$$;



DROP TABLE IF EXISTS details;
DROP TABLE IF EXISTS f5_monitor_json;
DROP TABLE IF EXISTS f5_node_json;
DROP TABLE IF EXISTS f5_persistence_json;
DROP TABLE IF EXISTS f5_pool_json;
DROP TABLE IF EXISTS f5_profile_json;
DROP TABLE IF EXISTS f5_snat_translation_json;
DROP TABLE IF EXISTS f5_snatpool_json;
DROP TABLE IF EXISTS f5_virtual_address_json;
DROP TABLE IF EXISTS f5_virtual_json;
DROP TABLE IF EXISTS attributes;
DROP TABLE IF EXISTS obj_names;
DROP TABLE IF EXISTS obj_grps;
DROP TABLE IF EXISTS modules;
DROP SEQUENCE IF EXISTS f5_snat_translation_json_id_seq;
DROP SEQUENCE IF EXISTS f5_snatpool_json_id_seq;
ALTER SEQUENCE f5_json_id_seq RENAME TO f5_attributes_json_id_seq;
ALTER SEQUENCE user_domains_id_seq RENAME TO domains_id_seq;

\echo '>>> Create table companies'
CREATE TABLE IF NOT EXISTS companies (
    id integer NOT NULL,
    name character varying NOT NULL,
    domain character varying NOT NULL
);

ALTER TABLE companies OWNER TO demonio;
DO $$ 
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_class c 
        JOIN pg_namespace n ON n.oid = c.relnamespace 
        WHERE c.relname='companies_id_seq' AND n.nspname = 'public') 
    THEN 
        CREATE SEQUENCE companies_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;
        INSERT INTO companies (id, name, domain) VALUES (0, 'NONE', 'NONE');
    END IF;
    ALTER TABLE companies_id_seq OWNER TO demonio;
    ALTER SEQUENCE companies_id_seq OWNED BY companies.id;
    ALTER TABLE ONLY companies ALTER COLUMN id SET DEFAULT nextval('companies_id_seq'::regclass);
END$$;

\echo '>>> Create companies_pkey primary key for table companies'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM information_schema.constraint_column_usage 
           WHERE table_name = 'companies'  
           AND constraint_name = 'companies_pkey') 
    THEN 
        ALTER TABLE ONLY companies
            ADD CONSTRAINT companies_pkey PRIMARY KEY (id);
    END IF;
END$$;


\echo '>> Add company_id column to table users'
DO $$
BEGIN
    IF NOT EXISTS ( 
        select 1 from information_schema.columns where table_name='users' and column_name='company_id')
    THEN 
        ALTER TABLE users 
            ADD COLUMN company_id integer;
        UPDATE users set company_id=0 WHERE company_id IS null;
        ALTER TABLE ONLY users
            ADD CONSTRAINT users_company_id_fkey FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE;
    END IF;
END$$;

\echo '>>> Creating Events table'
CREATE TABLE IF NOT EXISTS events (
    id integer NOT NULL,
    "timestamp" timestamp without time zone NOT NULL,
    company_id integer NOT NULL,
    user_id integer NOT NULL,
    event character varying NOT NULL,
    event_code integer
);

ALTER TABLE events OWNER TO demonio;
DO $$ 
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_class c 
        JOIN pg_namespace n ON n.oid = c.relnamespace 
        WHERE c.relname='events_id_seq' AND n.nspname = 'public') 
    THEN 
        CREATE SEQUENCE events_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;
    END IF;
    ALTER TABLE events_id_seq OWNER TO demonio;
    ALTER SEQUENCE events_id_seq OWNED BY events.id;
    ALTER TABLE ONLY events ALTER COLUMN id SET DEFAULT nextval('events_id_seq'::regclass);
END$$;

\echo '>>> Create events_pkey primary key for table events'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM information_schema.constraint_column_usage 
           WHERE table_name = 'events'  
           AND constraint_name = 'events_pkey') 
    THEN 
        ALTER TABLE ONLY events
            ADD CONSTRAINT events_pkey PRIMARY KEY (id);
    END IF;
END$$;

\echo '>>> Create foreign key events_company_id_fkey on table events'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'events_company_id_fkey' )
    THEN 
        ALTER TABLE ONLY events
            ADD CONSTRAINT events_company_id_fkey FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE;
    END IF;
END$$;

\echo '>>> Create foreign key events_user_id_fkey on table events'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'events_user_id_fkey' )
    THEN 
        ALTER TABLE ONLY events
            ADD CONSTRAINT events_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
    END IF;
END$$;


CREATE OR REPLACE VIEW events_full AS
 SELECT e.id AS event_id,
    e."timestamp",
    c.name AS company_name,
    c.id AS company_id,
    u.name AS user_name,
    (((u.firstname)::text || ' '::text) || (u.lastname)::text) AS user_fullname,
    u.id AS user_id,
    e.event,
    e.event_code
   FROM events e,
    companies c,
    users u
  WHERE ((e.company_id = c.id) AND (e.user_id = u.id));


ALTER TABLE events_full OWNER TO demonio;

\echo '>> Add opportunity_id column to table files'
DO $$
BEGIN
    IF NOT EXISTS ( 
        select 1 from information_schema.columns where table_name='files' and column_name='opportunity_id')
    THEN 
        ALTER TABLE files 
            ADD COLUMN opportunity_id character varying;
    END IF;
END$$;


\echo '>> Add size column to table files'
DO $$
BEGIN
    IF NOT EXISTS ( 
        select 1 from information_schema.columns where table_name='files' and column_name='size')
    THEN 
        ALTER TABLE files 
            ADD COLUMN size integer;
    END IF;
END$$;


\echo '>> Add feature_count column to table conversions'
DO $$
BEGIN
    IF NOT EXISTS ( 
        select 1 from information_schema.columns where table_name='conversions' and column_name='feature_count')
    THEN 
        ALTER TABLE conversions 
            ADD COLUMN feature_count integer;
    END IF;
END$$;

\echo '>> Add module_count column to table conversions'
DO $$
BEGIN
    IF NOT EXISTS ( 
        select 1 from information_schema.columns where table_name='conversions' and column_name='module_count')
    THEN 
        ALTER TABLE conversions 
            ADD COLUMN module_count integer;
    END IF;
END$$;


\echo '>> Add object_count column to table conversions'
DO $$
BEGIN
    IF NOT EXISTS ( 
        select 1 from information_schema.columns where table_name='conversions' and column_name='object_count')
    THEN 
        ALTER TABLE conversions 
            ADD COLUMN object_count integer;
    END IF;
END$$;

\echo '>> Add attribute_count column to table conversions'
DO $$
BEGIN
    IF NOT EXISTS ( 
        select 1 from information_schema.columns where table_name='conversions' and column_name='attribute_count')
    THEN 
        ALTER TABLE conversions 
            ADD COLUMN attribute_count integer;
    END IF;
END$$;


\echo '>> Add attribute_converted column to table conversions'
DO $$
BEGIN
    IF NOT EXISTS ( 
        select 1 from information_schema.columns where table_name='conversions' and column_name='attribute_converted')
    THEN 
        ALTER TABLE conversions 
            ADD COLUMN attribute_converted integer;
    END IF;
END$$;


\echo '>> Add np_version column to table conversions'
DO $$
BEGIN
    IF NOT EXISTS ( 
        select 1 from information_schema.columns where table_name='conversions' and column_name='np_version')
    THEN 
        ALTER TABLE conversions 
            ADD COLUMN np_version character varying;
    END IF;
END$$;

\echo '>> Add f5_version column to table conversions'
DO $$
BEGIN
    IF NOT EXISTS ( 
        select 1 from information_schema.columns where table_name='conversions' and column_name='f5_version')
    THEN 
        ALTER TABLE conversions 
            ADD COLUMN f5_version character varying;
    END IF;
END$$;


\echo '>> Add projectid column to table conversions'
DO $$
BEGIN
    IF NOT EXISTS ( 
        select 1 from information_schema.columns where table_name='conversions' and column_name='projectid')
    THEN 
        ALTER TABLE conversions 
            ADD COLUMN projectid integer;
    END IF;
END$$;

\echo '>> Add orphan column to table f5_attributes_json'
DO $$
BEGIN
    IF NOT EXISTS (
        select 1 from information_schema.columns where table_name='f5_attributes_json' and column_name='orphan')
    THEN
        ALTER TABLE f5_attributes_json
            ADD COLUMN orphan integer NOT NULL DEFAULT 0;
    END IF;
END$$;

\echo '>> Add orphan column to table f5_stats_modules'
DO $$
BEGIN
    IF NOT EXISTS (
        select 1 from information_schema.columns where table_name='f5_stats_modules' and column_name='orphan')
    THEN
        ALTER TABLE f5_stats_modules
            ADD COLUMN orphan integer NOT NULL DEFAULT 0;
    END IF;
END$$;
