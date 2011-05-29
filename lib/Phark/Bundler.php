<?php

namespace Phark;

/**
 * Builds a .phar bundle from a package directory
 */
class Bundler
{
	const FORMAT_VERSION=1;

	private $_dir, $_spec, $_env;

	/**
	 * Constructor
	 */
	public function __construct(Specification $spec, $dir, $env=null)
	{
		$this->_spec = $spec;
		$this->_dir = $dir;
		$this->_env = $env ?: new Environment();
	}

	/**
	 * Return the default filename for the spec
	 * @return string
	 */
	public function pharfile()
	{
		return $this->_spec->hash().'.phar';
	}

	/**
	 * Builds a {@link Phar} archive in the specified directory
	 * @return Phar
	 */
	public function bundle($dirname, $overwrite=false)
	{
		if(!\Phar::canWrite())
			throw new Exception("unable to bundle packages when phar.readonly=1 (php.ini)");

		$filename = (string)new Path($dirname, $this->pharfile());

		if($this->_env->shell()->isfile($filename))
		{
			if($overwrite)
				$this->_env->shell()->unlink($filename);
			else
				throw new Exception("$filename already exists");
		}

		$phar = new \Phar($filename, 0, $this->pharfile());

		foreach($this->_spec->files() as $file)
			$phar->addFile(new Path($this->_dir, $file), $file);

		$phar->setMetadata(array(
			'pharkversion'=>\Phark::VERSION,
			'bundleversion'=>Bundler::FORMAT_VERSION,
		));

		return $phar;
	}
}
