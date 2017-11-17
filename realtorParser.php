<meta charset="utf-8">
<?php

	abstract class RealtorParcer
	{
		protected $url = '';
		protected $connection = null;

		abstract protected function getOffersList();
		abstract protected function getOfferInfo();
		abstract protected function run();

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

	class AvitoParcer extends RealtorParcer
	{
		
		function __construct(
			$hostname = 'm.avito.ru', 
			$slug = array('respublika_krym', 'nedvizhimost'), 
			$params = array('user' => '1'), 
			$protocol = 'https')
		{
			$url = $this->buildUrl($hostname, $slug, $params, $protocol);
			if ($this->isUrlAvaible($url)) {
				$this->url = $url;
			}	
		}

		protected function getOffersList() {
			return null;
		}
		
		protected function getOfferInfo() {
			return null;
		}

		public function run() {
			return null;
		}
	}

	$parcer = new AvitoParcer();
	$parcer->connect();

	$parcer->run();

	$parcer->disconnect();