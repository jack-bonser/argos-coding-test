<?php
ini_set('display_errors', 1);
//Enter your IDs
define("Access_Key_ID", "AKIAJMAYFLZJT4GKEXZQ");
define("Associate_tag", "arghotdea-21");
// t6Jh/xdPoGbUD6YwWuhjcvFMvxrzk0g/0ZXx5+pI
amazon_api_query();

//Set up the operation in the request
function ItemSearch($SearchIndex, $Keywords){

	//Set the values for some of the parameters
	$Operation = "ItemSearch";
	$Version = "2013-08-01";
	$ResponseGroup = "ItemAttributes,Offers";

	//Define the request
	$url = "GET\necs.amazonaws.co.uk\n/onca/xml\n" .
		"AWSAccessKeyId=" . Access_Key_ID .
		"&AssociateTag=" . Associate_tag .
		"&ItemPage=1" .
		"&Keywords=" . rawurlencode(ucwords(str_replace("-", " ", $Keywords))) .
		"&Operation=" . $Operation .
		"&ResponseGroup=" . str_replace(",", "%2C", $ResponseGroup) .
		"&SearchIndex=" . str_replace(",", "%2C", "All") .
		"&Service=AWSECommerceService" .
		"&Timestamp=" . rawurlencode(gmdate("Y-m-d\TH:i:s\Z")) .
		"&Version=2013-08-01";
		
	$signature = base64_encode(hash_hmac("sha256", $url, "t6Jh/xdPoGbUD6YwWuhjcvFMvxrzk0g/0ZXx5+pI", True));
		
	$signature = str_replace("%7E", "~", rawurlencode($signature));
	
	$url = str_replace("GET\necs.amazonaws.co.uk\n/onca/xml\n", "http://ecs.amazonaws.co.uk/onca/xml?", $url) . "&Signature=" . $signature;
	//Define the request
		//echo $url;

	//Catch the response in the $response object
	$response = file_get_contents($url);
	//echo $response;
	$json = json_encode(simplexml_load_string($response));
	$json = json_decode($json);
	$json = $json->Items;
	$json = json_encode($json, JSON_PRETTY_PRINT);
	echo $json;
}


function amazon_api_query(){
	// Your AWS Access Key ID, as taken from the AWS Your Account page
	$aws_access_key_id = "AKIAJMAYFLZJT4GKEXZQ";

	// Your AWS Secret Key corresponding to the above ID, as taken from the AWS Your Account page
	$aws_secret_key = "t6Jh/xdPoGbUD6YwWuhjcvFMvxrzk0g/0ZXx5+pI";

	// The region you are interested in
	$endpoint = "webservices.amazon.co.uk";

	$uri = "/onca/xml";

	$Keywords = "Minecraft";
	
	$params = array(
		"Service" => "AWSECommerceService",
		"Operation" => "ItemSearch",
		"AWSAccessKeyId" => "AKIAJMAYFLZJT4GKEXZQ",
		"AssociateTag" => "arghotdea-21",
		"SearchIndex" => "All",
		"ResponseGroup" => "Images,ItemAttributes,Offers",
		"Keywords" => rawurlencode(ucwords(str_replace("-", " ", $Keywords)))
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

		//Catch the response in the $response object
	$response = file_get_contents($request_url);
	//echo $response;
	$json = json_encode(simplexml_load_string($response));
	$json = json_decode($json);
	$json = $json->Items;
	$json = json_encode($json, JSON_PRETTY_PRINT);
	echo $json;
	
	return $request_url;
}
?>