<?php

namespace Phark\Source;

use \Phark\Specification;
use \Phark\Package;

class DirectorySource implements \Phark\Source
{
	private $_specs, $_env;

	public function __construct($directory, $env)
	{
		$this->_env = $env;
		$this->_specs = new \Phark\FileList(array('*/Pharkspec'), $env->shell());
		$this->_specs->chdir($directory);
	}

	public function package($name, \Phark\Version $version)
	{
		foreach($this->packages() as $package)
			if($package->name() == $name && $package->version()->equal($version))
				return $package;

		throw new \Phark\Exception("Failed to find $name $version");
	}

	public function packages()
	{
		$packages = array();

		foreach($this->_specs->files() as $file)
		{
			$spec = Specification::load((string) new \Phark\Path($this->_specs->directory(), $file), $this->_env->shell());
			$packages []= Package::fromSpecification($spec, $this, $this->_env);
		}

		return $packages;
	}	

	public function fetch($name, \Phark\Version $version)
	{
		return (string) new \Phark\Path($this->_specs->directory(), sprintf("%s@%s",
			$name, $version));
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->packages());
	}
}
