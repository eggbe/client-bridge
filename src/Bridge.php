<?php
namespace Eggbe\ClientBridge;

use \Eggbe\Helper\Arr;
use \Eggbe\Helper\Curl;
use \Eggbe\Reglib\Reglib;

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
		if (!filter_var($url = strtolower(trim($url)), FILTER_VALIDATE_URL)) {
			throw new \Exception('Invalid url "' . $url . '"!');
		}

		$this->url = $url;
	}

	/**
	 * @var array
	 */
	private $Fields = [];

	/**
	 * @param string $name
	 * @param string $value
	 * @return Bridge
	 */
	public function with($name, $value) {
		$this->Fields[strtolower(trim($name))] = $value;

		return $this;
	}

	/**
	 * @param string $method
	 * @param array $Args
	 * @return Bridge
	 * @throws \Exception
	 */
	public function __call($method, array $Args = []) {
		if (!preg_match('/^with(' . Reglib::VAR . ')$/', $method, $Matches)){
			throw new \Exception('Call to undefined method "' . get_class($this) . '::' . $method . '()"!');
		}

		return call_user_func_array([$this, 'with'], Arr::unshift($Args, $Matches[1]));
	}

	/**
	 * @return string
	 */
	public function send() {
		return Curl::post($this->url,
			array_filter(array_change_key_case($this->Fields,
				CASE_LOWER)));
	}

}
