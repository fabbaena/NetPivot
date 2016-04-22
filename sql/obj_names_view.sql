CREATE OR REPLACE VIEW obj_names_view AS 
 SELECT obj_grp_id, o.id, name, line, 
 (SELECT COUNT(*) FROM attributes WHERE obj_name_id=o.id) AS attribute_count,
 (SELECT SUM(converted) FROM attributes WHERE obj_name_id=o.id) AS attribute_converted,
 (SELECT SUM(omitted) FROM attributes WHERE obj_name_id=o.id) AS attribute_omitted
  FROM obj_names o;


