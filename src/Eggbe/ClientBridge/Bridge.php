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
	 * @return ABridge
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
	 * @return ABridge
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
	 * @return ABridge
	 */
	public function with(array $Params){
		$this->Params = array_merge($this->Params, array_change_key_case($Params, CASE_LOWER));
		return $this;
	}

	/**
	 * @const int
	 */
	const DELEGATE_SESSION = 1;

	/**
	 * @var array
	 */
	private $Session = [];

	/**
	 * @const int
	 */
	const DELEGATE_COOKIES = 2;

	/**
	 * @var array
	 */
	private $Cookie = [];

	/**
	 * @const int
	 */
	const DELEGATE_POST = 3;

	/**
	 * @var array
	 */
	private $Post = [];

	/**
	 * @const int
	 */
	const DELEGATE_GET = 4;

	/**
	 * @var array
	 */
	private $Get = [];

	/**
	 * @param string $from
	 * @param string $key, ...
	 * @return ABridge
	 * @throws \Exception
	 */
	public function delegate($from, $key){
		switch((int)$from){
			case self::DELEGATE_SESSION:
				if (session_status() == PHP_SESSION_DISABLED) {
					throw new \Exception('Can not delegate session values because session is disabled!');
				}
				if (session_status() == PHP_SESSION_ACTIVE) {
					$this->Session = array_merge($this->Session,
						Arr::only($_SESSION, Arr::simplify(array_slice(func_get_args(), 1))));
				}
				break;
			case self::DELEGATE_COOKIES:
				$this->Cookie = array_merge($this->Cookie,
					Arr::only($_COOKIE, Arr::simplify(array_slice(func_get_args(), 1))));
				break;
			case self::DELEGATE_POST:
				$this->Post = array_merge($this->Post,
					Arr::only($_POST, Arr::simplify(array_slice(func_get_args(), 1))));
				break;
			case self::DELEGATE_GET:
				$this->Get = array_merge($this->Get,
					Arr::only($_GET, Arr::simplify(array_slice(func_get_args(), 1))));
				break;
			default:
				throw new \Exception('Unknown source type: "' . $from . '"!');
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

		return json_decode(trim(preg_replace('/^.*\r\n\r\n/s', null, $response)), true);

	}

}
