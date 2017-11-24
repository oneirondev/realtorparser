<meta charset="utf-8">
<?php

	abstract class RealtorParcer
	{
		protected $url = '';
		protected $connection = null;

		abstract protected function getOffersList();
		abstract protected function getOfferInfo();
		abstract protected function init();

		protected function buildUrl($hostname, $slug, $params, $protocol) {
			$url = $protocol.'://'.$hostname.'/';

			foreach ($slug as $slug_item) {
				$url .= $slug_item.'/';
			}
			$url = substr($url, 0, -1).'?'.rawurlencode(http_build_query($params));

			return $url;
		} 

		protected function isUrlAvaible ($url) {
			if (!filter_var( $url, FILTER_VALIDATE_URL)) {
				echo "<strong>RealtorParcer error:</strong> данный домен невалидный!";
				return false;
			}
	
			$ch = curl_init($url);
	
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
			$response = curl_exec($ch);	
	
			curl_close($ch);

        	if ($response) return true;
        	echo "<strong>RealtorParcer error:</strong> данный домен недоступен!";
        	return false;
		}

		public function connect() {
			$this->connection = curl_init();
			curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, true);
		}

		public function disconnect() {
			curl_close($this->connection);
		}
	}

	class VKParser
	{
		public $accessUrl = '';

		private function getAccessCodeUrl () {
			return 'https://oauth.vk.com/authorize?' . http_build_query(array(
				'client_id'	=> '6271159',
				'redirect_uri' => 'http://realtor.local/realtorParser.php',
				'scope' => implode( '&', array(
					'wall', 'groups'
				))
			));
		}
		
		function __construct()
		{
			$this->accessUrl = $this->getAccessCodeUrl ();
		}
	}

	$parcer = new VKParser();
	echo $parcer->accessUrl;