window.onload = function () {
	// The default method for loading the top 10 hot products and its competitors items
	getData("getData");
}

// Calls the argos_api.php file with the parameter method to execute the specific function
function getData(method){
	load_gif("#product-list");
	$(function() {
		$.getJSON("http://localhost/argosproject/lib/php/argos_api.php?method=" + method + "&jsoncallback=?",
			function(data) {
				console.log(data);
				// Replace bad unicode characters that are returned by the API
				var remove = JSON.stringify(data).replace(/\u00c2/g, ''); //convert to JSON string
				var newData = JSON.parse(remove); //convert back to array
				
				// sort data into containers
				var result = sort(newData);
				$("#product-list").hide().html(result).fadeIn('slow');
			}
		);
	});
}

// formats the returned JSON data 
function sort(data){
	var items = data.items;
	var result = "";
	for(var i = 0; i<items.length; i++){
		var item = items[i];
		result = result + displayItem(item, i);
	}
	
	return result;
}

// Builds the HTML for the JSON Data
function displayItem(item, index){
	// var difference = get_difference(item.price, altprice);
	index++;
	var imageRes = setImage(item);
	var deviceLink = setLink(item);
	var argosLink = setArgosLink(item);
	var table = getTable(item, argosLink);
	
	
	var result = "<div class='product' id='product" + index + "'>" +
	"<p class='title'><font color='red'><b>" + index + ". <a href='" + deviceLink + "'>" + item.title + "</a></b></font> </p>" +
	"<div class='info'>" +
		"<div class='image-container'><a href='" + deviceLink + "'><img class='item-image' src='" + imageRes + "'/></a></div>" +
		"<p class='description'>" + item.description + "</p>" +
		"<table>" +	table + "</table>" +
	"</div>" +
	"</div>";
	
	return result;
}

// Calculates the price difference and returns the appropriate HTML code
function get_difference(price1, price2){
	var difference = price2 - price1;
	difference = difference.toFixed(2);
	var result;
		if(difference >= 0){
			result = "<font color='green'>+" + difference + "</font>"; 
		}
		else {
			result = "<font color='red'>" + difference + "</font>"; 
		}
	return result;
}

// Checks if the search found an competitor product
// for the current item
function is_competitor(item){
	if(item.competitor.title === null || item.competitor.title===undefined){
		return false;
	}
	else {
		return true;
	}
}

// Set the image src dependent on the device
function setImage(item){
	if(isDesktop()){
		return item.deal_image_highres;
	}
	else {
		return item.deal_image;
	}
}
function setLink(item){
	if(isDesktop()){
		return item.deal_link;
	}
	else{
		return item.mobile_deal_link;
	}
}

// Checks if the device is a Desktop
function isDesktop(){
	if($(window).width() >= 1224) {
		return true;
	}
	else {
		return false;
	}
}

// Checks if the device is a Mobile
function isMobile(){
	if($(window).width() <= 600){
		return true;
	}
	else{
		return false;
	}
}

// Calculate the Argos Link for each product
function setArgosLink(item){
	var deal_image = item.deal_image;
	var splitSrc = deal_image.split("/");
	var imageName = splitSrc[(splitSrc.length)-1];
	var splitImage = imageName.split(".");
	return splitImage[0];	
}

// Builds the HTML for the table which contains the current items:
// price, temperature, competitor information and price difference
function getTable(item, argosLink){
	if(is_competitor(item)){
		var difference = get_difference(item.price, item.competitor.price);
		var competitorLink = setLink(item.competitor)
		if(isMobile()){
			table = "<thead><tr><th class='cell'>Price</th><th class='cell'>Temperature</th></tr></thead>" +
					"<tbody>" +
						"<tr><td class='cell'>\u00a3" + item.price + "</td><td class='cell'><font color='red'><b>" + item.temperature + "\u00b0</b></font></td></tr>" +
						"<tr><td class='cell'></td><td class='cell'></td></tr>" +
						"<tr><td class='cell'><a href='http://www.hotukdeals.com/visit?m=5&q=" + argosLink + "' class='deal-button'>GET ARGOS DEAL</a></td><td class='cell'></td></tr>" + 
						"<tr><th class='competitor'>Amazon Alt</th><th class='cell'>Price Difference</th></tr>" +
						"<tr><td class='competitor'><a href='" + competitorLink + "'>" + item.competitor.title + "</a></td><td class='cell'>" + difference + "</td></tr>" +
						"<tr><td class='competitor'>\u00a3" + item.competitor.price + "</td><td class='cell'></td></tr>" + 
						"<tr><td class='competitor'><img src='" + item.competitor.image + "'/></td><td class='cell'></td></tr>" +
					"</tbody>";				
		}
		else {
			table = "<thead><tr><th class='cell'>Price</th><th class='cell'>Temperature</th><th class='competitor'>Amazon Alt</th><th class='cell'>Price Difference</th></tr></thead>" +
					"<tbody>" +
						"<tr><td class='cell'>\u00a3" + item.price + "</td><td class='cell'><font color='red'><b>" + item.temperature + "\u00b0</b></font></td><td class='competitor'><a href='" + competitorLink + "'>" + item.competitor.title + "</a></td><td class='cell'>" + difference + "</td></tr>" +
						"<tr><td class='cell'></td><td class='cell'></td><td class='competitor'>\u00a3" + item.competitor.price + "</td><td class='cell'></td></tr>" +
						"<tr><td class='cell'><a href='http://www.hotukdeals.com/visit?m=5&q=" + argosLink + "' class='deal-button'>GET ARGOS DEAL</a></td><td class='cell'></td><td class='competitor'><img src='" + item.competitor.image + "'/></td><td class='cell'></td></tr>" + 
					"</tbody>";
		}
	}
	else {
		table = "<thead><tr><th class='cell2'>Price</th><th class='cell2'>Temperature</th></tr></thead>" + 
				"<tbody>" +
					"<tr><td class='cell2'>\u00a3" + item.price + "</td><td class='cell2'><font color='red'><b>" + item.temperature + "\u00b0</b></font></td></tr>" +
					"<tr><td class='cell2'></td><td class='cell2'></td></tr>" +
					"<tr><td class='cell2'><a href='http://www.hotukdeals.com/visit?m=5&q=" + argosLink + "' class='deal-button'>GET ARGOS DEAL</a></td><td class='cell2'></td></tr>" +
				"</tbody>";		
	}	

	return table;
}

// Loads the loading icon		
function load_gif(div) {
	$(div).html("<img class='load-gif' src='lib/images/ajax-loader.gif'/>");
}