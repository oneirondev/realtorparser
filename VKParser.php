<meta charset="utf-8">
<pre>
<?php
	$oauth_params = array(
		'client_id' 	=> '6271159',	
		'redirect_uri'	=> 'https://oauth.vk.com/blank.html',
		'scope'			=> implode('&', array(
			'groups', 'wall'
		))
	);

	$url = 'https://oauth.vk.com/authorize?' . http_build_query($oauth_params);
	# echo $url . "\n";

	$access_tokenparams = array(
		'client_id' 	=> $oauth_params['client_id'],
		'client_secret' => 'm4ahT6NGKmu4gtRhTSig',
		'redirect_uri'	=> $oauth_params['redirect_uri'],
		'code'			=> '6dd7dd9e66557f780d'
	);

	$url = 'https://oauth.vk.com/access_token?' . http_build_query($access_tokenparams);
	# echo $url . "\n";

	//access_token = 4d26fcfb14cefcd688a8c02bd37d0062635903aeb84a44dded5712ffa19a75727f9f1efc62b10798ba223

	/* Получение списка групп */

	$api_query_params = array(
		'user_id' 		=> 447802791,
		'extended'		=> 1,
		'access_token'	=> '4d26fcfb14cefcd688a8c02bd37d0062635903aeb84a44dded5712ffa19a75727f9f1efc62b10798ba223'
	);

	$url = 'https://api.vk.com/method/groups.get?' . http_build_query($api_query_params);
	
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$result = curl_exec($ch);
	curl_close($ch);

	$result = json_decode($result,  true)['response'];
	$groups = array();

	foreach ($result as $group) {
		if (isset($group['gid'])) {
			array_push($groups, $group['gid']);
		}
	}


	$posts = array();
	foreach ($groups as $group_id) {
		$api_query_params = array(
			'owner_id'	=> '-' . $group_id,
			'count'		=> 10,
			'access_token'	=> '4d26fcfb14cefcd688a8c02bd37d0062635903aeb84a44dded5712ffa19a75727f9f1efc62b10798ba223'
		);

		$url = 'https://api.vk.com/method/wall.get?' . http_build_query($api_query_params);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($result,  true)['response'];
		
		foreach ($result as $post) {
				if (!is_null($post['text']) and ($post['text'] != '')) {
					array_push($posts, $post['text']);
				}
			}	
	}

	print_r($posts);