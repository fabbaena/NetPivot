-- This UPDATES the Layout of an already deployed DB with Data.
-- USE WITH CARE, please backup DB before running this.

CREATE SEQUENCE f5_snat_translation_json_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE f5_snat_translation_json_id_seq OWNER TO demonio;

CREATE TABLE f5_snat_translation_json (
    id bigint DEFAULT nextval('f5_snat_translation_json_id_seq'::regclass) NOT NULL,
    files_uuid uuid NOT NULL,
    name character varying(255) NOT NULL,
    adminpart character varying(255) NOT NULL,
    attributes jsonb NOT NULL
);


ALTER TABLE f5_snat_translation_json OWNER TO demonio;
ALTER TABLE ONLY f5_snat_translation_json
    ADD CONSTRAINT f5_snat_translation_json_pkey PRIMARY KEY (id);


CREATE INDEX f5_snat_translation_json_files_uuid_idx ON f5_snat_translation_json USING hash (files_uuid);
CREATE UNIQUE INDEX f5_snat_translation_json_name_files_uuid_idx ON f5_snat_translation_json USING btree (name, files_uuid);
CREATE INDEX f5_snat_translation_json_name_idx ON f5_snat_translation_json USING btree (name);

ALTER TABLE ONLY f5_snat_translation_json
    ADD CONSTRAINT f5_snat_translation_files_uuid_fkey FOREIGN KEY (files_uuid) REFERENCES files(uuid) ON UPDATE CASCADE ON DELETE CASCADE;

CREATE SEQUENCE f5_snatpool_json_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE f5_snatpool_json_id_seq OWNER TO demonio;

CREATE TABLE f5_snatpool_json (
    id bigint DEFAULT nextval('f5_snatpool_json_id_seq'::regclass) NOT NULL,
    files_uuid uuid NOT NULL,
    name character varying(255) NOT NULL,
    adminpart character varying(255) NOT NULL,
    attributes jsonb NOT NULL
);


ALTER TABLE f5_snatpool_json OWNER TO demonio;
ALTER TABLE ONLY f5_snatpool_json
    ADD CONSTRAINT f5_snatpool_json_pkey PRIMARY KEY (id);

CREATE INDEX f5_snatpool_json_files_uuid_idx ON f5_snatpool_json USING hash (files_uuid);
CREATE UNIQUE INDEX f5_snatpool_json_name_files_uuid_idx ON f5_snatpool_json USING btree (name, files_uuid);
CREATE INDEX f5_snatpool_json_name_idx ON f5_snatpool_json USING btree (name);

ALTER TABLE ONLY f5_snatpool_json
    ADD CONSTRAINT f5_snatpool_json_files_uuid_fkey FOREIGN KEY (files_uuid) REFERENCES files(uuid) ON UPDATE CASCADE ON DELETE CASCADE;
