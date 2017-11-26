<meta charset="utf-8">
<pre>
<?php
	/*
	$oauth_params = array(
		'client_id' 	=> '6271159',	
		'redirect_uri'	=> 'https://oauth.vk.com/blank.html',
		'scope'			=> implode('&', array(
			'groups', 'wall'
		))
	);

	$url = 'https://oauth.vk.com/authorize?' . http_build_query($oauth_params);
	#echo $url . "\n";

	$access_tokenparams = array(
		'client_id' 	=> $oauth_params['client_id'],
		'client_secret' => 'm4ahT6NGKmu4gtRhTSig',
		'redirect_uri'	=> $oauth_params['redirect_uri'],
		'code'			=> 'cf6b1118be41fb3bff'
	);

	$url = 'https://oauth.vk.com/access_token?' . http_build_query($access_tokenparams);
	#echo $url . "\n";
	*/

	class VKParser {
		private $__access_token = null;
		private $__ch			= null;
		private $__user_id 		= null;
		private $__groups_ids	= array();	

		private function __print_apierror ($error_code, $error_msg) {
			echo '<strong>VK API ERROR </strong>' . $error_code . ': ' . $error_msg . "\n";
		}

		public function query ( $method, $params ) {
			$params['access_token'] = $this->__access_token;
			$url = 'https://api.vk.com/method/' . $method . '?' . http_build_query($params);
			curl_setopt($this->__ch, CURLOPT_URL, $url);
			
			$response = json_decode(curl_exec($this->__ch), true);
			if (isset($response['error'])){
				return array(
					'error_code' 	=> $response['error']['error_code'],
					'error_msg'		=> $response['error']['error_msg']
				);
			} elseif (isset($response['response'])) {
				return $response['response'];
			}
		}

		public function init () {
			$user_id 	= $this->query('users.get', array());
			
			if (isset($user_id['error_code'])) {
				$this->__print_apierror ( $user_id['error_code'], $user_id['error_msg']);
				return false;
			}

			$this->__user_id	= $user_id[0]['uid'];


			$groups = $this->query('groups.get', array(
				'user_id' 		=> $this->__user_id,
				'extended'		=> 1
			));

			if (isset($groups['error_code'])) {
				$this->__print_apierror ( $groups['error_code'], $groups['error_msg']);
				return false;
			}

			$groups_ids = array();
			foreach ($groups as $group) {
				if (isset($group['gid'])) {
					array_push($groups_ids, $group['gid']);
				}
			}

			$this->__groups_ids = $groups_ids;
			return true;
		}

		public function get_posts ($count) {
			$posts = array();
			foreach ($this->__groups_ids as $id) {
				$group_posts = $this->query( 
					'wall.get',
					array(
						'owner_id'	=> '-' . $id,
						'count'		=> $count
					) 
				);
				
				if (isset($group_posts['error_code'])) {
					$this->__print_apierror ( $group_posts['error_code'], $group_posts['error_msg']);
					return false;
				}

				foreach ($group_posts as $post) {
					if (!is_null($post['text']) and ($post['text'] != '') and !isset($post["is_pinned"])) {
						$temp_data 			= array();
						$temp_data['id'] 	= $post['id'];
						$temp_data['to_id']	= $post['to_id'];

						if (isset($post['signer_id'])) {
							$temp_data['signer_id'] = $post['signer_id'];
						} elseif ($post['from_id'] != $post['to_id']) {
							$temp_data['from_id'] = $post['from_id'];
						}

						$temp_data['text'] = $post['text'];

						array_push($posts, $temp_data);
					}
				}
			}

			return $posts;
		} 

		
		function __construct ( $access_token ) {
			$this->__ch = curl_init();
			curl_setopt($this->__ch, CURLOPT_RETURNTRANSFER, true);

			$this->__access_token = $access_token;
		}

		public function __destruct () {
			curl_close($this->__ch);
		}
	}

	$test_parser = new VKParser('84e1bb4bd3d869e50b21081f0974140b13dad302c6a35f4f676e29eed0ce590f6367ae40232fa7330de26');
	$test_parser->init();
	print_r($test_parser->get_posts(3));