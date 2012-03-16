<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Container operations
 *
 * Containers are storage compartments where you put your data (objects).
 * A container is similar to a directory or folder on a conventional filesystem
 * with the exception that they exist in a flat namespace, you can not create
 * containers inside of containers.
 *
 * You also have the option of marking a Container as "public" so that the
 * Objects stored in the Container are publicly available via the CDN.
 *
 * @package php-cloudfiles
 */
class CF_Container
{
    public $cfs_auth;
    public $cfs_http;
    public $name;
    public $object_count;
    public $bytes_used;

    public $cdn_enabled;
    public $cdn_streaming_uri;
    public $cdn_ssl_uri;
    public $cdn_uri;
    public $cdn_ttl;
    public $cdn_log_retention;
    public $cdn_acl_user_agent;
    public $cdn_acl_referrer;

    /**
     * Class constructor
     *
     * Constructor for Container
     *
     * @param obj $cfs_auth CF_Authentication instance
     * @param obj $cfs_http HTTP connection manager
     * @param string $name name of Container
     * @param int $count number of Objects stored in this Container
     * @param int $bytes number of bytes stored in this Container
     * @throws SyntaxException invalid Container name
     */
    function __construct(&$cfs_auth, &$cfs_http, $name, $count=0,
        $bytes=0, $docdn=True)
    {
        if (strlen($name) > MAX_CONTAINER_NAME_LEN) {
            throw new Kohana_Exception("Container name exceeds "
                . "maximum allowed length.");
        }
        if (strpos($name, "/") !== False) {
            throw new Kohana_Exception(
                "Container names cannot contain a '/' character.");
        }
        $this->cfs_auth = $cfs_auth;
        $this->cfs_http = $cfs_http;
        $this->name = $name;
        $this->object_count = $count;
        $this->bytes_used = $bytes;
        $this->cdn_enabled = NULL;
        $this->cdn_uri = NULL;
        $this->cdn_ssl_uri = NULL;
        $this->cdn_streaming_uri = NULL;
        $this->cdn_ttl = NULL;
        $this->cdn_log_retention = NULL;
        $this->cdn_acl_user_agent = NULL;
        $this->cdn_acl_referrer = NULL;
        if ($this->cfs_http->getCDNMUrl() != NULL && $docdn) {
            $this->_cdn_initialize();
        }
    }

    /**
     * String representation of Container
     *
     * Pretty print the Container instance.
     *
     * @return string Container details
     */
    function __toString()
    {
        $me = sprintf("name: %s, count: %.0f, bytes: %.0f",
            $this->name, $this->object_count, $this->bytes_used);
        if ($this->cfs_http->getCDNMUrl() != NULL) {
            $me .= sprintf(", cdn: %s, cdn uri: %s, cdn ttl: %.0f, logs retention: %s",
                $this->is_public() ? "Yes" : "No",
                $this->cdn_uri, $this->cdn_ttl,
                $this->cdn_log_retention ? "Yes" : "No"
                );

            if ($this->cdn_acl_user_agent != NULL) {
                $me .= ", cdn acl user agent: " . $this->cdn_acl_user_agent;
            }

            if ($this->cdn_acl_referrer != NULL) {
                $me .= ", cdn acl referrer: " . $this->cdn_acl_referrer;
            }


        }
        return $me;
    }

    /**
     * Enable Container content to be served via CDN or modify CDN attributes
     *
     * Either enable this Container's content to be served via CDN or
     * adjust its CDN attributes.  This Container will always return the
     * same CDN-enabled URI each time it is toggled public/private/public.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $public_container = $conn->create_container("public");
     *
     * # CDN-enable the container and set it's TTL for a month
     * #
     * $public_container->make_public(86400/2); # 12 hours (86400 seconds/day)
     * </code>
     *
     * @param int $ttl the time in seconds content will be cached in the CDN
     * @returns string the CDN enabled Container's URI
     * @throws CDNNotEnabledException CDN functionality not returned during auth
     * @throws AuthenticationException if auth token is not valid/expired
     * @throws InvalidResponseException unexpected response
     */
    function make_public($ttl=86400)
    {
        if ($this->cfs_http->getCDNMUrl() == NULL) {
            throw new Kohana_Exception(
                "Authentication response did not indicate CDN availability");
        }
        if ($this->cdn_uri != NULL) {
            # previously published, assume we're setting new attributes
            list($status, $reason, $cdn_uri, $cdn_ssl_uri) =
                $this->cfs_http->update_cdn_container($this->name,$ttl,
                                                      $this->cdn_log_retention,
                                                      $this->cdn_acl_user_agent,
                                                      $this->cdn_acl_referrer);
            #if ($status == 401 && $this->_re_auth()) {
            #    return $this->make_public($ttl);
            #}
            if ($status == 404) {
                # this instance _thinks_ the container was published, but the
                # cdn management system thinks otherwise - try again with a PUT
                list($status, $reason, $cdn_uri, $cdn_ssl_uri) =
                    $this->cfs_http->add_cdn_container($this->name,$ttl);

            }
        } else {
            # publish it for first time
            list($status, $reason, $cdn_uri, $cdn_ssl_uri) =
                $this->cfs_http->add_cdn_container($this->name,$ttl);
        }
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->make_public($ttl);
        #}
        if (!in_array($status, array(201,202))) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        $this->cdn_enabled = True;
        $this->cdn_ttl = $ttl;
        $this->cdn_ssl_uri = $cdn_ssl_uri;
        $this->cdn_uri = $cdn_uri;
        $this->cdn_log_retention = False;
        $this->cdn_acl_user_agent = "";
        $this->cdn_acl_referrer = "";
        return $this->cdn_uri;
    }
    /**
     * Purge Containers objects from CDN Cache.
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     * $container = $conn->get_container("cdn_enabled");
     * $container->purge_from_cdn("user@domain.com");
     * # or
     * $container->purge_from_cdn();
     * # or
     * $container->purge_from_cdn("user1@domain.com,user2@domain.com");
     * @returns boolean True if successful
     * @throws CDNNotEnabledException if CDN Is not enabled on this connection
     * @throws InvalidResponseException if the response expected is not returned
     */
    function purge_from_cdn($email=null)
    {
        if (!$this->cfs_http->getCDNMUrl())
        {
            throw new Kohana_Exception(
                "Authentication response did not indicate CDN availability");
        }
        $status = $this->cfs_http->purge_from_cdn($this->name, $email);
        if ($status < 199 or $status > 299) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        return True;
    }
    /**
     * Enable ACL restriction by User Agent for this container.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $public_container = $conn->get_container("public");
     *
     * # Enable ACL by Referrer
     * $public_container->acl_referrer("Mozilla");
     * </code>
     *
     * @returns boolean True if successful
     * @throws CDNNotEnabledException CDN functionality not returned during auth
     * @throws AuthenticationException if auth token is not valid/expired
     * @throws InvalidResponseException unexpected response
     */
    function acl_user_agent($cdn_acl_user_agent="") {
        if ($this->cfs_http->getCDNMUrl() == NULL) {
            throw new Kohana_Exception(
                "Authentication response did not indicate CDN availability");
        }
        list($status,$reason) =
            $this->cfs_http->update_cdn_container($this->name,
                                                  $this->cdn_ttl,
                                                  $this->cdn_log_retention,
                                                  $cdn_acl_user_agent,
                                                  $this->cdn_acl_referrer
                );
        if (!in_array($status, array(202,404))) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        $this->cdn_acl_user_agent = $cdn_acl_user_agent;
        return True;
    }

    /**
     * Enable ACL restriction by referer for this container.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $public_container = $conn->get_container("public");
     *
     * # Enable Referrer
     * $public_container->acl_referrer("http://www.example.com/gallery.php");
     * </code>
     *
     * @returns boolean True if successful
     * @throws CDNNotEnabledException CDN functionality not returned during auth
     * @throws AuthenticationException if auth token is not valid/expired
     * @throws InvalidResponseException unexpected response
     */
    function acl_referrer($cdn_acl_referrer="") {
        if ($this->cfs_http->getCDNMUrl() == NULL) {
            throw new Kohana_Exception(
                "Authentication response did not indicate CDN availability");
        }
        list($status,$reason) =
            $this->cfs_http->update_cdn_container($this->name,
                                                  $this->cdn_ttl,
                                                  $this->cdn_log_retention,
                                                  $this->cdn_acl_user_agent,
                                                  $cdn_acl_referrer
                );
        if (!in_array($status, array(202,404))) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        $this->cdn_acl_referrer = $cdn_acl_referrer;
        return True;
    }

    /**
     * Enable log retention for this CDN container.
     *
     * Enable CDN log retention on the container. If enabled logs will
     * be periodically (at unpredictable intervals) compressed and
     * uploaded to a ".CDN_ACCESS_LOGS" container in the form of
     * "container_name.YYYYMMDDHH-XXXX.gz". Requires CDN be enabled on
     * the account.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $public_container = $conn->get_container("public");
     *
     * # Enable logs retention.
     * $public_container->log_retention(True);
     * </code>
     *
     * @returns boolean True if successful
     * @throws CDNNotEnabledException CDN functionality not returned during auth
     * @throws AuthenticationException if auth token is not valid/expired
     * @throws InvalidResponseException unexpected response
     */
    function log_retention($cdn_log_retention=False) {
        if ($this->cfs_http->getCDNMUrl() == NULL) {
            throw new Kohana_Exception(
                "Authentication response did not indicate CDN availability");
        }
        list($status,$reason) =
            $this->cfs_http->update_cdn_container($this->name,
                                                  $this->cdn_ttl,
                                                  $cdn_log_retention,
                                                  $this->cdn_acl_user_agent,
                                                  $this->cdn_acl_referrer
                );
        if (!in_array($status, array(202,404))) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        $this->cdn_log_retention = $cdn_log_retention;
        return True;
    }

    /**
     * Disable the CDN sharing for this container
     *
     * Use this method to disallow distribution into the CDN of this Container's
     * content.
     *
     * NOTE: Any content already cached in the CDN will continue to be served
     *       from its cache until the TTL expiration transpires.  The default
     *       TTL is typically one day, so "privatizing" the Container will take
     *       up to 24 hours before the content is purged from the CDN cache.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $public_container = $conn->get_container("public");
     *
     * # Disable CDN accessability
     * # ... still cached up to a month based on previous example
     * #
     * $public_container->make_private();
     * </code>
     *
     * @returns boolean True if successful
     * @throws CDNNotEnabledException CDN functionality not returned during auth
     * @throws AuthenticationException if auth token is not valid/expired
     * @throws InvalidResponseException unexpected response
     */
    function make_private()
    {
        if ($this->cfs_http->getCDNMUrl() == NULL) {
            throw new Kohana_Exception(
                "Authentication response did not indicate CDN availability");
        }
        list($status,$reason) = $this->cfs_http->remove_cdn_container($this->name);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->make_private();
        #}
        if (!in_array($status, array(202,404))) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        $this->cdn_enabled = False;
        $this->cdn_ttl = NULL;
        $this->cdn_uri = NULL;
        $this->cdn_ssl_uri = NULL;
        $this->cdn_streaming_uri - NULL;
        $this->cdn_log_retention = NULL;
        $this->cdn_acl_user_agent = NULL;
        $this->cdn_acl_referrer = NULL;
        return True;
    }

    /**
     * Check if this Container is being publicly served via CDN
     *
     * Use this method to determine if the Container's content is currently
     * available through the CDN.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $public_container = $conn->get_container("public");
     *
     * # Display CDN accessability
     * #
     * $public_container->is_public() ? print "Yes" : print "No";
     * </code>
     *
     * @returns boolean True if enabled, False otherwise
     */
    function is_public()
    {
        return $this->cdn_enabled == True ? True : False;
    }

    /**
     * Create a new remote storage Object
     *
     * Return a new Object instance.  If the remote storage Object exists,
     * the instance's attributes are populated.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $public_container = $conn->get_container("public");
     *
     * # This creates a local instance of a storage object but only creates
     * # it in the storage system when the object's write() method is called.
     * #
     * $pic = $public_container->create_object("baby.jpg");
     * </code>
     *
     * @param string $obj_name name of storage Object
     * @return obj CF_Object instance
     */
    function create_object($obj_name=NULL)
    {
        return new CF_Object($this, $obj_name);
    }

    /**
     * Return an Object instance for the remote storage Object
     *
     * Given a name, return a Object instance representing the
     * remote storage object.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $public_container = $conn->get_container("public");
     *
     * # This call only fetches header information and not the content of
     * # the storage object.  Use the Object's read() or stream() methods
     * # to obtain the object's data.
     * #
     * $pic = $public_container->get_object("baby.jpg");
     * </code>
     *
     * @param string $obj_name name of storage Object
     * @return obj CF_Object instance
     */
    function get_object($obj_name=NULL)
    {
        return new CF_Object($this, $obj_name, True);
    }

    /**
     * Return a list of Objects
     *
     * Return an array of strings listing the Object names in this Container.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $images = $conn->get_container("my photos");
     *
     * # Grab the list of all storage objects
     * #
     * $all_objects = $images->list_objects();
     *
     * # Grab subsets of all storage objects
     * #
     * $first_ten = $images->list_objects(10);
     *
     * # Note the use of the previous result's last object name being
     * # used as the 'marker' parameter to fetch the next 10 objects
     * #
     * $next_ten = $images->list_objects(10, $first_ten[count($first_ten)-1]);
     *
     * # Grab images starting with "birthday_party" and default limit/marker
     * # to match all photos with that prefix
     * #
     * $prefixed = $images->list_objects(0, NULL, "birthday");
     *
     * # Assuming you have created the appropriate directory marker Objects,
     * # you can traverse your pseudo-hierarchical containers
     * # with the "path" argument.
     * #
     * $animals = $images->list_objects(0,NULL,NULL,"pictures/animals");
     * $dogs = $images->list_objects(0,NULL,NULL,"pictures/animals/dogs");
     * </code>
     *
     * @param int $limit <i>optional</i> only return $limit names
     * @param int $marker <i>optional</i> subset of names starting at $marker
     * @param string $prefix <i>optional</i> Objects whose names begin with $prefix
     * @param string $path <i>optional</i> only return results under "pathname"
     * @return array array of strings
     * @throws InvalidResponseException unexpected response
     */
    function list_objects($limit=0, $marker=NULL, $prefix=NULL, $path=NULL)
    {
        list($status, $reason, $obj_list) =
            $this->cfs_http->list_objects($this->name, $limit,
                $marker, $prefix, $path);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->list_objects($limit, $marker, $prefix, $path);
        #}
        if ($status < 200 || $status > 299) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        return $obj_list;
    }

    /**
     * Return an array of Objects
     *
     * Return an array of Object instances in this Container.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $images = $conn->get_container("my photos");
     *
     * # Grab the list of all storage objects
     * #
     * $all_objects = $images->get_objects();
     *
     * # Grab subsets of all storage objects
     * #
     * $first_ten = $images->get_objects(10);
     *
     * # Note the use of the previous result's last object name being
     * # used as the 'marker' parameter to fetch the next 10 objects
     * #
     * $next_ten = $images->list_objects(10, $first_ten[count($first_ten)-1]);
     *
     * # Grab images starting with "birthday_party" and default limit/marker
     * # to match all photos with that prefix
     * #
     * $prefixed = $images->get_objects(0, NULL, "birthday");
     *
     * # Assuming you have created the appropriate directory marker Objects,
     * # you can traverse your pseudo-hierarchical containers
     * # with the "path" argument.
     * #
     * $animals = $images->get_objects(0,NULL,NULL,"pictures/animals");
     * $dogs = $images->get_objects(0,NULL,NULL,"pictures/animals/dogs");
     * </code>
     *
     * @param int $limit <i>optional</i> only return $limit names
     * @param int $marker <i>optional</i> subset of names starting at $marker
     * @param string $prefix <i>optional</i> Objects whose names begin with $prefix
     * @param string $path <i>optional</i> only return results under "pathname"
     * @return array array of strings
     * @throws InvalidResponseException unexpected response
     */
    function get_objects($limit=0, $marker=NULL, $prefix=NULL, $path=NULL)
    {
        list($status, $reason, $obj_array) =
            $this->cfs_http->get_objects($this->name, $limit,
                $marker, $prefix, $path);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->get_objects($limit, $marker, $prefix, $path);
        #}
        if ($status < 200 || $status > 299) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        $objects = array();
        foreach ($obj_array as $obj) {
            $tmp = new CF_Object($this, $obj["name"], False, False);
            $tmp->content_type = $obj["content_type"];
            $tmp->content_length = (float) $obj["bytes"];
            $tmp->set_etag($obj["hash"]);
            $tmp->last_modified = $obj["last_modified"];
            $objects[] = $tmp;
        }
        return $objects;
    }

    /**
     * Copy a remote storage Object to a target Container
     *
     * Given an Object instance or name and a target Container instance or name, copy copies the remote Object
     * and all associated metadata.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $images = $conn->get_container("my photos");
     *
     * # Copy specific object
     * #
     * $images->copy_object_to("disco_dancing.jpg","container_target");
     * </code>
     *
     * @param obj $obj name or instance of Object to copy
     * @param obj $container_target name or instance of target Container
     * @param string $dest_obj_name name of target object (optional - uses source name if omitted)
     * @param array $metadata metadata array for new object (optional)
     * @param array $headers header fields array for the new object (optional)
     * @return boolean <kbd>true</kbd> if successfully copied
     * @throws SyntaxException invalid Object/Container name
     * @throws NoSuchObjectException remote Object does not exist
     * @throws InvalidResponseException unexpected response
     */
    function copy_object_to($obj,$container_target,$dest_obj_name=NULL,$metadata=NULL,$headers=NULL)
    {
        $obj_name = NULL;
        if (is_object($obj)) {
            if (get_class($obj) == "CF_Object") {
                $obj_name = $obj->name;
            }
        }
        if (is_string($obj)) {
            $obj_name = $obj;
        }
        if (!$obj_name) {
            throw new Kohana_Exception("Object name not set.");
        }

				if ($dest_obj_name === NULL) {
            $dest_obj_name = $obj_name;
				}

        $container_name_target = NULL;
        if (is_object($container_target)) {
            if (get_class($container_target) == "CF_Container") {
                $container_name_target = $container_target->name;
            }
        }
        if (is_string($container_target)) {
            $container_name_target = $container_target;
        }
        if (!$container_name_target) {
            throw new Kohana_Exception("Container name target not set.");
        }

        $status = $this->cfs_http->copy_object($obj_name,$dest_obj_name,$this->name,$container_name_target,$metadata,$headers);
        if ($status == 404) {
            $m = "Specified object '".$this->name."/".$obj_name;
            $m.= "' did not exist as source to copy from or '".$container_name_target."' did not exist as target to copy to.";
            throw new Kohana_Exception($m);
        }
        if ($status < 200 || $status > 299) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        return true;
    }

    /**
     * Copy a remote storage Object from a source Container
     *
     * Given an Object instance or name and a source Container instance or name, copy copies the remote Object
     * and all associated metadata.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $images = $conn->get_container("my photos");
     *
     * # Copy specific object
     * #
     * $images->copy_object_from("disco_dancing.jpg","container_source");
     * </code>
     *
     * @param obj $obj name or instance of Object to copy
     * @param obj $container_source name or instance of source Container
     * @param string $dest_obj_name name of target object (optional - uses source name if omitted)
     * @param array $metadata metadata array for new object (optional)
     * @param array $headers header fields array for the new object (optional)
     * @return boolean <kbd>true</kbd> if successfully copied
     * @throws SyntaxException invalid Object/Container name
     * @throws NoSuchObjectException remote Object does not exist
     * @throws InvalidResponseException unexpected response
     */
    function copy_object_from($obj,$container_source,$dest_obj_name=NULL,$metadata=NULL,$headers=NULL)
    {
        $obj_name = NULL;
        if (is_object($obj)) {
            if (get_class($obj) == "CF_Object") {
                $obj_name = $obj->name;
            }
        }
        if (is_string($obj)) {
            $obj_name = $obj;
        }
        if (!$obj_name) {
            throw new Kohana_Exception("Object name not set.");
        }

				if ($dest_obj_name === NULL) {
            $dest_obj_name = $obj_name;
				}

        $container_name_source = NULL;
        if (is_object($container_source)) {
            if (get_class($container_source) == "CF_Container") {
                $container_name_source = $container_source->name;
            }
        }
        if (is_string($container_source)) {
            $container_name_source = $container_source;
        }
        if (!$container_name_source) {
            throw new Kohana_Exception("Container name source not set.");
        }

        $status = $this->cfs_http->copy_object($obj_name,$dest_obj_name,$container_name_source,$this->name,$metadata,$headers);
        if ($status == 404) {
            $m = "Specified object '".$container_name_source."/".$obj_name;
            $m.= "' did not exist as source to copy from or '".$this->name."/".$obj_name."' did not exist as target to copy to.";
            throw new Kohana_Exception($m);
        }
        if ($status < 200 || $status > 299) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }

        return true;
    }

    /**
     * Move a remote storage Object to a target Container
     *
     * Given an Object instance or name and a target Container instance or name, move copies the remote Object
     * and all associated metadata and deletes the source Object afterwards
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $images = $conn->get_container("my photos");
     *
     * # Move specific object
     * #
     * $images->move_object_to("disco_dancing.jpg","container_target");
     * </code>
     *
     * @param obj $obj name or instance of Object to move
     * @param obj $container_target name or instance of target Container
     * @param string $dest_obj_name name of target object (optional - uses source name if omitted)
     * @param array $metadata metadata array for new object (optional)
     * @param array $headers header fields array for the new object (optional)
     * @return boolean <kbd>true</kbd> if successfully moved
     * @throws SyntaxException invalid Object/Container name
     * @throws NoSuchObjectException remote Object does not exist
     * @throws InvalidResponseException unexpected response
     */
    function move_object_to($obj,$container_target,$dest_obj_name=NULL,$metadata=NULL,$headers=NULL)
    {
    	$retVal = false;

        if(self::copy_object_to($obj,$container_target,$dest_obj_name,$metadata,$headers)) {
        	$retVal = self::delete_object($obj,$this->name);
        }

        return $retVal;
    }

    /**
     * Move a remote storage Object from a source Container
     *
     * Given an Object instance or name and a source Container instance or name, move copies the remote Object
     * and all associated metadata and deletes the source Object afterwards
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $images = $conn->get_container("my photos");
     *
     * # Move specific object
     * #
     * $images->move_object_from("disco_dancing.jpg","container_target");
     * </code>
     *
     * @param obj $obj name or instance of Object to move
     * @param obj $container_source name or instance of target Container
     * @param string $dest_obj_name name of target object (optional - uses source name if omitted)
     * @param array $metadata metadata array for new object (optional)
     * @param array $headers header fields array for the new object (optional)
     * @return boolean <kbd>true</kbd> if successfully moved
     * @throws SyntaxException invalid Object/Container name
     * @throws NoSuchObjectException remote Object does not exist
     * @throws InvalidResponseException unexpected response
     */
    function move_object_from($obj,$container_source,$dest_obj_name=NULL,$metadata=NULL,$headers=NULL)
    {
    	$retVal = false;

        if(self::copy_object_from($obj,$container_source,$dest_obj_name,$metadata,$headers)) {
        	$retVal = self::delete_object($obj,$container_source);
        }

        return $retVal;
    }

    /**
     * Delete a remote storage Object
     *
     * Given an Object instance or name, permanently remove the remote Object
     * and all associated metadata.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $images = $conn->get_container("my photos");
     *
     * # Delete specific object
     * #
     * $images->delete_object("disco_dancing.jpg");
     * </code>
     *
     * @param obj $obj name or instance of Object to delete
     * @param obj $container name or instance of Container in which the object resides (optional)
     * @return boolean <kbd>True</kbd> if successfully removed
     * @throws SyntaxException invalid Object name
     * @throws NoSuchObjectException remote Object does not exist
     * @throws InvalidResponseException unexpected response
     */
    function delete_object($obj,$container=NULL)
    {
        $obj_name = NULL;
        if (is_object($obj)) {
            if (get_class($obj) == "CF_Object") {
                $obj_name = $obj->name;
            }
        }
        if (is_string($obj)) {
            $obj_name = $obj;
        }
        if (!$obj_name) {
            throw new Kohana_Exception("Object name not set.");
        }

        $container_name = NULL;

        if($container === NULL) {
        	$container_name = $this->name;
        }
        else {
	        if (is_object($container)) {
	            if (get_class($container) == "CF_Container") {
	                $container_name = $container->name;
	            }
	        }
	        if (is_string($container)) {
	            $container_name = $container;
	        }
	        if (!$container_name) {
	            throw new Kohana_Exception("Container name source not set.");
	        }
        }

        $status = $this->cfs_http->delete_object($container_name, $obj_name);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->delete_object($obj);
        #}
        if ($status == 404) {
            $m = "Specified object '".$container_name."/".$obj_name;
            $m.= "' did not exist to delete.";
            throw new Kohana_Exception($m);
        }
        if ($status != 204) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        return True;
    }

    /**
     * Helper function to create "path" elements for a given Object name
     *
     * Given an Object whos name contains '/' path separators, this function
     * will create the "directory marker" Objects of one byte with the
     * Content-Type of "application/directory".
     *
     * It assumes the last element of the full path is the "real" Object
     * and does NOT create a remote storage Object for that last element.
     */
    function create_paths($path_name)
    {
        if ($path_name[0] == '/') {
            $path_name = mb_substr($path_name, 0, 1);
        }
        $elements = explode('/', $path_name, -1);
        $build_path = "";
        foreach ($elements as $idx => $val) {
            if (!$build_path) {
                $build_path = $val;
            } else {
                $build_path .= "/" . $val;
            }
            $obj = new CF_Object($this, $build_path);
            $obj->content_type = "application/directory";
            $obj->write(".", 1);
        }
    }

    /**
     * Internal method to grab CDN/Container info if appropriate to do so
     *
     * @throws InvalidResponseException unexpected response
     */
    private function _cdn_initialize()
    {
        list($status, $reason, $cdn_enabled, $cdn_ssl_uri, $cdn_streaming_uri, $cdn_uri, $cdn_ttl,
             $cdn_log_retention, $cdn_acl_user_agent, $cdn_acl_referrer) =
            $this->cfs_http->head_cdn_container($this->name);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->_cdn_initialize();
        #}
        if (!in_array($status, array(204,404))) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        $this->cdn_enabled = $cdn_enabled;
        $this->cdn_streaming_uri = $cdn_streaming_uri;
        $this->cdn_ssl_uri = $cdn_ssl_uri;
        $this->cdn_uri = $cdn_uri;
        $this->cdn_ttl = $cdn_ttl;
        $this->cdn_log_retention = $cdn_log_retention;
        $this->cdn_acl_user_agent = $cdn_acl_user_agent;
        $this->cdn_acl_referrer = $cdn_acl_referrer;
    }

    #private function _re_auth()
    #{
    #    $new_auth = new CF_Authentication(
    #        $this->cfs_auth->username,
    #        $this->cfs_auth->api_key,
    #        $this->cfs_auth->auth_host,
    #        $this->cfs_auth->account);
    #    $new_auth->authenticate();
    #    $this->cfs_auth = $new_auth;
    #    $this->cfs_http->setCFAuth($this->cfs_auth);
    #    return True;
    #}
}

?>