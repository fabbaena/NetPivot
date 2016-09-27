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

\echo '>>> Create f5_json_id_seq sequence'
DO $$ 
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_class c 
        JOIN pg_namespace n ON n.oid = c.relnamespace 
        WHERE c.relname='f5_json_id_seq' AND n.nspname = 'public') 
    THEN 
        CREATE SEQUENCE f5_json_id_seq 
            START WITH 1 
            INCREMENT BY 1 
            NO MINVALUE 
            NO MAXVALUE 
            CACHE 1; 
    END IF;
END$$;

\echo '>>> Change owner of f5_json_id_seq to demonio'
ALTER TABLE f5_json_id_seq OWNER TO demonio;

\echo '>>> Assign sequence f5_json_id_seq to column id of table f5_attributes_json'
ALTER SEQUENCE f5_json_id_seq OWNED BY f5_attributes_json.id;
ALTER TABLE ONLY f5_attributes_json ALTER COLUMN id SET DEFAULT nextval('f5_json_id_seq'::regclass);

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
END$$;

\echo '>>> Change owner of f5_stats_features_id_seq to demonio'
ALTER TABLE f5_stats_features_id_seq OWNER TO demonio;

\echo '>>> Assign sequence f5_stats_features_id_seq to column id of table f5_stats_features'
ALTER SEQUENCE f5_stats_features_id_seq OWNED BY f5_stats_features.id;
ALTER TABLE ONLY f5_stats_features ALTER COLUMN id SET DEFAULT nextval('f5_stats_features_id_seq'::regclass);

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
END$$;

\echo '>>> Change owner of f5_stats_modules_id_seq to demonio'
ALTER TABLE f5_stats_modules_id_seq OWNER TO demonio;

\echo '>>> Assign sequence f5_stats_modules_id_seq to column id of table f5_stats_modules'
ALTER SEQUENCE f5_stats_modules_id_seq OWNED BY f5_stats_modules.id;
ALTER TABLE ONLY f5_stats_modules ALTER COLUMN id SET DEFAULT nextval('f5_stats_modules_id_seq'::regclass);

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

\echo '>>> Create user_domains_id_seq sequence'
DO $$ 
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_class c 
        JOIN pg_namespace n ON n.oid = c.relnamespace 
        WHERE c.relname='user_domains_id_seq' AND n.nspname = 'public') 
    THEN 
        CREATE SEQUENCE user_domains_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;
            END IF;
END$$;

\echo '>>> Change owner of user_domains_id_seq to demonio'
ALTER TABLE user_domains_id_seq OWNER TO demonio;

\echo '>>> Assign sequence user_domains_id_seq to column id of table domains'
ALTER SEQUENCE user_domains_id_seq OWNED BY domains.id;
ALTER TABLE ONLY domains ALTER COLUMN id SET DEFAULT nextval('user_domains_id_seq'::regclass);


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

