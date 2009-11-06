<?php 
/**
 *
 * The Form class is meant to simplify the task of keeping
 * track of errors in user submitted forms and the form
 * field values that were entered correctly.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Dashboard Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General 
 * Public License (LGPL)
 */

class Form
{
   var $values = array();  //Holds submitted form field values
   var $errors = array();  //Holds submitted form error messages
   var $num_errors;   //The number of errors in submitted form

   /* Class constructor */
   function Form(){
      /**
       * Get form value and error arrays, used when there
       * is an error with a user-submitted form.
       */
      if(isset($_SESSION['value_array']) && isset($_SESSION['error_array'])){
         $this->values = $_SESSION['value_array'];
         $this->errors = $_SESSION['error_array'];
         $this->num_errors = count($this->errors);

         unset($_SESSION['value_array']);
         unset($_SESSION['error_array']);
      }
      else{
         $this->num_errors = 0;
      }
   }

   /**
    * setValue - Records the value typed into the given
    * form field by the user.
    * @param String field( field name of a particular input field )
    * @param String value ( the actual value entered into the field )
    */
   function set_value($field, $value){
      $this->values[$field] = $value;
   }

   /**
    * set_error - Records new form error given the form
    * field name and the error message attached to it.
    * @param String field( field name of particular input field )
    * @param String errmsg( error message to dispaly to user )
    */
   function set_error($field, $errmsg){
      $this->errors[$field] = $errmsg;
      $this->num_errors = count($this->errors);
   }

   /**
    * value - Returns the value attached to the given
    * field, if none exists, the empty string is returned.
    * @param String field ( field name of a particular input field )
    * @return String
    */
   function value($field){
      if(array_key_exists($field,$this->values)){
         return htmlspecialchars(stripslashes($this->values[$field]));
      }else{
         return "";
      }
   }

   /**
    * error - Returns the error message attached to the
    * given field, if none exists, the empty string is returned.
    * @param String error( field name of a particular input field )
    * @return String
    */
   function error($field){
      if(array_key_exists($field,$this->errors)){
         return $this->errors[$field];
      }else{
         return "";
      }
   }

   /**
    * get_error_array - Returns the array of error messages
    * @return String
    */
   function get_error_array(){
      return $this->errors;
   }
}

?>
