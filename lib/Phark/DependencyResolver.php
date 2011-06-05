<?php

namespace Phark;

/**
 * Resolves dependencies using a simple graph. There are some gaping holes in 
 * this implementation, eventually a more serious resolver will be required.
 *
 * @see http://www.electricmonk.nl/log/2008/08/07/dependency-resolving-algorithm/
 * @see http://www.pkgcore.org/~ferringb/misc-pdfs/model.pdf
 */
class DependencyResolver
{
	private $_requirements=array();
	private $_resolve=array();
	private $_index;
	private $_tree;

	/**
	 * Constructor
	 */
	public function __construct(Source\SourceIndex $index, $dependencies=null)
	{
		$this->_index = $index;

		if($dependencies)
			foreach($dependencies as $dep) $this->dependency($dep);
	}

	/**
	 * Adds a package to be installed
	 */
	public function package(Package $package)
	{
		$this->dependency(new Dependency($package->name(), '='.$package->version()));
		return $this;
	}

	/**
	 * Adds a dependency to be installed 
	 * @chainable
	 */
	public function dependency(Dependency $dependency)
	{
		$this->_requirements[$dependency->package] []= $dependency->requirement;
		$this->_resolve []= $this->_leaf($this->_index->find($dependency));
		return $this;
	}

	/**
	 * Adds a leaf to the internal tree of package=>dependencies
	 */
	private function _leaf($package)
	{
		$hash = $package->hash();

		if(!isset($this->_tree[$hash]))
		{	
			$this->_tree[$hash] = array();

			foreach($package->dependencies() as $dep)
			{
				$this->_requirements[$dep->package] []= $dep->requirement;
				$this->_tree[$hash][]= $this->_leaf($this->_index->find($dep));
			}
		}

		return $hash;
	}

	/**
	 * Returns the a list of exact package version Dependency objects
	 * @return array
	 */
	public function resolve()
	{
		$traversed = array();
		$install = array();

		// build a list of visited versions
		foreach($this->_resolve as $hash)
			$this->_walk($hash, $traversed);

		// apply requirements
		foreach($traversed as $hash)
		{
			list($package,$version) = explode('@',$hash);
			$version = new \Phark\Version($version);

			if(isset($this->_requirements[$package]))
			{
				$requirement = new \Phark\Requirement($this->_requirements[$package]);

				if(!$requirement->isSatisfiedBy($version))
					continue;
			}
			
			if(!isset($install[$package]) || $install[$package]->less($version))
				$install[$package] = $version;
		}

		// check all requirements have been installed
		foreach($this->_requirements as $name=>$requirements)
			if(!isset($install[$name]))
				throw new Exception("Failed to find package to satisfy $name");	

		// return dependency objects
		$dependencies = array();
		foreach($install as $name=>$version)
			$dependencies []= new Dependency($name, Requirement::version($version));

		return array_reverse($dependencies);
	}

	/**
	 * Traverse a tree depth first, record all traversed nodes
	 * @return void
	 */
	private function _walk($node, &$traversed)
	{
		if(in_array($node, $traversed))
			return;

		$traversed []= $node;

		foreach($this->_tree[$node] as $subnode)
			$this->_walk($subnode, $traversed);
	}	
}
