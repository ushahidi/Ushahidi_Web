<?php defined('SYSPATH') OR die('No direct access allowed.');
/* Viddler PHP Wrapper for Viddler's API 
  Version 2.2
  Released: December 2010.
  http://developers.viddler.com/projects/api-wrappers/phpviddler/
*/

/* Viddler Class
  use $var = new Viddler(API KEY); */
class ViddlerV2_Core {
  public $api_key = NULL;
  
  // Construct! Like the Matrix.
  public function __construct($api_key) {
    if (!$api_key) {
      return FALSE;
    } else {
      $this->api_key = $api_key;
    }
  }

  /**
  Can be called like such:
  $__api = new Viddler_API("YOUR KEY");
  $array = $__api->viddler_users_getProfile(array("user"=>"phpfunk"));
  **/
  public function __call($method, $args) {
  	return self::call($method, $args, "object");
  }
  
  protected function call($method, $args, $call)
  { 
    /**
    Format the Method
    Accepted Formats:
    
    $viddler->viddler_users_auth();
    **/
    $method = str_replace("_", ".", $method);
    
    //If the method exists here, call it
    if (method_exists($this, $method)) { return $this->$method($args[0]); }
    
    // Used to construct the querystring.
    $query = array();
    
    // Methods that require HTTPS
    $secure_methods = array(
      'viddler.users.auth',
      'viddler.users.register'
    );
    
    // Methods that require POST
    $post_methods = array(
      'viddler.encoding.cancel',
      'viddler.encoding.encode',
      'viddler.encoding.setOptions',
      'viddler.groups.addVideo',
      'viddler.groups.join',
      'viddler.groups.leave',
      'viddler.groups.removeVideo',
      'viddler.playlists.addVideo',
      'viddler.playlists.create',
      'viddler.playlists.delete',
      'viddler.playlists.removeVideo',
      'viddler.playlists.moveVideo',
      'viddler.playslists.setDetails',
      'viddler.users.setSettings',
      'viddler.users.setProfile',
      'viddler.users.setOptions',
      'viddler.users.acceptFriendRequest',
      'viddler.users.ignoreFriendRequest',
      'viddler.users.sendFriendRequest',
      'viddler.users.subscribe',
      'viddler.users.unsubscribe',
      'viddler.videos.setDetails',
      'viddler.videos.setPermalink',
      'viddler.videos.comments.add',
      'viddler.videos.comments.remove',
      'viddler.videos.upload',
      'viddler.videos.delete',
      'viddler.videos.delFile',
      'viddler.videos.favorite',
      'viddler.videos.unfavorite',
      'viddler.videos.setPermalink',
      'viddler.videos.setThumbnail',
      'viddler.videos.setDetails',
      'viddler.videos.enableAds',
      'viddler.videos.disableAds'
    );
    
    // Methods that require Binary transfer
    $binary_methods = array(
      'viddler.videos.setThumbnail',
      'viddler.videos.upload'
    );
    
    $binary = (in_array($method, $binary_methods)) ? TRUE : FALSE;
    $post = (in_array($method, $post_methods)) ? TRUE : FALSE;
    
    // Figure protocol http:// or https://
    $protocol = (in_array($method, $secure_methods)) ? "https" : "http";
    
    // Build API endpoint URL
    // This is generally used to switch the end-point for uploads. See /examples/uploadExample.php in PHPViddler 2
    if(isset($args[1])) {
      $url = $args[1];
    } else {
      $url = $protocol . "://api.viddler.com/api/v2/" . $method . ".php";
    }
    
    if ($post === TRUE) { // Is a post method
        array_push($query, "key=" . $this->api_key); // Adds API key to the POST arguments array
    } else {
      $url .= "?key=" . $this->api_key;
    }
    
    //Figure the query string
    if (@count($args[0]) > 0 && is_array($args[0])) {
      foreach ($args[0] as $k => $v) {
        if ($k != "response_type" && $k != "api_key") {
          array_push($query, "$k=$v");
        }
      }
      $query_arr = $query;
      $query = implode("&", $query);
      if ($post === FALSE) {
        $url .= (!empty($query)) ? "&" . $query : "";
      }
    }
    else {
      $query = NULL;
      $args[0] = array();
    }
    
    // Custruct the cURL call
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_HEADER, 0);
    curl_setopt ($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    
    // Figure POST vs. GET
    if ($post == TRUE) {
      curl_setopt($ch, CURLOPT_POST, TRUE);
      if ($binary === TRUE) {
        $binary_args = array();
        foreach($args[0] as $k=>$v) {
          if($k != 'file') $binary_args[$k] = $v;
        }
        
        if(!isset($binary_args['key'])) $binary_args['key'] = $this->api_key;
        $binary_args['file'] = $args[0]['file'];
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $binary_args);
      }
      else {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
      }
    }
    else {
      curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
    }
    
    //G et the response
    $response = curl_exec($ch);
    
    if (!$response) {
      $response = $error = curl_error($ch);
      
      return $response;
    }
    else {
      $response = unserialize($response);
    }
    
    curl_close($ch);
    return $response;
  }
}

?>