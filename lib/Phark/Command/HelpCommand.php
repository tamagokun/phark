<?php

namespace Phark\Command;

use \Phark\Path;
use \Phark\Options;

class HelpCommand implements \Phark\Command
{
	public function summary()
	{
		return 'Show help for a command or help topic';
	}

	public function execute($args, $env)
	{
		$opts = new \Phark\Options($args);
		$result = $opts->parse(array(), array('command','topic'));
		
		// configure man page reader
		$viewer = $env->{'viewer'};
		$path = Path::join(__DIR__.'/../../../man', $result->params['topic'].'.1');

		if(!$env->shell()->isfile($path))
			throw new \Phark\Exception("Unknown help topic {$result->params['topic']}");
		
		passthru(sprintf("%s %s > `tty`", escapeshellcmd($viewer), escapeshellarg($path)));
	}
}
