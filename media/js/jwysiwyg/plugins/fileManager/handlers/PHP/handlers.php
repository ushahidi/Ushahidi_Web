<?php

/**
 * PHP handler for jWYSIWYG file uploader.
 *
 * By Alec Gorge <alecgorge@gmail.com>
 */
class AuthHandler extends ResponseHandler {
	private $authorized = false;

	public function __construct () {
		$this->authorized = (array_key_exists('auth', $_GET) && $_GET['auth'] === 'jwysiwyg');
	}

	public function getStatus ($router) {
		return $this->authorized ? "" : ResponseRouter::$Status401;
	}

	public function getStatusNumber ($router) {
		return $this->authorized ? 200 : 401;
	}

	public function getResponse ($router) {
		$cap = $router->getConfig()->getCapabilities();

		$r = array();
		$r['baseUrl'] = $router->getConfig()->getBaseUrl();
		foreach($cap as $k => $v) {
			// var_dump($router->getBaseFile());
			$r[$k] = array(
				"handler" => $router->getBaseFile(),
				"enabled" => $v
			);

			if($k == "upload") {
				$r[$k]["accept_ext"] = $router->getConfig()->getValidExtensions();
			}
		}
		return array(
			"success" => true,
			"data" => $r
		);
	}
}
ResponseRouter::getInstance()->setHandler("auth", new AuthHandler());

class ListHandler extends ResponseHandler {
	private function remove_path($file, $path) {
		$path = rtrim($path, '/');
		if (strpos($file, $path) !== false) {
			return substr($file, strlen($path));
		}
	}

	public function getResponse ($router) {
		$data = array(
			"directories" => array(),
			"files" => array()
		);
		$dir = $router->normalizeDir($_GET['dir']);

		if(!file_exists($router->getConfig()->getRootDir().$dir)) {
			return new Response404();
		}
		foreach(new DirectoryIterator($router->getConfig()->getRootDir().$dir) as $info) {
			if($info->isDot()) continue;
			if($info->isFile()) {
				$data["files"][$info->getFilename()] = $this->remove_path($info->getPathname(), $router->getConfig()->getPubDir());
			}
			else if($info->isDir()) {
				$data["directories"][$info->getFilename()] = $this->remove_path($info->getPathname(), $router->getConfig()->getPubDir());
			}
		}

		return array(
			"success" => true,
			"data" => $data
		);
	}
}
ResponseRouter::getInstance()->setHandler("list", new ListHandler());

class RenameHandler extends ResponseHandler {
	public function getResponse ($router) {
		$dir = $router->normalizeDir($_GET['dir']);
		$root = $router->getConfig()->getRootDir();
		$file = $_GET['file'] == "null" ? "" : $router->cleanFile($_GET['file']);

		if(!file_exists($root.$dir)) {
			return new Response404();
		}

		if(rename($root.$dir.$file, $root.$dir.$router->cleanFile($_GET['newName']))) {
			return array(
				"success" => true,
				"data" => "message .. "
			);
		}
		else {
			return array(
				"success" => false,
				"error" => "Couldn't rename the file."
			);
		}
	}
}
ResponseRouter::getInstance()->setHandler("rename", new RenameHandler());

class RemoveHandler extends ResponseHandler {
	public function getResponse ($router) {
		$dir = $router->normalizeDir($_GET['dir']);
		$root = $router->getConfig()->getRootDir();
		$file = $router->cleanFile($_GET['file']);

		if (!file_exists($root.$dir.$file)) {
			return new Response404();
		} else if (!is_writable($root.$dir.$file)) {
			return array(
				'success' => false,
				'error' => 'Don\'t have permission'
			);
		}

		$is_removed = false;
		if (is_dir($root.$dir.$file)) {
			// :TODO: recursive remove
			$is_removed = rmdir($root.$dir.$file);
		} else {
			$is_removed = unlink($root.$dir.$file);
		}
		if ($is_removed) {
			return array(
				"success" => true,
				"data" => "message .. "
			);
		} else {
			return array(
				"success" => false,
				"error" => "Couldn't delete the file."
			);
		}
	}
}
ResponseRouter::getInstance()->setHandler("remove", new RemoveHandler());

class MkdirHandler extends ResponseHandler {
	public function getResponse ($router) {
		$dir = $router->normalizeDir($_GET['dir']);
		$root = $router->getConfig()->getRootDir();
		$newName = $router->cleanFile($_GET['newName']);

		if(!file_exists($root.$dir)) {
			return new Response404();
		}
		if(mkdir($root.$dir.$newName)) {
			return array(
				"success" => true,
				"data" => "Made: ".$root.$dir.$newName
			);
		}
		else {
			return array(
				"success" => false,
				"error" => "Couldn't make directory."
			);
		}
	}
}
ResponseRouter::getInstance()->setHandler("mkdir", new MkdirHandler());

class MoveHandler extends ResponseHandler {
	public function getResponse ($router) {
		$dir = $router->normalizeDir($_GET['dir']);
		$root = $router->getConfig()->getRootDir();
		$newPath = $router->cleanFile($_GET['newPath']);
		$file = $router->cleanFile($_GET['file']);

		if(!file_exists($root.$dir.$file)) {
			return new Response404();
		}
		if(rename($root.$dir.$file, $root.$newPath)) {
			return array(
				"success" => true,
				"data" => "message .. "
			);
		}
		else {
			return array(
				"success" => false,
				"error" => "Couldn't move file."
			);
		}
	}
}
ResponseRouter::getInstance()->setHandler("move", new MoveHandler());

class UploadHandler extends ResponseHandler {
	public function getResponse ($router) {
		$dir = $router->normalizeDir($_POST['dir']);
		$root = $router->getConfig()->getRootDir();
		$dst = $root . $dir . $_POST['newName'];

		if (file_exists($dst)) {
			return array(
				'success' => false,
				'error' => sprintf('Destination file "%s" exists.', $dir . $_POST['newName'])
			);
		}

		if (!is_uploaded_file($_FILES['handle']['tmp_name'])) {
			return array(
				'success' => false,
				'error' => 'Couldn\'t upload file.'
			);
		}

		if (!move_uploaded_file($_FILES['handle']['tmp_name'], $dst)) {
			return array(
				'success' => false,
				'error' => 'Couldn\'t upload file.'
			);
		}

		return array(
			'success' => true,
			'data' => 'File upload successful.'
		);
	}
}
ResponseRouter::getInstance()->setHandler("upload", new UploadHandler());
