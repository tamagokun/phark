<?php

namespace Phark;

/**
 * A dependency links a package name and a requirement
 */
class Dependency
{
	const FILENAME='Pharkdeps';
	
	public $package, $requirement, $group, $source=array();

	public function __construct($package)
	{
		$args = func_get_args();
		$this->package = array_shift($args);
		$this->configure($args);
	}
	
	public function configure($options)
	{
		foreach($options as $option)
		{
			if(is_string($option))
				$this->requirement = Requirement::parse($option);
			if(is_array($option))
			{
				foreach($option as $key=>$value)
				{
					if($key == "group") $this->group = $value;
					else $this->source = array_merge($this->source,$value);
				}
			}
		}
	}
	
	public function fetch_source($destination)
	{
		foreach($this->source as $method=>$location)
		{
			$source_dir = "{$destination}/{$this->package}";
			if(is_dir($source_dir)) continue;
			$output = null;
			$status = null;
			switch($method)
			{
				case "git":
					exec("git clone {$location} {$destination}/{$this->package}",$output,$status);
					if($status > 0) throw new Exception("Unable to clone git repository");
					break;
			}
		}
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
	
	/**
	 * Returns an Array of Dependency objects from a Pharkdep
	 */
	public static function load($file, $shell=null)
	{
		$shell = $shell ?: new Shell();
	
		if($shell->isdir($file))
			$file = (string) new Path($file, Dependency::FILENAME);
	
		if(!$shell->isfile($file))
			throw new Exception("Failed to find $file");
	
		$deps = new DependencyBuilder($shell);
		require $file;
	
		$result = $deps->build();
	
		return $result;
	}
}
