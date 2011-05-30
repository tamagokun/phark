<?php

require_once __DIR__.'/base.php';

\Mock::generate('\Phark\Shell','MockShell'); 
\Mock::generate('\Phark\Environment','MockEnvironment'); 

class PackageTest extends \Phark\Tests\TestCase
{
	public function testInstalling()
	{
		$shell = new MockShell();
		$shell->setReturnValue('getcwd', '/some/path');
		$shell->setReturnValue('glob', array('Pharkspec','bin/myexec','bin/another','myfile.php'), array('/some/path','**'));
		$shell->setReturnValueAt(0, 'isdir', false, array('/packages/mypackage@1.0.0'));
		$shell->setReturnValueAt(1, 'isdir', true, array('/packages/mypackage@1.0.0'));
		$shell->setReturnValueAt(2, 'isdir', true, array('/active/mypackage@1.0.0'));
		$shell->setReturnReference('chmod', $shell);

		$shell->expectAt(0,'copy',array('/some/path/Pharkspec', '/packages/mypackage@1.0.0/Pharkspec'));
		$shell->expectAt(1,'copy',array('/some/path/bin/myexec', '/packages/mypackage@1.0.0/bin/myexec'));
		$shell->expectAt(2,'copy',array('/some/path/bin/another', '/packages/mypackage@1.0.0/bin/another'));
		$shell->expectAt(3,'copy',array('/some/path/myfile.php', '/packages/mypackage@1.0.0/myfile.php'));
		$shell->expectCallCount('copy',4);

		$env = new MockEnvironment();
		$env->setReturnReference('shell', $shell);

		$builder = new \Phark\SpecificationBuilder($shell);
		$spec = $builder
				->name('mypackage')
				->version('1.0.0')
				->files('myfile.php', 'bin/**')
				->executables('bin/**')
				->build()
				;

		$package = new \Phark\Package($spec, '/some/path', 'file', $env);
		$installer = new \Phark\PackageInstaller('/packages/mypackage@1.0.0', $env);
		$installer->install($package);
	}
}


