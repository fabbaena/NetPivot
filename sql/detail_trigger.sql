DROP TRIGGER new_detail_record;
delimiter //
CREATE TRIGGER new_detail_record BEFORE INSERT ON details  
FOR EACH ROW
BEGIN

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
END//
delimiter ;


