<?php
/**
 * Media Controller
 * A controller used to serve css, images and js files from the media directory.
 *
 * This class also compresses js and css files with gzip compression (if the browser supports it) and correctly handles
 * ETag/Last-Modified headers to prevent sending unmodified files to the client.
 * 
 * //// GZIP Compression has been disabled in this controller and is handled
 * by settings in the config.php file where it can be enabled/disabled ////
 * 
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Media Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class Media_Controller extends Controller {
	
	// Javascript URI
	public function js() {
		$this->_send();
	}
	
	// CSS URI
	public function css() {
		$this->_send();
	}
	
	// Image URI
	public function img() {
		$this->_send();
	}
	
	// Method retrieves file data via file_get_contents
	public function _send() {
		$gzip = false;	// Enable/Disable GZip Compression
		
		$segments = $this->uri->segment_array();	// URI Segments
		$file = array_pop($segments);
		$file_path = implode("/", $segments);
		
		$pos = strrpos($file, '.');
		if ($pos === false) $ext = '';
		else {
			$ext = substr($file,$pos+1);
			$file = substr($file,0,$pos);
		}
		
		$file = $file_path . "/" . $file . "." . $ext;
		if (!file_exists($file)) {
			$file = false;
		}
		
		$mtime = filemtime($file);
		
		$file_data = file_get_contents($file);
		
		if ($ext == "css")
		{ // Compress CSS data
			$file_data = $this->_css_compress($file_data);
		}
		
		// HTTP Headers
		$expiry_time = 613200;	// 1 Week
		$mime = ($ext == 'css') ? 'text/css' : 'application/javascript';
		header('Content-type: '.$mime);
        header('Cache-Control: must-revalidate');
        header('Expires: ' . gmdate("D, d M Y H:i:s", time() + $expiry_time) . ' GMT');
		header('ETag: '.$mtime);
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", $mtime)." GMT");

		$oldetag = isset($_SERVER['HTTP_IF_NONE_MATCH'])?trim($_SERVER['HTTP_IF_NONE_MATCH']):'';
		$oldmtime = isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])?$_SERVER['HTTP_IF_MODIFIED_SINCE']:'';
		$accencoding = isset($_SERVER['HTTP_ACCEPT_ENCODING'])?$_SERVER['HTTP_ACCEPT_ENCODING']:'';
		
		if (($oldmtime && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $oldmtime) || $oldetag == $mtime)
		{
			header('HTTP/1.1 304 Not Modified');
		}
		else 
		{
			if (strpos($accencoding, 'gzip') !== false && $gzip)
			{
				header('Content-Encoding: gzip');
				echo gzencode($file_data);
			}
			else echo $file_data;
		}
	}
	
	private function _css_compress($data)
    {
            // Remove comments
            $data = preg_replace('~/\*[^*]*\*+([^/][^*]*\*+)*/~', '', $data);

            // Replace all whitespace by single spaces
            $data = preg_replace('~\s+~', ' ', $data);

            // Remove needless whitespace
            $data = preg_replace('~ *+([{}+>:;,]) *~', '$1', trim($data));

            // Remove ; that closes last property of each declaration
            $data = str_replace(';}', '}', $data);

            // Remove empty CSS declarations
            $data = preg_replace('~[^{}]++\{\}~', '', $data);


            return $data;
    }

    private function _js_compress($data)
    {
            $packer = new JavaScriptPacker($data, $this->pack_js);
            return $packer->pack();
    }
    
}
?>