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
	 * @var array
	 */
	private $Session = [];

	/**
	 * @var array
	 */
	private $Cookie = [];

	/**
	 * @var array
	 */
	private $Post = [];

	/**
	 * @var array
	 */
	private $Get = [];

	/**
	 * @param string $key, ...
	 * @return Bridge
	 */
	public function delegate($key){
		if (session_status() == PHP_SESSION_ACTIVE) {
			$this->Session = array_merge($this->Session,
				Arr::only($_SESSION, Arr::simplify(func_get_args())));
		}
		return $this;
	}

	/**
	 * @throws \Exception
	 * @return array
	 */
	public function send() {

		$response = preg_replace('/^HTTP\/[0-9.]+ *[0-9]* [A-Za-z]+(?:\r?\n)+/', null,
			Curl::post($this->url, Arr::not(array_change_key_case(get_object_vars($this),
				CASE_LOWER), 'url'), [], Curl::F_RETURN_HEADERS));

		if (strlen($response) < 1) {
			throw new \Exception('Invalid response length!');
		}

		$Headers = Arr::unpack(preg_split('/\r\n/', preg_replace('/\r\n\r\n.*$/s', null, $response), -1, PREG_SPLIT_NO_EMPTY), ':');
		if (count($Headers) < 1) {
			throw new \Exception('Invalid headers!');
		}

		if (!array_key_exists('Content-Type', $Headers)) {
			throw new \Exception('Unknown response type!');
		}

		if (!preg_match('/application\/json/', $Headers['Content-Type'])) {
			throw new \Exception('Unsupported response type!');
		}

		die(preg_replace('/^.*\r\n\r\n/s', null, $response));

		return json_decode(trim(preg_replace('/^.*\r\n\r\n/s', null, $response)), true);

	}

}
