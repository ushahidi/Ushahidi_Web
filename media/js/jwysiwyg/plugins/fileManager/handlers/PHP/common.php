<?php

/**
 * PHP handler for jWYSIWYG file uploader.
 *
 * By Alec Gorge <alecgorge@gmail.com>
 */
require_once 'config.php';

class Config {
	public static $instance;

	public static function getInstance() {
		if(!(self::$instance instanceof Config)) {
			self::$instance = new Config();
		}
		return self::$instance;
	}

	private $valid_extensions = array();
	private $capabilities = array();
	private $root_dir = "";
	private $pub_dir = "";
	private $baseUrl = '';

	public function setValidExtensions(array $extensions) {
		$this->valid_extensions = $extensions;
	}

	public function setBaseUrl($url) {
		if ($url !== '/') {
			$url = rtrim($url, '/');
		}

		$this->baseUrl = $url;
	}

	public function getBaseUrl() {
		return $this->baseUrl;
	}

	public function setRootDir($dir) {
		$dir = str_replace("\\", '/', realpath($dir));

		if ($dir !== '/') {
			$dir = rtrim($dir, '/');
		}

		$this->root_dir = $dir;
	}

	public function getRootDir() {
		return $this->root_dir;
	}

	public function setPubDir($dir) {
		$this->pub_dir = $this->normalizeDir($dir);
	}

	public function getPubDir() {
		return $this->pub_dir;
	}

	public function setCapabilities(array $capabilities) {
		$this->capabilities = $capabilities;
	}

	public function getCapabilities() {
		return $this->capabilities;
	}

	public function getValidExtensions() {
		return $this->valid_extensions;
	}

	public function extensionIsValid ($ext) {
		return !(array_search(trim($ext, "."), $this->getValidExtensions()) === false);
	}

	public function addValidExtension($extension) {
		if(is_string($extension)) {
			$this->valid_extensions[] = $extension;
			return;
		}
		throw new Exception("Invalid argument. String expected.");
	}

	public function normalizeDir($dir) {
		$dir = str_replace("\\", "/", $dir);
		if($dir != "/") {
			$dir = rtrim($dir, "/")."/";
		}
		return $dir;
	}

	public function cleanFile ($f) {
		return str_replace(array(
			"../",
			"..\\"
		), array("", ""), $f);
	}
}

Config::getInstance()->setValidExtensions($accepted_extensions);
Config::getInstance()->setCapabilities($capabilities);
Config::getInstance()->setRootDir($uploads_dir);
Config::getInstance()->setPubDir($uploads_access_dir);
Config::getInstance()->setBaseUrl($base_url);

function json_response ($json, $exit = true) {
	if(!is_string($json)) {
		$json = json_encode($json);
	}
	header("Content-type: application/json; charset=utf-8");
	header("Cache-Control: no-cache, must-revalidate");
	echo $json;

	if($exit) {
		exit();
	}
}

class ResponseRouter {
	public static $instance;
	public static $Status401 = "401 Unauthorized";
	public static $Status404 = "404 Not Found";

	public static function getInstance() {
		if(!(self::$instance instanceof ResponseRouter)) {
			self::$instance = new ResponseRouter();
		}
		return self::$instance;
	}

	private $config;
	private $clean_base_filename;
	private $handlers = array();

	public function __construct () {
		$this->config = Config::getInstance();
		$t_var_pass_by_ref = explode("?", $_SERVER['REQUEST_URI']);
		$this->clean_base_filename = array_shift($t_var_pass_by_ref);
	}

	public function run () {
		if (array_key_exists('action', $_GET)) {
			if ($this->handlers[$_GET['action']]) {
				$this->handle($this->handlers[$_GET['action']]);
				return true;
			}
		} else if (array_key_exists('action', $_POST)) {
			if ($this->handlers[$_POST['action']]) {
				$this->handle($this->handlers[$_POST['action']]);
				return true;
			}
		}

		$this->handle($this->handlers["401"]);
	}

	public function getConfig () {
		return $this->config;
	}

	public function getBaseFile () {
		return $this->clean_base_filename;
	}

	private function handle(ResponseHandler $obj) {
		if($obj->getStatusNumber($this) != 200) {
			header($_SERVER["SERVER_PROTOCOL"]." ".$obj->getStatus($this));

			// FastCGI, blecch
			header("Status: ".$obj->getStatus($this));
			$_SERVER['REDIRECT_STATUS'] = $obj->getStatusNumber($this);
			// end FastCGI
		}
		$ret = $obj->getResponse($this);
		if($ret instanceof ResponseHandler) {
			$this->handle($ret);
		}
		else {
			json_response($ret);
		}
	}

	public function setHandler($name, ResponseHandler $call) {
		$this->handlers[$name] = $call;
	}

	public function normalizeDir($dir) {
		$dir = str_replace("\\", "/", $dir);
		if($dir != "/") {
			$dir = rtrim($dir, "/")."/";
		}
		return $dir;
	}

	public function cleanFile ($f) {
		return str_replace(array(
			"../",
			"..\\"
		), array("", ""), $f);
	}
}

abstract class ResponseHandler {
	public function getStatus($router) {
		return "200 OK";
	}
	public function getStatusNumber($router) {
		return 200;
	}
	public abstract function getResponse($router);
}

class Response404 extends ResponseHandler {
	public function getStatus($router) {
		return ResponseRouter::$Status404;
	}

	public function getStatusNumber($router) {
		return 404;
	}

	public function getResponse($router) {
		return array(
			"success" => false,
			"error" => "Not found"
		);
	}
}
ResponseRouter::getInstance()->setHandler("404", new Response404());

class Response401 extends ResponseHandler {
	public function getStatus($router) {
		return ResponseRouter::$Status401;
	}

	public function getStatusNumber($router) {
		return 401;
	}

	public function getResponse($router) {
		return array(
			"success" => false,
			"error" => "Not authorized."
		);
	}
}
ResponseRouter::getInstance()->setHandler("401", new Response401());
