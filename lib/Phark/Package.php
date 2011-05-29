<?php

namespace Phark;

/**
 * A package is all of the code to be included and executed, including 
 * a specification
 */
class Package
{
	private $_spec, $_url, $_urlType, $_env;

	/**
	 * Constructor
	 */
	public function __construct(Specification $spec, $url, $urlType, Environment $env=null)
	{
		$this->_spec = $spec;
		$this->_url = $url;
		$this->_urlType = $urlType;
		$this->_env = $env ?: new Environment();
	}

	/**
	 * The name of the package
	 * @return string
	 */
	public function name()
	{
		return $this->_spec->name();
	}

	/**
	 * The package version
	 * @return Version
	 */
	public function version()
	{
		return $this->_spec->version();
	}

	/**
	 * The hashname of the package, e.g mypackage@1.0.0
	 * @return string
	 */
	public function hash()
	{
		return $this->_spec->hash();
	}

	/**
	 * The package dependencies
	 * @return array
	 */
	public function dependencies()
	{
		return $this->_spec->dependencies();
	}

	/**
	 * The {@link Specification} object
	 * @return object
	 */
	public function spec()
	{
		return $this->_spec;
	}

	/**
	 * Sorts an array of Package objects by version
	 * @return array
	 */
	public static function sort($packages)
	{
		usort($packages, function($a,$b) {
			return $a->version()->compare($b->version()) * -1;
		});

		return $packages;				
	}

	/**
	 * Copies the package files to a particular directory
	 */
	public function copy($dir)
	{
		if($this->_urlType != 'file')
			throw new \BadMethodCallException("Only file urls are implemented at present");

		$sourceDir = $this->_url;

		// copy the files from source to target
		foreach($this->_spec->files() as $file)
		{
			$this->_env->shell()->copy(
				(string) new Path($sourceDir, $file),
				(string) new Path($dir, $file)
			);
		}
	}
}
