-- This UPDATES the Layout of an already deployed DB with Data.
-- USE WITH CARE, please backup DB before running this.

DROP TABLE IF EXISTS NetPivot.settings CASCADE;
DROP TABLE IF EXISTS NetPivot.stats CASCADE;

LOCK TABLES
    NetPivot.files WRITE,
    NetPivot.conversions WRITE,
    NetPivot.details WRITE;

ALTER TABLE NetPivot.conversions
    DROP FOREIGN KEY fk_conversions_files1
;

ALTER TABLE NetPivot.details
    DROP FOREIGN KEY fk_details_files1
;

ALTER TABLE NetPivot.files
    MODIFY uuid CHAR(36) NOT NULL FIRST
;

ALTER TABLE NetPivot.conversions
    MODIFY files_uuid CHAR(36) NOT NULL AFTER time_conversion,
    ADD CONSTRAINT fk_conversions_files1
        FOREIGN KEY(files_uuid) REFERENCES NetPivot.files(uuid)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
;

ALTER TABLE NetPivot.details
    MODIFY files_uuid CHAR(36) NOT NULL FIRST,
    ADD CONSTRAINT fk_details_files1
        FOREIGN KEY(files_uuid) REFERENCES NetPivot.files(uuid)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
;

UNLOCK TABLES;

