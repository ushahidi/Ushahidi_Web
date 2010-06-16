<?php
/**
 * MHI Sing In Box JS file
 * 
 * Non-clustered map rendering (Please refer to main_cluster_js for Server Side Clusters)
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Main_JS View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>


$(function(){
    $('#btn_sign-in').toggle(function(){
        //show the dagum form	
        $("#login-form").show()
        //add the active class to sign-in link
        $(this).addClass("active")
    }, function(){
        //hide the dagum form
        $("#login-form").hide()
        //remove the active class from the sign-in link
        $(this).removeClass("active")
    });
});