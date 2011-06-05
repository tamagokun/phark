<?php

namespace Phark\Source;

class ArraySource implements \Phark\Source
{
	private $_packages=array();

	public function package($name, \Phark\Version $version)
	{
		foreach($this->packages() as $package)
			if($package->name() == $name && $package->version()->equal($version))
				return $package;

		throw new \Phark\Exception("Failed to find $name $version");
	}

	public function packages()
	{
		return $this->_packages;
	}	

	public function add(\Phark\Package $package)
	{
		$this->_packages []= $package;
		return $this;
	}

	public function fetch($name, \Phark\Version $version)
	{
		throw new \BadMethodCallException("Fetch not supported");
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->packages());
	}
}
