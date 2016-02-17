<?php
include "amazon_api.php";

// if the function exists, execute it
if(function_exists($_GET['method'])) {
	$_GET['method']();
}

// return the results from Hot UK Deals API
function getData(){
	$json = get_hukd_data();
	$json = get_competitor_products($json);
	
	$json = json_decode($json);
	usort($json->items, 'my_sort');
	$json = json_encode($json);
	
	echo $_GET['jsoncallback'] . '(' . $json . ')';
}

// Get top 10 Argos Deals From UK Deals API and use Amazon's
// Product Advertising API to search for Competitor products
function getDataAmazon(){
	$json = get_hukd_data();
	$keywords = get_keywords($json);
	$json = get_amazon_competitors($json, $keywords);
	
	$json = json_decode($json);
	usort($json->items, 'my_sort');
	$json = json_encode($json);
	
	echo $_GET['jsoncallback'] . '(' . $json . ')';
}

//get top 10 Argos Products
function get_hukd_data(){
	$api_string = "http://api.hotukdeals.com/rest_api/v2/?key=&output=json&merchant=argos&order=hot&results_per_page=10";
	$content = file_get_contents($api_string);

	$json = json_decode($content);
	$json = $json->deals;
	$json = json_encode($json);
	return $json;
}

// Loop through each product and finds the competitors alternative
function get_competitor_products($json){
	$json = json_decode($json,true);
	$i = 0;
	
	foreach($json['items'] as $item) { 
		$query = build_query($item);
		$price = $item['price'];
						
		$competitor = find_product($query, $price);	
		$json['items'][$i] = array_merge($item, $competitor);
		
		$i++;
	}

	return json_encode($json);
}

/* Using Hot UK Deals API Method */

// Use search parameter on the merchant & tag values
// Slightly improved performance
// Still can be inaccurate if the tags have vague values 
function find_product($query, $price){
	// time consuming - easier way?
	$api_string = "http://api.hotukdeals.com/rest_api/v2/?key=&output=json&merchant=amazon-uk&results_per_page=1&page=1&exclude_expired=true&forum=deals" . $query;
	
	// get results
	$content = file_get_contents($api_string);	
	$json = json_decode($content);
	
	// Create competitor array
	if(($json->total_results)!=0){
		$json = $json->deals->items;
		foreach($json as $item) {
			$c = array('title'=>$item->title, 'price'=>$item->price, 'image'=>$item->deal_image, 'deal_link'=>$item->deal_link, 'mobile_deal_link'=>$item->mobile_deal_link);
			$competitor = array('competitor' => $c);
		}
		
		return $competitor;
	}
	else{
		$c = array('title'=> null, 'price'=> $price, 'image'=> null, 'link'=>null, 'mobile_link'=>null);
		$competitor = array('competitor' => $c);
		return $competitor;
	}
}

// build query string for the Hot UK Deals API
function build_query($item){
	$category = "&category=" . $item['category']['url_name'];
	$merchant = $item['merchant']['name'];
	$tag = get_tags($item['tags'],$merchant);
	
	return $category . $tag;
}

// Get the tag values of the current product
function get_tags($tags, $merchant){
	$tagString = "&search=amazon";
	foreach($tags['items'] as $tag) { //foreach element in $arr
		if(!(is_merchant_tag($merchant, $tag['name']))){
			$t = str_replace(" ","-", $tag['name']);			
			$tagString = $tagString . "+" .  $t;
		}
	}
	
	return $tagString;
}

// get keywords for Amazon API query
function get_keywords($json){
	$json = json_decode($json, true);
	$keywords = array();
	
	foreach($json['items'] as $item) { //foreach element in $arr
		$merchant = $item['merchant']['name'];
		$keyword = $item['category']['name'];
		
		foreach($item['tags']['items'] as $tag)
			if(!(is_merchant_tag($merchant, $tag['name']))){
				$keyword = $keyword . "," . $tag['name'];
			}
		//echo $keyword . "\n";
		array_push($keywords, $keyword);
	}
	
	return $keywords;
}

// check that the tag being append to the queryString is not the current products merchant (Argos)
function is_merchant_tag($merchant, $tag){
	if(strtolower($merchant) == strtolower($tag)){
		return true;
	}
	else{
		return false;
	}
}

// Sort the products on price difference
function my_sort($a, $b)
{
	$a = ($a->competitor->price - $a->price);
	$b = ($b->competitor->price - $b->price);
	
    if ($a > $b) {
        return -1;
    } else if ($a < $b) {
        return 1;
    } else {
        return 0;
    }
}

/* Previous find products methods - searched on the tag paramaters instead of the search parameter 
function find_product($query, $price){
	// time consuming - easier way?
	$api_string = "http://api.hotukdeals.com/rest_api/v2/?key=&output=json&merchant=amazon-uk&results_per_page=1&page=1&exclude_expired=true&forum=deals" . $query;
	//echo $api_string . "\n";
	//$milliseconds = round(microtime(true) * 1000);
	$content = file_get_contents($api_string);
	//echo (round(microtime(true) * 1000) - $milliseconds) . " ms JSON\n";
	
	$json = json_decode($content);
	//echo $json->total_results;
	if(($json->total_results)!=0){
		$json = $json->deals->items;
		foreach($json as $item) {
			$c = array('title'=>$item->title, 'price'=>$item->price, 'image'=>$item->deal_image, 'deal_link'=>$item->deal_link, 'mobile_deal_link'=>$item->mobile_deal_link);
			$competitor = array('competitor' => $c);
		}
		
		return $competitor;
	}
	else{
		$c = array('title'=> null, 'price'=> $price, 'image'=> null, 'link'=>null, 'mobile_link'=>null);
		$competitor = array('competitor' => $c);
		return $competitor;
	}
}
*/
?>
