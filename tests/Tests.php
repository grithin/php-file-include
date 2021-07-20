<?php
use PHPUnit\Framework\TestCase;

use \Grithin\Debug;
use \Grithin\Time;
use \Grithin\Arrays;
use \Grithin\FileInclude;


class Tests extends TestCase{
	use Bootstrap\Test;
	function __construct(){
		parent::__construct();

		$this->files = [
			__DIR__.'/test_files/include.php',
			__DIR__.'/test_files/include2.php',
			__DIR__.'/test_files/include3.php'
		];
	}
	function test_all(){
		$result = FileInclude::include($this->files[0]);
		$this->assertEquals('bill', $result, 'return unexpected');

		$result = FileInclude::include($this->files[0], ['bob'=>'bob']);
		$this->assertEquals('bob', $result, 'return unexpected');

		$result = FileInclude::include($this->files[0], ['bob'=>'bob'], ['extract'=>['bob']]);
		$this->assertEquals('bob', $result['_return'], 'return unexpected');
		$this->assertEquals('bob', $result['bob'], 'return unexpected');

		# file was already included, php will just return a succes message of `1`
		$result = FileInclude::include_once($this->files[0]);
		$this->assertEquals(1, $result, 'return unexpected');

		$closure = function(){
			return FileInclude::require($this->files[0].'not_a_file');
		};
		$this->assert_exception($closure);
	}
	function test_require_once(){
		#+ test require_once {
		$result = FileInclude::require_once($this->files[1], ['bob'=>'bob'], ['extract'=>['bob']]);
		$this->assertEquals('bob', $result['_return'], 'return unexpected');
		$this->assertEquals('bob', $result['bob'], 'return unexpected');

		$result = FileInclude::require_once($this->files[1], ['bob'=>'bob'], ['extract'=>['bob']]);
		$this->assertEquals(1, $result['_return'], 'return unexpected');
		$this->assertEquals('bob', $result['bob'], 'return unexpected');
		#+ }
	}
	function test_globals(){
		global $bob;
		$bob = 'bob';

		$result = FileInclude::require_once($this->files[2], null, ['globals'=>['bob']]);
		$this->assertEquals('bob', $result, 'return unexpected');
	}
}