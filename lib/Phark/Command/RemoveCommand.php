<?php

namespace Phark\Command;

use \Phark\Path;

class RemoveCommand implements \Phark\Command
{
	public function summary()
	{
		return 'Removes a package globally';
	}

	public function execute($args, $env)
	{
		$opts = new \Phark\Options($args);
		$result = $opts->parse(array(), array('command','package'));
		$spec = null;

		$package = $env->package($result->params['package']);

		$env->shell()->printf(" * removing package %s\n", $package);

		$installer = new \Phark\PackageInstaller($env);
		$installer->uninstall($package, Path::join($env->{'active_dir'}, $package->name()));

		$env->shell()->printf("  removed √\n");		
	}
}

