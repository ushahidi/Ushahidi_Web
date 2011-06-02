<?php defined('SYSPATH') or die('No direct script access.');
/**
 * CustomForms_Api_Object
 *
 * This class handles reports activities via the API.
 *
 * @version 1 - Antonio Lettieri 2011-01-31
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Konpagroup <info@konpagroup.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Konpagroup - http://konpagroup.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */


class CustomForms_Api_Object extends Api_Object_Core {


	public function __construct($api_service)
	{
		parent::__construct($api_service);
	}



    /**
     * Implementation of abstract method in parent
     *
     * Handles the API task parameters
     */
	public function perform_task()
	{
		$this->_get_custom_forms();	
	}


	/**
	*	Handles API Task
	*
	*	Sets the response_data property of the parent
	*/
	private function _get_custom_forms()
	{
	 	//Verify that the by query parameter is set
		if (!$this->api_service->verify_array_index($this->request, 'by')) 
		{

			return $this->set_error_message(array(
                "error" => $this->api_service->get_error_msg(001, 'by')
            ));
            
		}
		else 
		{
			$this->by = $this->request['by'];
		}

		//Verify which call is being made
		switch ($this->by) {
			case "all":
				//Get all forms
				$this->response_data = $this->_get_all_forms();
			break;
			case "fields":
				//Get custom field values and meta
				$this->response_data = $this->_get_custom_form_fields();
			break;
			case "meta":
				//Get custom field meta
				$this->response_data = $this->_get_custom_form_meta();
			break;
		}

	}

	/**
     * Returns all form details in the platform
     *
     */
	private function _get_all_forms()
	{
		//Call to customforms helper to return all forms
		$forms = customforms::get_custom_forms();

		$is_json = $this->_is_json();

		if($forms->count() == 0) {
			//Nothing was returned	
			return $this->response(4); //We don't have any forms.

		}

		if ($is_json)
		{
			$json_item = array();
			$json = array();
		}
		else
		{
			$xml = new XmlWriter();
	        $xml->openMemory();
	        $xml->startDocument('1.0', 'UTF-8');
	        $xml->startElement('response');
	        $xml->startElement('payload');
	        $xml->writeElement('domain',$this->domain);
	        $xml->startElement('customforms');
	        $xml->startElement('forms');	        	
		}


		foreach ($forms as $form)
		{

			if ($is_json)
			{
				//Setup JSON array
				$json_item[] = array("id" => $form->id,
									 "title" => $form->form_title,
									  "description" => $form->form_description);
			}
			else
			{
				//Setup XML Elements
				$xml->startElement("form");
				$xml->writeElement("id",$form->id);
				$xml->writeElement("title",$form->form_title);
				$xml->writeElement("description",$form->form_description);
				$xml->endElement(); //End form

			}

		}

		if ($is_json)
		{
			$json = array(
				"payload" => array("customforms" => $json_item),
				"error" =>$this->api_service->get_error_msg(0)
				);
			return $this->array_as_json($json); //Return array as json
		}
		else
		{

			$xml->endElement(); //End forms
	        $xml->endElement(); //End customforms
	        $xml->endElement(); //End payload
	        $xml->startElement('error');
            $xml->writeElement('code',0);
            $xml->writeElement('message','No Error');
            $xml->endElement();//end error
            $xml->endElement(); //End response
	      	return $xml->outputMemory(true); //return XML output

		}

	}


	/**
	*	Gets the custom form field values and 
	*
	*	meta information by incidentid
	*/	
	private function _get_custom_form_fields(){

		$is_json = $this->_is_json();

		if ( !$this->api_service->verify_array_index($this->request, 'id')) 
		{
          //Ensure that the incidentid is set, error out if not
          return    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'id')
                    ));
                    
        }
		else
		{
        	$incident_id = $this->request['id'];
        }
        
        //Retrieve the form_id from the incident object
        $incident = ORM::factory("incident")->select("form_id")->where("id",$this->check_id_value($incident_id))->find();
        
        
        if (! $incident ) 
		{
        	return $this->response(4); //We don't have this incident
        }
        
        $form_id = $incident->form_id;
        
		//Call the customforms helper method to return the field values
		$custom_form_fields = customforms::get_custom_form_fields($incident_id, $form_id, true);

		//Call the customforms helper method to return the field meta information
		$custom_form_field_meta = customforms::get_custom_form_fields($incident_id, $form_id, false);

		if (count($custom_form_fields) == 0)
		{
			return $this->response(4); //We don't have any forms for this incident.
		}

		if($is_json)
		{
			$json_item = array();
			$json = array();

		}
		else 
		{
			$xml = new XmlWriter();
	        $xml->openMemory();
	        $xml->startDocument('1.0', 'UTF-8');
	        $xml->startElement('response');
	        $xml->startElement('payload');
	        $xml->writeElement('domain',$this->domain);
	        $xml->startElement('customforms');
	        $xml->startElement("fields");        
        }


		foreach ($custom_form_fields as $field_id => $field)
		{


			$field_value = $field;
			$field_meta = $custom_form_field_meta[$field_id];


			// Always return values as array
			if (customforms::field_is_multi_value($field_meta))
			{
				//This is a multi-select field, return it as an multi value array
				$field_value = explode(",",$field_value);
			}
			else
			{
				//This is either text or html, return as single array object
				$field_value = array($field_value);
			}

			if ($is_json)
			{
				$json_item["fields"][] = array("values"=>$field_value, "meta"=>$this->_meta_fields($field_meta));
			}
			else
			{

				$xml->startElement("field");
				$xml->startElement("values");

				foreach ($field_value as $val)
				{
					$xml->writeElement("value",html::specialchars($val,FALSE)); //Write the field value
				}

				$xml->endElement(); //end values;
				$xml->startElement("meta");

				$this->_meta_fields($field_meta,$xml);

				$xml->endElement(); //end meta
				$xml->endElement(); //field;

			}
		}


		if ($is_json)
		{

			$json = array(
				"payload"=>array("customforms"=>$json_item),
				"error" => $this->api_service->get_error_msg(0)
				);

			$json_item = null;
			return $this->array_as_json($json); //Write the json array

		}
		else
		{

			$xml->endElement(); //End fields
        	$xml->endElement(); //End customforms
        	$xml->endElement(); //End payload
        	$xml->startElement('error');
            $xml->writeElement('code',0);
            $xml->writeElement('message','No Error');
            $xml->endElement();//end error
            $xml->endElement(); //end response
        	return $xml->outputMemory(true); //write out the xml stream
        }

	}

	/**
	*	Gets the form field meta values
	*	
	*/
	private function _get_custom_form_meta()
	{

		$is_json = $this->_is_json();

		if (!$this->api_service->verify_array_index($this->request, 'formid')) 
		{

			return $this->set_error_message(array(
                "error" => $this->api_service->get_error_msg(001, 'formid')
            ));
            
		}
		else 
		{
			$this->formid = $this->request['formid'];
		}

		$form_meta = customforms::get_custom_form_fields(false,$this->formid); //Get the meta fields for the specified formId

		if(count($form_meta) == 0)
		{
			return $this->response(4); //We don't have any fields for this form
		}


		if($is_json)
		{
			$json_item = array();
			$json = array();
		}
		else
		{
			$xml = new XmlWriter();
	        $xml->openMemory();
	        $xml->startDocument('1.0', 'UTF-8');
	        $xml->startElement('response');
	        $xml->startElement('payload');
	        $xml->writeElement('domain',$this->domain);
	        $xml->startElement('customforms');
	        $xml->startElement("fields");
		}

		foreach ($form_meta as $meta_val)
		{

			if($is_json)
			{
				$json_item["fields"][] = $this->_meta_fields($meta_val); //return meta key array
			}
			else
			{
				$this->_meta_fields($meta_val,$xml);		//write the xml nodes for the meta fields
			}
		}


		if($is_json)
		{

			$json = array(
					"payload"=>array("customforms"=>$json_item),
					"error" => $this->api_service->get_error_msg(0)
					);
			$json_item = null;

			return $this->array_as_json($json); //return json as response_data

		}
		else
		{
			$xml->endElement(); //end fields
			$xml->endElement(); //end customforms
			$xml->endElement(); //end payload
			$xml->startElement('error');
            $xml->writeElement('code',0);
            $xml->writeElement('message','No Error');
            $xml->endElement();//end error
            $xml->endElement(); //end response
			return $xml->outputMemory(true); //write out the xml stream	as response_data
		}

	}


	/**
	*	Helper function to manage keynames for meta fields
	*
	*	@param $meta_val Array of meta key names and values
	*	@optional $xml XML object used when QueryString:resp=xml
	*/
	private function _meta_fields($meta_val, &$xml=null)
	{
		if (!isset($xml))
		{
			return array(
							'id' 				=> 	$meta_val['field_id'],
							'name' 				=> 	$meta_val['field_name'],
							'type' 				=> 	$meta_val['field_type'],
							'default' 			=> 	$meta_val['field_default'],
							'required' 			=> 	$meta_val['field_required'],
							'maxlen' 			=> 	$meta_val['field_maxlength'],
							'isdate' 			=> 	$meta_val['field_isdate'],
							'ispublicvisible'	=>	$meta_val['field_ispublic_visible'],
							'ispublicsubmit'	=>	$meta_val['field_ispublic_submit']
					);

		}
		else
		{

				$xml->startElement("meta");
				$xml->writeElement("id",$meta_val['field_id']);
				$xml->writeElement("name",$meta_val["field_name"]);
				$xml->writeElement("type",$meta_val["field_type"]);
				$xml->writeElement("default",$meta_val["field_default"]);
				$xml->writeElement("required",$meta_val["field_required"]);
				$xml->writeElement("maxlen",$meta_val["field_maxlength"]);
				$xml->writeElement("height", $meta_val["field_height"]);
				$xml->writeElement("isdate",$meta_val["field_isdate"]);
				$xml->writeElement("ispublicvisible",$meta_val["field_ispublic_visible"]);
				$xml->writeElement("ispublicsubmit",$meta_val["field_ispublic_submit"]);
				$xml->endElement(); //end meta	

		}
	}




	/**
	*	Checks to see if the response is asking for xml or json return
	*
	*/
	private function _is_json()
	{
		return ($this->response_type == "json") ? TRUE : FALSE;
	}
}