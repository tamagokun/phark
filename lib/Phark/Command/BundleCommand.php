<?php

namespace Phark\Command;

use \Phark\Path;
use \Phark\Exception;
use \Phark\Specification;
use \Phark\Bundler;

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
		$spec = Specification::load($env->shell()->getcwd());
		$shell->printf(" * bundling %s %s\n", $spec->name(), $spec->version());

		$bundler = new Bundler($spec, $env->shell()->getcwd());
		$phar = $bundler->bundle($shell->getcwd(), isset($result->opts['-f']));

		$shell->printf(" * wrote %d files into %s √\n", count($phar), $bundler->pharfile());
	}
}


