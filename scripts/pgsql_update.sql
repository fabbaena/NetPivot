-- This UPDATES the Layout of an already deployed DB with Data.
-- USE WITH CARE, please backup DB before running this.
CREATE TABLE f5_attributes_json (
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


ALTER TABLE f5_attributes_json OWNER TO demonio;
CREATE SEQUENCE f5_json_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE f5_json_id_seq OWNER TO demonio;
ALTER SEQUENCE f5_json_id_seq OWNED BY f5_attributes_json.id;
ALTER TABLE ONLY f5_attributes_json ALTER COLUMN id SET DEFAULT nextval('f5_json_id_seq'::regclass);

ALTER TABLE ONLY f5_attributes_json
    ADD CONSTRAINT f5_json_pkey PRIMARY KEY (id);

CREATE INDEX f5_attributes_files_uuid_feature_module ON f5_attributes_json USING btree (files_uuid, feature, module);
ALTER TABLE ONLY f5_attributes_json
    ADD CONSTRAINT f5_json_files_fkey FOREIGN KEY (files_uuid) REFERENCES files(uuid) ON DELETE CASCADE;



CREATE TABLE f5_stats_features (
    id integer NOT NULL,
    files_uuid uuid NOT NULL,
    name character varying NOT NULL,
    objects numeric NOT NULL,
    attributes numeric NOT NULL,
    converted numeric NOT NULL,
    modules numeric NOT NULL
);

ALTER TABLE f5_stats_features OWNER TO demonio;
CREATE SEQUENCE f5_stats_features_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE f5_stats_features_id_seq OWNER TO demonio;
ALTER SEQUENCE f5_stats_features_id_seq OWNED BY f5_stats_features.id;
ALTER TABLE ONLY f5_stats_features ALTER COLUMN id SET DEFAULT nextval('f5_stats_features_id_seq'::regclass);
ALTER TABLE ONLY f5_stats_features
    ADD CONSTRAINT f5_stats_features_files_uuid_feature UNIQUE (files_uuid, name);
ALTER TABLE ONLY f5_stats_features
    ADD CONSTRAINT f5_stats_features_pkey PRIMARY KEY (id);
ALTER TABLE ONLY f5_stats_features
    ADD CONSTRAINT f5_stats_features_files_uuid_fkey FOREIGN KEY (files_uuid) REFERENCES files(uuid) ON DELETE CASCADE;


CREATE TABLE f5_stats_modules (
    id integer NOT NULL,
    files_uuid uuid NOT NULL,
    feature_id integer NOT NULL,
    name character varying NOT NULL,
    objects numeric NOT NULL,
    attributes numeric NOT NULL,
    converted numeric NOT NULL
);
ALTER TABLE f5_stats_modules OWNER TO demonio;
CREATE SEQUENCE f5_stats_modules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE f5_stats_modules_id_seq OWNER TO demonio;
ALTER SEQUENCE f5_stats_modules_id_seq OWNED BY f5_stats_modules.id;
ALTER TABLE ONLY f5_stats_modules ALTER COLUMN id SET DEFAULT nextval('f5_stats_modules_id_seq'::regclass);
ALTER TABLE ONLY f5_stats_modules
    ADD CONSTRAINT f5_stats_modules_pkey PRIMARY KEY (id);
ALTER TABLE ONLY f5_stats_modules
    ADD CONSTRAINT f5_stats_modules_ukey UNIQUE (files_uuid, feature_id, name);
ALTER TABLE ONLY f5_stats_modules
    ADD CONSTRAINT f5_stats_modules_feature_id_fkey FOREIGN KEY (feature_id) REFERENCES f5_stats_features(id) ON DELETE CASCADE;
ALTER TABLE ONLY f5_stats_modules
    ADD CONSTRAINT f5_stats_modules_files_uuid_fkey FOREIGN KEY (files_uuid) REFERENCES files(uuid) ON DELETE CASCADE;

