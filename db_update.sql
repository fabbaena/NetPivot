-- This UPDATES the Layout of an already deployed DB with Data.
-- USE WITH CARE, please backup DB before running this.

LOCK TABLES
    NetPivot.details WRITE;

ALTER TABLE NetPivot.details
    MODIFY obj_name VARCHAR(160) NULL AFTER obj_component,
    MODIFY attribute VARCHAR(128) NULL AFTER obj_name;

UNLOCK TABLES;

