<?php

namespace Phark\Source;

use \Phark\Specification;

class DirectorySource implements \Phark\Source
{
	private $_specs;

	public function __construct($directory, $shell=null)
	{
		$this->_specs = new \Phark\FileList(array('*/Pharkspec'), $shell);
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
			$spec = Specification::load((string) new \Phark\Path($this->_specs->directory(), $file));
			$packages []= new \Phark\Package($spec, dirname($file), 'file');
		}

		return $packages;
	}	

	public function getIterator()
	{
		return new \ArrayIterator($this->packages());
	}
}
