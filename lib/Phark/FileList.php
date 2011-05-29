<?php

namespace Phark;

/**
 * A list of files globbed against the cwd.
 */
class FileList implements \IteratorAggregate
{
	private $_shell, $_files=array(), $_exclude=array();

	/**
	 * Constructor
	 */
	public function __construct(array $patterns=array(), Shell $shell=null)
	{
		$this->_shell = $shell ?: new Shell();

		foreach($patterns as $pattern)
		{
			if($pattern[0] == '!')
				$this->exclude(substr($pattern,1));
			else
				$this->add($pattern);
		}
	}

	/* (non-phpdoc)
	 * See \IteratorAggregate
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->files());
	}

	/**
	 * Returns the base directory
	 */ 
	public function directory()
	{
		return $this->_shell->getcwd();
	}

	/**
 	 * Builds an array of relative filepaths
	 */
	public function files()
	{
		$exclude = $this->_exclude;

		return array_values(array_filter($this->_files, function($f) use($exclude) {
			foreach($exclude as $pattern)
				if(FileList::match($f, $pattern)) return false;
			return true;
		}));
	}

	/**
	 * Add a glob pattern for inclusion to the file list
	 * @ see \Phark\Shell::glob()
	 */
	public function add($pattern)
	{
		$files = array_filter($this->_shell->glob($this->directory(), $pattern), function($f) use($pattern) {
			return FileList::match($f, $pattern);
		});

		$this->_files = array_merge($this->_files, $files);
		return $this;
	}

	/**
	 * Exclude a glob pattern from the file list
	 * @see \Phark\Shell::glob()
	 */
	public function exclude($pattern)
	{
		$this->_exclude []= $pattern;
		return $this;
	}	

	/**
	 * Match a glob to a filename
	 * return bool
	 */
	public static function match($filename, $glob)
	{
		$pattern = preg_replace('/\*\*/','(.+?)',$glob);
		$pattern = preg_replace('/\*/','([^/]+)',$pattern);
		$pattern = '#^'.$pattern.'$#';

		return preg_match($pattern, $filename);
	}
}
