-- This UPDATES the Layout of an already deployed DB with Data.
-- USE WITH CARE, please backup DB before running this.

-- LOCK TABLES
--    NetPivot.details WRITE;
-- UNLOCK TABLES;

CREATE TABLE IF NOT EXISTS NetPivot.settings (
    host_name TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    timezone VARCHAR(45) NOT NULL DEFAULT 'US/Eastern',
    files_path VARCHAR(255) NULL
) ENGINE = InnoDB;

REPLACE INTO NetPivot.settings(host_name, timezone, files_path) VALUES (1, 'US/Eastern', '/var/www/html/dashboard/files');

