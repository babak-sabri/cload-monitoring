<?php
namespace Kayer\Monitoring\MonitoringAPI\Zabbix;
use Illuminate\Support\Facades\Http;
use Kayer\Monitoring\AdaptorInterface;
use Kayer\Monitoring\Exceptions\MonitoringException;
use Symfony\Component\HttpFoundation\Response;

class Adaptor implements AdaptorInterface
{
	/**
	 *
	 * @var string monitoring path
	 */
	private $url;
	
	/**
	 *
	 * @var string monitoring username
	 */
	private $username;
	
	/**
	 *
	 * @var string monitoring password
	 */
	private $password;
	
	/**
	 *
	 * @var string zabbix API token
	 */
	private $authToken;
	
	public function __construct($url, $username, $password)
	{
		$this->url		= $url;
		$this->username	= $username;
		$this->password	= $password;
	}
	
	public function getUrl()
	{
		return $this->url;
	}
	
	private function getTocken()
	{
		$result		= $this->call('user.login', [
			'user'		=> $this->username,
			'password'	=> $this->password,
		], 1, false);
		
		if(isset($result['result']) && !empty($result['result'])) {
			$this->authToken	= $result['result'];
			return $this->authToken;
		} else {
			throw new MonitoringException(config('zabbix.failed-login-message'), Response::HTTP_SERVICE_UNAVAILABLE);
		}
	}

	/**
	 * call an API
	 * 
	 * @param string $api api name
	 * @param array $params api parameters
	 * @param type $identifier an identifire for specific operation
	 * @param type $useToken use authentication token
	 * 
	 * @return array API response
	 * @throws MonitoringException
	 */
	public function call(string $api, array $params=[], $identifier=1, $useToken=true)
	{
		$apiParams	= [
			'jsonrpc'	=> '2.0',
			'method'	=> $api,
			'id'		=> $identifier,
			'params'	=> $params,
		];
		
		if($useToken && is_null($this->authToken)) {
			$this->getTocken();
		}

		if($useToken) {
			$apiParams['auth']	= $this->authToken;
		}
		
		$response = Http::post($this->url, $apiParams);
		
		if($response->ok()) {
			return $response->json();
		}
		throw new MonitoringException(config('monitoring.request-droped'), $response->status());
	}
	
	/**
	 * logout form monitoring
	 */
	public function __destruct()
	{
		$this->call('user.logout');
	}
}