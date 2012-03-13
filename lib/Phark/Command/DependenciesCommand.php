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
		
		//download any dependencies from source
		foreach($project->dependencies() as $dependency)
		{
			if(!empty($dependency->source))
			{
				$env->shell()->printf(" * fetching source for %s\n", $dependency);
				try {
					$dependency->fetch_source($project->vendorDir());
				}
				catch(Exception $e)
				{
					$env->shell()->printf("Failed to fetch source\n");
				}
			}
		}
		
		$installed = new SourceIndex(array($project->packages()));

		// create a source index
		$index = new SourceIndex(array_merge($env->sources()));
		$resolver = new DependencyResolver($index, $project->dependencies());
		
		$env->shell()->printf(" * checking dependencies for %s\n", $project->name());
		
		foreach($resolver->resolve() as $dependency)
		{
			$env->shell()->printf("     %s ", $dependency);

			try
			{
				$package = $installed->find($dependency);
				$env->shell()->printf("√\n");
				continue;
			}
			catch(Exception $e) 
			{
				$env->shell()->printf("required\n");
			}

			$package = $index->find($dependency);
			$installer = new \Phark\PackageInstaller($env);
			$installer->install($package, Path::join($project->vendorDir(), $package->name()));			

			$env->shell()->printf("       installed √\n", $package->hash());
		}

		$env->shell()->printf(" * dependencies are up to date √\n");
	}
}
