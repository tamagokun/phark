<?php

namespace Phark;

/**
 * A package source
 */
interface Source extends \IteratorAggregate
{
	/**
	 * Returns a package for a particular name and version
	 * @return Package
	 */
	public function package($name, Version $version);

	/**
	 * Returns all of the packages as an array
	 * @return array
	 */
	public function packages();

	/**
	 * Fetch the package and return a full path to a directory
	 * @return string
	 */
	public function fetch($name, Version $version);
}
