-- PRIVILEGES NetPivot to demonio@localhost
REVOKE ALL PRIVILEGES ON *.* FROM 'demonio'@'localhost';
DROP USER 'demonio'@'localhost';

-- DATABASE NetPivot
DROP DATABASE IF EXISTS NetPivot;
CREATE DATABASE IF NOT EXISTS NetPivot CHARACTER SET = 'utf8' COLLATE = 'utf8_general_ci';
USE NetPivot;

-- TABLE NetPivot.users
DROP TABLE IF EXISTS NetPivot.users CASCADE;
CREATE TABLE IF NOT EXISTS NetPivot.users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(45) NOT NULL UNIQUE,
    password VARCHAR(60) NULL,
    type VARCHAR(45) NULL,
    max_files TINYINT UNSIGNED NULL,
    max_conversions TINYINT UNSIGNED NULL
) ENGINE = InnoDB;

INSERT INTO NetPivot.users(id,name,password,type,max_files,max_conversions) VALUES (1,'admin','$2y$10$XEAw/cVMGTy4H8flaMjpLesrkZVlRo1ZVC0fm6FjHlGTWul5vh2Ae','Administrator',100,100);

-- TABLE NetPivot.files
DROP TABLE IF EXISTS NetPivot.files CASCADE;
CREATE TABLE IF NOT EXISTS NetPivot.files (
    uuid VARCHAR(36) NOT NULL UNIQUE PRIMARY KEY,
    filename VARCHAR(255) NULL,
    upload_time DATETIME(0) NULL,
    users_id INT UNSIGNED NOT NULL,
    CONSTRAINT fk_files_users
	FOREIGN KEY(users_id) REFERENCES NetPivot.users(id)
	ON DELETE CASCADE
	ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- TABLE NetPivot.conversions
DROP TABLE IF EXISTS NetPivot.conversions CASCADE;
CREATE TABLE IF NOT EXISTS NetPivot.conversions (
    id_conversions BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
    users_id INT UNSIGNED NOT NULL,
    time_conversion DATETIME(0) NOT NULL,
    files_uuid VARCHAR(36) NOT NULL,
    converted_file VARCHAR(255) NOT NULL,
    error_file VARCHAR(255) NULL,
    stats_file VARCHAR(255) NULL,
    CONSTRAINT fk_conversions_users1
	FOREIGN KEY(users_id) REFERENCES NetPivot.users(id)
	ON DELETE CASCADE
	ON UPDATE NO ACTION,
    CONSTRAINT fk_conversions_files1
	FOREIGN KEY(files_uuid) REFERENCES NetPivot.files(uuid)
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- TABLE NetPivot.settings
DROP TABLE IF EXISTS NetPivot.settings CASCADE;
CREATE TABLE IF NOT EXISTS NetPivot.settings (
    host_name TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    timezone VARCHAR(45) NULL,
    files_path VARCHAR(255) NULL
) ENGINE = InnoDB;

-- TABLE NetPivot.stats
DROP TABLE IF EXISTS NetPivot.stats CASCADE;
CREATE TABLE IF NOT EXISTS NetPivot.stats (
    files_uuid VARCHAR(36) NOT NULL UNIQUE PRIMARY KEY,
    cli_status BOOLEAN NOT NULL DEFAULT 0,
    cli_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    cli_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    cli_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    auth_status BOOLEAN NOT NULL DEFAULT 0,
    auth_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    auth_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    auth_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    apm_status BOOLEAN NOT NULL DEFAULT 0,
    apm_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    apm_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    apm_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    cm_status BOOLEAN NOT NULL DEFAULT 0,
    cm_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    cm_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    cm_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    gtm_status BOOLEAN NOT NULL DEFAULT 0,
    gtm_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    gtm_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    gtm_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_mon_status BOOLEAN NOT NULL DEFAULT 0,
    ltm_mon_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_mon_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_mon_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    irules_status BOOLEAN NOT NULL DEFAULT 0,
    irules_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    irules_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    irules_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    mon_status BOOLEAN NOT NULL DEFAULT 0,
    mon_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    mon_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    mon_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    nodes_status BOOLEAN NOT NULL DEFAULT 0,
    nodes_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    nodes_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    nodes_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    pools_status BOOLEAN NOT NULL DEFAULT 0,
    pools_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    pools_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    pools_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    virtuals_status BOOLEAN NOT NULL DEFAULT 0,
    virtuals_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    virtuals_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    virtuals_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    vip_status BOOLEAN NOT NULL DEFAULT 0,
    vip_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    vip_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    vip_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    prof_status BOOLEAN NOT NULL DEFAULT 0,
    prof_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    prof_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    prof_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    pers_status BOOLEAN NOT NULL DEFAULT 0,
    pers_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    pers_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    pers_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_auth_status BOOLEAN NOT NULL DEFAULT 0,
    ltm_auth_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_auth_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_auth_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_snat_status BOOLEAN NOT NULL DEFAULT 0,
    ltm_snat_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_snat_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_snat_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_snat_t_status BOOLEAN NOT NULL DEFAULT 0,
    ltm_snat_t_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_snat_t_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_snat_t_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_snat_p_status BOOLEAN NOT NULL DEFAULT 0,
    ltm_snat_p_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_snat_p_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_snat_p_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_class_status BOOLEAN NOT NULL DEFAULT 0,
    ltm_class_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_class_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_class_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_data_status BOOLEAN NOT NULL DEFAULT 0,
    ltm_data_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_data_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_data_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_global_status BOOLEAN NOT NULL DEFAULT 0,
    ltm_global_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_global_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_global_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_pol_status BOOLEAN NOT NULL DEFAULT 0,
    ltm_pol_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_pol_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_pol_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_pol_s_status BOOLEAN NOT NULL DEFAULT 0,
    ltm_pol_s_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_pol_s_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ltm_pol_s_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    net_status BOOLEAN NOT NULL DEFAULT 0,
    net_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    net_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    net_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    pem_status BOOLEAN NOT NULL DEFAULT 0,
    pem_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    pem_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    pem_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    security_status BOOLEAN NOT NULL DEFAULT 0,
    security_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    security_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    security_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    sys_status BOOLEAN NOT NULL DEFAULT 0,
    sys_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    sys_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    sys_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    wom_status BOOLEAN NOT NULL DEFAULT 0,
    wom_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    wom_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    wom_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ignored_status BOOLEAN NOT NULL DEFAULT 0,
    ignored_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ignored_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ignored_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    total_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    total_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    total_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    total_p_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    total_p_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    total_p_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    total_s_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    total_s_chars SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    total_s_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    CONSTRAINT fk_stats_files1
	FOREIGN KEY(files_uuid) REFERENCES NetPivot.files(uuid)
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- PRIVILEGES NetPivot to demonio@localhost
CREATE USER 'demonio'@'localhost' IDENTIFIED BY 'password';
GRANT create,delete,insert,select,update ON NetPivot.* TO 'demonio'@'localhost';
-- GRANT ALL PRIVILEGES ON NetPivot.* TO 'demonio'@'localhost';

