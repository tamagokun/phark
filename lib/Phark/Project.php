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

	public function includePaths()
	{
		return array(
			(string) new Path($this->_dir, 'vendor')
		);
	}

	/**
	 * Returns Dependency objects for the Project
	 */
	public function dependencies()
	{
		$pharkspec = new Path($this->_dir, 'Pharkspec');
		$pharkdeps = new Path($this->_dir, 'Pharkdeps');

		if($this->_env->shell()->isfile($pharkspec))
		{
			$spec = SpecificationBuilder::fromFile($pharkspec)->build();
			return $spec->dependencies();
		}
		else
		{
			throw new Exception("Didn't find a Pharkspec file");
		}
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
		$projectRoot = false;

		do
		{
			if($shell->isfile("$dir/Pharkspec") || $shell->isfile("$dir/Pharkdeps"))
				$projectRoot = $dir;
			else
				$dir = dirname($dir);
		} 
		while(!$projectRoot && $dir != '/');

		if($projectRoot)
			return new self($projectRoot);
	}
}
