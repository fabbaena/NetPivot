

--------------
-- CUTOMERS -- 
--------------

\echo '>>> Create customers table'
CREATE TABLE IF NOT EXISTS customers (
    id integer NOT NULL,
    name character varying(100),
    phone character varying(45),
    createdate date,
    updatedate date,
    usercreate integer,
    userupdate integer,
    ip character varying(45)
);


ALTER TABLE customers OWNER TO demonio;


\echo '>>> Create customers_id_seq sequence'
\echo '>>> Change owner of customers_id_seq to demonio'
\echo '>>> Assign sequence customers_id_seq to column id of table customers'
DO $$ 
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_class c 
        JOIN pg_namespace n ON n.oid = c.relnamespace 
        WHERE c.relname='customers_id_seq' AND n.nspname = 'public') 
    THEN 
        CREATE SEQUENCE customers_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;
        ALTER TABLE customers_id_seq OWNER TO demonio;
        ALTER SEQUENCE customers_id_seq OWNED BY customers.id;
        ALTER TABLE ONLY customers ALTER COLUMN id SET DEFAULT nextval('customers_id_seq'::regclass);
    END IF;
END$$;

\echo '>>> Create customers_pkey primary key for table customers'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM information_schema.constraint_column_usage 
           WHERE table_name = 'customers'  
           AND constraint_name = 'customers_pkey') 
    THEN 
        ALTER TABLE ONLY customers
            ADD CONSTRAINT customers_pkey PRIMARY KEY (id);
    END IF;
END$$;


--------------
-- CONTACTS --
--------------

\echo '>>> Create contacts table'
CREATE TABLE IF NOT EXISTS contacts (
    id integer NOT NULL,
    name character varying(100),
    position character varying(100),
    phone character varying(100),
    createdate date,
    updatedate date,
    usercreate integer,
    userupdate integer,
    ip character varying(45),
    customerid integer NOT NULL
);

ALTER TABLE contacts OWNER TO demonio;

\echo '>>> Create contacts_contactid_seq sequence'
\echo '>>> Change owner of contacts_id_seq to demonio'
\echo '>>> Assign sequence contacts_id_seq to column id of table contacts'
DO $$ 
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_class c 
        JOIN pg_namespace n ON n.oid = c.relnamespace 
        WHERE c.relname='contacts_id_seq' AND n.nspname = 'public') 
    THEN 
        CREATE SEQUENCE contacts_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;
        ALTER TABLE contacts_id_seq OWNER TO demonio;
        ALTER SEQUENCE contacts_id_seq OWNED BY contacts.id;
        ALTER TABLE ONLY contacts ALTER COLUMN id SET DEFAULT nextval('contacts_id_seq'::regclass);
    END IF;
END$$;

\echo '>>> Create customers_pkey primary key for table customers'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM information_schema.constraint_column_usage 
           WHERE table_name = 'contacts'  
           AND constraint_name = 'contacts_pkey') 
    THEN 
        ALTER TABLE ONLY contacts
            ADD CONSTRAINT contacts_pkey PRIMARY KEY (id);
    END IF;
END$$;

CREATE INDEX IF NOT EXISTS fki_customerid ON contacts USING btree (customerid);

\echo '>>> Create foreign key contacts_customer_id_fkey on table contacts'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'contacts_customer_id_fkey' )
    THEN 
        ALTER TABLE ONLY contacts
            ADD CONSTRAINT contacts_customer_id_fkey 
            FOREIGN KEY (customerid) 
            REFERENCES customers(id) ON UPDATE CASCADE ON DELETE CASCADE;
    END IF;
END$$;



--------------
-- PROJECTS --
--------------

\echo '>>> Create projects table'
CREATE TABLE IF NOT EXISTS projects (
    id integer NOT NULL,
    name character varying(100),
    description character varying(500),
    customerid integer NOT NULL,
    usercreate integer,
    userupdate integer,
    createdate date,
    updatedate date,
    total integer,
    ip character varying(45),
    attachment character(100),
    opportunityid character varying(512)
);

ALTER TABLE projects OWNER TO demonio;

\echo '>>> Create projects_id_seq sequence'
\echo '>>> Change owner of projects_id_seq to demonio'
\echo '>>> Assign sequence projects_id_seq to column id of table projects'
DO $$ 
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_class c 
        JOIN pg_namespace n ON n.oid = c.relnamespace 
        WHERE c.relname='projects_id_seq' AND n.nspname = 'public') 
    THEN 
        CREATE SEQUENCE projects_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;
        ALTER TABLE projects_id_seq OWNER TO demonio;
        ALTER SEQUENCE projects_id_seq OWNED BY projects.id;
        ALTER TABLE ONLY projects ALTER COLUMN id SET DEFAULT nextval('projects_id_seq'::regclass);
    END IF;
END$$;

\echo '>>> Create projects_pkey primary key for table projects'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM information_schema.constraint_column_usage 
           WHERE table_name = 'projects'  
           AND constraint_name = 'projects_pkey_pkey') 
    THEN 
        ALTER TABLE ONLY projects
            ADD CONSTRAINT projects_pkey PRIMARY KEY (id);
    END IF;
END$$;

CREATE INDEX IF NOT EXISTS fki_projects_customerid ON projects USING btree (customerid);

\echo '>>> Create foreign key projects_customer_id_fkey on table projects'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'projects_customer_id_fkey' )
    THEN 
        ALTER TABLE ONLY projects
            ADD CONSTRAINT projects_customer_id_fkey 
            FOREIGN KEY (customerid) 
            REFERENCES customers(id) ON UPDATE CASCADE ON DELETE CASCADE;
    END IF;
END$$;

-----------------------
-- BILL OF MATERIALS --
-----------------------

\echo '>>> Create billofmaterials table'
CREATE TABLE IF NOT EXISTS billofmaterials (
    id integer NOT NULL,
    sku character varying(45),
    description character varying(500),
    quantity integer,
    price character varying(100),
    projectid integer NOT NULL
);

ALTER TABLE billofmaterials OWNER TO demonio;

\echo '>>> Create billofmaterials_id_seq sequence'
\echo '>>> Change owner of billofmaterials_id_seq to demonio'
\echo '>>> Assign sequence billofmaterials_id_seq to column id of table billofmaterials'
DO $$ 
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_class c 
        JOIN pg_namespace n ON n.oid = c.relnamespace 
        WHERE c.relname='billofmaterials_id_seq' AND n.nspname = 'public') 
    THEN 
        CREATE SEQUENCE billofmaterials_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;
        ALTER TABLE billofmaterials_id_seq OWNER TO demonio;
        ALTER SEQUENCE billofmaterials_id_seq OWNED BY billofmaterials.id;
        ALTER TABLE ONLY billofmaterials ALTER COLUMN id SET DEFAULT nextval('billofmaterials_id_seq'::regclass);
    END IF;
END$$;


\echo '>>> Create billofmaterials_pkey primary key for table billofmaterials'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM information_schema.constraint_column_usage 
           WHERE table_name = 'billofmaterials'  
           AND constraint_name = 'billofmaterials_pkey') 
    THEN 
        ALTER TABLE ONLY billofmaterials
            ADD CONSTRAINT billofmaterials_pkey PRIMARY KEY (id);
    END IF;
END$$;

CREATE INDEX fki_billofmaterials_projectid ON billofmaterials USING btree (projectid);

\echo '>>> Create foreign key billofmaterials_projects_id_fkey on table billofmaterials'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'billofmaterials_projects_id_fkey' )
    THEN 
        ALTER TABLE ONLY billofmaterials
            ADD CONSTRAINT billofmaterials_projects_id_fkey 
            FOREIGN KEY (projectid) 
            REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;
    END IF;
END$$;


--------------
-- TIPS --
--------------

\echo '>>> Create tips table'
CREATE TABLE IF NOT EXISTS tips (
    id integer NOT NULL,
    name character varying(100),
    description character varying(500),
    createdate date,
    updatedate date,
    usercreate integer,
    userupdate integer,
    ip character varying(45)
);

ALTER TABLE tips OWNER TO demonio;

\echo '>>> Create tips_id_seq sequence'
\echo '>>> Change owner of tips_id_seq to demonio'
\echo '>>> Assign sequence tips_id_seq to column id of table tips'
DO $$ 
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_class c 
        JOIN pg_namespace n ON n.oid = c.relnamespace 
        WHERE c.relname='tips_id_seq' AND n.nspname = 'public') 
    THEN 
        CREATE SEQUENCE tips_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;
        ALTER TABLE tips_id_seq OWNER TO demonio;
        ALTER SEQUENCE tips_id_seq OWNED BY tips.id;
        ALTER TABLE ONLY tips ALTER COLUMN id SET DEFAULT nextval('tips_id_seq'::regclass);
    END IF;
END$$;

\echo '>>> Create tips_pkey primary key for table tips'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM information_schema.constraint_column_usage 
           WHERE table_name = 'tips'  
           AND constraint_name = 'tips_pkey') 
    THEN 
        ALTER TABLE ONLY tips
            ADD CONSTRAINT tips_pkey PRIMARY KEY (id);
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


