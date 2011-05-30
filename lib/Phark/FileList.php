<?php

namespace Phark;

/**
 * A list of files globbed against the cwd.
 */
class FileList implements \IteratorAggregate
{
	private $_path, $_shell, $_include=array(), $_exclude=array();

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
	 * Set the working directory of the FileList
	 * @chainable
	 */
	public function chdir($directory)
	{
		$this->_path = $directory;
		return $this;
	}

	/**
	 * Returns the base directory, defaults to cwd
	 */ 
	public function directory()
	{
		return $this->_path ?: $this->_shell->getcwd();
	}

	/**
 	 * Builds an array of relative filepaths
	 */
	public function files()
	{
		$include = $this->_include;
		$exclude = $this->_exclude;
		$files = $this->_shell->glob($this->directory(), '**');

		$files = array_filter($files, function($f) use($include, $exclude) {
			return FileList::match($f, $include) && !FileList::match($f, $exclude);
		});

		return array_values($files);
	}

	/**
	 * Add a glob pattern for inclusion to the file list
	 * @ see \Phark\Shell::glob()
	 */
	public function add($pattern)
	{
		$this->_include []= $pattern;
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
	 * Match a glob to a filename, $glob can be an array of globs
	 * return bool
	 */
	public static function match($filename, $glob)
	{
		$patterns = array();

		foreach((array) $glob as $g)
		{
			$pattern = preg_replace('/\*\*/','(.+?)',$g);
			$pattern = preg_replace('/\*/','([^/]+)',$pattern);
			$patterns []= $pattern;
		}	

		return preg_match('#^('.implode('|',$patterns).')$#', $filename);
	}
}
