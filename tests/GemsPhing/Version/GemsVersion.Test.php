<?php

use GemsPhing\Version\GemsVersion;

class GemsVersionTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function get_1()
	{
		GemsVersion::get('1.2.3');
	}
}