<?php

namespace Phark;

/**
 * A local package is one with an existing local directory and a specification 
 */
class LocalPackage extends Package
{
	/**
	 * Constructor
	 */
	public function __construct($dir, $spec=null, $env=null)
	{	
		$this->_env = $env ?: new Environment();
		$this->_spec = $spec ?: Specification::load($dir, $this->_env->shell());
		$this->_dir = $this->_env->shell()->realpath($dir);

		// create a dummy source
		$source = new Source\ArraySource();
		$source->add($this);

		parent::__construct(
			$this->_spec->name(), $this->_spec->version(), $this->_spec->dependencies(), $source, $env
		);
	}
}
