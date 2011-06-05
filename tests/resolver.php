<?php

require_once __DIR__.'/base.php';

class ResolverTest extends \Phark\Tests\TestCase
{
	public function setUp()
	{
		$this->source = new \Phark\Source\ArraySource();
		$this->index = new \Phark\Source\SourceIndex(array($this->source));
	}

	private function _package($name, $version, $deps=array())
	{
		$deps = array_map(function($dep) { return \Phark\Dependency::parse($dep); }, $deps);
		
		return new \Phark\Package($name, new \Phark\Version($version), $deps, $this->source);
	}

	public function testResolvingSimpleDependencies()
	{
		$package = $this->_package('package','1.0.0', array(
			'packageA 1.0.0',
			'packageB >=2.0.1',	
		));

		$this->source
			->add($package)
			->add($this->_package('packageA', '1.0.0'))
			->add($this->_package('packageA', '2.0.0'))
			->add($this->_package('packageB', '1.0.1'))
			->add($this->_package('packageB', '2.0.1', array('packageC >=3.0.0')))
			->add($this->_package('packageC', '3.0.0', array('packageA >=1.0.0')))
			->add($this->_package('packageC', '3.5.0beta1', array('packageA >=1.0.0')))
			;

		$resolver = new \Phark\DependencyResolver($this->index);
		$resolver->package($package);
		$solution = $resolver->resolve();

		$this->assertEqual($solution, array(
			'packageC@3.5.0beta1',
			'packageB@2.0.1',
			'packageA@1.0.0',
			'package@1.0.0',	
		));
	}

	public function testCircularDependencies()
	{
		$package = $this->_package('packageA','1.0.0', array(
			'packageB 1.0.0',
		));

		$this->source
			->add($package)
			->add($this->_package('packageB', '1.0.0', array('packageA 1.0.0')))
			;

		$resolver = new \Phark\DependencyResolver($this->index);
		$resolver->package($package);
		$solution = $resolver->resolve();

		$this->assertEqual($solution, array(
			'packageB@1.0.0',
			'packageA@1.0.0',
		));
	}	

	public function testDependencyClash()
	{
		$package = $this->_package('packageA','1.0.0', array(
			'packageB 1.0.0',
			'packageC 2.0.1',
		));

		$this->source
			->add($package)
			->add($this->_package('packageB', '1.0.0', array('packageA 1.0.0')))
			->add($this->_package('packageB', '2.0.0', array('packageA 1.0.0')))
			->add($this->_package('packageC', '2.0.1', array('packageB 2.0.0')))
			;

		$resolver = new \Phark\DependencyResolver($this->index);
		$resolver->package($package);
		
		$this->expectException();
		$solution = $resolver->resolve($package);
	}	
}
