<?php

namespace Phark;

class PackageList implements \IteratorAggregate
{
	private $_list;
    
  public function __construct(FileList $list)
  {
    $this->_list = $list;
  }

	public function getIterator()
	{
		return \ArrayIterator(array());
	}
}
