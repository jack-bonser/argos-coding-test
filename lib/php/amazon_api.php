<?php
ini_set('display_errors', 1);

// Find competitor product per argos product based on catergory and tags elements
// from HUKD API
function get_amazon_competitors($json, $Keywords){
	$json = json_decode($json,true);
	$i = 0;
	
	foreach($json['items'] as $item) { 
		$Price = $item['price'];
						
		$competitor = amazon_api_query($Keywords[$i], $Price);	
		$json['items'][$i] = array_merge($item, $competitor);
		$i++;
	}
	return json_encode($json);
}

function amazon_api_query($Keywords, $Price){
	// Your AWS Access Key ID, as taken from the AWS Your Account page
	$aws_access_key_id = ""; // Your Access Key

	// Your AWS Secret Key corresponding to the above ID, as taken from the AWS Your Account page
	$aws_secret_key = ""; // Your Secret Key
	$associateTag = ""; // Your Associate Tag
	
	// The region you are interested in
	$endpoint = "webservices.amazon.co.uk";

	$uri = "/onca/xml";
	$params = array(
		"Service" => "AWSECommerceService",
		"Operation" => "ItemSearch",
		"AWSAccessKeyId" => $aws_access_key_id,
		"AssociateTag" => $associateTag,
		"SearchIndex" => "All",
		"ResponseGroup" => "Images,ItemAttributes,Offers",
		"Keywords" => ucwords($Keywords)
	);

	// Set current timestamp if not set
	if (!isset($params["Timestamp"])) {
		$params["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z');
	}

	// Sort the parameters by key
	ksort($params);

	$pairs = array();

	foreach ($params as $key => $value) {
		array_push($pairs, rawurlencode($key)."=".rawurlencode($value));
	}

	// Generate the canonical query
	$canonical_query_string = join("&", $pairs);

	// Generate the string to be signed
	$string_to_sign = "GET\n".$endpoint."\n".$uri."\n".$canonical_query_string;

	// Generate the signature required by the Product Advertising API
	$signature = base64_encode(hash_hmac("sha256", $string_to_sign, $aws_secret_key, true));

	// Generate the signed URL
	$request_url = 'http://'.$endpoint.$uri.'?'.$canonical_query_string.'&Signature='.rawurlencode($signature);
	//echo $request_url . "\n\n";
	//Catch the response in the $response object
	$response = file_get_contents($request_url);
	
	// Convert to JSON
	$xml = simplexml_load_string($response);
	$xml = json_encode($xml);
	//echo $xml;	
	$xml = json_decode($xml, true);

	// Create competitor array
	if(($xml['Items']['TotalResults'])!=0){
		if($xml['Items']['TotalResults']==1){
			$item = $xml['Items']['Item'];
		}
		else{
			$item = $xml['Items']['Item'][0];
		}
		$title = $item['ItemAttributes']['Title'];
		if(isset($item['ItemAttributes']['ListPrice']['FormattedPrice'])){
			$price = $item['ItemAttributes']['ListPrice']['FormattedPrice'];
		}
		else {
			$price = $item['OfferSummary']['LowestNewPrice']['FormattedPrice'];
		}
		$image = $item['SmallImage']['URL'];
		$deal_link = $item['DetailPageURL'];

			$c = array('title'=>$title, 
						'price'=>str_replace("£","",$price),
						'image'=>$image,
						'deal_link'=>$deal_link,
						'mobile_deal_link'=>$deal_link);
			$competitor = array('competitor' => $c);
		
		
		return $competitor;
	}
	else{
		$c = array('title'=> null, 'price'=> $Price, 'image'=> null, 'link'=>null, 'mobile_link'=>null);
		$competitor = array('competitor' => $c);
		return $competitor;
	}
}
?>