<?php

namespace Phark;

/**
 * A package is all of the code to be included and executed, including 
 * a specification
 */
class Package
{
	private $_name, $_version, $_deps, $_env, $_dir, $_spec, $_source;

	/**
	 * Constructor
	 */
	public function __construct($name, Version $version, $deps, Source $source, Environment $env=null)
	{
		$this->_name = $name;
		$this->_version = $version;
		$this->_deps = $deps;
		$this->_source = $source;
		$this->_env = $env ?: new Environment();
	}

	/**
	 * The name of the package
	 * @return string
	 */
	public function name()
	{
		return $this->_name;
	}

	/**
	 * The package version
	 * @return Version
	 */
	public function version()
	{
		return $this->_version;
	}

	/**
	 * The hashname of the package, e.g mypackage@1.0.0
	 * @return string
	 */
	public function hash()
	{
		return sprintf('%s@%s',$this->name(), $this->version());
	}

	/**
	 * The package dependencies
	 * @return array
	 */
	public function dependencies()
	{
		return $this->_deps;
	}

	/**
	 * The {@link Specification} object
	 * @return object
	 */
	public function spec()
	{
		if(!isset($this->_spec))
			$this->_spec = Specification::load($this->directory(), $this->_env->shell());

		return $this->_spec;
	}

	/**
	 * Returns a FileList for the package directory
	 */
	public function files()
	{
		return $this->spec()->files()->chdir($this->directory());
	}

	/**
	 * Gets a directory for the package, triggers a fetch 
	 * @return string
	 */
	public function directory()
	{
		if(!isset($this->_dir))
			$this->_dir = $this->_source->fetch($this->name(), $this->version());

		return $this->_dir;
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

	public static function fromSpecification(Specification $spec, Source $source, Environment $env=null)
	{
		return new self($spec->name(), $spec->version(), $spec->dependencies(), $source, $env);
	}

	public static function parseHash($hash)
	{
		$components = explode('@', $hash, 2);
		return array($components[0], new Version($components[1]));
	}
}
