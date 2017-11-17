<meta charset="utf-8">
<?php

	class RealtorParcer
	{
		protected $url = '';

		function __construct($hostname, $slug, $params = array(), $protocol = "https")
		{
			$url = $this->buildUrl($hostname, $slug, $params, $protocol);
			if ($this->isUrlAvaible($url)) {
				$this->url = $url;
				echo $this->url;
			}
		}

		private function buildUrl($hostname, $slug, $params, $protocol = "https") {
			$url = $protocol.'://'.$hostname.'/';

			foreach ($slug as $slug_item) {
				$url .= $slug_item.'/';
			}
			$url = substr($url, 0, -1).'?'.rawurlencode(http_build_query($params));

			return $url;
		} 

		private function isUrlAvaible ($url) {
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
	}

	$parcer = new RealtorParcer(
		'm.avito.ru',
		array(
			'respublika_krym',
			'nedvizhimost'
		),
		array(
			's' 	=> 101, 
			'user' 	=> 1
		)
	);