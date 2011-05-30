<?php

namespace Phark\Source;

use \Phark\Version;
use \Phark\Package;
use \Phark\Dependency;
use \Phark\Specification;

class HttpSource implements \Phark\Source
{
	const TYPE='http';

	private $_url, $_index;

	public function __construct($url)
	{
		$this->_url = $url;
	}

	public function package($name, \Phark\Version $version)
	{	
		$index = $this->index();
		$deps = array_map(function($d){ return Dependency::parse($d); },
			$index[$name][(string)$version]); 

		$spec = new Specification(array(
			'name'=>$name, 'version'=>$version, 'dependencies'=>$deps
		));

		return new Package($spec, $this->url("/packages/%s@%s.phark", $name, $version),
			self::TYPE
		);
	}

	public function packages()
	{
		$packages = array();

		foreach($this->index() as $name=>$versions)
			$packages []= $this->package($name, new Version(key($versions)));

		return $packages;
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

