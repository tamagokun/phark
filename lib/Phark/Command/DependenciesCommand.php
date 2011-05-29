<?php

namespace Phark\Command;

use \Phark\Path;
use \Phark\Exception;
use \Phark\SpecificationBuilder;
use \Phark\Source\SourceIndex;

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

		// create a source index
		$index = new SourceIndex($env->sources());
		$resolver = new DependencyResolver($index);

		$env->shell()->printf(" * installing dependencies for %s\n", $project->name());

		foreach($project->dependencies() as $dep)
			$resolver->dependency($dep);
	}
}
