<?php

namespace Phark\Command;

class EnvironmentCommand implements \Phark\Command
{
	public function summary()
	{
		return 'Shows the paths and environment used by phark';
	}

	public function execute($args, $env)
	{
		$shell = $env->shell();
		$shell
			->printf("Phark Environment\n")
			->printf("  - PHARK VERSION: %s\n", \Phark::VERSION)
			->printf("  - PHP VERSION: %s\n", phpversion())
			->printf("  - INSTALLATION DIRECTORY: %s\n", $env->{'install_dir'})
			->printf("  - PHP EXECUTABLE: %s\n", trim(`which php`))
			->printf("  - EXECUTABLE DIRECTORY: %s\n", $env->{'executable_dir'})
			//->printf("  - PACKAGE DIRS: \n")
			;

		$shell->printf("  - SOURCES: \n");

		foreach($env->sources() as $source)
			$shell->printf("    - $source\n");

		$shell->printf("  - INCLUDE PATHS: \n");

		foreach(explode(PATH_SEPARATOR,get_include_path()) as $path)
			if(trim($path, '.'))
				$shell->printf("    - $path\n");

		// optionally show project details
		if($project = $env->project())
		{
			$shell->printf("  - PROJECT NAME: %s\n", $project->name());
			$shell->printf("  - PROJECT PATH: %s\n", $project->directory());
		}
	}
}

