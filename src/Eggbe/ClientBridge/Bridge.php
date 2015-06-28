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
	public function __construct($url) {
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
	private $namespace = null;

	/**
	 * @param string $namespace
	 * @return Bridge
	 * @throws \Exception
	 */
	public function where($namespace){
		if (!preg_match('/[A-Za-z]+[A-Za-z0-9-]+/', ($namespace = trim($namespace)))){
			throw new \Exception('Invalid namespace format "' . $namespace . '"!');
		}
		$this->namespace = strtolower($namespace);
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

		if (!Arr::has($Response, 'error', 'data')){
			throw new \Exception('Invalid response format!');
		}

		if ($Response['error']){
			if (Arr::has($Response, 'message')){
				throw new \Exception($Response['message']);
			}else{
				throw new \Exception('Unknown error!');
			}
		}

		return $Response['data'];

	}

}
