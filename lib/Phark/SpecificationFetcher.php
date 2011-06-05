<?php

namespace Phark;

class SpecificationFetcher
{
	private $_env;

	public function __construct($env)
	{
		$this->_env = $env;
	}

	public function fetch($url)
	{
		$cacheFile = (string) new Path($this->_env->{'cache_dir'}, basename($url));

		if(!$contents = @file_get_contents($url))
		{
			$error = error_get_last();
			throw new Exception("Fetching {$this->_url} failed: ".$error['message']);
		}

		// TODO: this should be done with streams, too much memory used here
		file_put_contents($cacheFile, $contents);
	
		return Specification::load($cacheFile);
	}
}
