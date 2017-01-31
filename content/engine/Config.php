<?php

class Config {
	private $_path_pivot;
	private $_path_files;
	private $_f5conv;
	private $_uuid;
	private $_filecmd;
	private $_services;
	private $_orphan;
	private $_use_cache;

	function __construct(
			$uuid       = null, 
			$f5conv     = "bin/f5conv", 
			$services   = "etc/services.F5",
			$path_pivot = "/opt/netpivot/",
			$path_files = "/var/www/files/",
			$filecmd    = "/usr/bin/file") {

		$this->_uuid = $uuid;
		$this->_path_pivot = $path_pivot;
		$this->_path_files = $path_files;
		$this->_f5conv = $f5conv;
		$this->_filecmd = $filecmd;
		$this->_services = $services;
		$this->_orphan = 0;
		$this->_ignore_cache = true;

	}

	function ignore_cache() {
		return $this->_ignore_cache;
	}

	function set_uuid($uuid) {
		$this->_uuid = $uuid;
	}

	function path_files() {
		return $this->_path_files;
	}

	function f5conv() {
		return $this->_path_pivot. $this->_f5conv;
	}

	function f5_file() {
		if(isset($this->_uuid))
			return $this->_path_files. $this->_uuid;
		else 
			return false;
	}

	function ns_file() {
		if(isset($this->_uuid))
			return $this->_path_files. $this->_uuid. "_.conf";
		else 
			return false;
	}

	function error_file() {
		if(isset($this->_uuid))
			return $this->_path_files. $this->_uuid. "_error.txt";
		else 
			return false;
	}

	function stats_file() {
		if(isset($this->_uuid))
			return $this->_path_files. $this->_uuid. "_stats.csv";
		else 
			return false;
	}

	function json_file() {
		if(isset($this->_uuid))
			return $this->_path_files. $this->_uuid. ".json";
		else 
			return false;
	}

	function services_file() {
		return $this->_path_pivot. $this->_services;
	}

	function convert_orphan($o = null) {
		if(isset($o)) {
			$this->_orphan = $o;
		} else {
			return $this->_orphan?" -p":"";
		}
	}

	function command() {
		if(isset($this->_uuid)) {
			$command = $this->f5conv(). 
				" -f ". $this->f5_file().
				" -e ". $this->error_file().
				" -C ". $this->stats_file().
				" -O ". $this->ns_file().
				" -J ". $this->json_file().
				" -s ". $this->services_file().
				$this->convert_orphan().
				" -g";
			syslog(LOG_INFO, $command);
			return $command;			
		} else 
			return false;
	}

	function np_version() {
		$command = $this->f5conv(). " -v 2>&1";
		return $command;
	}

	function file_type() {
		if(isset($this->_uuid))
			return $this->_filecmd. " -b ". $this->f5_file();
		else 
			return false;
	}

}

?>

