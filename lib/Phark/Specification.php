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
		$files=array(),
		$executables=array(),
		$path
		;

	/**
	 * Constructor
	 */
	public function __construct($properties=array(), $path=null)
	{
		foreach($properties as $prop=>$value)
			$this->$prop = $value;

		$this->path = $path;
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
			$file = (string) new Path($file, Specification::FILENAME);

		if(!$shell->isfile($file))
			throw new Exception("Failed to find $file");

		$spec = new SpecificationBuilder($shell);
		require $file;

		$result = $spec->build();
		$result->path = $file;

		return $result;
	}
}
