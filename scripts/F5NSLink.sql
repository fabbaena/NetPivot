\echo '>>> Create f5nslink table'
CREATE TABLE IF NOT EXISTS f5nslink (
    f5 integer NOT NULL,
    ns integer NOT NULL,
    files_uuid uuid NOT NULL
);

\echo '>>> Change owner of f5nslink to demonio'
ALTER TABLE f5nslink OWNER TO demonio;

CREATE INDEX IF NOT EXISTS f5nslink_f5 ON f5nslink USING btree (f5);
CREATE INDEX IF NOT EXISTS f5nslink_ns ON f5nslink USING btree (ns);


\echo '>>> Create foreign key f5nslink_files_fkey on table f5nslink'
DO $$
BEGIN
    IF NOT EXISTS ( 
        SELECT 1 FROM pg_constraint WHERE conname = 'f5nslink_files_fkey' )
    THEN 
        ALTER TABLE ONLY f5nslink
            ADD CONSTRAINT f5nslink_files_fkey 
            FOREIGN KEY (files_uuid) 
            REFERENCES files(uuid) ON DELETE CASCADE;
    END IF;
END$$;

