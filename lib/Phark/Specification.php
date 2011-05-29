<?php

namespace Phark;

/**
 * Describes the contents of a Phark package. Call magic is used to access any 
 * of the public properties as method calls.
 */
class Specification
{
	const FILENAME='Pharkspec';

	public
		$name,
		$authors=array(),
		$homepage,
		$version,
		$phpVersion,
		$summary,
		$description,
		$dependencies=array(),
		$devDependencies=array(),
		$files=array()
		;

	/**
	 * Constructor
	 */
	public function __construct($properties=array())
	{
		foreach($properties as $prop=>$value)
			$this->$prop = $value;
	}

	public function __call($method, $params)
	{
		return $this->$method;
	}

	public function hash()
	{
		return $this->name() . '@' . $this->version();
	}

	/**
	 * Returns a Specification from a Pharkspec or directory
	 */
	public static function load($file, $shell=null)
	{
		$shell = $shell ?: new Shell();

		if($shell->isdir($file))
			$file = new Path($file, Specification::FILENAME);

		$spec = new SpecificationBuilder($shell);
		require $file;
		return $spec->build();
	}
}
