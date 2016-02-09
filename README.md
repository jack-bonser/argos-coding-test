# argos-coding-test

Argos HotUKDeals Coding Test

The Current Site Allows you to retrieve the top 10 "hottest" Argos products from Hot UK Deals and compares them against competitor products
by using either the Hot UK Deals API or Amazon's Product Advertising API.

In order to use Amazon's API you must sign up for it from here:
https://affiliate-program.amazon.co.uk/gp/advertising/api/detail/main.html

Then you must become an Associate with Amazon, which can be done here:
https://affiliate-program.amazon.co.uk/

Once you have signed up for the Product Advertising API and have joined Amazon's Associates, copy your Access Key ID, Secret Access Key
and Associate Tag into the corresponding variables in "amazon_api.php".

Notes: 
The API allows you to retrive the top Argos deals and sorts them by price difference to there competitors. Depending on what button
the user clicks on the website, the API will either retrieve the competitor product information from HUKD API or from Amazon's Product
Advertsing API.

For the Competitor search I have used the category and tag parameters as the basis, as the title parameter was difficult to retrieve
the appropraite information from it. So the levels of accuracy for the competitor products depends on the information that is 
supplied in the tag elements, which at times provides some inaccurate results.

If I had more time or if I could do this test again, I would use the title element in conjunction with the category and tag elements
to return more accurate results and code my API on the client side to improve performance.
