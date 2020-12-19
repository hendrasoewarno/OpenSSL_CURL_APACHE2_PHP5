<?php
	function request($url, $endpoint, $header, $body) {
		# atur CURL Options
		$ch = curl_init();
		
		$version = curl_version();
		var_dump($version);
		
		curl_setopt_array($ch, array(
		CURLOPT_URL => trim($url . $endpoint), # URL endpoint
				CURLOPT_HTTPHEADER => $header, # HTTP Headers
				CURLOPT_RETURNTRANSFER => 1, # return hasil curl_exec ke variabel, tidak langsung dicetak
				CURLOPT_FOLLOWLOCATION => 1, # atur flag followlocation untuk mengikuti bila ada url redirect di server penerima tetap difollow
				CURLOPT_CONNECTTIMEOUT => 60, # set connection timeout ke 60 detik, untuk mencegah request gantung
				CURLOPT_POST => 1, # set flag request method POST
				CURLOPT_POSTFIELDS => $body # data
		));
		//logger($body);
		# eksekusi CURL request dan tampung hasil responsenya ke variabel $resp
		$resp = curl_exec($ch);
		echo curl_error($ch);
		return $resp;
		//logger(this->resp);
	}	
	
	echo request("https://www.detik.com", "", array(), "");
?>