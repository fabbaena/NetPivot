-- This UPDATES the Layout of an already deployed DB with Data.
-- USE WITH CARE, please backup DB before running this.

LOCK TABLES
    NetPivot.users WRITE,
    NetPivot.files WRITE,
    NetPivot.conversions WRITE,
    NetPivot.settings WRITE,
    NetPivot.stats WRITE;

ALTER TABLE NetPivot.conversions
    DROP FOREIGN KEY fk_conversions_users1;

ALTER TABLE NetPivot.files
    DROP FOREIGN KEY fk_files_users;

ALTER TABLE NetPivot.stats
    DROP FOREIGN KEY fk_stats_files1;

ALTER TABLE NetPivot.users
    MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
    MODIFY max_files TINYINT UNSIGNED NULL AFTER type,
    MODIFY max_conversions TINYINT UNSIGNED NULL AFTER max_files;

ALTER TABLE NetPivot.files
    MODIFY filename VARCHAR(255) NULL AFTER uuid,
    ADD project_name VARCHAR(64) NULL AFTER filename,
    MODIFY upload_time DATETIME(0) NULL AFTER project_name,
    MODIFY users_id INT UNSIGNED NOT NULL AFTER upload_time,
    ADD CONSTRAINT fk_files_users
	FOREIGN KEY(users_id) REFERENCES NetPivot.users(id)
	ON DELETE CASCADE
	ON UPDATE NO ACTION;

ALTER TABLE NetPivot.conversions
    MODIFY id_conversions BIGINT UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
    MODIFY users_id INT UNSIGNED NOT NULL AFTER id_conversions,
    MODIFY time_conversion DATETIME(0) NOT NULL AFTER users_id,
    MODIFY converted_file VARCHAR(255) NOT NULL AFTER files_uuid,
    MODIFY error_file VARCHAR(255) NULL AFTER converted_file,
    MODIFY stats_file VARCHAR(255) NULL AFTER error_file,
    ADD CONSTRAINT fk_conversions_users1
	FOREIGN KEY(users_id) REFERENCES NetPivot.users(id)
	ON DELETE CASCADE
	ON UPDATE NO ACTION;

ALTER TABLE NetPivot.settings
    MODIFY host_name TINYINT UNSIGNED NOT NULL FIRST,
    MODIFY files_path VARCHAR(255) NULL AFTER timezone;

ALTER TABLE NetPivot.stats
    MODIFY files_uuid VARCHAR(36) NOT NULL FIRST,
    DROP char_conv,
    DROP char_skip,
    DROP mon_count,
    DROP node_count,
    DROP pools_count,
    DROP virtual_count,
    DROP vip_count,
    DROP profile_count,
    DROP persist_count,
    ADD cli_status BOOLEAN NOT NULL DEFAULT 0 AFTER files_uuid,
    ADD cli_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER cli_status,
    ADD cli_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER cli_obj,
    ADD cli_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER cli_chars,
    ADD auth_status BOOLEAN NOT NULL DEFAULT 0 AFTER cli_lines,
    ADD auth_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER auth_status,
    ADD auth_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER auth_obj,
    ADD auth_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER auth_chars,
    ADD apm_status BOOLEAN NOT NULL DEFAULT 0 AFTER auth_lines,
    ADD apm_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER apm_status,
    ADD apm_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER apm_obj,
    ADD apm_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER apm_chars,
    ADD cm_status BOOLEAN NOT NULL DEFAULT 0 AFTER apm_lines,
    ADD cm_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER cm_status,
    ADD cm_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER cm_obj,
    ADD cm_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER cm_chars,
    ADD gtm_status BOOLEAN NOT NULL DEFAULT 0 AFTER cm_lines,
    ADD gtm_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER gtm_status,
    ADD gtm_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER gtm_obj,
    ADD gtm_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER gtm_chars,
    ADD ltm_mon_status BOOLEAN NOT NULL DEFAULT 0 AFTER gtm_lines,
    ADD ltm_mon_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_mon_status,
    ADD ltm_mon_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_mon_obj,
    ADD ltm_mon_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_mon_chars,
    ADD irules_status BOOLEAN NOT NULL DEFAULT 0 AFTER ltm_mon_lines,
    ADD irules_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER irules_status,
    ADD irules_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER irules_obj,
    ADD irules_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER irules_chars,
    ADD mon_status BOOLEAN NOT NULL DEFAULT 0 AFTER irules_lines,
    ADD mon_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER mon_status,
    MODIFY mon_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER mon_obj,
    MODIFY mon_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER mon_chars,
    ADD nodes_status BOOLEAN NOT NULL DEFAULT 0 AFTER mon_lines,
    ADD nodes_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER nodes_status,
    CHANGE node_chars nodes_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER nodes_obj,
    CHANGE node_lines nodes_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER nodes_chars,
    ADD pools_status BOOLEAN NOT NULL DEFAULT 0 AFTER nodes_lines,
    ADD pools_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER pools_status,
    MODIFY pools_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER pools_obj,
    MODIFY pools_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER pools_chars,
    ADD virtuals_status BOOLEAN NOT NULL DEFAULT 0 AFTER pools_lines,
    ADD virtuals_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER virtuals_status,
    CHANGE virtual_chars virtuals_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER virtuals_obj,
    CHANGE virtual_lines virtuals_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER virtuals_chars,
    ADD vip_status BOOLEAN NOT NULL DEFAULT 0 AFTER virtuals_lines,
    ADD vip_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER vip_status,
    MODIFY vip_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER vip_obj,
    MODIFY vip_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER vip_chars,
    ADD prof_status BOOLEAN NOT NULL DEFAULT 0 AFTER vip_lines,
    ADD prof_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER prof_status,
    CHANGE profile_chars prof_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER prof_obj,
    CHANGE profile_lines prof_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER prof_chars,
    ADD pers_status BOOLEAN NOT NULL DEFAULT 0 AFTER prof_lines,
    ADD pers_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER pers_status,
    CHANGE persist_chars pers_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER pers_obj,
    CHANGE persist_lines pers_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER pers_chars,
    ADD ltm_auth_status BOOLEAN NOT NULL DEFAULT 0 AFTER pers_lines,
    ADD ltm_auth_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_auth_status,
    ADD ltm_auth_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_auth_obj,
    ADD ltm_auth_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_auth_chars,
    ADD ltm_snat_status BOOLEAN NOT NULL DEFAULT 0 AFTER ltm_auth_lines,
    ADD ltm_snat_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_snat_status,
    ADD ltm_snat_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_snat_obj,
    ADD ltm_snat_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_snat_chars,
    ADD ltm_snat_t_status BOOLEAN NOT NULL DEFAULT 0 AFTER ltm_snat_lines,
    ADD ltm_snat_t_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_snat_t_status,
    ADD ltm_snat_t_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_snat_t_obj,
    ADD ltm_snat_t_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_snat_t_chars,
    ADD ltm_snat_p_status BOOLEAN NOT NULL DEFAULT 0 AFTER ltm_snat_t_lines,
    ADD ltm_snat_p_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_snat_p_status,
    ADD ltm_snat_p_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_snat_p_obj,
    ADD ltm_snat_p_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_snat_p_chars,
    ADD ltm_class_status BOOLEAN NOT NULL DEFAULT 0 AFTER ltm_snat_p_lines,
    ADD ltm_class_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_class_status,
    ADD ltm_class_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_class_obj,
    ADD ltm_class_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_class_chars,
    ADD ltm_data_status BOOLEAN NOT NULL DEFAULT 0 AFTER ltm_class_lines,
    ADD ltm_data_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_data_status,
    ADD ltm_data_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_data_obj,
    ADD ltm_data_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_data_chars,
    ADD ltm_global_status BOOLEAN NOT NULL DEFAULT 0 AFTER ltm_data_lines,
    ADD ltm_global_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_global_status,
    ADD ltm_global_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_global_obj,
    ADD ltm_global_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_global_chars,
    ADD ltm_pol_status BOOLEAN NOT NULL DEFAULT 0 AFTER ltm_global_lines,
    ADD ltm_pol_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_pol_status,
    ADD ltm_pol_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_pol_obj,
    ADD ltm_pol_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_pol_chars,
    ADD ltm_pol_s_status BOOLEAN NOT NULL DEFAULT 0 AFTER ltm_pol_lines,
    ADD ltm_pol_s_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_pol_s_status,
    ADD ltm_pol_s_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_pol_s_obj,
    ADD ltm_pol_s_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ltm_pol_s_chars,
    ADD net_status BOOLEAN NOT NULL DEFAULT 0 AFTER ltm_pol_s_lines,
    ADD net_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER net_status,
    ADD net_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER net_obj,
    ADD net_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER net_chars,
    ADD pem_status BOOLEAN NOT NULL DEFAULT 0 AFTER net_lines,
    ADD pem_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER pem_status,
    ADD pem_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER pem_obj,
    ADD pem_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER pem_chars,
    ADD security_status BOOLEAN NOT NULL DEFAULT 0 AFTER pem_lines,
    ADD security_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER security_status,
    ADD security_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER security_obj,
    ADD security_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER security_chars,
    ADD sys_status BOOLEAN NOT NULL DEFAULT 0 AFTER security_lines,
    ADD sys_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER sys_status,
    ADD sys_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER sys_obj,
    ADD sys_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER sys_chars,
    ADD wom_status BOOLEAN NOT NULL DEFAULT 0 AFTER sys_lines,
    ADD wom_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER wom_status,
    ADD wom_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER wom_obj,
    ADD wom_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER wom_chars,
    ADD ignored_status BOOLEAN NOT NULL DEFAULT 0 AFTER wom_lines,
    ADD ignored_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ignored_status,
    ADD ignored_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER ignored_obj,
    ADD ignored_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ignored_chars,
    ADD total_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER ignored_lines,
    CHANGE char_total total_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER total_obj,
    ADD total_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER total_chars,
    ADD total_p_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER total_lines,
    ADD total_p_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER total_p_obj,
    ADD total_p_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER total_p_chars,
    ADD total_s_obj SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER total_p_lines,
    ADD total_s_chars MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 AFTER total_s_obj,
    ADD total_s_lines SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER total_s_chars,
    ADD CONSTRAINT fk_stats_files1
	FOREIGN KEY(files_uuid) REFERENCES NetPivot.files(uuid)
	ON DELETE NO ACTION
	ON UPDATE NO ACTION;

UNLOCK TABLES;

DROP TABLE IF EXISTS NetPivot.details CASCADE;
CREATE TABLE IF NOT EXISTS NetPivot.details (
    files_uuid VARCHAR(36) NOT NULL,
    module VARCHAR(16) NULL,
    obj_grp VARCHAR(16) NULL,
    obj_component VARCHAR(32) NULL,
    obj_name VARCHAR(128) NULL,
    attribute VARCHAR(32) NULL,
    converted BOOLEAN NULL,
    omitted BOOLEAN NULL,
    line SMALLINT UNSIGNED NULL,
    INDEX files_uuid_idx USING BTREE (files_uuid),
    CONSTRAINT fk_details_files1
        FOREIGN KEY(files_uuid) REFERENCES NetPivot.files(uuid)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
) ENGINE = InnoDB;

