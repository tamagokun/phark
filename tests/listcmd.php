<?php

require_once __DIR__.'/base.php';

\Mock::generate('\Phark\Shell','MockShell'); 
\Mock::generate('\Phark\Environment','MockEnvironment'); 
\Mock::generate('\Phark\Package','MockPackage'); 
\Mock::generate('\Phark\Project','MockProject'); 

class ListCommandTest extends \Phark\Tests\TestCase
{
	public function setUp()
	{
		$this->shell = new MockShell();

		$this->package = new MockPackage();
		$this->package->setReturnValue('name', 'blargh');
		$this->package->setReturnValue('version', new \Phark\Version('1.0.0beta'));

		$this->project = new MockProject();

		$this->env = new MockEnvironment();
		$this->env->setReturnValue('shell', $this->shell);
		$this->env->setReturnValue('project', $this->project);
	}

	public function testListingGlobalPackages()
	{
		$this->package->expectOnce('name');
		$this->package->expectOnce('version');
			
		$this->env->setReturnValue('packages', array($this->package));
		$this->env->expectOnce('packages');

		$command = new \Phark\Command\ListCommand();
		$command->execute(array('list','-g'), $this->env);
	}

	public function testProjectPackages()
	{
		$this->package->expectOnce('name');
		$this->package->expectOnce('version');

		$this->env->expectOnce('project');
		$this->env->expectNever('packages');

		$this->project->setReturnValue('packages',array($this->package));
		$this->project->expectOnce('packages');

		$command = new \Phark\Command\ListCommand();
		$command->execute(array('list'), $this->env);
	}
}
