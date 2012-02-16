<?php

namespace Phark;

class Project
{
	private $_dir, $_env, $_spec;

	public function __construct($dir, $env=null)
	{
		$this->_dir = $dir;
		$this->_env = $env ?: new Environment();
	}

	public function name()
	{
		return basename($this->_dir);
	}

	public function directory()
	{
		return $this->_dir;
	}

	public function vendorDir()
	{
		return (string) new Path($this->directory(), 'vendor');
	}

	public function packages()
	{
		return new Source\DirectorySource(new Path($this->_dir, 'vendor'), $this->_env);
	}

	/**
	 * Returns Dependency objects for the Project
	 */
	public function dependencies()
	{
		$pharkspec = new Path($this->_dir, 'Pharkspec');
		$pharkdeps = new Path($this->_dir, 'Pharkdeps');
		$found = false;
		
		if($this->_env->shell()->isfile($pharkspec))
		{
			$found = true;
			if($deps = Specification::load($pharkspec)->dependencies() && count($deps) > 0)
				return $deps;
		}
		if($this->_env->shell()->isfile($pharkdeps))
		{
			$found = true;
			return Dependency::load($pharkdeps);
		}
		if(!$found) throw new Exception("Didn't find a Pharkspec file");
	}

	/**
	 * Finds the nearest path with a Pharkspec or Pharkdep file
	 * @return Project or null
	 */
	public static function locate($env=null)
	{
		$env = $env ?: new Environment();
		$shell = $env->shell();
		$dir = $shell->getcwd();

		do
		{
			if($shell->isfile("$dir/Pharkspec") || $shell->isfile("$dir/Pharkdeps"))
				return new self($dir);
			else
				$dir = dirname($dir);
		} 
		while($dir != '/');
	}
}
