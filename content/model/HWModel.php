<?php 

require_once "NPObject.php";
require_once "F5Features.php";
require_once "F5Objects.php";
require_once "Crud.php";

class HWModel extends NPObject {
	public $id;
	public $brand;
	public $model;
	public $type;
	public $l7_req_per_sec;
	public $l4_con_per_sec;
	public $l4_http_req_per_sec;
	public $l4_max_con;
	public $l4_gbps;
	public $l7_gpbs;
	public $ssl_tps_incl;
	public $ssl_tps_max;
	public $ssl_gbps;
	public $ssl_fips;
	public $ssl_fips_tps;
	public $ssl_fips_gbps;
	public $ddos_syn_per_sec;
	public $cmp_gbps_incl;
	public $cmp_gbps_max;
	public $software_arch;
	public $virtual_instances_incl;
	public $virtual_instances_max;
	public $proc_type;
	public $proc_sock;
	public $proc_cor_per_sock;
	public $cpu;
	public $memory;
	public $hd_cap;
	public $hd_disks;
	public $hd_type;
	public $hd_raid;
	public $eth_cu;
	public $eth_1g_sfp;
	public $eth_10g_sfp;
	public $eth_40g_qsfp;
	public $eth_100g_qsfp;
	public $pow_supl;
	public $pow_max_power;
	public $pow_dc;
	public $pow_typ_power;
	public $pow_voltage;
	public $pow_heat;
	public $dim_h;
	public $dim_w;
	public $dim_d;
	public $dim_u;
	public $dim_weight;
	public $op_temp;
	public $op_hum;
	public $safety;
	public $suscept;
	public $ns_type_map;
	public $ns_model_map;


	function __construct($record = null) {
		$this->_tablename = "adc_hw";
		if(!isset($record)) return;
		foreach($this as $key => $value) {
			if(isset($record[$key])) {
				$this->$key = $record[$key];
			}
		}
		if(isset($this->proc_sock) && isset($this->proc_cor_per_sock)) {
			$this->cpu = $this->proc_sock * $this->proc_cor_per_sock;
		}
	}
}

class HWModelList {
	public $brand;
	public $type;
	private $_models;

	function __construct($record = null) {
		if(isset($record)) {
			foreach($this as $key => $value) {
				if(isset($record[$key])) $this->$key = $record[$key];
			}
		}
		$this->_models = [];

	}

	function load($cols) {
		$first = true;
		foreach($cols as $col) {
			if(!isset($this->$col)) throw new Exception("Column $col doesn't exist");
			if($first) {
				$first = false;
				$c = new Condition(new Column($col), '=', new Value($this->$col));
			} else {
				$c = new Condition($c, 'and', 
					new Condition(new Column($col), '=', new Value($this->$col)));
			}
		}

		$db = new Crud();
		$db->select = "*";
		$db->from = 'adc_hw';
		$db->condition = $c;
		$db->orderby = array("column" => "id", "direction" => "DESC");
		$db->Read5();

		foreach($db->rows as $f) {
			$hw = new HWModel($f);
			array_push($this->_models, $hw);
		}

	}

	function get() {
		return $this->_models;
	}
}
?>