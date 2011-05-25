<?php

namespace Phark\Command;

use \Phark\Path;
use \Phark\Exception;

class BundleCommand implements \Phark\Command
{
	public function summary()
	{
		return 'Bundle the current directory into a package';
	}

	public function execute($args, $env)
	{
		$opts = new \Phark\Options($args);
		$result = $opts->parse(array('-f'), array('command'));

		$shell = $env->shell();
		$package = new \Phark\Package($shell->getcwd());

		$shell->printf(" * bundling %s %s\n", 
			$package->spec()->name(), $package->spec()->version());

		$bundler = new \Phark\Bundler($package);
		$phar = $bundler->bundle($shell->getcwd(), isset($result->opts['-f']));

		$shell->printf(" * wrote %d files into %s √\n", count($phar), $bundler->pharfile());
	}
}


