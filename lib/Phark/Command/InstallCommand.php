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

		// load the spec from file/url
		if(isset($result->opts['-s']))
		{
			$fetcher = new \Phark\SpecificationFetcher($env);
			$spec = $fetcher->fetch($result->opts['-s'][0]);
		}

		// if a directory is specified
		if($realpath = $env->shell()->realpath($result->params['package']))
		{
			$env->shell()->printf(" * installing from %s\n", $realpath);

			//$package = new \Phark\Package($realpath);
			//$env->packages()->install($package);

			//$env->shell()->printf(" * package %s installed √\n", $package->spec()->hash());
		}
		else
		{
			$env->shell()->printf(" * installing %s\n", $result->params['package']);

			$index = new \Phark\Source\SourceIndex($env->sources());
			$package = $index->find(new \Phark\Dependency($result->params['package']));

			// install the package
			$installer = new \Phark\PackageInstaller($env);
			$installer->install($package, new \Phark\Path($env->{'package_dir'}, $package->hash()));

			// lookup from the local package store, activate
			$package = $env->packages()->package($package->name(), $package->version());
			$installer->activate($package, new \Phark\Path($env->{'active_dir'}, $package->name()));

			$env->shell()->printf(" * package %s installed √\n", $package->hash());
		}
	}
}
