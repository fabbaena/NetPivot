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


\echo '>>> Dropping and Creating table adc_hw'
DROP TABLE IF EXISTS adc_hw;

CREATE TABLE adc_hw (
    id integer NOT NULL,
    brand character varying NOT NULL,
    model character varying NOT NULL,
    type character varying NOT NULL,
    l7_req_per_sec numeric NOT NULL,
    l4_con_per_sec numeric,
    l4_http_req_per_sec numeric,
    l4_max_con numeric,
    l4_gbps numeric,
    l7_gpbs numeric NOT NULL,
    ssl_tps_incl numeric NOT NULL,
    ssl_tps_max numeric NOT NULL,
    ssl_gbps numeric NOT NULL,
    ssl_fips character varying NOT NULL,
    ssl_fips_tps numeric,
    ssl_fips_gbps numeric,
    ddos_syn_per_sec numeric,
    cmp_gbps_incl numeric,
    cmp_gbps_max numeric ,
    software_arch character varying,
    virtual_instances_incl numeric,
    virtual_instances_max numeric,
    proc_type character varying,
    proc_sock numeric,
    proc_cor_per_sock numeric,
    memory numeric,
    hd_cap numeric,
    hd_disks numeric,
    hd_type character varying,
    hd_raid character varying,
    eth_cu character varying,
    eth_1g_sfp character varying,
    eth_10g_sfp character varying,
    eth_40g_qsfp character varying,
    eth_100g_qsfp character varying,
    pow_supl character varying,
    pow_max_power numeric,
    pow_dc character varying,
    pow_typ_power numeric,
    pow_voltage character varying,
    pow_heat numeric,
    dim_h numeric,
    dim_w numeric,
    dim_d numeric,
    dim_u numeric,
    dim_weight numeric,
    op_temp character varying,
    op_hum character varying,
    safety character varying,
    suscept character varying,
    ns_type_map character varying,
    ns_model_map character varying
);

ALTER TABLE adc_hw OWNER TO demonio;
CREATE SEQUENCE adc_hw_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1; 
ALTER TABLE adc_hw_id_seq OWNER TO demonio;
ALTER SEQUENCE adc_hw_id_seq OWNED BY adc_hw.id;
ALTER TABLE ONLY adc_hw ALTER COLUMN id SET DEFAULT nextval('adc_hw_id_seq'::regclass);
ALTER TABLE ONLY adc_hw
    ADD CONSTRAINT adc_hw_id_pkey PRIMARY KEY (id);

COPY adc_hw (brand, model, type, l7_req_per_sec, l4_con_per_sec, l4_http_req_per_sec, l4_max_con, l4_gbps, l7_gpbs, ssl_tps_incl, ssl_tps_max, ssl_gbps, ssl_fips, ssl_fips_tps, ssl_fips_gbps, ddos_syn_per_sec, cmp_gbps_incl, cmp_gbps_max, software_arch, virtual_instances_incl, virtual_instances_max, proc_type, proc_sock, proc_cor_per_sock, memory, hd_cap, hd_disks, hd_type, hd_raid, eth_cu, eth_1g_sfp, eth_10g_sfp, eth_40g_qsfp, eth_100g_qsfp, pow_supl, pow_max_power, pow_dc, pow_typ_power, pow_voltage, pow_heat, dim_h, dim_w, dim_d, dim_u, dim_weight, op_temp, op_hum, safety, suscept, ns_type_map, ns_model_map) FROM stdin WITH NULL 'na';
NetScaler	25160T	MPX	4.6	na	na	na	na	160	2	2	21	No	na	na	na	14	14	64bit	na	na	na	na	na	128	na	na	na	na	0	0	32	0	na	2	1000	Yes	594	100-240VAC full range, 50-60 Hz	na	3.45	17.3	28.25	2	60	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	25100T	MPX	3.2	na	na	na	na	100	1.4	1.4	15	No	na	na	na	10	10	64bit	na	na	na	na	na	128	na	na	na	na	0	0	32	0	na	2	1000	Yes	594	100-240VAC full range, 50-60 Hz	na	3.45	17.3	28.25	2	60	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	25160TA	MPX	4.6	na	na	na	na	160	2	2	21	No	na	na	na	4	4	64bit	na	na	na	na	na	128	na	na	na	na	0	0	0	8	na	2	1000	Yes	594	100-240VAC full range, 50-60 Hz	na	3.45	17.3	28.25	2	60	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	25100TA	MPX	3.2	na	na	na	na	100	1.4	1.4	15	No	na	na	na	10	10	64bit	na	na	na	na	na	128	na	na	na	na	0	0	0	8	na	2	1000	Yes	594	100-240VAC full range, 50-60 Hz	na	3.45	17.3	28.25	2	60	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	25160A	MPX	4	na	na	na	na	160	69	69	42	No	na	na	na	14	14	64bit	na	na	na	na	na	256	na	na	na	na	0	0	0	8	na	2	822	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	25100A	MPX	3	na	na	na	na	100	43	43	34	No	na	na	na	10	10	64bit	na	na	na	na	na	256	na	na	na	na	0	0	0	8	na	2	822	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	25160-40G	MPX	4	na	na	na	na	160	69	69	43	No	na	na	na	14	14	64bit	na	na	na	na	na	256	na	na	na	na	0	0	0	4	na	2	822	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	25160-40G	SDX	4	na	na	na	na	160	69	69	43	No	na	na	na	14	14	64bit	70	115	na	na	na	256	na	na	na	na	0	0	0	4	na	2	822	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	25100-40G	MPX	3	na	na	na	na	100	43	43	34	No	na	na	na	10	10	64bit	na	na	na	na	na	256	na	na	na	na	0	0	0	4	na	2	822	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	25100-40G	SDX	3	na	na	na	na	100	43	43	34	No	na	na	na	10	10	64bit	20	115	na	na	na	256	na	na	na	na	0	0	0	4	na	2	822	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	24150	MPX	5.8	na	na	na	na	150	135	135	44	No	na	na	na	14.6	14.6	64bit	na	na	na	na	na	256	na	na	na	na	0	0	24	0	na	4	1100	Yes	850	100-240VAC full range, 50-60 Hz	2900	3.45	17.3	28.25	2	58	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	24150	SDX	5.8	na	na	na	na	150	135	135	44	No	na	na	na	14.6	14.6	64bit	80	80	na	na	na	256	na	na	na	na	0	0	24	0	na	4	1100	Yes	850	100-240VAC full range, 50-60 Hz	2900	3.45	17.3	28.25	2	58	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	24100	MPX	3.5	na	na	na	na	100	100	100	40	No	na	na	na	10	10	64bit	na	na	na	na	na	256	na	na	na	na	0	0	24	0	na	4	1100	Yes	850	100-240VAC full range, 50-60 Hz	2900	3.45	17.3	28.25	2	58	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	24100	SDX	3.5	na	na	na	na	100	100	100	40	No	na	na	na	10	10	64bit	40	80	na	na	na	256	na	na	na	na	0	0	24	0	na	4	1100	Yes	850	100-240VAC full range, 50-60 Hz	2900	3.45	17.3	28.25	2	58	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	22120	MPX	4.7	na	na	na	na	120	560	560	75	No	na	na	na	14	14	64bit	na	na	na	na	na	256	na	na	na	na	0	12	24	0	na	4	1100	Yes	850	100-240VAC full range, 50-60 Hz	2900	3.45	17.3	28.25	2	58	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	22120	SDX	3.7	na	na	na	na	120	155	155	56	No	na	na	na	12.8	12.8	64bit	80	80	na	na	na	256	na	na	na	na	0	12	24	0	na	4	1100	Yes	850	100-240VAC full range, 50-60 Hz	2900	3.45	17.3	28.25	2	58	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	22100	MPX	4.5	na	na	na	na	100	460	460	65	No	na	na	na	14	14	64bit	na	na	na	na	na	256	na	na	na	na	0	12	24	0	na	4	1100	Yes	850	100-240VAC full range, 50-60 Hz	2900	3.45	17.3	28.25	2	58	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	22100	SDX	3.7	na	na	na	na	100	130	130	56	No	na	na	na	12.8	12.8	64bit	80	80	na	na	na	256	na	na	na	na	0	12	24	0	na	4	1100	Yes	850	100-240VAC full range, 50-60 Hz	2900	3.45	17.3	28.25	2	58	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	22080	MPX	4	na	na	na	na	80	340	340	55	No	na	na	na	12.5	12.5	64bit	na	na	na	na	na	256	na	na	na	na	0	12	24	0	na	4	1100	Yes	850	100-240VAC full range, 50-60 Hz	2900	3.45	17.3	28.25	2	58	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	22080	SDX	3.7	na	na	na	na	80	80	80	30	No	na	na	na	12.5	12.5	64bit	80	80	na	na	na	256	na	na	na	na	0	12	24	0	na	4	1100	Yes	850	100-240VAC full range, 50-60 Hz	2900	3.45	17.3	28.25	2	58	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	22060	MPX	3.5	na	na	na	na	60	225	225	45	No	na	na	na	11	11	64bit	na	na	na	na	na	256	na	na	na	na	0	12	24	0	na	4	1100	Yes	850	100-240VAC full range, 50-60 Hz	2900	3.45	17.3	28.25	2	58	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	22060	SDX	3.5	na	na	na	na	60	65	65	22	No	na	na	na	11	11	64bit	80	80	na	na	na	256	na	na	na	na	0	12	24	0	na	4	1100	Yes	850	100-240VAC full range, 50-60 Hz	2900	3.45	17.3	28.25	2	58	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	22040	MPX	2.6	na	na	na	na	40	120	120	35	No	na	na	na	8	8	64bit	na	na	na	na	na	256	na	na	na	na	0	12	24	0	na	4	1100	Yes	850	100-240VAC full range, 50-60 Hz	2900	3.45	17.3	28.25	2	58	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	22040	SDX	2.6	na	na	na	na	40	40	40	18	No	na	na	na	8	8	64bit	20	80	na	na	na	256	na	na	na	na	0	12	24	0	na	4	1100	Yes	850	100-240VAC full range, 50-60 Hz	2900	3.45	17.3	28.25	2	58	0-40C	20%-80%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14100	MPX	3	na	na	na	na	100	69	69	46	No	na	na	na	9.4	9.4	64bit	na	na	na	na	na	256	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14100	SDX	3	na	na	na	na	100	69	69	46	No	na	na	na	9.4	9.4	64bit	25	25	na	na	na	256	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14080	MPX	2.8	na	na	na	na	80	69	69	43	No	na	na	na	8.5	8.5	64bit	na	na	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14080	SDX	2.8	na	na	na	na	80	69	69	43	No	na	na	na	8.5	8.5	64bit	25	25	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14060	MPX	2.4	na	na	na	na	60	69	69	40	No	na	na	na	7.7	7.7	64bit	na	na	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14060	SDX	2.4	na	na	na	na	60	69	69	40	No	na	na	na	7.7	7.7	64bit	25	25	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14040	MPX	2.4	na	na	na	na	40	42	42	34	No	na	na	na	6	6	64bit	na	na	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14040	SDX	2.4	na	na	na	na	40	42	42	34	No	na	na	na	6	6	64bit	20	25	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14030	MPX	1.7	na	na	na	na	30	30	30	23	No	na	na	na	5	5	64bit	na	na	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14030	SDX	1.7	na	na	na	na	30	30	30	23	No	na	na	na	5	5	64bit	10	25	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14020	MPX	1.8	na	na	na	na	20	22	22	21	No	na	na	na	4.4	4.4	64bit	na	na	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14020	SDX	1.8	na	na	na	na	20	22	22	21	No	na	na	na	4.4	4.4	64bit	5	25	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14100-40G	MPX	2.8	na	na	na	na	100	69	69	45	No	na	na	na	8.6	8.6	64bit	na	na	na	na	na	64	na	na	na	na	0	0	16	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14100-40G	SDX	2.8	na	na	na	na	100	69	69	45	No	na	na	na	8.6	8.6	64bit	25	25	na	na	na	64	na	na	na	na	0	0	16	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14080-40G	MPX	2.8	na	na	na	na	80	69	69	43	No	na	na	na	8.5	8.5	64bit	na	na	na	na	na	64	na	na	na	na	0	0	16	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14080-40G	SDX	2.8	na	na	na	na	80	69	69	43	No	na	na	na	8.5	8.5	64bit	25	25	na	na	na	64	na	na	na	na	0	0	16	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14060-40G	MPX	2.4	na	na	na	na	60	69	69	40	No	na	na	na	7.7	7.7	64bit	na	na	na	na	na	64	na	na	na	na	0	0	16	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14060-40G	SDX	2.4	na	na	na	na	60	69	69	40	No	na	na	na	7.7	7.7	64bit	25	25	na	na	na	64	na	na	na	na	0	0	16	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14040-40G	MPX	2.4	na	na	na	na	40	42	42	34	No	na	na	na	6	6	64bit	na	na	na	na	na	64	na	na	na	na	0	0	16	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14040-40G	SDX	2.4	na	na	na	na	40	42	42	34	No	na	na	na	6	6	64bit	20	25	na	na	na	64	na	na	na	na	0	0	16	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14020-40G	MPX	1.4	na	na	na	na	20	22	22	21	No	na	na	na	4.4	4.4	64bit	na	na	na	na	na	64	na	na	na	na	0	0	16	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14020-40G	SDX	1.4	na	na	na	na	20	22	22	21	No	na	na	na	4.4	4.4	64bit	5	25	na	na	na	64	na	na	na	na	0	0	16	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14100-40S	MPX	2.9	na	na	na	na	100	275	275	59	No	na	na	na	10.5	10.5	64bit	na	na	na	na	na	64	na	na	na	na	0	0	8	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14100-40S	SDX	2.9	na	na	na	na	100	275	275	59	No	na	na	na	10.5	10.5	64bit	25	25	na	na	na	64	na	na	na	na	0	0	8	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14080-40S	MPX	2.8	na	na	na	na	80	207	207	54	No	na	na	na	8.6	8.6	64bit	na	na	na	na	na	64	na	na	na	na	0	0	8	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14080-40S	SDX	2.8	na	na	na	na	80	207	207	54	No	na	na	na	8.6	8.6	64bit	25	25	na	na	na	64	na	na	na	na	0	0	8	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14060-40S	MPX	2.7	na	na	na	na	60	172	172	49	No	na	na	na	8	8	64bit	na	na	na	na	na	64	na	na	na	na	0	0	8	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14060-40S	SDX	2.7	na	na	na	na	60	172	172	49	No	na	na	na	8	8	64bit	25	25	na	na	na	64	na	na	na	na	0	0	8	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14040-40S	MPX	2.4	na	na	na	na	40	103	103	43	No	na	na	na	7.5	7.5	64bit	na	na	na	na	na	64	na	na	na	na	0	0	8	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14040-40S	SDX	2.4	na	na	na	na	40	103	103	43	No	na	na	na	7.5	7.5	64bit	20	25	na	na	na	64	na	na	na	na	0	0	8	4	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	11542	MPX	2.7	na	na	na	na	42	69	69	20.5	No	na	na	na	8	8	64bit	na	na	na	na	na	48	na	na	na	na	0	4	8	0	na	2	650	Yes	500	100-240VAC full range, 50-60 Hz	1706	3.45	17.3	28.25	2	45	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	11542	SDX	2	na	na	na	na	42	69	69	20.5	No	na	na	na	7	7	64bit	20	20	na	na	na	48	na	na	na	na	0	4	8	0	na	2	650	Yes	500	100-240VAC full range, 50-60 Hz	1706	3.45	17.3	28.25	2	45	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	11540	MPX	2.5	na	na	na	na	40	43	43	19	No	na	na	na	7.5	7.5	64bit	na	na	na	na	na	48	na	na	na	na	0	4	8	0	na	2	650	Yes	500	100-240VAC full range, 50-60 Hz	1706	3.45	17.3	28.25	2	45	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	11540	SDX	2	na	na	na	na	40	43	43	19	No	na	na	na	7	7	64bit	20	20	na	na	na	48	na	na	na	na	0	4	8	0	na	2	650	Yes	500	100-240VAC full range, 50-60 Hz	1706	3.45	17.3	28.25	2	45	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	11530	MPX	2.2	na	na	na	na	30	30	30	17	No	na	na	na	6.5	6.5	64bit	na	na	na	na	na	48	na	na	na	na	0	4	8	0	na	2	650	Yes	500	100-240VAC full range, 50-60 Hz	1706	3.45	17.3	28.25	2	45	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	11530	SDX	2	na	na	na	na	30	30	30	17	No	na	na	na	6.5	6.5	64bit	20	20	na	na	na	48	na	na	na	na	0	4	8	0	na	2	650	Yes	500	100-240VAC full range, 50-60 Hz	1706	3.45	17.3	28.25	2	45	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	11520	MPX	1.8	na	na	na	na	20	25	25	15	No	na	na	na	6	6	64bit	na	na	na	na	na	48	na	na	na	na	0	4	8	0	na	2	650	Yes	500	100-240VAC full range, 50-60 Hz	1706	3.45	17.3	28.25	2	45	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	11520	SDX	1.8	na	na	na	na	20	25	25	15	No	na	na	na	6	6	64bit	20	20	na	na	na	48	na	na	na	na	0	4	8	0	na	2	650	Yes	500	100-240VAC full range, 50-60 Hz	1706	3.45	17.3	28.25	2	45	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	11515	MPX	1.6	na	na	na	na	15	22.5	22.5	14	No	na	na	na	5	5	64bit	na	na	na	na	na	48	na	na	na	na	0	4	8	0	na	2	650	Yes	500	100-240VAC full range, 50-60 Hz	1706	3.45	17.3	28.25	2	45	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	11515	SDX	1.6	na	na	na	na	15	22.5	22.5	14	No	na	na	na	5	5	64bit	5	20	na	na	na	48	na	na	na	na	0	4	8	0	na	2	650	Yes	500	100-240VAC full range, 50-60 Hz	1706	3.45	17.3	28.25	2	45	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14080 FIPS	MPX	2	na	na	na	na	80	33	33	9	Yes	33	9	na	6.8	6.8	64bit	na	na	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14080 FIPS	SDX	2	na	na	na	na	80	33	33	9	Yes	33	9	na	6.8	6.8	64bit	25	25	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14060 FIPS	MPX	1.8	na	na	na	na	60	25	25	7.5	Yes	25	7.5	na	6	6	64bit	na	na	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14060 FIPS	SDX	1.8	na	na	na	na	60	25	25	7.5	Yes	25	7.5	na	6	6	64bit	25	25	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14030 FIPS	MPX	1.4	na	na	na	na	30	18	18	6	Yes	18	6	na	5	5	64bit	na	na	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	14030 FIPS	SDX	1.4	na	na	na	na	30	18	18	6	Yes	18	6	na	5	5	64bit	10	25	na	na	na	64	na	na	na	na	0	0	16	0	na	2	528	Yes	300	100-240VAC full range, 50-60 Hz	1024	3.45	17.3	28.25	2	60	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	15500 FIPS	MPX	1.2	na	na	na	na	15	8	8	4.5	Yes	8	4.5	na	6.5	6.5	64bit	na	na	na	na	na	16	na	na	na	na	0	8	2	0	na	2	450	Yes	360	100-240VAC full range, 50-60 Hz	1229	3.45	17.3	28.25	2	30	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	12500 FIPS	MPX	0.7	na	na	na	na	10	6	6	3.6	Yes	6	3.6	na	6	6	64bit	na	na	na	na	na	16	na	na	na	na	0	8	2	0	na	2	450	Yes	360	100-240VAC full range, 50-60 Hz	1229	3.45	17.3	28.25	2	30	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	12500 FIPS	MPX	0.7	na	na	na	na	10	6	6	3.6	Yes	6	3.6	na	6	6	64bit	na	na	na	na	na	16	na	na	na	na	0	8	2	0	na	2	450	Yes	360	100-240VAC full range, 50-60 Hz	1229	3.45	17.3	28.25	2	30	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	10500 FIPS	MPX	0.5	na	na	na	na	6	4	4	2	Yes	4	2	na	5	5	64bit	na	na	na	na	na	16	na	na	na	na	0	8	2	0	na	2	450	Yes	360	100-240VAC full range, 50-60 Hz	1229	3.45	17.3	28.25	2	30	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	9700 FIPS	MPX	0.2	na	na	na	na	3	2	2	1	Yes	2	1	na	2	2	64bit	na	na	na	na	na	16	na	na	na	na	0	8	2	0	na	2	450	Yes	360	100-240VAC full range, 50-60 Hz	1229	3.45	17.3	28.25	2	30	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	8015	MPX	1.2	na	na	na	na	15	11	11	6	No	na	na	na	3.5	3.5	64bit	na	na	na	na	na	32	na	na	na	na	6	6	0	0	na	1 (2nd optional)	250	Yes	185	100-240VAC full range, 50-60 Hz	631	1.75	17.3	28.25	1	32	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	8015	SDX	1.2	na	na	na	na	15	11	11	6	No	na	na	na	3.5	3.5	64bit	2	5	na	na	na	32	na	na	na	na	6	6	0	0	na	1 (2nd optional)	250	Yes	185	100-240VAC full range, 50-60 Hz	631	1.75	17.3	28.25	1	32	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	8005	MPX	0.375	na	na	na	na	5	6.5	6.5	4	No	na	na	na	2.3	2.3	64bit	na	na	na	na	na	32	na	na	na	na	6	6	0	0	na	1 (2nd optional)	250	Yes	185	100-240VAC full range, 50-60 Hz	631	1.75	17.3	28.25	1	32	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	5650	MPX	0.35	na	na	na	na	5	2.8	2.8	2	No	na	na	na	3	3	64bit	na	na	na	na	na	8	na	na	na	na	6	0	0	0	na	1	180	Yes	135	100-240VAC full range, 50-60 Hz	461	1.75	17.3	28.25	1	32	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
NetScaler	5550	MPX	0.175	na	na	na	na	0.5	1.5	1.5	0.5	No	na	na	na	0.5	0.5	64bit	na	na	na	na	na	8	na	na	na	na	6	0	0	0	na	1	180	Yes	135	100-240VAC full range, 50-60 Hz	461	1.75	17.3	28.25	1	32	0-40C	5%-95%, non-condensing	CSA	FCC (Part 15 Class A), DoC, CE, VCCI, CNS, AN/NES	na	na
F5	12250v	BigIP	4	1.5	14	80	84	40	240	240	40	No	na	na	80	40	40	64bit	24	24	Intel Xeon	1	12	128	800	1	SSD	na	Optional SFP	Optional SFP (SX or LX)	16 SR or LR (2 SR included)	2 SR4	na	2	850	Yes	330	90–240 VAC +/- 10% auto switching, 50/60hz	1125	3.45	17.3	21.4	2	43	0-40C	10 to 90% @ 40º C	UL 60950-1 2nd Edition; CAN/CSA C22.2 No. 60950-1-07; EN 60950-1:2006, 2nd Edition; IEC 60950-1:2006, 2nd Edition; Evaluated to all CB Countries;	EEN 300 386 V1.5.1 (2010-10) EN 55022:2006 + A1:2007 EN 61000-3-2:2006 EN 61000-3-3:1995 + A1:2000 + A2:2005 EN 55024: 2010 USA FCC Class A VCCI Class A	SDX	14040-40G
F5	11050	BigIP	2.5	1	0	24	42	40	0.5	20	15	Option	9	15	na	0.05	12	64bit	na	na	Intel Xeon	2	6	32	600	2	10,000 RPM	1	Optional SFP	Optional SFP (SX or LX)	10 SR or LR (2 SR included)	na	na	2	850	Yes	440	90–240 VAC +/- 10% auto switching, 50/60 hz	1501	5.2	17.3	21.4	3	52	0-40C	5 to 85% at 40° C	UL 60950-1:2001, 1st Edition; CSA C22.2 No. 60950-1-03; IEC 60950-1: 2005, 2nd Edition; EN 60950-1: 2005, 2nd Edition	EN 55022:2006 + C1:2006; EN 55024:1998 + A1: 2001 + A2:2003; FCC Part 15B Class A; VCCI Class A; NEBS compliant (option) 	MPX	14080
F5	11000	BigIP	2.5	1	na	30	24	24	0.5	20	15	Option	9	15	na	0.05	16	64bit	na	na	Intel Xeon	2	6	48	600	2	10,000 RPM	1	Optional SFP	Optional SFP (SX or LX)	10 SR or LR (2 SR included)	na	na	2	850	Yes	440	90–240 VAC +/- 10% auto switching, 50/60 hz	1501	5.2	17.3	21.4	3	52	0-40C	5 to 85% at 40° C	UL 60950-1:2001, 1st Edition; CSA C22.2 No. 60950-1-03; IEC 60950-1: 2005, 2nd Edition; EN 60950-1: 2005, 2nd Edition	EN 55022:2006 + C1:2006; EN 55024:1998 + A1: 2001 + A2:2003; FCC Part 15B Class A; VCCI Class A	MPX	14020
F5	10350v	BigIP	3	1.2	14	80	84	40	42	42	24	No	na	na	80	24	24	64bit	0	20	Intel Xeon	1	20	128	800	1	SSD	na	Optional SFP	Optional SFP (SX or LX)	16 SR or LR (2 SR included)	2 SR4	na	2	850	Yes	320	90–240 VAC +/- 10% auto switching, 50/60 hz	1095	3.45	17.3	21.4	2	43	0-40C	5 to 85% at 40° C	UL 60950-1 2nd Edition; CAN/CSA C22.2 No. 60950-1-07; EN 60950-1:2006, 2nd Edition; IEC 60950-1:2006, 2nd Edition; Evaluated to all CB Countries	EEN 300 386 V1.5.1 (2010-10); EN 55022:2006 + A1:2007; EN 61000-3-2:2006; EN 61000-3-3:1995 + A1:2000 + A2:2005; EN 55024: 2010; USA FCC Class A; NEBS compliant; VCCI Class A	SDX	14040-40G
F5	10350v-F	BigIP	3	1.2	14	80	84	40	42	42	24	Yes	35	24	80	24	24	64bit	0	20	Intel Xeon	1	20	128	800	1	SSD	na	Optional SFP	Optional SFP (SX or LX)	16 SR or LR (2 SR included)	2 SR4	na	2	850	Yes	320	90–240 VAC +/- 10% auto switching, 50/60 hz	1095	3.45	17.3	21.4	2	43	0-40C	5 to 85% at 40° C	UL 60950-1 2nd Edition; CAN/CSA C22.2 No. 60950-1-07; EN 60950-1:2006, 2nd Edition; IEC 60950-1:2006, 2nd Edition; Evaluated to all CB Countries	EEN 300 386 V1.5.1 (2010-10); EN 55022:2006 + A1:2007; EN 61000-3-2:2006; EN 61000-3-3:1995 + A1:2000 + A2:2005; EN 55024: 2010; USA FCC Class A; NEBS compliant; VCCI Class A	SDX	14040-40G
F5	10255v	BigIP	2	1	14	36	80	40	42	42	22	Yes	9	22	80	24	24	64bit	12	12	Intel Xeon	2	6	48	400	2	SSD	1	Optional SFP	Optional SFP (SX or LX)	16 SR or LR (2 SR included)	2 SR4	na	2	850	Yes	320	90–240 VAC +/- 10% auto switching, 50/60hz	1090	3.45	17.3	21.4	2	43	0-40C	5 to 85% at 40° C	UL 60950-1 2nd Edition; CAN/CSA C22.2 No. 60950-1-07; EN 60950-1:2006, 2nd Edition; IEC 60950-1:2006, 2nd Edition; Evaluated to all CB Countries	EEN 300 386 V1.5.1 (2010-10); EN 55022:2006 + A1:2007 EN 61000-3-2:2006; EN 61000-3-3:1995 + A1:2000 + A2:2005 EN 55024: 2010; USA FCC Class A; VCCI Class A	SDX	14040-40G
F5	10250v	BigIP	2	1	14	36	80	40	42	42	22	Yes	9	22	80	24	24	64bit	12	12	Intel Xeon	2	6	48	400	1	SSD	na	Optional SFP	Optional SFP (SX or LX)	16 SR or LR (2 SR included)	2 SR4	na	2	850	Yes	320	90–240 VAC +/- 10% auto switching, 50/60hz	1090	3.45	17.3	21.4	2	43	0-40C	5 to 85% at 40° C	UL 60950-1 2nd Edition; CAN/CSA C22.2 No. 60950-1-07; EN 60950-1:2006, 2nd Edition; IEC 60950-1:2006, 2nd Edition; Evaluated to all CB Countries	EEN 300 386 V1.5.1 (2010-10); EN 55022:2006 + A1:2007 EN 61000-3-2:2006; EN 61000-3-3:1995 + A1:2000 + A2:2005 EN 55024: 2010; USA FCC Class A; VCCI Class A	SDX	14040-40G
F5	10200v-SSL	BigIP	2	1	14	36	80	40	42	75	33	Option	9	22	80	24	24	64bit	6	6	Intel Xeon	2	6	48	1000	2	10,000 RPM	1	Optional SFP	Optional SFP (SX or LX)	16 SR or LR (2 SR included)	2 SR4	na	2	850	Yes	320	90–240 VAC +/- 10% auto switching, 50/60hz	1090	3.45	17.3	21.4	2	43	0-40C	5 to 85% at 40° C	UL 60950-1 2nd Edition; CAN/CSA C22.2 No. 60950-1-07; EN 60950-1:2006, 2nd Edition; IEC 60950-1:2006, 2nd Edition; Evaluated to all CB Countries	EEN 300 386 V1.5.1 (2010-10); EN 55022:2006 + A1:2007 EN 61000-3-2:2006; EN 61000-3-3:1995 + A1:2000 + A2:2005 EN 55024: 2010; USA FCC Class A; VCCI Class A	SDX	14040-40G
F5	10055s	BigIP	1	0.5	7	36	80	40	21	21	22	No	na	na	40	12	12	64bit	na	na	Intel Xeon	2	6	48	400	2	SSD	1	Optional SFP	Optional SFP (SX or LX)	16 SR or LR (2 SR included)	2 SR4	na	2	850	Yes	320	90–240 VAC +/- 10% auto switching, 50/60hz	1090	3.45	17.3	21.4	2	43	0-40C	5 to 85% at 40° C	UL 60950-1 2nd Edition; CAN/CSA C22.2 No. 60950-1-07; EN 60950-1:2006, 2nd Edition; IEC 60950-1:2006, 2nd Edition; Evaluated to all CB Countries	EEN 300 386 V1.5.1 (2010-10); EN 55022:2006 + A1:2007; EN 61000-3-2:2006; EN 61000-3-3:1995 + A1:2000 + A2:2005; EN 55024: 2010; USA FCC Class A; VCCI Class A	MPX	14060
F5	10050s	BigIP	1	0.5	7	36	80	40	21	21	22	No	na	na	40	12	12	64bit	na	na	Intel Xeon	2	6	48	400	1	SSD	na	Optional SFP	Optional SFP (SX or LX)	16 SR or LR (2 SR included)	2 SR4	na	2	850	Yes	320	90–240 VAC +/- 10% auto switching, 50/60hz	1090	3.45	17.3	21.4	2	43	0-40C	5 to 85% at 40° C	UL 60950-1 2nd Edition; CAN/CSA C22.2 No. 60950-1-07; EN 60950-1:2006, 2nd Edition; IEC 60950-1:2006, 2nd Edition; Evaluated to all CB Countries	EEN 300 386 V1.5.1 (2010-10); EN 55022:2006 + A1:2007; EN 61000-3-2:2006; EN 61000-3-3:1995 + A1:2000 + A2:2005; EN 55024: 2010; USA FCC Class A; VCCI Class A	MPX	14060
F5	10000s	BigIP	1	0.5	7	36	80	40	21	21	22	No	na	na	40	12	12	64bit	na	na	Intel Xeon	2	6	48	400	2	SSD	1	Optional SFP	Optional SFP (SX or LX)	16 SR or LR (2 SR included)	2 SR4	na	2	850	Yes	320	90–240 VAC +/- 10% auto switching, 50/60hz	1090	3.45	17.3	21.4	2	43	0-40C	5 to 85% at 40° C	UL 60950-1 2nd Edition; CAN/CSA C22.2 No. 60950-1-07; EN 60950-1:2006, 2nd Edition; IEC 60950-1:2006, 2nd Edition; Evaluated to all CB Countries	EEN 300 386 V1.5.1 (2010-10); EN 55022:2006 + A1:2007; EN 61000-3-2:2006; EN 61000-3-3:1995 + A1:2000 + A2:2005; EN 55024: 2010; USA FCC Class A; VCCI Class A	MPX	14060
F5	7255v	BigIP	1.6	0.775	7	24	40	20	25	25	18	Yes	9	18	40	18	18	64bit	8	8	Intel Xeon	1	4	32	400	2	SSD	1	4	Optional SFP (SX, LX, or copper)	8 SR or LR (2 SR included)	na	na	2	400	Yes	205	90–240 VAC +/- 10% auto switching, 50/60hz	700	3.45	17.3	21.4	2	43	0-40C	5 to 85% at 40° C	ANSI/UL 60950-1-2011; CSA 60950-1-07, including Amendment 1:2011; Low Voltage Directive 2006/95/EC; CB Scheme; EN 60950-1:2006+A11:2009+A1:2010+A12:2011; IEC 60950-1:2005, A1:2009	EN 300 386 V1.5.1 (2010-10); EN 55022:2010; EN 61000-3-2:2006+A1:2009+A2:2009; EN 61000-3-3:2008; EN 55024:2010; EN 55022:2010; EN 61000-3-3:2008; EN 55024:2010; USA FCC Class A; VCCI Class A	SDX	14020
F5	7250v	BigIP	1.6	0.775	7	24	40	20	25	25	18	Yes	9	18	40	18	18	64bit	8	8	Intel Xeon	1	4	32	400	1	SSD	na	4	Optional SFP (SX, LX, or copper)	8 SR or LR (2 SR included)	na	na	2	400	Yes	205	90–240 VAC +/- 10% auto switching, 50/60hz	700	3.45	17.3	21.4	2	43	0-40C	5 to 85% at 40° C	ANSI/UL 60950-1-2011; CSA 60950-1-07, including Amendment 1:2011; Low Voltage Directive 2006/95/EC; CB Scheme; EN 60950-1:2006+A11:2009+A1:2010+A12:2011; IEC 60950-1:2005, A1:2009	EN 300 386 V1.5.1 (2010-10); EN 55022:2010; EN 61000-3-2:2006+A1:2009+A2:2009; EN 61000-3-3:2008; EN 55024:2010; EN 55022:2010; EN 61000-3-3:2008; EN 55024:2010; USA FCC Class A; VCCI Class A	SDX	14020
F5	7200v-SSL	BigIP	1.6	0.775	7	24	40	20	25	60	19	Option	9	18	40	18	18	64bit	4	4	Intel Xeon	1	4	32	1000	2	10,000 RPM	1	4	Optional SFP (SX, LX, or copper)	8 SR or LR (2 SR included)	na	na	2	400	Yes	205	90–240 VAC +/- 10% auto switching, 50/60hz	700	3.45	17.3	21.4	2	43	0-40C	5 to 85% at 40° C	ANSI/UL 60950-1-2011; CSA 60950-1-07, including Amendment 1:2011; Low Voltage Directive 2006/95/EC; CB Scheme; EN 60950-1:2006+A11:2009+A1:2010+A12:2011; IEC 60950-1:2005, A1:2009	EN 300 386 V1.5.1 (2010-10); EN 55022:2010; EN 61000-3-2:2006+A1:2009+A2:2009; EN 61000-3-3:2008; EN 55024:2010; EN 55022:2010; EN 61000-3-3:2008; EN 55024:2010; USA FCC Class A; VCCI Class A	SDX	14020
F5	7055s	BigIP	0.8	0.39	3.5	24	40	20	15	15	18	No	na	na	20	9	9	64bit	na	na	Intel Xeon	1	4	32	400	2	SSD	1	4	Optional SFP (SX, LX, or copper)	8 SR or LR (2 SR included)	na	na	2	400	Yes	205	90–240 VAC +/- 10% auto switching, 50/60hz	700	3.45	17.3	21.4	2	43	0-40C	5 to 85% at 40° C	ANSI/UL 60950-1-2011; CSA 60950-1-07, including Amendment 1:2011; Low Voltage Directive 2006/95/EC; CB Scheme; EN 60950-1:2006+A11:2009+A1:2010+A12:2011; IEC 60950-1:2005, A1:2009	EN 300 386 V1.5.1 (2010-10); EN 55022:2010; EN 61000-3-2:2006+A1:2009+A2:2009; EN 61000-3-3:2008; EN 55024:2010; EN 55022:2010; EN 61000-3-3:2008; EN 55024:2010; USA FCC Class A; VCCI Class A	MPX	14060
F5	7050s	BigIP	0.8	0.39	3.5	24	40	20	15	15	18	No	na	na	20	9	9	64bit	na	na	Intel Xeon	1	4	32	400	1	SSD	na	4	Optional SFP (SX, LX, or copper)	8 SR or LR (2 SR included)	na	na	2	400	Yes	205	90–240 VAC +/- 10% auto switching, 50/60hz	700	3.45	17.3	21.4	2	43	0-40C	5 to 85% at 40° C	ANSI/UL 60950-1-2011; CSA 60950-1-07, including Amendment 1:2011; Low Voltage Directive 2006/95/EC; CB Scheme; EN 60950-1:2006+A11:2009+A1:2010+A12:2011; IEC 60950-1:2005, A1:2009	EN 300 386 V1.5.1 (2010-10); EN 55022:2010; EN 61000-3-2:2006+A1:2009+A2:2009; EN 61000-3-3:2008; EN 55024:2010; EN 55022:2010; EN 61000-3-3:2008; EN 55024:2010; USA FCC Class A; VCCI Class A	MPX	14060
F5	7000s	BigIP	0.8	0.39	3.5	24	40	20	15	15	18	No	na	na	20	9	9	64bit	na	na	Intel Xeon	1	4	32	1000	2	10,000 RPM	1	4	Optional SFP (SX, LX, or copper)	8 SR or LR (2 SR included)	na	na	2	400	Yes	205	90–240 VAC +/- 10% auto switching, 50/60hz	700	3.45	17.3	21.4	2	43	0-40C	5 to 85% at 40° C	ANSI/UL 60950-1-2011; CSA 60950-1-07, including Amendment 1:2011; Low Voltage Directive 2006/95/EC; CB Scheme; EN 60950-1:2006+A11:2009+A1:2010+A12:2011; IEC 60950-1:2005, A1:2009	EN 300 386 V1.5.1 (2010-10); EN 55022:2010; EN 61000-3-2:2006+A1:2009+A2:2009; EN 61000-3-3:2008; EN 55024:2010; EN 55022:2010; EN 61000-3-3:2008; EN 55024:2010; USA FCC Class A; VCCI Class A	MPX	14060
F5	5250v	BigIP	1.5	0.7	7	24	30	15	21	21	12	Yes - No for virtual	5	12	40	12	12	64bit	8	8	Intel Xeon	1	4	32	400	1	SSD	na	4	Optional SFP (SX, LX, or copper)	8 SR or LR 	na	na	1 (2nd optional)	400	Yes	165	90-240 VAC +/- 10% auto switching, 50/60hz	564	1.75	17.3	21.4	1	21	0-40C	5 to 85% at 40° C	ANSI/UL 60950-1-2011; CSA 60950-1-07, including Amendment 1:2011; Low Voltage Directive 2006/95/EC; CB Scheme; EN 60950-1:2006+A11:2009+A1:2010+A12:2011; IEC 60950-1:2005, A1:2009	EN 300 386 V1.5.1 (2010-10); EN 55022:2010; EN 61000-3-2:2006+A1:2009+A2:2009; EN 61000-3-3:2008; EN 55024:2010; EN 55022:2010; EN 61000-3-3:2008; EN 55024:2010; USA FCC Class A; VCCI Class A	SDX	14020
F5	5200v	BigIP	1.5	0.7	7	24	30	15	21	21	12	Yes - No for virtual	5	12	40	12	12	64bit	4	4	Intel Xeon	1	4	32	1000	1	10,000 RPM	na	4	Optional SFP (SX, LX, or copper)	8 SR or LR	na	na	1 (2nd optional)	400	Yes	165	90-240 VAC +/- 10% auto switching, 50/60hz	564	1.75	17.3	21.4	1	21	0-40C	5 to 85% at 40° C	ANSI/UL 60950-1-2011; CSA 60950-1-07, including Amendment 1:2011; Low Voltage Directive 2006/95/EC; CB Scheme; EN 60950-1:2006+A11:2009+A1:2010+A12:2011; IEC 60950-1:2005, A1:2009	EN 300 386 V1.5.1 (2010-10); EN 55022:2010; EN 61000-3-2:2006+A1:2009+A2:2009; EN 61000-3-3:2008; EN 55024:2010; EN 55022:2010; EN 61000-3-3:2008; EN 55024:2010; USA FCC Class A; VCCI Class A	SDX	14020
F5	5050s	BigIP	0.75	0.35	3.5	24	30	15	10	10	12	No	na	na	20	6	6	64bit	na	na	Intel Xeon	1	4	32	400	1	SSD	na	4	Optional SFP (SX, LX, or copper)	8 SR or LR 	na	na	1 (2nd optional)	400	Yes	165	90-240 VAC +/- 10% auto switching, 50/60hz	564	1.75	17.3	21.4	1	21	0-40C	5 to 85% at 40° C	ANSI/UL 60950-1-2011; CSA 60950-1-07, including Amendment 1:2011; Low Voltage Directive 2006/95/EC; CB Scheme; EN 60950-1:2006+A11:2009+A1:2010+A12:2011; IEC 60950-1:2005, A1:200	EN 300 386 V1.5.1 (2010-10); EN 55022:2010; EN 61000-3-2:2006+A1:2009+A2:2009; EN 61000-3-3:2008; EN 55024:2010; EN 55022:2010; EN 61000-3-3:2008; EN 55024:2010; USA FCC Class A; VCCI Class A	MPX	14040
F5	5000s	BigIP	0.75	0.35	3.5	24	30	15	10	10	12	No	na	na	20	6	6	64bit	na	na	Intel Xeon	1	4	32	1000	1	10,000 RPM	na	4	Optional SFP (SX, LX, or copper)	8 SR or LR	na	na	1 (2nd optional)	400	Yes	165	90-240 VAC +/- 10% auto switching, 50/60hz	564	1.75	17.3	21.4	1	21	0-40C	5 to 85% at 40° C	ANSI/UL 60950-1-2011; CSA 60950-1-07, including Amendment 1:2011; Low Voltage Directive 2006/95/EC; CB Scheme; EN 60950-1:2006+A11:2009+A1:2010+A12:2011; IEC 60950-1:2005, A1:200	EN 300 386 V1.5.1 (2010-10); EN 55022:2010; EN 61000-3-2:2006+A1:2009+A2:2009; EN 61000-3-3:2008; EN 55024:2010; EN 55022:2010; EN 61000-3-3:2008; EN 55024:2010; USA FCC Class A; VCCI Class A	MPX	14040
F5	4200v	BigIP	0.85	0.3	2.5	10	10	10	9	9	8	No	na	na	na	8	8	64bit	na	na	Intel Xeon	1	4	16	500	1	10,000 RPM	na	8	Optional SFP (SX, LX, or copper)	2 SR or LR 	na	na	1 (2nd optional)	400	Yes	95	90-240 VAC +/- 10% auto switching, 50/60hz	324	1.75	17.3	21.4	1	20	0-40C	5 to 85% at 40° C	UL 60950-1 2nd Edition; CAN/CSA C22.2 No. 60950-1-07; EN 60950-1:2006, 2nd Edition; IEC 60950-1:2006, 2nd Edition; Evaluated to all CB Countries 	EN 300 386 V1.5.1 (2010-10); EN 55022:2006 + A1:2007; EN 61000-3-2:2006; EN 61000-3-3:1995 + A1:2000 + A2:2005; EN 55024:2010; USA-FCC Class A; VCCI Class A	SDX	11515
F5	4000s	BigIP	0.425	0.15	1.25	10	10	10	4.5	4.5	8	No	na	na	na	4	4	64bit	na	na	Intel Xeon	1	4	16	500	1	10,000 RPM	na	8	Optional SFP (SX, LX, or copper)	2 SR or LR	na	na	1 (2nd optional)	400	Yes	95	90-240 VAC +/- 10% auto switching, 50/60hz	324	1.75	17.3	21.4	1	20	0-40C	5 to 85% at 40° C	EN 60950-1:2006, 2nd Edition; IEC 60950-1:2006, 2nd Edition; Evaluated to all CB Countries; UL 60950-1 2nd Edition; CAN/CSA C22.2 No. 60950-1-07	EN 300 386 V1.5.1 (2010-10); EN 55022:2006 + A1:2007; EN 61000-3-2:2006; EN 61000-3-3:1995 + A1:2000 + A2:2005; EN 55024: 2010; USA FCC Class A; VCCI Class A	MPX	8015
F5	2200s	BigIP	0.425	0.15	1.1	5	5	5	4	4	4	No	na	na	na	4	4	64bit	na	na	Intel Xeon	1	2	8	500	1	10,000 RPM	na	8	Optional SFP (SX, LX, or copper)	2 SR or LR 	na	na	1 (2nd optional)	400	Yes	74	90–240 VAC +/- 10% auto switching, 50/60hz	252	1.75	17.3	21.4	1	20	0-40C	5 to 85% at 40° C	UL 60950-1 2nd Edition; CAN/CSA C22.2 No. 60950-1-07; EN 60950-1:2006, 2nd Edition; IEC 60950-1:2006, 2nd Edition; Evaluated to all CB Countries 	EN 300 386 V1.5.1 (2010-10); EN 55022:2006 + A1:2007; EN 61000-3-2:2006; EN 61000-3-3:1995 + A1:2000 + A2:2005; EN 55024: 2010; USA FCC Class A; VCCI Class A	MPX	8005
F5	2000s	BigIP	0.212	0.075	0.55	5	5	5	2	2	4	No	na	na	na	2.5	2.5	64bit	na	na	Intel Xeon	1	2	8	500	1	10,000 RPM	na	8	Optional SFP (SX, LX, or copper)	2 SR or LR 	na	na	1 (2nd optional)	400	Yes	74	90–240 VAC +/- 10% auto switching, 50/60hz	252	1.75	17.3	21.4	1	20	0-40C	5 to 85% at 40° C	UL 60950-1 2nd Edition; CAN/CSA C22.2 No. 60950-1-07; EN 60950-1:2006, 2nd Edition; IEC 60950-1:2006, 2nd Edition; Evaluated to all CB Countries 	EN 300 386 V1.5.1 (2010-10); EN 55022:2006 + A1:2007; EN 61000-3-2:2006; EN 61000-3-3:1995 + A1:2000 + A2:2005; EN 55024: 2010; USA FCC Class A; VCCI Class A	MPX	5650
F5	4450	Viprion Blade	5	2.9	na	180	140	140	160	160	80	 No	na	na	115	80	80	64bit	24	6	Intel Xeon	2	12	256	1200	1	SSD	na	na	na	na	6	2	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na	MPX	24150
F5	4340N	Viprion Blade	2	1.1	na	72	80	40	12	30	20	No	na	na	80	1.2	20	64bit	1	6	Intel Xeon	2	6	96	600	1	10,000 RPM	na	na	na	8	2	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na
F5	4300	Viprion Blade	2.5	1.4	na	36	80	40	12	30	20	No	na	na	80	1.2	20	64bit	1	6	Intel Xeon	2	6	48	600	1	10,000 RPM	na	na	na	8	2	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na	MPX	24150
F5	2250	Viprion Blade	2	1	14	48	155	80	10	44	36	No	na	na	60	1	40	64bit	1	20	Intel Xeon	1	10	64	800	1	SSD	na	1	na	na	4	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na	MPX	14080
F5	2150	Viprion Blade	1	0.4	7	24	40	18	4	10	9	No	na	na	40	0.4	10	64bit	1	8	Intel Xeon	1	4	32	400	1	SSD	na	1	na	8	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na	MPX	14080
F5	2100	Viprion Blade	1	0.4	7	12	40	18	4	10	9	No	na	na	40	0.4	10	64bit	1	4	Intel Xeon	1	4	16	300	1	10,000 RPM	na	1	na	8	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na	na