=== Smart Search Engine ===
Contributors: Contesio
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3BZF85K9HJ8QJ
Tags: search, archives
Requires at least: 3.3.1
Tested up to: 3.9.2
Stable tag: 0.9.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Smart Search replaces the native WordPress search engine by giving relevance to what matters.
Results are displayed according to your current theme.

== Description ==

Bring a REAL search engine into your WordPress website in 5 minutes, for free!

Smart Search enhances the WordPress search engine by querying Microsoft BING and its Search API.
The search query is performed in the context of your website url, just like you would type the following search query on BING:

`site:http://www.yourblog.com find this and that in New York`

= Brief =

URLs from the BING result set are converted by Smart Search to WordPress post IDs, thus the display process is the same your theme implements for regular searches.
Search results come from **Microsoft BING**. This means **semantics and relevance** on board!! 

You can also choose to cache the search engine's results for a defined period of time, which basically means that your site will load a little bit faster if the same search query is used.

In order to use it, you must have a valid [Windows Account key][1] and you will need to activate at least the [free subscription here][3] or [here][2]

Easy, simple and fast!
If this is your way and fits your needs, you could consider [Smart Search Pro][4] and its extended features.

**Note:** *your blog content must be indexed by BING in order to see the matching result set.* [Go here][6] to submit yuor site to BING.
Feel free to leave a [review here][7]. To get in touch with me you can [drop me an email][5].

= Features = 

* Works with built-in post types. Custom post types are supported as well
* Highlight occurences in search results with custom background and text color
* Auto detect misspelling and synonyms words
* For post title and post excerpt, whether to display the WordPress or the BING one
* Define how long to store search results for each query. This cuts down response time and saves transaction amount
* Custom endpoint for empty search results
* Use a custom domain as the search context, useful for testing purposes

[1]: https://datamarket.azure.com/account/keys "Create Windows Account key"
[2]: https://datamarket.azure.com/dataset/bing/search "Activate your Bing Search API plan"
[3]: https://datamarket.azure.com/dataset/8818F55E-2FE5-4CE3-A617-0B8BA8419F65 "BING Search API | Web Results only plan"
[4]: mailto:cristian@contesio.com "Get notified about Smart Search Pro"
[5]: mailto:cristian@contesio.com "Contact Smart Search developer"
[6]: http://www.bing.com/toolbox/submit-site-url "Start indexing your site with BING"
[7]: http://wordpress.org/support/view/plugin-reviews/smart-search "Review this plugin"

== Installation ==

Once you have the Windows Account key and the basic plan active:

1. Upload `smart-search` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Under the Plugins menu click on "Smart Search Engine" or visit http://yourblog.com/wp-admin/plugins.php?page=smart-search
1. Fill in the required settings:
	* Insert the Windows Account Key you've obtained from the link in the "Description" section
1. Adjust optional settings:
	* Increase or decrease the cache lifetime to store search results and save time for the same search query
	* You can set a different domain to be used as the search context, eg: "another.domain.com" will be used to search like so: "site:another.domain.com the search query"
	* You can also customize the endpoint url which will be used when search results are empty
	* Customize displayed text by choosing between WordPress or BING data
	* Customize highlight background and text color by typing Web Colors http://en.wikipedia.org/wiki/Web_colors
1. Be sure that your blog is already indexed by BING typing. Visit http://bing.com and type site:http://yourblog.com in the search input, you should see all indexed pages.
1. You're now able to search through your posts:
	* by filling your theme's search input box
 	* if your theme doesn't have a search form use 
`<?php get_search_form(); ?>`
it will display the content of the searchform.php template in your Theme or the WP built-in form if this file is missing
 	* or simply by URL: _http://yourblog.com?s=sentence+to+search+for_


== Frequently Asked Questions ==

= Why should I install this search goodness? =

* You don't have to setup and mantain an indexing engine since Microsoft BING crawls your website for you
* Keep in mind that a large percentage of users, first will scroll the page looking for a search input box - [Steve Krug]
* It is not always easy to provide a lean navigation structure, especially when you have a deep content structure. An efficient search and find approach leads to more page views.
* Sooner or later, users will think "Let me find it" or "Let me see if is here". Allow them to do it well!
* User experience is more pleasant if you can easily find what you need
* A website with a good search engine gains reliability

= Why BING and not something else? =

* BING is free up to 5,000 search queries per month
* No credit card required
* Other plans over this threshold are competitive too
* BING is the most competitive one I've been able to find for this kind of API
* BING search results are always satisfactory as well as those of Google

On top of that Smart Search is flexible enough for developers who want to use a different search engine provider.

= Troubleshooting =

* **Highlighter is broken in title or is breaking something else**

This issue is generally theme related.
Let me guess, your theme's search.php file has a function call that looks like this:
`<h2 title="<?php the_title() ?>"><?php the_title() ?></h2>`
This happens because *the_title()* **is not supposed** to be used in HTML attributes. *the_title_attribute()* should be used instead.<br>
This is the correct usage:
`<h2 title="<?php the_title_attribute() ?>"><?php the_title() ?></h2>`
Have a look at [this post](https://pippinsplugins.com/use-the_title-and-the_title_attribute-correctly/ "Use the_title() and the_title_attribute() Correctly") to deepen.

* **Results are always empty**
1. Check if your website is indexed by BING: go to http://bing.com and type the following by replacing "mysite.com" with your domain:
`site:mysite.com`
if you see some results you're on the good way, otherwise you have [let BING index you blog](http://www.bing.com/toolbox/submit-site-url).<br>
*Important Note:* Taxonomy pages like *http://mysite.com/category/news*, *http://mysite.com/tags/politics*, *http://mysite.com/author/john* etc. are not included in search results. This is one of the core features of **Smart Search Pro**<br>
2. If you have any other search related plugin active, try to deactivate it in order to avoid unexpected conflicts.

== Screenshots ==
1. You can see the semantic at work: search for something generic in a geographical region. No custom fields or schema.org data here, everything comes from the search engine!
2. Smart Search lean and effortless options page

1. Settings Page

== Changelog ==

= 0.9.4 =
* Admin feature: color pickers to choose highlight colors
* Extended troubleshooting doc section

= 0.9.3.1 =
* Multimatch highlighter fix

= 0.9.3 =
* Extended emphasis themes compatibility
* Removed direct function in add_filter to debug cache hits and miss
* Added troubleshooting section to documentation
* Tested compatibility with Wordpress 3.3.1

= 0.9.2 =
* Bugfix: do not use plugin's features for search queries in admin section
* Bugfix: prevented to apply rendering options to page elements other than search results
* Bugfix: fixed words emphasis recognizer system

= 0.9.1 =
* Highligth occurrences
* Use curl instead of file_get_contents

= 0.9 =
* First release with custom post types support

== Upgrade Notice ==

= 0.9.4 =
* Added color pickers to choose highlight colors

= 0.9.3.1 =
* Fixed a bug that was affecting highlighter on multiple matches with the same word

= 0.9.3 =
* Bugfix and compatibility improvements

= 0.9.2 =
* Bugfix and performance improvements

= 0.9.1 =
* Emphasis feature and compatibility improvements

= 0.9 =
* Join the new WordPress search engine Era