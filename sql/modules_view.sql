
CREATE OR REPLACE VIEW modules_view AS
 SELECT files_uuid, id, name,
 (SELECT COUNT(*) FROM obj_grps_view WHERE module_id=m.id) AS objgrp_count,
 (SELECT SUM(object_count) FROM obj_grps_view WHERE module_id=m.id) AS object_count,
 (SELECT SUM(attribute_count) FROM obj_grps_view WHERE module_id=m.id) AS attribute_count,
 (SELECT SUM(attribute_converted) FROM obj_grps_view WHERE module_id=m.id) AS attribute_converted,
 (SELECT SUM(attribute_omitted) FROM obj_grps_view WHERE module_id=m.id) AS attribute_omitted
 FROM modules m;

