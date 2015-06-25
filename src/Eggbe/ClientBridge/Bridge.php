<?php
namespace Eggbe\ClientBridge;

use \Eggbe\Helpers\Arr;
use \Eggbe\Helpers\Curl;

class Bridge {

	/**
	 * @var string
	 */
	private $url = null;

	/**
	 * @param string $url
	 * @throws \Exception
	 */
	public final function __construct($url) {
		if (!filter_var(($url = trim($url)), FILTER_VALIDATE_URL)) {
			throw new \Exception('Invalid url format!');
		}
		$this->url = $url;
	}

	/**
	 * @var string
	 */
	private $method = null;

	/**
	 * @param $method
	 * @return Bridge
	 * @throws \Exception
	 */
	public function to($method){
		if (!preg_match('/[A-Za-z]+[A-Za-z0-9-]+/', ($method = trim($method)))){
			throw new \Exception('Invalid method format "' . $method . '"!');
		}
		$this->method = strtolower($method);
		return $this;
	}

	/**
	 * @var string
	 */
	private $layout = null;

	/**
	 * @param string $layout
	 * @return Bridge
	 * @throws \Exception
	 */
	public function where($layout){
		if (!preg_match('/[A-Za-z]+[A-Za-z0-9-]+/', ($layout = trim($layout)))){
			throw new \Exception('Invalid layout format "' . $layout . '"!');
		}
		$this->layout = strtolower($layout);
		return $this;
	}

	/**
	 * @var array
	 */
	private $Params = [];

	/**
	 * @param array $Params
	 * @return Bridge
	 */
	public function with(array $Params){
		$this->Params = array_merge($this->Params, array_change_key_case($Params, CASE_LOWER));
		return $this;
	}

	/**
	 * @throws \Exception
	 * @return array
	 */
	public function send() {

		$Response = json_decode(trim(Curl::post($this->url, Arr::not(array_change_key_case(get_object_vars($this),
			CASE_LOWER), 'url'))), true);

		if (is_null($Response)){
			throw new \Exception('Invalid response length!');
		}

		if (!isset($Response['error'])){
			throw new \Exception('Invalid response format!');
		}

		if ($Response['error'] && isset($Response['message'])){
			throw new \Exception($Response['message']);
		}

		if ($Response['error']){
			throw new \Exception('Unknown error!');
		}

		return $Response;

	}

}
