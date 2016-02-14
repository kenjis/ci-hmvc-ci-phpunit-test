<?php

class Ping_test extends TestCase
{
	public function test_ping()
	{
		$api_key = '1234567890aaaabbbbb';

		try{
			$output = $this->request(
				'GET', '/ping', ['apikey' => $api_key]
			);
		} catch (CIPHPUnitTestExitException $e) {
			$output = ob_get_clean();
		}

		$this->assertResponseCode(200);
		$output = json_decode($output);
		$this->assertNotNull($output);
		$this->assertEquals('Pong', $output->message);
	}
}
