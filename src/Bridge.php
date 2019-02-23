<?php
namespace Eggbe\ClientBridge;

use \Able\Helpers\Arr;
use \Able\Helpers\Curl;

use \Able\Reglib\Regex;

class Bridge {

	/**
	 * @var string
	 */
	private $url = null;

	/**
	 * @const
	 */
	const RM_POST = 'post';

	/**
	 * @const
	 */
	const RM_GET = 'get';

	/**
	 * @var null
	 */
	private $method = null;

	/**
	 * @param string $url
	 * @param string $method
	 * @throws \Exception
	 */
	public function __construct($url, $method = self::RM_GET) {
		if (!filter_var($url = strtolower(trim($url)), FILTER_VALIDATE_URL)) {
			throw new \Exception('Invalid url "' . $url . '"!');
		}
		$this->url = $url;

		if (!in_array($method = strtolower(trim($method)), [self::RM_GET, self::RM_POST])){
			throw new \Exception('Invalid method "' . $method . '"!');
		}
		$this->method = $method;
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
		if (!preg_match('/^with(' . Regex::RE_VARIABLE . ')$/', $method, $Matches)){
			throw new \Exception('Call to undefined method "' . get_class($this) . '::' . $method . '()"!');
		}

		return call_user_func_array([$this, 'with'], Arr::unshift($Args, $Matches[1]));
	}

	/**
	 * @return string
	 */
	public function send() {
		return forward_static_call_array([Curl::class, $this->method], [$this->url,
			array_filter(array_change_key_case($this->Fields, CASE_LOWER))]);
	}

}
