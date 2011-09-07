Ushahidi Platform
=================
The Ushahidi Platform is an open source web application for information collection, vizualisation and interactive 
mapping. It allows people to collect and share their own stories using various mediums such
as SMS, Web Forms, Email or Twitter. For more information about the platform and use cases (case studies) visit: http://www.ushahidi.com


System Requirements
-------------------
To install the platform on your computer/server, the target system must meet the following requirements:

* PHP version 5.2.3 or greater
* MySQL version 5.0 or greater
* An HTTP Server. Kohana, which Ushahidi is built on, is known to work with the following web servers:
    - Apache 1.3+
    - Apache2.0+
    - lighttpd
    - Microsoft Internet Information Server (MS IIS)
* Unicode support in the operating system


Required Extensions
-------------------
The follwing is a list of PHP extensions that must be installed on your server in order for Ushahidi to run properly:

* PCRE (http://php.net/pcre) must be compiled with –enable-utf8 and –enable-unicode-properties for UTF-8 functions to work properly.
* iconv (http://php.net/iconv) is required for UTF-8 transliteration.
* mcrypt (http://php.net/mcrypt) is required for encryption.
* SPL (http://php.net/spl) is required for several core libraries
* mbstring (http://php.net/mbstring) which speeds up Kohana's UTF-8 functions.
* cURL (http://php.net/curl) which is used to access remote sites.
* MySQL (http://php.net/mysql) is required for database access.

#####NOTE: Need to figure out what extensions you already have installed on your server? Here are instructions to do just that: http://jontangerine.com/silo/php/phpinfo/


Optional Server Requirements
----------------------------
* To use Ushahidi's "Clean URLS" feature on an Apache Web Server, you will need the mod_rewrite module
  and the ability to use local .htaccess files. To check if local .htaccess files are allowed, verify that the 
  "AllowOverride" directive in your Apache config (for the web server directory in which you have installed Ushahidi) 
  has been set to "All" i.e.:

        <Directory [your-document-root-directory]>
            ...
            AllowOverride All
            ...
        </Directory>

#####NOTE: Clean URLs means that the URLs of your deployment will not have the 'index.php' prefix


Installation
------------
* ####Download and extract Ushahidi
    You can obtain the official release of the software from [the download site](http://download.ushahidi.com). 
    Alternatively, you can obtain the release running the latest version  of the from [GitHub](https://github.com/ushahidi/Ushahidi_Web/archives/master) - the files are available in .zip and .tar.gz
    
    To unzip/extract the archive on a typical Unix/Linux command line:
    
        tar -xvf Ushahidi_Web-xxxx.tar.gz
    
    or in the case of a zip file:

        unzip Ushahidi_Web-xxxx.zip
    
    This will create a new directory Ushahidi_Web-xxxx containing all the Ushahidi platform files and directories - Move the contents of this directory
    into a directory within your webserver's document root or your public HTML directory.

* ####Ensure the following directories are writable (i.e. have their permission values set to 777)
    - application/config/config.php
    - application/config
    - application/cache
    - application/logs
    - media/uploads
    - .htaccess
    
    On Unix/Linux, you can change the permissions as follows:

        cd path-to-webserver-document-root-directory
        chmod -R 777 application/config
        chmod -R 777 application/cache
        chmod -R 777 application/logs
        chmod -R 777 media/uploads
        chmod 777 .htaccess
        
    #####NOTE: The process of configuring file permissions is different for various operating systems. Here are some helpful links about permissions for the Windows (http://support.microsoft.com/kb/308419) and Unix (http://www.washington.edu/computing/unix/permissions.html) operating systems.

* ####Create the Ushahidi database
    Ushahidi stores all its information in a database. You must therefore create this database in order to install Ushahidi. This is done as follows:
    
        mysqladmin -u 'username' -p create 'databasename'
    
    MySQL will prompt for the password for the <username> database password and then create the initial database files. Next, you must log in and set the 
    database access rights:
    
        mysql -u 'username' -p
    
    Again, you will be prompted for the 'username' database password. At the MySQL prompt, enter the following command:
    
        GRANT SELECT, INSERT, DELETE, UPDATE, CREATE, DROP, ALTER, INDEX on 'databasename'.* 
        TO 'username'@'localhost' IDENFIFIED BY 'password';
    
    Where:
    - 'databasename' is the name of your database
    - 'username@localhost' is the name of your MySQL account
    - 'password' is the password required for that username

    #####NOTE: Your account must have all the privileges listed above in order to run Ushahidi on your webserver.

* ####Run the install script
    To run the install script, point your browser to the base url of your website: (e.g. http://www.example.com).
    
    You will be guided through a series of screens to set up the database and site settings depending on the installation method you choose (Basic or Advanced)


Additional Information
----------------------
For further references and documentation, head over to our wiki (http://wiki.ushahidi.com/doku.php?id=how_to_use_ushahidi_alpha). Also, we encourage you to drop by our forums (http://forums.ushahidi.com/) if you have any additional questions or concerns.