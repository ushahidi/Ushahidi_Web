<?php
/**
 * Arr Helper Unit Tests
 *
 * @package	Core
 * @subpackage	Helpers
 * @author	Kiall Mac Innes
 * @group	core
 * @group	core.helpers
 * @group	core.helpers.arr
 */
class Helper_Arr_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * DataProvider for the arr::rotate() test
	 */
	public function rotate_provider()
	{
		return array(
			array(
				array(
					'CD'  => array('700', '780'),
					'DVD' => array('4700','650'),
					'BD' => array('25000','405')
				),
				array(
					0 => array('CD' => 700, 'DVD' => 4700, 'BD' => 25000),
					1 => array('CD' => 780, 'DVD' => 650, 'BD' => 405),
				),
			)
		);
	}

	/**
	 * Tests the arr::rotate() function.
	 * @dataProvider rotate_provider
	 * @group core.helpers.arr.rotate
	 * @test
	 */
	public function rotate($input_array, $expected_result)
	{
		$result = arr::rotate($input_array);
		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the arr::remove() test
	 */
	public function remove_provider()
	{
		return array(
			array(
				'CD',
				array(
					'CD'  => array('700', '780'),
					'DVD' => array('4700','650'),
					'BD' => array('25000','405')
				),
				array(
					'DVD' => array('4700','650'),
					'BD' => array('25000','405')
				),
				array('700', '780')
			)
		);
	}

	/**
	 * Tests the arr::remove() function.
	 * @dataProvider remove_provider
	 * @group core.helpers.arr.remove
	 * @test
	 */
	public function remove($input_key, $input_array, $expected_result, $expected_result2)
	{
		$result = arr::remove($input_key, $input_array);
		$this->assertEquals($expected_result, $input_array);
		$this->assertEquals($expected_result2, $result);
	}
	
	/**
	 * DataProvider for the arr::callback_string() test
	 */
	public function callback_string_provider()
	{
		return array(
			array('trim', array('trim', NULL)),
			array('valid::digit', array('valid::digit', NULL)),
			array('limit[10]', array('limit', array('10'))),
			array('limit[10,20]', array('limit', array('10','20'))),
			array('chars[a,b,c,d]', array('chars', array('a','b','c','d'))),
		);
	}

	/**
	 * Tests the arr::callback_string() function.
	 * @dataProvider callback_string_provider
	 * @group core.helpers.arr.callback_string
	 * @test
	 */
	public function callback_string($input_str, $expected_result)
	{
		$result = arr::callback_string($input_str);

		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the arr::extract() test
	 */
	public function extract_provider()
	{
		return array(
			array(
				array(
					'CD'  => array('700', '780'),
					'DVD' => array('4700','650'),
					'BD' => array('25000','405')
				),
				array(
					'DVD' => array('4700','650'),
					'Blueray' => NULL
				),
				'DVD',
				'Blueray'
			),
			array(
				array(
					'CD'  => array('700', '780'),
					'DVD' => array('4700','650'),
					'BD' => array('25000','405')
				),
				array(
					'DVD' => array('4700','650'),
					'BD' => array('25000','405')
				),
				'DVD',
				'BD'
			)
		);
	}

	/**
	 * Tests the arr::extract() function.
	 * @dataProvider extract_provider
	 * @group core.helpers.arr.extract
	 * @test
	 */
	public function extract($input_array, $expected_result, $input_keys)
	{
		$args = array_slice(func_get_args(), 2);
		
		array_unshift($args, $input_array);
		
		$result = call_user_func_array('arr::extract', $args);
		
		$this->assertEquals($expected_result, $result);
	}
	
	/**
	 * DataProvider for the arr::merge() test
	 */
	public function merge_provider()
	{
		return array(
			array(
				array(
					0 => 'a',
					1 => 'b',
					2 => 'c',
					3 => 'd',
					'e' => array(
						0 => 'f',
						1 => 'g'
					)
				),
				array('a', 'b'),
				array('c', 'd'),
				array('e' => array('f',	'g'))
			),
			array(
				array(
					'a' => 'E',
					'b' => 'F',
					'c' => array('d' => 'G'),
				),
				array('a' => 'A'),
				array('b' => 'B', 'c' => array('d' => 'D')),
				array('a' => 'E', 'b' => 'F', 'c' => array('d' => 'G'))
			),
		);
	}

	/**
	 * Tests the arr::merge() function.
	 * @dataProvider merge_provider
	 * @group core.helpers.arr.merge
	 * @test
	 */
	public function merge($expected_result, $input_keys)
	{
		$args = array_slice(func_get_args(), 1);
		
		$result = call_user_func_array('arr::merge', $args);
		
		$this->assertEquals($expected_result, $result);
	}
	
	/**
	 * DataProvider for the arr::overwrite() test
	 */
	public function overwrite_provider()
	{
		return array(
			array(
				array('fruit1' => 'strawberry', 'fruit2' => 'kiwi', 'fruit3' => 'pineapple'),
				array('fruit1' => 'apple', 'fruit2' => 'mango', 'fruit3' => 'pineapple'),
				array('fruit1' => 'strawberry', 'fruit4' => 'coconut'),
				array('fruit2' => 'kiwi', 'fruit5' => 'papaya'),
			),
		);
	}

	/**
	 * Tests the arr::overwrite() function.
	 * @dataProvider overwrite_provider
	 * @group core.helpers.arr.overwrite
	 * @test
	 */
	public function overwrite($expected_result, $input_array1)
	{
		$args = array_slice(func_get_args(), 1);

		$result = call_user_func_array('arr::overwrite', $args);
		
		$this->assertEquals($expected_result, $result);
	}
	
	/**
	 * DataProvider for the arr::map_recursive() test
	 */
	public function map_recursive_provider()
	{
		return array(
			array(
				array($this, 'map_recursive_callback'),
				array('a' => 1, 'b' => 2, 'c' => array(3, 4), 'd' => array('e' => 5)),
				array(
					'a' => 2,
					'b' => 3,
					'c' => array(
							0 => 4,
							1 => 5,
						),
					'd' => array(
							'e' => 6
						),
				)
			),
		);
	}
	
	/**
	 * Test callback for the arr::map_recursive() test
	 */
	public function map_recursive_callback($value)
	{
		return $value + 1;
	}

	/**
	 * Tests the arr::map_recursive() function.
	 * @dataProvider map_recursive_provider
	 * @group core.helpers.arr.map_recursive
	 * @test
	 */
	public function map_recursive($input_callback, $input_array, $expected_result)
	{
		$result = arr::map_recursive($input_callback, $input_array);
		
		$this->assertEquals($expected_result, $result);
	}

	/**
	 * DataProvider for the arr::unshift_assoc() test
	 */
	public function unshift_assoc_provider()
	{
		return array(
			array(
				array('fruit1' => 'apple', 'fruit2' => 'mango', 'fruit3' => 'pineapple'),
				'fruit1',
				'strawberry',
				array('fruit1' => 'strawberry', 'fruit2' => 'mango', 'fruit3' => 'pineapple')
			),
		);
	}

	/**
	 * Tests the arr::unshift_assoc() function.
	 * @dataProvider unshift_assoc_provider
	 * @group core.helpers.arr.unshift_assoc
	 * @test
	 */
	public function unshift_assoc($input_array, $input_key, $input_value, $expected_result)
	{
		$result = arr::unshift_assoc($input_array, $input_key, $input_value);
		
		$this->assertEquals($expected_result, $result);
	}
	
	/**
	 * DataProvider for the arr::to_object() test
	 */
	public function to_object_provider()
	{
		$expected_result1 = new stdClass();
		$expected_result1->test = 13;
		
		$expected_result2 = new stdClass;
		$expected_result2->test = $expected_result1;

		return array(
			array(
				array('test' => 13),
				'stdClass',
				$expected_result1
			),
			array(
				array('test' => array('test' => 13)),
				'stdClass',
				$expected_result2
			)
		);
	}

	/**
	 * Tests the arr::to_object() function.
	 * @dataProvider to_object_provider
	 * @group core.helpers.arr.to_object
	 * @test
	 */
	public function to_object($input_array, $input_class, $expected_result)
	{
		$result = arr::to_object($input_array, $input_class);
		
		$this->assertEquals($expected_result, $result);
	}
	
	/**
	 * DataProvider for the arr::pluck() test
	 */
	public function pluck_provider()
	{
		$array1 = array ('foo' => 1, 'bar' => 2);
		$array2 = array('foo' => 3, 'bar' => 4);
		$array3 = array('foo');

		return array(
			array('foo', array($array1, $array2, $array3), array (0 => 1, 1 => 3, 2 => NULL)),
			array('snuff', array($array1, $array2, $array3), array (0 => NULL, 1 => NULL, 2 => NULL)),
			array('bar', array($array2, $array3), array (0 => 4, 1 => NULL))
		);
	}

	/**
	 * Tests the arr::pluck() function.
	 * @dataProvider pluck_provider
	 * @group core.helpers.arr.pluck
	 * @test
	 */
	public function pluck($input_key, $input_array, $expected_result)
	{
		$result = arr::pluck($input_key, $input_array);

		$this->assertEquals($expected_result, $result);
	}
}