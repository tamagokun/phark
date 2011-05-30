<?php

namespace Phark;

class Environment
{
	private $_config=array();

	public function __construct($config=null)
	{
		$this->_config = $config ?: array(
			'install_dir' => '/usr/local/phark',
			'package_dir' => '/usr/local/phark/packages',
			'active_dir' => '/usr/local/phark/active',
			'cache_dir' => '/usr/local/phark/cache',
			'executable_dir' => '/usr/local/bin',
		);
	}

	public function shell()
	{
		return new Shell();
	}

	public function sources()
	{
		return array( new Source\HttpSource('http://phark.s3.amazonaws.com/') );
	}

	public function project()
	{
		return Project::locate($this);
	}

	public function packages()
	{
		return new Source\DirectorySource($this->{'package_dir'});
	}

	public function __get($key)
	{
		return $this->_config[$key];
	}
}
