<?php

namespace Phark\Command;

use \Phark\Path;
use \Phark\Exception;
use \Phark\DependencyResolver;
use \Phark\Source\SourceIndex;
use \Phark\Package;
use \Phark\Dependency;
use \Phark\Requirement;

class DependenciesCommand implements \Phark\Command
{
	public function summary()
	{
		return 'Installs dependencies for the current project';
	}

	public function execute($args, $env)
	{
		if(!($project = $env->project()))
			throw new Exception("This command only works inside a project");

		$installed = new SourceIndex(array($project->packages()));

		// create a source index
		$index = new SourceIndex($env->sources());
		$resolver = new DependencyResolver($index);

		$env->shell()->printf(" * checking dependencies for %s\n", $project->name());

		foreach($project->dependencies() as $dep)
			$resolver->dependency($dep);

		foreach($resolver->resolve() as $hash)
		{
			list($name, $version) = Package::parseHash($hash);
			$env->shell()->printf("   * %s@%s ", $name, $version);

			try
			{
				$package = $installed->find(new Dependency($name), Requirement::version($version));
				$env->shell()->printf("√\n");
				continue;
			}
			catch(Exception $e) 
			{
				$env->shell()->printf("required\n");
			}

			$package = $index->find(new Dependency($name), Requirement::version($version));
			$installer = new \Phark\PackageInstaller($env);
			$installer->install($package, new \Phark\Path($project->vendorDir(), $package->name()));			

			$env->shell()->printf("     * installed √\n", $package->hash());
		}
	}
}
