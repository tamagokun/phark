<?php

namespace Phark;

/**
 * Responsible for physically installing package files into a named directory
 */
class PackageInstaller
{
	private $_env, $_shell;

	/**
	 * Constructor
	 */
	public function __construct(Environment $env=null)
	{
		$this->_env = $env ?: new Environment();
		$this->_shell = $env->shell();
	}

	/**
	 * Copies the files from the package based on the specification
	 * @chainable
	 */
	public function install($package, $dir)
	{
		if($this->_shell->isdir($dir))
			throw new Exception("Directory $dir already exists, cannot replace");
		else
			$this->_shell->mkdir($dir, 0777);

		// copy the package files into our directory
		try
		{
			foreach($package->files() as $file)
			{
				$this->_shell->copy(
					(string) new Path($package->directory(), $file),
					(string) new Path($dir, $file)
				);
			}
		}
		catch(\Exception $e)
		{
			$this->_shell->rmdir($dir);
			throw $e;
		}

		return $this;
	}

	/**
	 * Links a package directory to another location, optionally installs 
	 * executables into execdir 
	 */ 
	public function activate($package, $dir, $executables=true)
	{
		// remove any previously active versions
		if($this->_shell->isdir($dir)) $this->deactivate($dir);

		// link in the new version
		$this->_shell->symlink($package->directory(), $dir);

		if($executables)
		{
			foreach($package->spec()->executables() as $bin)
			{
				$this->_shell
					->chmod((string) new Path($dir, $bin), 0777)
					->symlink(
						(string) new Path($dir, $bin),
						(string) new Path($this->_env->{'executable_dir'}, basename($bin))
					);
			}
		}

		return $this;
	}

	/**
	 * Removes a symlink, as well as any executables linked to the dir
	 */ 
	public function deactivate($dir)
	{
		$spec = Specification::load($dir);

		// unlink executables
		foreach($spec->executables() as $bin)
		{
			$path = new Path($this->_env->{'executable_dir'}, basename($bin));

			if($this->_shell->isfile($path)) $this->_shell->unlink($path); 
		}

		// remove the active link
		$this->_shell->unlink($dir);

		return $this;
		
	}
}
