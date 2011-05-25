<?php

namespace Phark;

interface Source
{
	/**
	 * Finds matching packages 
	 * @return Specification
	 */
	public function find($name, Requirement $requirement=null);
}
