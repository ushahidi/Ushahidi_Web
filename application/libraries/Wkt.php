<?php
/**
 * PHP Geometry/WKT encoder/decoder
 *
 * Mainly inspired/adapted from OpenLayers( http://www.openlayers.org ) 
 *	 Openlayers/format/WKT.js
 *
 * @package		GeoJSON
 * @subpackage	WKT
 * @author		Camptocamp <info@camptocamp.com>
 * @author    	Ushahidi Team <team@ushahidi.com> 
 * @copyright  	Copyright (c) 2009, Camptocamp <info@camptocamp.com>
 * @copyright  	Ushahidi - http://www.ushahidi.com
 * @license    	http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class WKT {

	private $regExes = array(
		'typeStr'			=> '/^\s*(\w+)\s*\(\s*(.*)\s*\)\s*$/',
		'spaces'			=> '/\s+/',
		'parenComma'		=> '/\)\s*,\s*\(/',
		'doubleParenComma'	=> '/\)\s*\)\s*,\s*\(\s*\(/',
		'trimParens'		=> '/^\s*\(?(.*?)\)?\s*$/'
	);

	const POINT				= 'point';
	const MULTIPOINT		= 'multipoint';
	const LINESTRING		= 'linestring';
	const MULTILINESTRING	= 'multilinestring';
	const LINEARRING		= 'linearring';
	const POLYGON			= 'polygon';
	const MULTIPOLYGON		= 'multipolygon';
	const GEOMETRYCOLLECTION= 'geometrycollection';

	/**
	 * Read WKT string into geometry objects
	 *
	 * @param string $WKT A WKT string
	 *
	 * @return Geometry|GeometryCollection
	 */
	public function read($WKT)
	{
		$matches = array();
		if (!preg_match($this->regExes['typeStr'], $WKT, $matches))
		{
			return null;
		}

		return $this->parse(strtolower($matches[1]), $matches[2]);
	}

	/**
	 * Parse WKT string into geometry objects
	 *
	 * @param string $WKT A WKT string
	 *
	 * @return Geometry|GeometryCollection
	 */
	public function parse($type, $str)
	{
		$matches = array();
		$components = array();

		switch ($type)
		{
			case self::POINT:
				$coords = $this->pregExplode('spaces', $str);
				return new Point($coords[0], $coords[1]);

			case self::MULTIPOINT:
				foreach (explode(',', trim($str)) as $point)
				{
					$components[] = $this->parse(self::POINT, $point);
				}
				return new MultiPoint($components);

			case self::LINESTRING:
				foreach (explode(',', trim($str)) as $point)
				{
					$components[] = $this->parse(self::POINT, $point);
				}
				return new LineString($components);

			case self::MULTILINESTRING:
				$lines = $this->pregExplode('parenComma', $str);
				foreach ($lines as $l)
				{
					$line = preg_replace($this->regExes['trimParens'], '$1', $l);
					$components[] = $this->parse(self::LINESTRING, $line);
				}
				return new MultiLineString($components);

			case self::POLYGON:
				$rings= $this->pregExplode('parenComma', $str);
				foreach ($rings as $r)
				{
					$ring = preg_replace($this->regExes['trimParens'], '$1', $r);
					$linestring = $this->parse(self::LINESTRING, $ring);
					$components[] = new LinearRing($linestring->getComponents());
				}
				return new Polygon($components);

			case self::MULTIPOLYGON:
				$polygons = $this->pregExplode('doubleParenComma', $str);
				foreach ($polygons as $p)
				{
					$polygon = preg_replace($this->regExes['trimParens'], '$1', $p);
					$components[] = $this->parse(self::POLYGON, $polygon);
				}
				return new MultiPolygon($components);

			case self::GEOMETRYCOLLECTION:
				$str = preg_replace('/,\s*([A-Za-z])/', '|$1', $str);
				$wktArray = explode('|', trim($str));
				foreach ($wktArray as $wkt)
				{
					$components[] = $this->read($wkt);
				}
				return new GeometryCollection($components);

			default:
				return null;
		}
	}

	/**
	 * Split string according to first match of passed regEx index of $regExes
	 *
	 */
	protected function pregExplode($regEx, $str)
	{
		$matches = array();
		preg_match($this->regExes[$regEx], $str, $matches);
		return empty($matches)?array(trim($str)):explode($matches[0], trim($str));
	}

	/**
	 * Serialize geometries into a WKT string.
	 *
	 * @param Geometry $geometry
	 *
	 * @return string The WKT string representation of the input geometries
	 */
	public function write(Geometry $geometry)
	{
		$type = strtolower(get_class($geometry));

		if (is_null($data = $this->extract($geometry)))
		{
			return null;
		}

		return strtoupper($type).'('.$data.')';
	}

	/**
	 * Extract geometry to a WKT string
	 *
	 * @param Geometry $geometry A Geometry object
	 *
	 * @return strin
	 */
	public function extract(Geometry $geometry)
	{
		$array = array();
		switch (strtolower(get_class($geometry)))
		{
			case self::POINT:
				return $geometry->getX().' '.$geometry->getY();
			case self::MULTIPOINT:
			case self::LINESTRING:
			case self::LINEARRING:
				foreach ($geometry as $geom)
				{
					$array[] = $this->extract($geom);
				}
				return implode(',', $array);
			case self::MULTILINESTRING:
			case self::POLYGON:
			case self::MULTIPOLYGON:
				foreach ($geometry as $geom)
				{
					$array[] = '('.$this->extract($geom).')';
				}
				return implode(',', $array);
			case self::GEOMETRYCOLLECTION:
				foreach ($geometry as $geom)
				{
					$array[] = strtoupper(get_class($geom)).'('.$this->extract($geom).')';
				}
				return implode(',', $array);
			default:
				return null;
		}
	}

	/**
	 * Loads a WKT string into a Geometry Object
	 *
	 * @param string $WKT
	 *
	 * @return	Geometry
	 */
	static public function load($WKT)
	{
		$instance = new self;
		return $instance->read($WKT);
	}

	/**
	 * Dumps a Geometry Object into a	 WKT string
	 *
	 * @param Geometry $geometry
	 *
	 * @return String A WKT string corresponding to passed object
	 */
	static public function dump(Geometry $geometry)
	{
		$instance = new self;
		return $instance->write($geometry);
	}
}

abstract class Geometry 
{
	protected $geom_type;

	abstract public function getCoordinates();
	
	/**
	 * Accessor for the geometry type
	 *
	 * @return string The Geometry type.
	 */
	public function getGeomType()
	{
		return $this->geom_type;
	}

	/**
	 * Returns an array suitable for serialization
	 *
	 * @return array
	 */
	public function getGeoInterface() 
	{
		return array(
			'type'=> $this->getGeomType(),
			'coordinates'=> $this->getCoordinates()
		);
	}

	/**
	 * Shortcut to dump geometry as GeoJSON
	 *
	 * @return string The GeoJSON representation of the geometry
	 */
	public function __toString()
	{
		return $this->toGeoJSON();
	}

	/**
	 * Dumps Geometry as GeoJSON
	 *
	 * @return string The GeoJSON representation of the geometry
	 */
	public function toGeoJSON()
	{
		return json_encode($this->getGeoInterface());
	}
}

abstract class Collection extends Geometry implements Iterator
{
	protected $components = array();

	/**
	 * Constructor
	 *
	 * @param array $components The components array
	 */
	public function __construct(array $components)
	{
		foreach ($components as $component)
		{
			$this->add($component);
		}
	}

	private function add($component)
	{
		$this->components[] = $component;
	}

	/**
	 * An accessor method which recursively calls itself to build the coordinates array
	 *
	 * @return array The coordinates array
	 */
	public function getCoordinates()
	{
		$coordinates = array();
		foreach ($this->components as $component)
		{
			$coordinates[] = $component->getCoordinates();
		}
		return $coordinates;
	}

	/**
	 * Returns Colection components
	 *
	 * @return array
	 */
	public function getComponents()
	{
		return $this->components;
	}

	# Iterator Interface functions

	public function rewind()
	{
		reset($this->components);
	}

	public function current()
	{
		return current($this->components);
	}

	public function key()
	{
		return key($this->components);
	}

	public function next()
	{
		return next($this->components);
	}

	public function valid()
	{
		return $this->current() !== false;
	}
}

class GeometryCollection extends Collection 
{
	protected $geom_type = 'GeometryCollection';
	
	/**
	 * Constructor
	 *
	 * @param array $geometries The Geometries array
	 */
	public function __construct(array $geometries = null) 
	{
		parent::__construct($geometries);
	}

	/**
	 * Returns an array suitable for serialization
	 *
	 * Overrides the one defined in parent class
	 *
	 * @return array
	 */
	public function getGeoInterface() 
	{
		$geometries = array();
		foreach ($this->components as $geometry) 
		{
			$geometries[] = $geometry->getGeoInterface();
		}
		return array(
			'type' => $this->getGeomType(),
			'geometries' => $geometries
		);
	}
}

class Point extends Geometry
{
	private $position = array(2);

	protected $geom_type = 'Point';

	/**
	 * Constructor
	 *
	 * @param float $x The x coordinate (or longitude)
	 * @param float $y The y coordinate (or latitude)
	 */
	public function __construct($x, $y)
	{
		if (!is_numeric($x) || !is_numeric($y))
		{
			throw new Exception("Bad coordinates: x and y should be numeric");
		}
		$this->position = array($x, $y);
	}

	/**
	 * An accessor method which returns the coordinates array
	 *
	 * @return array The coordinates array
	 */
	public function getCoordinates()
	{
		return $this->position;
	}

	/**
	 * Returns X coordinate of the point
	 *
	 * @return integer The X coordinate
	 */
	public function getX()
	{
		return $this->position[0];
	}

	/**
	 * Returns X coordinate of the point
	 *
	 * @return integer The X coordinate
	 */
	public function getY()
	{
		return $this->position[1];
	}
}

class LineString extends Collection 
{
	protected $geom_type = 'LineString';
	
	/**
	 * Constructor
	 *
	 * @param array $positions The Point array
	 */
	public function __construct(array $positions) 
	{
		if (count($positions) > 1)
		{
			parent::__construct($positions);
		}
		else
		{
			throw new Exception("Linestring with less than two points");
		}
	}
}

class LinearRing extends LineString
{
	protected $geom_type = 'LinearRing';

	/**
	 * Constructor
	 *
	 * @param array $positions The Point array
	 */
	public function __construct(array $positions)
	{
		if (count($positions) > 1)
		{
			parent::__construct($positions);
		}
		else
		{
			throw new Exception("Linestring with less than two points");
		}
	}
}

class Polygon extends Collection 
{
	protected $geom_type = 'Polygon';
	
	/**
	 * Constructor
	 *
	 * The first linestring is the outer ring
	 * The subsequent ones are holes
	 * All linestrings should be linearrings
	 *
	 * @param array $linestrings The LineString array
	 */
	public function __construct(array $linestrings) 
	{
		// the GeoJSON spec (http://geojson.org/geojson-spec.html) says nothing about linestring count. 
		// What should we do ?
		if (count($linestrings) > 0) 
		{
			parent::__construct($linestrings);
		}
		else
		{
			throw new Exception("Polygon without an exterior ring");
		}
	}
}

class MultiPoint extends Collection 
{
	protected $geom_type = 'MultiPoint';
	
	/**
	 * Constructor
	 *
	 * @param array $points The Point array
	 */
	public function __construct(array $points) 
	{
		parent::__construct($points);
	}
}

class MultiLineString extends Collection 
{
	protected $geom_type = 'MultiLineString';
	
	/**
	 * Constructor
	 *
	 * @param array $linestrings The LineString array
	 */
	public function __construct(array $linestrings) 
	{
		parent::__construct($linestrings);
	}
}

class MultiPolygon extends Collection 
{
	protected $geom_type = 'MultiPolygon';
	
	/**
	 * Constructor
	 *
	 * @param array $polygons The Polygon array
	 */
	public function __construct(array $polygons) 
	{
		parent::__construct($polygons);
	}
	
}