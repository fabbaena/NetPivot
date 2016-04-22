CREATE OR REPLACE VIEW obj_grps_view AS 
 SELECT module_id, o.id, name, 
 (SELECT COUNT(*) FROM obj_names WHERE obj_grp_id=o.id) AS object_count,
 (SELECT SUM(attribute_count) FROM obj_names_view WHERE obj_grp_id=o.id) AS attribute_count,
 (SELECT SUM(attribute_converted) FROM obj_names_view WHERE obj_grp_id=o.id) AS attribute_converted,
 (SELECT SUM(attribute_omitted) FROM obj_names_view WHERE obj_grp_id=o.id) AS attribute_omitted
 FROM obj_grps o;

