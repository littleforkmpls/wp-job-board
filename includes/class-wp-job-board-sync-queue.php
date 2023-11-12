<?php

class WP_Job_Board_Sync_Queue {

	const CLIENT_ID= 'f0d3dac8-1151-4f69-8522-f4cb3e655ab1';
	const CLIENT_SECRET = 'txPBOgowLETE2xKM3b92sb8y';
	const API_USERNAME= 'nsctechnologies.parqa';
	const API_PASSWORD = 'Ct!un3,m8a92';

	private $get_data_center_url = "https://rest.bullhornstaffing.com/rest-services/loginInfo?username={API_Username}";

	public function __construct()
	{

	}

	public function sync_queue()
	{
		$dataCenterUrl = wp_remote_get($this->getUrl($this->get_data_center_url, ['{API_Username}' => self::API_USERNAME]));
		$response = wp_remote_get(
			'',
			[]
		);
	}

	private function getUrl($url, $tokens)
	{
		return str_replace($url, array_keys($tokens), array_values($tokens));
	}
}