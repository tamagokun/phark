<?php

namespace Phark;

/**
 * A dependency links a package name and a requirement
 */
class Dependency
{
	public $package, $requirement;

	public function __construct($package, $requirement)
	{
		$this->package = $package;
		$this->requirement = Requirement::parse($requirement); 
	}

	public function isSatisfiedBy($package, $version)
	{
		if($package != $this->package)
			return false;
		else
			return $this->requirement->isSatisfiedBy($version);
	}

	public function __toString()
	{
		return sprintf('%s %s', $this->package, $this->requirement);
	}	

	public static function parse($string)
	{
		list($package, $requirement) = explode(' ', $string, 2);
		return new self($package, $requirement);	
	}
}
