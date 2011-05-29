<?php

namespace Phark;

/**
 * Responsible for physically installing package files into a named directory
 */
class PackageInstaller
{
	private $_dir, $_env;

	/**
	 * Constructor
	 */
	public function __construct($dir, Environment $env=null)
	{
		$this->_dir = $dir;
		$this->_env = $env ?: new Environment();
	}

	/**
	 * Copies the files from the package based on the specification
	 */
	public function install($package)
	{
		if($this->_env->shell()->isdir($this->_dir))
			throw new Exception("Directory $this->_dir already exists, cannot replace");
		else
			$this->_env->shell()->mkdir($this->_dir, 0777);

		// copy the package files into our directory
		$package->copy($this->_dir);
	}

	/**
	 * Creates symbolic links from one dir to another
	 */ 
	public function link($packagedir, $activedir)
	{
		
	}
}
