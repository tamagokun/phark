<?php

namespace Phark;

interface Source
{
	public function package($name, Version $version);

	public function packages();
}
