<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Object operations
 *
 * An Object is analogous to a file on a conventional filesystem. You can
 * read data from, or write data to your Objects. You can also associate
 * arbitrary metadata with them.
 *
 * @package php-cloudfiles
 */
class CF_Object
{
    public $container;
    public $name;
    public $last_modified;
    public $content_type;
    public $content_length;
    public $metadata;
    public $headers;
    public $manifest;
    private $etag;

    /**
     * Class constructor
     *
     * @param obj $container CF_Container instance
     * @param string $name name of Object
     * @param boolean $force_exists if set, throw an error if Object doesn't exist
     */
    function __construct(&$container, $name, $force_exists=False, $dohead=True)
    {
        if ($name[0] == "/") {
            $r = "Object name '".$name;
            $r .= "' cannot contain begin with a '/' character.";
            throw new Kohana_Exception($r);
        }
        if (strlen($name) > MAX_OBJECT_NAME_LEN) {
            throw new Kohana_Exception("Object name exceeds "
                . "maximum allowed length.");
        }
        $this->container = $container;
        $this->name = $name;
        $this->etag = NULL;
        $this->_etag_override = False;
        $this->last_modified = NULL;
        $this->content_type = NULL;
        $this->content_length = 0;
        $this->metadata = array();
        $this->headers = array();
        $this->manifest = NULL;
        if ($dohead) {
            if (!$this->_initialize() && $force_exists) {
                throw new Kohana_Exception("No such object '".$name."'");
            }
        }
    }

    /**
     * String representation of Object
     *
     * Pretty print the Object's location and name
     *
     * @return string Object information
     */
    function __toString()
    {
        return $this->container->name . "/" . $this->name;
    }

    /**
     * Internal check to get the proper mimetype.
     *
     * This function would go over the available PHP methods to get
     * the MIME type.
     *
     * By default it will try to use the PHP fileinfo library which is
     * available from PHP 5.3 or as an PECL extension
     * (http://pecl.php.net/package/Fileinfo).
     *
     * It will get the magic file by default from the system wide file
     * which is usually available in /usr/share/magic on Unix or try
     * to use the file specified in the source directory of the API
     * (share directory).
     *
     * if fileinfo is not available it will try to use the internal
     * mime_content_type function.
     *
     * @param string $handle name of file or buffer to guess the type from
     * @return boolean <kbd>True</kbd> if successful
     * @throws BadContentTypeException
     */
    function _guess_content_type($handle) {
        if ($this->content_type)
            return;

        if (function_exists("finfo_open")) {
            $local_magic = dirname(__FILE__) . "/share/magic";
            $finfo = @finfo_open(FILEINFO_MIME, $local_magic);

            if (!$finfo)
                $finfo = @finfo_open(FILEINFO_MIME);

            if ($finfo) {

                if (is_file((string)$handle))
                    $ct = @finfo_file($finfo, $handle);
                else
                    $ct = @finfo_buffer($finfo, $handle);

                /* PHP 5.3 fileinfo display extra information like
                   charset so we remove everything after the ; since
                   we are not into that stuff */
                if ($ct) {
                    $extra_content_type_info = strpos($ct, "; ");
                    if ($extra_content_type_info)
                        $ct = substr($ct, 0, $extra_content_type_info);
                }

                if ($ct && $ct != 'application/octet-stream')
                    $this->content_type = $ct;

                @finfo_close($finfo);
            }
        }

        if (!$this->content_type && (string)is_file($handle) && function_exists("mime_content_type")) {
            $this->content_type = @mime_content_type($handle);
        }

        if (!$this->content_type) {
            throw new Kohana_Exception("Required Content-Type not set");
        }
        return True;
    }

    /**
     * String representation of the Object's public URI
     *
     * A string representing the Object's public URI assuming that it's
     * parent Container is CDN-enabled.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * # Print out the Object's CDN URI (if it has one) in an HTML img-tag
     * #
     * print "<img src='$pic->public_uri()' />\n";
     * </code>
     *
     * @return string Object's public URI or NULL
     */
    function public_uri()
    {
        if ($this->container->cdn_enabled) {
            return $this->container->cdn_uri . "/" . $this->name;
        }
        return NULL;
    }

       /**
     * String representation of the Object's public SSL URI
     *
     * A string representing the Object's public SSL URI assuming that it's
     * parent Container is CDN-enabled.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * # Print out the Object's CDN SSL URI (if it has one) in an HTML img-tag
     * #
     * print "<img src='$pic->public_ssl_uri()' />\n";
     * </code>
     *
     * @return string Object's public SSL URI or NULL
     */
    function public_ssl_uri()
    {
        if ($this->container->cdn_enabled) {
            return $this->container->cdn_ssl_uri . "/" . $this->name;
        }
        return NULL;
    }
    /**
     * String representation of the Object's public Streaming URI
     *
     * A string representing the Object's public Streaming URI assuming that it's
     * parent Container is CDN-enabled.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * # Print out the Object's CDN Streaming URI (if it has one) in an HTML img-tag
     * #
     * print "<img src='$pic->public_streaming_uri()' />\n";
     * </code>
     *
     * @return string Object's public Streaming URI or NULL
     */
    function public_streaming_uri()
    {
        if ($this->container->cdn_enabled) {
            return $this->container->cdn_streaming_uri . "/" . $this->name;
        }
        return NULL;
    }

    /**
     * Read the remote Object's data
     *
     * Returns the Object's data.  This is useful for smaller Objects such
     * as images or office documents.  Object's with larger content should use
     * the stream() method below.
     *
     * Pass in $hdrs array to set specific custom HTTP headers such as
     * If-Match, If-None-Match, If-Modified-Since, Range, etc.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * $my_docs = $conn->get_container("documents");
     * $doc = $my_docs->get_object("README");
     * $data = $doc->read(); # read image content into a string variable
     * print $data;
     *
     * # Or see stream() below for a different example.
     * #
     * </code>
     *
     * @param array $hdrs user-defined headers (Range, If-Match, etc.)
     * @return string Object's data
     * @throws InvalidResponseException unexpected response
     */
    function read($hdrs=array())
    {
        list($status, $reason, $data) =
            $this->container->cfs_http->get_object_to_string($this, $hdrs);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->read($hdrs);
        #}
        if (($status < 200) || ($status > 299
                && $status != 412 && $status != 304)) {
            throw new Kohana_Exception("Invalid response (".$status."): "
                . $this->container->cfs_http->get_error());
        }
        return $data;
    }

    /**
     * Streaming read of Object's data
     *
     * Given an open PHP resource (see PHP's fopen() method), fetch the Object's
     * data and write it to the open resource handle.  This is useful for
     * streaming an Object's content to the browser (videos, images) or for
     * fetching content to a local file.
     *
     * Pass in $hdrs array to set specific custom HTTP headers such as
     * If-Match, If-None-Match, If-Modified-Since, Range, etc.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * # Assuming this is a web script to display the README to the
     * # user's browser:
     * #
     * <?php
     * // grab README from storage system
     * //
     * $my_docs = $conn->get_container("documents");
     * $doc = $my_docs->get_object("README");
     *
     * // Hand it back to user's browser with appropriate content-type
     * //
     * header("Content-Type: " . $doc->content_type);
     * $output = fopen("php://output", "w");
     * $doc->stream($output); # stream object content to PHP's output buffer
     * fclose($output);
     * ?>
     *
     * # See read() above for a more simple example.
     * #
     * </code>
     *
     * @param resource $fp open resource for writing data to
     * @param array $hdrs user-defined headers (Range, If-Match, etc.)
     * @return string Object's data
     * @throws InvalidResponseException unexpected response
     */
    function stream(&$fp, $hdrs=array())
    {
        list($status, $reason) =
                $this->container->cfs_http->get_object_to_stream($this,$fp,$hdrs);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->stream($fp, $hdrs);
        #}
        if (($status < 200) || ($status > 299
                && $status != 412 && $status != 304)) {
            throw new Kohana_Exception("Invalid response (".$status."): "
                .$reason);
        }
        return True;
    }

    /**
     * Store new Object metadata
     *
     * Write's an Object's metadata to the remote Object.  This will overwrite
     * an prior Object metadata.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * $my_docs = $conn->get_container("documents");
     * $doc = $my_docs->get_object("README");
     *
     * # Define new metadata for the object
     * #
     * $doc->metadata = array(
     *     "Author" => "EJ",
     *     "Subject" => "How to use the PHP tests",
     *     "Version" => "1.2.2"
     * );
     *
     * # Define additional headers for the object
     * #
     * $doc->headers = array(
     *     "Content-Disposition" => "attachment",
     * );
     *
     * # Push the new metadata up to the storage system
     * #
     * $doc->sync_metadata();
     * </code>
     *
     * @return boolean <kbd>True</kbd> if successful, <kbd>False</kbd> otherwise
     * @throws InvalidResponseException unexpected response
     */
    function sync_metadata()
    {
        if (!empty($this->metadata) || !empty($this->headers) || $this->manifest) {
            $status = $this->container->cfs_http->update_object($this);
            #if ($status == 401 && $this->_re_auth()) {
            #    return $this->sync_metadata();
            #}
            if ($status != 202) {
                throw new Kohana_Exception("Invalid response ("
                    .$status."): ".$this->container->cfs_http->get_error());
            }
            return True;
        }
        return False;
    }
    /**
     * Store new Object manifest
     *
     * Write's an Object's manifest to the remote Object.  This will overwrite
     * an prior Object manifest.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * $my_docs = $conn->get_container("documents");
     * $doc = $my_docs->get_object("README");
     *
     * # Define new manifest for the object
     * #
     * $doc->manifest = "container/prefix";
     *
     * # Push the new manifest up to the storage system
     * #
     * $doc->sync_manifest();
     * </code>
     *
     * @return boolean <kbd>True</kbd> if successful, <kbd>False</kbd> otherwise
     * @throws InvalidResponseException unexpected response
     */

    function sync_manifest()
    {
        return $this->sync_metadata();
    }
    /**
     * Upload Object's data to Cloud Files
     *
     * Write data to the remote Object.  The $data argument can either be a
     * PHP resource open for reading (see PHP's fopen() method) or an in-memory
     * variable.  If passing in a PHP resource, you must also include the $bytes
     * parameter.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * $my_docs = $conn->get_container("documents");
     * $doc = $my_docs->get_object("README");
     *
     * # Upload placeholder text in my README
     * #
     * $doc->write("This is just placeholder text for now...");
     * </code>
     *
     * @param string|resource $data string or open resource
     * @param float $bytes amount of data to upload (required for resources)
     * @param boolean $verify generate, send, and compare MD5 checksums
     * @return boolean <kbd>True</kbd> when data uploaded successfully
     * @throws SyntaxException missing required parameters
     * @throws BadContentTypeException if no Content-Type was/could be set
     * @throws MisMatchedChecksumException $verify is set and checksums unequal
     * @throws InvalidResponseException unexpected response
     */
    function write($data=NULL, $bytes=0, $verify=True)
    {
        if (!$data && !is_string($data)) {
            throw new Kohana_Exception("Missing data source.");
        }
        if ($bytes > MAX_OBJECT_SIZE) {
            throw new Kohana_Exception("Bytes exceeds maximum object size.");
        }
        if ($verify) {
            if (!$this->_etag_override) {
                $this->etag = $this->compute_md5sum($data);
            }
        } else {
            $this->etag = NULL;
        }

        $close_fh = False;
        if (!is_resource($data)) {
            # A hack to treat string data as a file handle.  php://memory feels
            # like a better option, but it seems to break on Windows so use
            # a temporary file instead.
            #
            $fp = fopen("php://temp", "wb+");
            #$fp = fopen("php://memory", "wb+");
            fwrite($fp, $data, strlen($data));
            rewind($fp);
            $close_fh = True;
            $this->content_length = (float) strlen($data);
            if ($this->content_length > MAX_OBJECT_SIZE) {
                throw new Kohana_Exception("Data exceeds maximum object size");
            }
            $ct_data = substr($data, 0, 64);
        } else {
            $this->content_length = $bytes;
            $fp = $data;
            $ct_data = fread($data, 64);
            rewind($data);
        }

        $this->_guess_content_type($ct_data);

        list($status, $reason, $etag) =
                $this->container->cfs_http->put_object($this, $fp);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->write($data, $bytes, $verify);
        #}
        if ($status == 412) {
            if ($close_fh) { fclose($fp); }
            throw new Kohana_Exception("Missing Content-Type header");
        }
        if ($status == 422) {
            if ($close_fh) { fclose($fp); }
            throw new Kohana_Exception(
                "Supplied and computed checksums do not match.");
        }
        if ($status != 201) {
            if ($close_fh) { fclose($fp); }
            throw new Kohana_Exception("Invalid response (".$status."): "
                . $this->container->cfs_http->get_error());
        }
        if (!$verify) {
            $this->etag = $etag;
        }
        if ($close_fh) { fclose($fp); }
        return True;
    }

    /**
     * Upload Object data from local filename
     *
     * This is a convenience function to upload the data from a local file.  A
     * True value for $verify will cause the method to compute the Object's MD5
     * checksum prior to uploading.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * $my_docs = $conn->get_container("documents");
     * $doc = $my_docs->get_object("README");
     *
     * # Upload my local README's content
     * #
     * $doc->load_from_filename("/home/ej/cloudfiles/readme");
     * </code>
     *
     * @param string $filename full path to local file
     * @param boolean $verify enable local/remote MD5 checksum validation
     * @return boolean <kbd>True</kbd> if data uploaded successfully
     * @throws SyntaxException missing required parameters
     * @throws BadContentTypeException if no Content-Type was/could be set
     * @throws MisMatchedChecksumException $verify is set and checksums unequal
     * @throws InvalidResponseException unexpected response
     * @throws IOException error opening file
     */
    function load_from_filename($filename, $verify=True)
    {
        $fp = @fopen($filename, "r");
        if (!$fp) {
            throw new Kohana_Exception("Could not open file for reading: ".$filename);
        }

        clearstatcache();

        $size = (float) sprintf("%u", filesize($filename));
        if ($size > MAX_OBJECT_SIZE) {
            throw new Kohana_Exception("File size exceeds maximum object size.");
        }

        $this->_guess_content_type($filename);

        $this->write($fp, $size, $verify);
        fclose($fp);
        return True;
    }

    /**
     * Save Object's data to local filename
     *
     * Given a local filename, the Object's data will be written to the newly
     * created file.
     *
     * Example:
     * <code>
     * # ... authentication/connection/container code excluded
     * # ... see previous examples
     *
     * # Whoops!  I deleted my local README, let me download/save it
     * #
     * $my_docs = $conn->get_container("documents");
     * $doc = $my_docs->get_object("README");
     *
     * $doc->save_to_filename("/home/ej/cloudfiles/readme.restored");
     * </code>
     *
     * @param string $filename name of local file to write data to
     * @return boolean <kbd>True</kbd> if successful
     * @throws IOException error opening file
     * @throws InvalidResponseException unexpected response
     */
    function save_to_filename($filename)
    {
        $fp = @fopen($filename, "wb");
        if (!$fp) {
            throw new Kohana_Exception("Could not open file for writing: ".$filename);
        }
        $result = $this->stream($fp);
        fclose($fp);
        return $result;
    }
       /**
     * Purge this Object from CDN Cache.
     * Example:
     * <code>
     * # ... authentication code excluded (see previous examples) ...
     * #
     * $conn = new CF_Authentication($auth);
     * $container = $conn->get_container("cdn_enabled");
     * $obj = $container->get_object("object");
     * $obj->purge_from_cdn("user@domain.com");
     * # or
     * $obj->purge_from_cdn();
     * # or
     * $obj->purge_from_cdn("user1@domain.com,user2@domain.com");
     * @returns boolean True if successful
     * @throws CDNNotEnabledException if CDN Is not enabled on this connection
     * @throws InvalidResponseException if the response expected is not returned
     */
    function purge_from_cdn($email=null)
    {
        if (!$this->container->cfs_http->getCDNMUrl())
        {
            throw new Kohana_Exception(
                "Authentication response did not indicate CDN availability");
        }
        $status = $this->container->cfs_http->purge_from_cdn($this->container->name . "/" . $this->name, $email);
        if ($status < 199 or $status > 299) {
            throw new Kohana_Exception(
                "Invalid response (".$status."): ".$this->container->cfs_http->get_error());
        }
        return True;
    }

    /**
     * Set Object's MD5 checksum
     *
     * Manually set the Object's ETag.  Including the ETag is mandatory for
     * Cloud Files to perform end-to-end verification.  Omitting the ETag forces
     * the user to handle any data integrity checks.
     *
     * @param string $etag MD5 checksum hexidecimal string
     */
    function set_etag($etag)
    {
        $this->etag = $etag;
        $this->_etag_override = True;
    }

    /**
     * Object's MD5 checksum
     *
     * Accessor method for reading Object's private ETag attribute.
     *
     * @return string MD5 checksum hexidecimal string
     */
    function getETag()
    {
        return $this->etag;
    }

    /**
     * Compute the MD5 checksum
     *
     * Calculate the MD5 checksum on either a PHP resource or data.  The argument
     * may either be a local filename, open resource for reading, or a string.
     *
     * <b>WARNING:</b> if you are uploading a big file over a stream
     * it could get very slow to compute the md5 you probably want to
     * set the $verify parameter to False in the write() method and
     * compute yourself the md5 before if you have it.
     *
     * @param filename|obj|string $data filename, open resource, or string
     * @return string MD5 checksum hexidecimal string
     */
    function compute_md5sum(&$data)
    {

        if (function_exists("hash_init") && is_resource($data)) {
            $ctx = hash_init('md5');
            while (!feof($data)) {
                $buffer = fgets($data, 65536);
                hash_update($ctx, $buffer);
            }
            $md5 = hash_final($ctx, false);
            rewind($data);
        } elseif ((string)is_file($data)) {
            $md5 = md5_file($data);
        } else {
            $md5 = md5($data);
        }
        return $md5;
    }

    /**
     * PRIVATE: fetch information about the remote Object if it exists
     */
    private function _initialize()
    {
        list($status, $reason, $etag, $last_modified, $content_type,
            $content_length, $metadata, $manifest, $headers) =
                $this->container->cfs_http->head_object($this);
        #if ($status == 401 && $this->_re_auth()) {
        #    return $this->_initialize();
        #}
        if ($status == 404) {
            return False;
        }
        if ($status < 200 || $status > 299) {
            throw new Kohana_Exception("Invalid response (".$status."): "
                . $this->container->cfs_http->get_error());
        }
        $this->etag = $etag;
        $this->last_modified = $last_modified;
        $this->content_type = $content_type;
        $this->content_length = $content_length;
        $this->metadata = $metadata;
        $this->headers = $headers;
        $this->manifest = $manifest;
        return True;
    }

    #private function _re_auth()
    #{
    #    $new_auth = new CF_Authentication(
    #        $this->cfs_auth->username,
    #        $this->cfs_auth->api_key,
    #        $this->cfs_auth->auth_host,
    #        $this->cfs_auth->account);
    #    $new_auth->authenticate();
    #    $this->container->cfs_auth = $new_auth;
    #    $this->container->cfs_http->setCFAuth($this->cfs_auth);
    #    return True;
    #}
}

?>