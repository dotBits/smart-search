=== Smart Search Engine ===
Contributors: Contesio
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3BZF85K9HJ8QJ
Tags: search, archives
Requires at least: 3.5.1
Tested up to: 3.9
Stable tag: 0.9.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Smart Search replaces the native Wordpress search engine using the Microsoft Bing Search API.
Results are displayed according to your current theme.

== Description ==
Providing a search engine is a very complex subject if you aim to handle topics like relevance, semantics, performances and so on.

Smart Search enhance the Wordpress search engine by querying Microsoft Bing and its Search API. <br>
The search query is performed in the context of your website url, as you type in the http://bing.com search input like so:

*site:http://www.yourblog.com find this or that*
= Brief =
URLs from the BING result set are converted by Smart Search to Wordpress post IDs, thus the display process is the same your theme implements for regular searches.<br>
You can also choose to cache search engine's results for a defined period of time, which will save your Transaction amount for the same search query.

In order to use it, you must have a valid [Windows Account key][1] and you will need to activate at least the [free subscription here][3] or [here][2]

Easy, simple and fast!
If this is your way and fits your needs, you could consider [Smart Search Pro][4] and its extended features.

**Note:** *your blog content must be indexed by BING in order to see the matching result set.* [Go here][6] to submit yuor site to Bing
To get in touch with me you can [drop me an email][5]

= Features = 
* Works with built-in post types. Custom post types are supported as well
* Highlight occurences in search results with custom background and text color
* For post title and post excerpt, whether to display the Wordpress or the BING one
* Define how long to store search results saving BING transaction amount for the same query
* Custom endpoint for empty search results
* Use a custom domain as the search context, useful to test local dumps

[1]: https://datamarket.azure.com/account/keys "Create Windows Account key"
[2]: https://datamarket.azure.com/dataset/bing/search "Activate your Bing Search API plan"
[3]: https://datamarket.azure.com/dataset/8818F55E-2FE5-4CE3-A617-0B8BA8419F65 "Bing Search API | Web Results only plan"
[4]: http://www.contesio.com/wordpress-plugins/smart-search-pro "Go for Smart Search Pro"
[5]: mailto:cristian@contesio.com "Contact Smart Search developer"
[6]: http://www.bing.com/toolbox/submit-site-url "Start indexing your site with Bing"

== Installation ==

Once you have the Windows Account key and the basic plan active:

1. Upload `smart-search` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Under the Plugins menu click on "Smart Search Engine" or visit http://yourblog.com/wp-admin/plugins.php?page=smart-search
1. Fill required settings:
	* Insert the Windows Account Key you've obtained from the link in the "Description" section
1. Adjust optional settings:
	* Increase or decrease the cache lifetime to store search results and save your transaction amount
	* You can set a different domain to be used as the search context, eg: "another.domain.com" will be used to search like so: "site:another.domain.com the search query"
	* You can also customize the endpoint url which will be used when search results are empty
	* Customize displayed text by choosing between WordPress or BING data
	* Customize displayed text by typing your css colors
1. Be sure that your blog is already indexed by http://bing.com typing site:http://yourblog.com in the search input
1. You're now able to search through your posts:
	* by filling your theme's search input box
 	* if your theme doesn't have a search form use `<?php get_search_form(); ?>`, it will display the content of searchform.php in your Theme or the WP built-in form if this file is missing
 	* or simply by URL: _http://yourblog.com?s=sentence+to+search+for_


== Frequently Asked Questions ==

= Why should I install this search goodness? =

* You don't have to setup and mantain an indexing engine since Microsoft Bing crawls your website for you
* Keep in mind that a large percentage of users, as the first act will scroll the page looking for a search input box - [Steve Krug]
* Is not always easy to provide a lean navigation structure, especially when you have a deep content structure. <br>
An efficient search and find approach leads to more page views.
* Sooner or later, users will think "Let me find it" or "Let me see if is here". Allow them to do it well!
* User experience is more pleasant if you can easily find what you need
* A website with a good search engine gains reliability

= Why Bing and not something else? =

* Bing it's free up to 5.000 transactions per month (or search queries)
* No credit card required
* Other plans over this threshold are competitive too
* Bing search results always meet, like many others
* This is the most competitive one I've been able to find out
* However, Smart Search is flexible enough for developers who wants to use a different search provider

== Screenshots ==

1. Settings Page

== Changelog ==

= 0.9.1 =
* Highligth occurrences
* Use curl instead of file_get_contents

= 0.9 =
* First release with custom post types support

== Upgrade Notice ==

= 0.9 =
* Join the new Wordpress search engine Era