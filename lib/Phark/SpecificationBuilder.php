<?php

namespace Phark;

/**
 * Builds a {@link Specification} object, used in Pharkspec files
 */
class SpecificationBuilder
{
	private $_props, $_shell;

	/**
	 * Constructor
	 */
	public function __construct(Shell $shell=null)
	{
		$this->_shell = $shell ?: new Shell();
		$this->_props = array(
			'files' => new FileList(array(Specification::FILENAME), $this->_shell),
			'executables' => new FileList(array(), $this->_shell),
		);
	}

	/**
	 * The name of the package, e.g foo or phark
	 */
	public function name($name) 
	{ 
		$this->_props['name'] = $name; 
		return $this; 
	}

	/**
	 * The authors of the package, e.g Lachlan Donald <lachlan@ljd.cc>
	 */
	public function authors($authors) 
	{ 
		$this->_props['authors'] = func_get_args();
		return $this;
	}

	/**
	 * The homepage of the project, e.g http://github.com/lox/phark
	 */
	public function homepage($homepage) 
	{ 
		$this->_props['homepage'] = $homepage; 
		return $this;
	}

	public function summary($summary) 
	{ 
		$this->_props['summary'] = $summary;
		return $this;
	}

	public function description($description)
	{ 
		$this->_props['description'] = $description; 
		return $this;
	}

	public function includePath($path)
	{ 
		foreach(func_get_args() as $p)
			$this->_props['includePath'] []= $p; 
		return $this;
	}	

	public function version($version) 
	{ 
		$this->_props['version'] = new \Phark\Version($version); 
		return $this;
	}

	public function phpVersion($phpVersion) 
	{
		$this->_props['phpVersion'] = new \Phark\Requirement($phpVersion); 
		return $this;
	}

	public function executables($path)
	{
		foreach(func_get_args() as $filespec)
			$this->_props['executables']->add($filespec);
		return $this;
	}		
	
	public function files($files) 
	{ 
		foreach(func_get_args() as $filespec)
			$this->_props['files']->add($filespec);
		return $this;
	}

	public function dependency($name, $requirement=null) 
	{ 
		$this->_props['dependencies'][] = new \Phark\Dependency($name, $requirement); 
		return $this;
	}

	public function devDependency($name, $requirement=null) 
	{ 
		$this->_props['devDependencies'][] = new \Phark\Dependency($name, $requirement); 
		return $this;
	}

	/**
	 * Builds the specification object
	 */
	public function build()
	{
		return new Specification($this->_props);
	}
}
