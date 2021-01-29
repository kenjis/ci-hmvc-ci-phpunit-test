<?php

class Foo_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		log_message('info', 'Foo_model Class Initialized');
	}

	public function get()
	{
		return 'Foo';
	}
}
