<?php

namespace Phark;

interface Source extends \IteratorAggregate
{
	public function package($name, Version $version);

	public function packages();
}
