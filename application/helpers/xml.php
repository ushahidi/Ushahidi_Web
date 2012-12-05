<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * XML Import/Export helper class.
 *
 * @package	   Admin
 * @author	   Ushahidi Team
 * @copyright  (c) 2012 Ushahidi Team
 * @license	   http://www.ushahidi.com/license.html
 */
class xml_Core{
	
	/**
	 * Given an XMLWriter instance, an associative array map and 
	 * array of attribute/element tags to match with the array map,
	 * generate element/attribute tags
	 * @param XMLWriter object $writer
	 * @param Associative array map $object_map
	 * @param array $elements
	 *
	 */
	public static function generate_tags( $writer, $object_map, $elements)
	{
		foreach ($elements as $element)
		{	
			// For Attributes
			if (array_key_exists($element, $object_map['attributes']))
			{
				$writer->startAttribute($element);
					$writer->text($object_map['attributes'][$element]);
			}

			// For elements
			elseif (array_key_exists($element, $object_map['elements']))
			{
				$writer->startElement($element);
					$writer->text($object_map['elements'][$element]);
				$writer->endElement();
			}
		}	
	}
	
	/**
	 * Given an ORM object and an associative array, generates and returns
	 * an associative array with the corresponding values from the ORM object
	 * e.g given ORM Object Incident with id = 1 and title = 'Test Incident'
	 * Pass associative array $map = array(
	 *									'attributes' => array('id' => 'id'),
	 *									'elements' => array('title' => 'incident_title')
	 *									)
	 *
	 * Associative array $returned = array(
	 *									'attributes' => array('id' => 1),
	 *									'elements' => array('title' => 'Test Incident')
	 * 									)
	 *
	 * @param object $object
	 * @param array $map 
	 * @return array
	 */
	public static function generate_element_attribute_map($object, $map)
	{
		$output_map = array(
			'elements' => array(),
			'attributes' => array()
		);
		
		// For each item in 'attributes' in the map, get the value from the (orm) object
		foreach ($map['attributes'] as $attribute => $column)
		{
			$output_map['attributes'][$attribute] = $object->$column;
		}
		
		// For each element in 'elements', get corresponding value from orm object
		foreach ($map['elements'] as $element => $column)
		{
			$output_map['elements'][$element] = $object->$column;
		}
		
		return $output_map;
	}
	
	/**
	 * Get node values from DOMNodeList element /attribute, and sanitize
	 * @param DOMNodeList Object $node
	 * @param string Element within DOMNodelist object $tag_name
	 * @param boolean $element Set to FALSE if getting an attribute value
	 * @return mixed String if the node value exists, FALSE otherwise
	 */	
	public function get_node_text($node, $tag_name, $element = TRUE)
	{
		$node_value = NULL;
		try
		{
			// This is an element
			if ($element)
			{
				if ($node->getElementsByTagName($tag_name)->length > 0)
				{
					$element = $node->getElementsByTagName($tag_name)->item(0);
					$node_value = trim($element->nodeValue);
				}
				else
				{
					return FALSE;
				}
			}
			
			// This is an attribute
			else
			{
				$attribute = $node->getAttribute($tag_name);
				$node_value = trim($attribute);
			}
		}
		catch (Exception $e)
		{
			Kohana::log("xml_upload_error", $e->getMessage());
			return FALSE;
		}
		
		return ! empty($node_value) ? $node_value : FALSE;		
	}
}
?>