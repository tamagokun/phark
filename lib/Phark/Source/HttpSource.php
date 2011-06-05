<?php

namespace Phark\Source;

use \Phark\Version;
use \Phark\Package;
use \Phark\Dependency;
use \Phark\PartialSpecification;

class HttpSource implements \Phark\Source
{
	private $_url, $_index, $_env;

	public function __construct($url, $env)
	{
		$this->_url = $url;
		$this->_env = $env;
	}

	public function package($name, \Phark\Version $version)
	{	
		$index = $this->index();
		$deps = array_map(function($d){ return Dependency::parse($d); },
			$index[$name][(string)$version]); 

		return new Package($name, $version, $deps, $this, $this->_env);
	}

	public function packages()
	{
		$packages = array();

		foreach($this->index() as $name=>$versions)
			$packages []= $this->package($name, new Version(key($versions)));

		return $packages;
	}

	public function fetch($name, \Phark\Version $version)
	{
		$url = $this->url("/packages/%s@%s.phar",$name,$version);

		return $this->_env->cache()->fetch($url, function($url) {
			return fopen($url, 'rb');
		});	
	}
	
	protected function index()
	{
		if(!isset($this->_index))
		{
			if(!$response = @file_get_contents($this->url('/packages.json')))
				throw new HttpException("Fetching {$this->_url} failed: {$http_response_header[0]}");

			$this->_index = json_decode($response, true);
		}

		return $this->_index;
	}

	protected function url($string)
	{
		return rtrim($this->_url,'/').call_user_func_array('sprintf',func_get_args());
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->packages());
	}
}

