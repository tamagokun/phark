<?php

namespace Phark\Command;

class InstallCommand implements \Phark\Command
{
	public function summary()
	{
		return 'Install a package globally';
	}

	public function execute($args, $env)
	{
		$opts = new \Phark\Options($args);
		$result = $opts->parse(array('-f','-s:'), array('command','package'));
		$spec = null;

		// if a directory is specified
		if($realpath = $env->shell()->realpath($result->params['package']))
		{
			$env->shell()->printf(" * installing from %s\n", $realpath);

			if(isset($result->opts['-s']))
			{
				$specfile = $result->opts['-s'][0];
				$fetcher = new \Phark\SpecificationFetcher($env);
				$spec = $fetcher->fetch($specfile);
				$env->shell()->printf(" * reading spec %s\n", $specfile);
			}

			$package = new \Phark\LocalPackage($realpath, $spec);
			$package->install();

			// copy over the spec
			if(isset($specfile))
				$env->shell()->copy($specfile, new \Phark\Path($package->directory(), \Phark\Specification::FILENAME));
		}
		else
		{
			$env->shell()->printf(" * installing %s\n", $result->params['package']);

			$index = new \Phark\Source\SourceIndex($env->sources());
			$package = $index->find(new \Phark\Dependency($result->params['package']));		
			$package->install();
		}

		$package->activate();
		$env->shell()->printf(" * package %s installed √\n", $package->hash());		
	}
}
