<?php

namespace Phark\Source;

class SourceIndex
{
	private $_sources=array();
	private $_packages;

	public function __construct($sources)
	{
		$this->_sources = $sources;
	}

	public function find(\Phark\Dependency $dependency)
	{
		if(!isset($this->_packages))
			$this->_packages = $this->_index($this->_sources);

		if(!isset($this->_packages[$dependency->package]))
			throw new \Phark\Exception("No packages for $dependency");

		$requirement = $dependency->requirement;
		$candidates = array();

		foreach($this->_packages[$dependency->package] as $package)
		{
			if(!$requirement || $requirement->isSatisfiedBy($package->version()))
				return $package;
		}	

		throw new \Phark\Exception("No versions of $name meet {$dependency->requirement}");
	}

	private function _index($sources)
	{
		$packages = array();

		foreach($sources as $source)
			foreach($source as $package)
				$packages[$package->name()][] = $package;

		// sort packages
		foreach(array_keys($packages) as $name)
			$packages[$name] = \Phark\Package::sort($packages[$name]);
		
		return $packages;
	}
}

