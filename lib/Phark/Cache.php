<?php

namespace Phark;

class Cache
{
	private $_path, $_env;

	public function __construct($path, $env)
	{
		$this->_path = $path;
		$this->_env = $env;
	}

	public function fetch($url, $callback)
	{
		$filename = $this->_filename($url);
		$shell = $this->_env->shell();

		// does the decompressed directory exist?
		if($shell->isdir($filename->dir))
		{
			return $filename->dir;
		}

		// how about the archive?
		if(!$shell->isfile($filename->archive))
		{
			if(!$fp = fopen($filename->archive, 'w+'))
				throw new Exception("Failed to open {$filename->archive} for writing");

			$remote = $callback($url);
			stream_copy_stream($remote, $fp);

			fclose($fp);
			fclose($remote);
		}

		// decompress the archive
		if($shell->isfile($filename->archive))
		{
			if($filename->type != 'phar')
				throw new Exception("Only phar archives are supporter at present");

			$phar = new \Phar($filename->archive);
			$phar->extractTo($filename->dir);

			return $filename->dir;
		}

		throw new Exception("Failed to fetch $url");
	}

	private function _filename($url)
	{
		$filename = (string) new Path($this->_env->{'cache_dir'}, basename($url));
		$pathinfo = pathinfo($filename);

		return (object) array(
			'dir' => (string) new Path($pathinfo['dirname'], $pathinfo['filename']),
			'archive' => $filename,
			'type' => $pathinfo['extension'],
		);
	}
	
}
