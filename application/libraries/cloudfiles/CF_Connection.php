<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Class for establishing connections to the Cloud Files storage system.
 * Connection instances are used to communicate with the storage system at
 * the account level; listing and deleting Containers and returning Container
 * instances.
 *
 * Example:
 * <code>
 * # Create the authentication instance
 * #
 * $auth = new CF_Authentication("username", "api_key");
 *
 * # Perform authentication request
 * #
 * $auth->authenticate();
 *
 * # Create a connection to the storage/cdn system(s) and pass in the
 * # validated CF_Authentication instance.
 * #
 * $conn = new CF_Connection($auth);
 *
 * # NOTE: Some versions of cURL include an outdated certificate authority (CA)
 * #       file.  This API ships with a newer version obtained directly from
 * #       cURL's web site (http://curl.haxx.se).  To use the newer CA bundle,
 * #       call the CF_Authentication instance's 'ssl_use_cabundle()' method.
 * #
 * # $conn->ssl_use_cabundle(); # bypass cURL's old CA bundle
 * </code>
 *
 * @package php-cloudfiles
 */
class CF_Connection
{
    public $dbug;
    public $cfs_http;
    public $cfs_auth;

    /**
     * Pass in a previously authenticated CF_Authentication instance.
     *
     * Example:
     * <code>
     * # Create the authentication instance
     * #
     * $auth = new CF_Authentication("username", "api_key");
     *
     * # Perform authentication request
     * #
     * $auth->authenticate();
     *
     * # Create a connection to the storage/cdn system(s) and pass in the
     * # validated CF_Authentication instance.
     * #
     * $conn = new CF_Connection($auth);
     *
     * # If you are connecting via Rackspace servers and have access
     * # to the servicenet network you can set the $servicenet to True
     * # like this.
     *
     * $conn = new CF_Connection($auth, $servicenet=True);
     *
     * </code>
     *
     * If the environement variable RACKSPACE_SERVICENET is defined it will
     * force to connect via the servicenet.
     *
     * @param obj $cfs_auth previously authenticated CF_Authentication instance
     * @param boolean $servicenet enable/disable access via Rackspace servicenet.
     * @throws AuthenticationException not authenticated
     */
    function __construct($cfs_auth, $servicenet=False)
    {
        if (isset($_ENV['RACKSPACE_SERVICENET']))
            $servicenet=True;
        $this->cfs_http = new CF_Http(DEFAULT_CF_API_VERSION);
        $this->cfs_auth = $cfs_auth;
        if (!$this->cfs_auth->authenticated()) {
            $e = "Need to pass in a previously authenticated ";
            $e .= "CF_Authentication instance.";
            throw new Kohana_Exception($e);
        }
        $this->cfs_http->setCFAuth($this->cfs_auth, $servicenet=$servicenet);
        $this->dbug = False;
    }

    /**
     * Toggle debugging of instance and back-end HTTP module
     *
     * @param boolean $bool enable/disable cURL debugging
     */
    function setDebug($bool)
    {
        $this->dbug = (boolean) $bool;
        $this->cfs_http->setDebug($this->dbug);
    }

    /**
     * Close a connection
     *
     * Example:
     * <code>
     *
     * $conn->close();
     *
     * </code>
     *
     * Will close all current cUrl active connections.
     *
     */
    public function close()
    {
        $this->cfs_http->close();
    }

    /**
     * Cloud Files account information
     *
     * Return an array of two floats (since PHP only supports 32-bit integers);
     * number of containers on the account and total bytes used for the account.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * list($quantity, $bytes) = $conn->get_info();
     * print "Number of containers: " . $quantity . "\n";
     * print "Bytes stored in container: " . $bytes . "\n";
     * </code>
     *
     * @return array (number of containers, total bytes stored)
     * @throws InvalidResponseException unexpected response
     */
    function get_info()
    {
        list($status, $reason, $container_count, $total_bytes) =
                $this->cfs_http->head_account();
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->get_info();
        #}
        if ($status < 200 || $status > 299) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        return array($container_count, $total_bytes);
    }

    /**
     * Create a Container
     *
     * Given a Container name, return a Container instance, creating a new
     * remote Container if it does not exit.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $images = $conn->create_container("my photos");
     * </code>
     *
     * @param string $container_name container name
     * @return CF_Container
     * @throws SyntaxException invalid name
     * @throws InvalidResponseException unexpected response
     */
    function create_container($container_name=NULL)
    {
        if ($container_name != "0" and !isset($container_name))
            throw new Kohana_Exception("Container name not set.");

        if (!isset($container_name) or $container_name == "")
            throw new Kohana_Exception("Container name not set.");

        if (strpos($container_name, "/") !== False) {
            $r = "Container name '".$container_name;
            $r .= "' cannot contain a '/' character.";
            throw new Kohana_Exception($r);
        }
        if (strlen($container_name) > MAX_CONTAINER_NAME_LEN) {
            throw new Kohana_Exception(sprintf(
                "Container name exeeds %d bytes.",
                MAX_CONTAINER_NAME_LEN));
        }

        $return_code = $this->cfs_http->create_container($container_name);
        if (!$return_code) {
            throw new Kohana_Exception("Invalid response ("
                . $return_code. "): " . $this->cfs_http->get_error());
        }
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->create_container($container_name);
        #}
        if ($return_code != 201 && $return_code != 202) {
            throw new Kohana_Exception(
                "Invalid response (".$return_code."): "
                    . $this->cfs_http->get_error());
        }
        return new CF_Container($this->cfs_auth, $this->cfs_http, $container_name);
    }

    /**
     * Delete a Container
     *
     * Given either a Container instance or name, remove the remote Container.
     * The Container must be empty prior to removing it.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $conn->delete_container("my photos");
     * </code>
     *
     * @param string|obj $container container name or instance
     * @return boolean <kbd>True</kbd> if successfully deleted
     * @throws SyntaxException missing proper argument
     * @throws InvalidResponseException invalid response
     * @throws NonEmptyContainerException container not empty
     * @throws NoSuchContainerException remote container does not exist
     */
    function delete_container($container=NULL)
    {
        $container_name = NULL;

        if (is_object($container)) {
            if (get_class($container) == "CF_Container") {
                $container_name = $container->name;
            }
        }
        if (is_string($container)) {
            $container_name = $container;
        }

        if ($container_name != "0" and !isset($container_name))
            throw new Kohana_Exception("Must specify container object or name.");

        $return_code = $this->cfs_http->delete_container($container_name);

        if (!$return_code) {
            throw new Kohana_Exception("Failed to obtain http response");
        }
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->delete_container($container);
        #}
        if ($return_code == 409) {
            throw new Kohana_Exception(
                "Container must be empty prior to removing it.");
        }
        if ($return_code == 404) {
            throw new Kohana_Exception(
                "Specified container did not exist to delete.");
        }
        if ($return_code != 204) {
            throw new Kohana_Exception(
                "Invalid response (".$return_code."): "
                . $this->cfs_http->get_error());
        }
        return True;
    }

    /**
     * Return a Container instance
     *
     * For the given name, return a Container instance if the remote Container
     * exists, otherwise throw a Not Found exception.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $images = $conn->get_container("my photos");
     * print "Number of Objects: " . $images->count . "\n";
     * print "Bytes stored in container: " . $images->bytes . "\n";
     * </code>
     *
     * @param string $container_name name of the remote Container
     * @return container CF_Container instance
     * @throws NoSuchContainerException thrown if no remote Container
     * @throws InvalidResponseException unexpected response
     */
    function get_container($container_name=NULL)
    {
        list($status, $reason, $count, $bytes) =
                $this->cfs_http->head_container($container_name);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->get_container($container_name);
        #}
        if ($status == 404) {
            throw new Kohana_Exception("Container not found.");
        }
        if ($status < 200 || $status > 299) {
            throw new Kohana_Exception(
                "Invalid response: ".$this->cfs_http->get_error());
        }
        return new CF_Container($this->cfs_auth, $this->cfs_http,
            $container_name, $count, $bytes);
    }

    /**
     * Return array of Container instances
     *
     * Return an array of CF_Container instances on the account.  The instances
     * will be fully populated with Container attributes (bytes stored and
     * Object count)
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $clist = $conn->get_containers();
     * foreach ($clist as $cont) {
     *     print "Container name: " . $cont->name . "\n";
     *     print "Number of Objects: " . $cont->count . "\n";
     *     print "Bytes stored in container: " . $cont->bytes . "\n";
     * }
     * </code>
     *
     * @return array An array of CF_Container instances
     * @throws InvalidResponseException unexpected response
     */
    function get_containers($limit=0, $marker=NULL)
    {
        list($status, $reason, $container_info) =
                $this->cfs_http->list_containers_info($limit, $marker);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->get_containers();
        #}
        if ($status < 200 || $status > 299) {
            throw new Kohana_Exception(
                "Invalid response: ".$this->cfs_http->get_error());
        }
        $containers = array();
        foreach ($container_info as $name => $info) {
            $containers[] = new CF_Container($this->cfs_auth, $this->cfs_http,
                $info['name'], $info["count"], $info["bytes"], False);
        }
        return $containers;
    }

    /**
     * Return list of remote Containers
     *
     * Return an array of strings containing the names of all remote Containers.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $container_list = $conn->list_containers();
     * print_r($container_list);
     * Array
     * (
     *     [0] => "my photos",
     *     [1] => "my docs"
     * )
     * </code>
     *
     * @param integer $limit restrict results to $limit Containers
     * @param string $marker return results greater than $marker
     * @return array list of remote Containers
     * @throws InvalidResponseException unexpected response
     */
    function list_containers($limit=0, $marker=NULL)
    {
        list($status, $reason, $containers) =
            $this->cfs_http->list_containers($limit, $marker);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->list_containers($limit, $marker);
        #}
        if ($status < 200 || $status > 299) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        return $containers;
    }

    /**
     * Return array of information about remote Containers
     *
     * Return a nested array structure of Container info.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     *
     * $container_info = $conn->list_containers_info();
     * print_r($container_info);
     * Array
     * (
     *     ["my photos"] =>
     *         Array
     *         (
     *             ["bytes"] => 78,
     *             ["count"] => 2
     *         )
     *     ["docs"] =>
     *         Array
     *         (
     *             ["bytes"] => 37323,
     *             ["count"] => 12
     *         )
     * )
     * </code>
     *
     * @param integer $limit restrict results to $limit Containers
     * @param string $marker return results greater than $marker
     * @return array nested array structure of Container info
     * @throws InvalidResponseException unexpected response
     */
    function list_containers_info($limit=0, $marker=NULL)
    {
        list($status, $reason, $container_info) =
                $this->cfs_http->list_containers_info($limit, $marker);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->list_containers_info($limit, $marker);
        #}
        if ($status < 200 || $status > 299) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        return $container_info;
    }

    /**
     * Return list of Containers that have been published to the CDN.
     *
     * Return an array of strings containing the names of published Containers.
     * Note that this function returns the list of any Container that has
     * ever been CDN-enabled regardless of it's existence in the storage
     * system.
     *
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     *
     * $public_containers = $conn->list_public_containers();
     * print_r($public_containers);
     * Array
     * (
     *     [0] => "images",
     *     [1] => "css",
     *     [2] => "javascript"
     * )
     * </code>
     *
     * @param bool $enabled_only Will list all containers ever CDN enabled if     * set to false or only currently enabled CDN containers if set to true.      * Defaults to false.
     * @return array list of published Container names
     * @throws InvalidResponseException unexpected response
     */
    function list_public_containers($enabled_only=False)
    {
        list($status, $reason, $containers) =
                $this->cfs_http->list_cdn_containers($enabled_only);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->list_public_containers();
        #}
        if ($status < 200 || $status > 299) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }
        return $containers;
    }

    /**
     * Set a user-supplied callback function to report download progress
     *
     * The callback function is used to report incremental progress of a data
     * download functions (e.g. $container->list_objects(), $obj->read(), etc).
     * The specified function will be periodically called with the number of
     * bytes transferred until the entire download is complete.  This callback
     * function can be useful for implementing "progress bars" for large
     * downloads.
     *
     * The specified callback function should take a single integer parameter.
     *
     * <code>
     * function read_callback($bytes_transferred) {
     *     print ">> downloaded " . $bytes_transferred . " bytes.\n";
     *     # ... do other things ...
     *     return;
     * }
     *
     * $conn = new CF_Connection($auth_obj);
     * $conn->set_read_progress_function("read_callback");
     * print_r($conn->list_containers());
     *
     * # output would look like this:
     * #
     * >> downloaded 10 bytes.
     * >> downloaded 11 bytes.
     * Array
     * (
     *      [0] => fuzzy.txt
     *      [1] => space name
     * )
     * </code>
     *
     * @param string $func_name the name of the user callback function
     */
    function set_read_progress_function($func_name)
    {
        $this->cfs_http->setReadProgressFunc($func_name);
    }

    /**
     * Set a user-supplied callback function to report upload progress
     *
     * The callback function is used to report incremental progress of a data
     * upload functions (e.g. $obj->write() call).  The specified function will
     * be periodically called with the number of bytes transferred until the
     * entire upload is complete.  This callback function can be useful
     * for implementing "progress bars" for large uploads/downloads.
     *
     * The specified callback function should take a single integer parameter.
     *
     * <code>
     * function write_callback($bytes_transferred) {
     *     print ">> uploaded " . $bytes_transferred . " bytes.\n";
     *     # ... do other things ...
     *     return;
     * }
     *
     * $conn = new CF_Connection($auth_obj);
     * $conn->set_write_progress_function("write_callback");
     * $container = $conn->create_container("stuff");
     * $obj = $container->create_object("foo");
     * $obj->write("The callback function will be called during upload.");
     *
     * # output would look like this:
     * # >> uploaded 51 bytes.
     * #
     * </code>
     *
     * @param string $func_name the name of the user callback function
     */
    function set_write_progress_function($func_name)
    {
        $this->cfs_http->setWriteProgressFunc($func_name);
    }

    /**
     * Use the Certificate Authority bundle included with this API
     *
     * Most versions of PHP with cURL support include an outdated Certificate
     * Authority (CA) bundle (the file that lists all valid certificate
     * signing authorities).  The SSL certificates used by the Cloud Files
     * storage system are perfectly valid but have been created/signed by
     * a CA not listed in these outdated cURL distributions.
     *
     * As a work-around, we've included an updated CA bundle obtained
     * directly from cURL's web site (http://curl.haxx.se).  You can direct
     * the API to use this CA bundle by calling this method prior to making
     * any remote calls.  The best place to use this method is right after
     * the CF_Authentication instance has been instantiated.
     *
     * You can specify your own CA bundle by passing in the full pathname
     * to the bundle.  You can use the included CA bundle by leaving the
     * argument blank.
     *
     * @param string $path Specify path to CA bundle (default to included)
     */
    function ssl_use_cabundle($path=NULL)
    {
        $this->cfs_http->ssl_use_cabundle($path);
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