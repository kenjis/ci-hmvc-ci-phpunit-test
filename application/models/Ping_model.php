<?php

class Ping_model extends CI_Model
{
	public $table_name = 'keys';

	public function __construct()
	{
		parent::__construct();
		log_message('info', 'Ping_model Class Initialized');
	}

	public function get()
	{
		return 'User object';
	}
}
