<?php

namespace Phark;

/**
 * A helper for manipulating paths
 */
class Path
{
	private $_path;

	/**
	 * Constructor, takes any number of path components
	 */
	public function __construct($components)
	{
		$this->_path = array_reduce(func_get_args(), function($result, $token) {

			$delim = (empty($result) || $result == '/') ? '' : '/';
			$token = ($token == '/') ? $token : rtrim($token,'/');
			return $result . $delim . $token;
		});
	}

	public function __toString()
	{
		return $this->_path;
	}

	/**
	 * Join path components, return a string
	 * @return string
	 */
	public static function join($components)
	{
		$class = new \ReflectionClass('\Phark\Path');
		$instance = $class->newInstanceArgs(func_get_args());

		return (string) $instance;
	}
}
