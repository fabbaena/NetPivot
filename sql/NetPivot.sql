-- phpMyAdmin SQL Dump
-- version 4.6.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 22, 2016 at 01:35 PM
-- Server version: 5.5.47-MariaDB-1ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `NetPivot2`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `converted` int(11) NOT NULL,
  `omitted` int(11) NOT NULL,
  `line` int(11) NOT NULL,
  `obj_name_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `conversions`
--

CREATE TABLE `conversions` (
  `id_conversions` bigint(20) UNSIGNED NOT NULL,
  `users_id` int(10) UNSIGNED NOT NULL,
  `time_conversion` datetime NOT NULL,
  `files_uuid` char(36) NOT NULL,
  `converted_file` varchar(255) NOT NULL,
  `error_file` varchar(255) DEFAULT NULL,
  `stats_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `details`
--

CREATE TABLE `details` (
  `files_uuid` char(36) NOT NULL,
  `module` varchar(16) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `obj_grp` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `obj_component` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `obj_name` varchar(160) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `attribute` varchar(128) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `converted` tinyint(1) DEFAULT NULL,
  `omitted` tinyint(1) DEFAULT NULL,
  `line` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Triggers `details`
--
DELIMITER $$
CREATE TRIGGER `new_detail_record` BEFORE INSERT ON `details` FOR EACH ROW BEGIN

 SET @module_id   = (SELECT id FROM modules   WHERE name=NEW.module   AND files_uuid=NEW.files_uuid);
 IF(@module_id IS NULL) THEN
   INSERT INTO modules (name, files_uuid) VALUES (NEW.module, NEW.files_uuid);
  SET @module_id = (SELECT id FROM modules WHERE name=NEW.module AND files_uuid=NEW.files_uuid);
 END IF;

 SET @obj_grp_id  = (SELECT id FROM obj_grps  WHERE name=NEW.obj_grp  AND module_id =@module_id);
 IF (@obj_grp_id IS NULL) THEN
  INSERT INTO obj_grps (name, obj_component, module_id) VALUES (NEW.obj_grp, NEW.obj_component, @module_id);
  SET @obj_grp_id = (SELECT id FROM obj_grps WHERE name=NEW.obj_grp AND module_id=@module_id);
 END IF;

 IF ((NEW.attribute IS NOT NULL OR NEW.attribute <> "") AND (NEW.obj_name IS NULL OR NEW.obj_name = "")) THEN
  SET NEW.obj_name = '---';
 END IF;
 SET @obj_name_id = (SELECT id FROM obj_names WHERE name=NEW.obj_name AND obj_grp_id=@obj_grp_id);
 IF (@obj_name_id IS NULL) THEN
  INSERT INTO obj_names (name, line, obj_grp_id) VALUES (NEW.obj_name, NEW.line, @obj_grp_id);
  SET @obj_name_id = (SELECT id FROM obj_names WHERE name=NEW.obj_name AND obj_grp_id=@obj_grp_id);
 END IF;

 IF (NEW.attribute IS NOT NULL AND NEW.attribute <> "") THEN
  INSERT INTO attributes (name, converted, omitted, line, obj_name_id)  VALUES (NEW.attribute, NEW.converted, NEW.omitted, NEW.line, @obj_name_id);
 END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `uuid` char(36) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `project_name` varchar(64) DEFAULT NULL,
  `upload_time` datetime DEFAULT NULL,
  `users_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `files_uuid` char(36) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `modules_view`
-- (See below for the actual view)
--
CREATE TABLE `modules_view` (
`files_uuid` char(36)
,`id` int(11)
,`name` varchar(255)
,`objgrp_count` bigint(21)
,`object_count` decimal(42,0)
,`attribute_count` decimal(64,0)
,`attribute_converted` decimal(65,0)
,`attribute_omitted` decimal(65,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `obj_grps`
--

CREATE TABLE `obj_grps` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `obj_component` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `module_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `obj_grps_view`
-- (See below for the actual view)
--
CREATE TABLE `obj_grps_view` (
`module_id` int(11)
,`id` int(11)
,`name` varchar(255)
,`object_count` bigint(21)
,`attribute_count` decimal(42,0)
,`attribute_converted` decimal(54,0)
,`attribute_omitted` decimal(54,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `obj_names`
--

CREATE TABLE `obj_names` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `line` int(11) NOT NULL,
  `obj_grp_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `obj_names_view`
-- (See below for the actual view)
--
CREATE TABLE `obj_names_view` (
`obj_grp_id` int(11)
,`id` int(11)
,`name` varchar(255)
,`line` int(11)
,`attribute_count` bigint(21)
,`attribute_converted` decimal(32,0)
,`attribute_omitted` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `host_name` tinyint(3) UNSIGNED NOT NULL,
  `timezone` varchar(45) NOT NULL DEFAULT 'US/Eastern',
  `files_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL,
  `password` varchar(60) NOT NULL,
  `type` varchar(45) DEFAULT NULL,
  `max_files` tinyint(3) UNSIGNED DEFAULT NULL,
  `max_conversions` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for view `modules_view`
--
DROP TABLE IF EXISTS `modules_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `modules_view`  AS  select `m`.`files_uuid` AS `files_uuid`,`m`.`id` AS `id`,`m`.`name` AS `name`,(select count(0) from `obj_grps_view` where (`obj_grps_view`.`module_id` = `m`.`id`)) AS `objgrp_count`,(select sum(`obj_grps_view`.`object_count`) from `obj_grps_view` where (`obj_grps_view`.`module_id` = `m`.`id`)) AS `object_count`,(select sum(`obj_grps_view`.`attribute_count`) from `obj_grps_view` where (`obj_grps_view`.`module_id` = `m`.`id`)) AS `attribute_count`,(select sum(`obj_grps_view`.`attribute_converted`) from `obj_grps_view` where (`obj_grps_view`.`module_id` = `m`.`id`)) AS `attribute_converted`,(select sum(`obj_grps_view`.`attribute_omitted`) from `obj_grps_view` where (`obj_grps_view`.`module_id` = `m`.`id`)) AS `attribute_omitted` from `modules` `m` ;

-- --------------------------------------------------------

--
-- Structure for view `obj_grps_view`
--
DROP TABLE IF EXISTS `obj_grps_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `obj_grps_view`  AS  select `o`.`module_id` AS `module_id`,`o`.`id` AS `id`,`o`.`name` AS `name`,(select count(0) from `obj_names` where (`obj_names`.`obj_grp_id` = `o`.`id`)) AS `object_count`,(select sum(`obj_names_view`.`attribute_count`) from `obj_names_view` where (`obj_names_view`.`obj_grp_id` = `o`.`id`)) AS `attribute_count`,(select sum(`obj_names_view`.`attribute_converted`) from `obj_names_view` where (`obj_names_view`.`obj_grp_id` = `o`.`id`)) AS `attribute_converted`,(select sum(`obj_names_view`.`attribute_omitted`) from `obj_names_view` where (`obj_names_view`.`obj_grp_id` = `o`.`id`)) AS `attribute_omitted` from `obj_grps` `o` ;

-- --------------------------------------------------------

--
-- Structure for view `obj_names_view`
--
DROP TABLE IF EXISTS `obj_names_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `obj_names_view`  AS  select `o`.`obj_grp_id` AS `obj_grp_id`,`o`.`id` AS `id`,`o`.`name` AS `name`,`o`.`line` AS `line`,(select count(0) from `attributes` where (`attributes`.`obj_name_id` = `o`.`id`)) AS `attribute_count`,(select sum(`attributes`.`converted`) from `attributes` where (`attributes`.`obj_name_id` = `o`.`id`)) AS `attribute_converted`,(select sum(`attributes`.`omitted`) from `attributes` where (`attributes`.`obj_name_id` = `o`.`id`)) AS `attribute_omitted` from `obj_names` `o` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `obj_name_id` (`obj_name_id`),
  ADD KEY `idx_name_obj_name_id` (`name`,`obj_name_id`) USING BTREE;

--
-- Indexes for table `conversions`
--
ALTER TABLE `conversions`
  ADD PRIMARY KEY (`id_conversions`),
  ADD UNIQUE KEY `id_conversions` (`id_conversions`),
  ADD KEY `fk_conversions_users1` (`users_id`),
  ADD KEY `fk_conversions_files1` (`files_uuid`);

--
-- Indexes for table `details`
--
ALTER TABLE `details`
  ADD KEY `files_uuid_idx` (`files_uuid`) USING BTREE;

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`uuid`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `fk_files_users` (`users_id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `files_uuid_name` (`files_uuid`,`name`) USING BTREE,
  ADD KEY `files_uuid` (`files_uuid`);

--
-- Indexes for table `obj_grps`
--
ALTER TABLE `obj_grps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_name_module_id` (`name`,`module_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `obj_names`
--
ALTER TABLE `obj_names`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_name_obj_grp_id` (`name`,`obj_grp_id`),
  ADD KEY `obj_grp_id` (`obj_grp_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`host_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=285525;
--
-- AUTO_INCREMENT for table `conversions`
--
ALTER TABLE `conversions`
  MODIFY `id_conversions` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;
--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;
--
-- AUTO_INCREMENT for table `obj_grps`
--
ALTER TABLE `obj_grps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=485;
--
-- AUTO_INCREMENT for table `obj_names`
--
ALTER TABLE `obj_names`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74409;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `attributes`
--
ALTER TABLE `attributes`
  ADD CONSTRAINT `attributes_ibfk_1` FOREIGN KEY (`obj_name_id`) REFERENCES `obj_names` (`id`);

--
-- Constraints for table `details`
--
ALTER TABLE `details`
  ADD CONSTRAINT `fk_details_files1` FOREIGN KEY (`files_uuid`) REFERENCES `files` (`uuid`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`files_uuid`) REFERENCES `files` (`uuid`);

--
-- Constraints for table `obj_grps`
--
ALTER TABLE `obj_grps`
  ADD CONSTRAINT `obj_grps_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`);

--
-- Constraints for table `obj_names`
--
ALTER TABLE `obj_names`
  ADD CONSTRAINT `obj_names_ibfk_1` FOREIGN KEY (`obj_grp_id`) REFERENCES `obj_grps` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

