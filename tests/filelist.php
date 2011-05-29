<?php

require_once __DIR__.'/base.php';

\Mock::generate('\Phark\Shell','MockShell'); 

class FileListTest extends \Phark\Tests\TestCase
{
	public function testFiles()
	{
		$shell = new MockShell();
		$shell->expectAtLeastOnce('getcwd');
		$shell->setReturnValue('glob', array(
			'lib/Package/A.php',
			'lib/Package/Blargh/B.php',
			'bin/llamas.php',
			'bin/ignore.php',
			'.git/config',
			'README.md',
			'LICENSE',
			'CONTRIBUTORS'
		));

		$list = new \Phark\FileList(array('lib/**','README.md','bin/*','!bin/ignore.php'), $shell);
		$this->assertEqual(iterator_to_array($list), array(
			'lib/Package/A.php',
			'lib/Package/Blargh/B.php',
			'README.md',
			'bin/llamas.php',
		));
	}
}
