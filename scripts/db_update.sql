-- This UPDATES the Layout of an already deployed DB with Data.
-- USE WITH CARE, please backup DB before running this.

-- LOCK TABLES
--    NetPivot.details WRITE;

UPDATE NetPivot.users SET password = '$2y$10$G.TH1hSw9wQcQOTqZjIJNudYm1jfQIjxFthJBnbJhmSTJQrpiU2la' WHERE id = '1';

-- UNLOCK TABLES;

