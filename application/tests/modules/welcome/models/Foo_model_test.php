<?php

class Foo_model_test extends TestCase
{

	public function setUp()
	{
		$this->resetInstance();
		CI::$APP->router->module = 'foo';
		$this->CI->load->model('foo_model');
		$this->obj = $this->CI->foo_model;
	}

	public function test_get()
	{
		$ret = $this->obj->get();
		$this->assertEquals('Foo', $ret);
	}
}
