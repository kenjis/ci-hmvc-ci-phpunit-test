<?php

class Ping extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('ping_model');
	}

	public function index_get()
	{
		$user = $this->ping_model->get(); //this will cause error if called from phpunit
		$this->response(
			array(
					'response' => REST_Controller::HTTP_OK,
					'status'   => true,
					'message'  => "Pong",
					'data'     => '',
			),
			REST_Controller::HTTP_OK
		);
	}
}
