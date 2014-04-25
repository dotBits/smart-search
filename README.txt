=== Smart Search Lite ===
Contributors: Contesio
Donate link: http://example.com/
Tags: search, archives
Requires at least: 3.5.1
Tested up to: 3.9
Stable tag: 0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Smart Search replace the native Wordpress search engine using the Microsoft Bing Search API.
Results are displayed according to your current theme.

== Description ==

Providing a search engine is a very complex subject if you aim to have a good one by showing results in the context of a specific website.

Smart Search enhance the Wordpress search engine by querying Microsoft Bing using its Search API. <br>
The search query is performed in the context of your website url, as you type in the http://bing.com search input like so:

*site:http://www.yourblog.com find this or that*

URLs from the BING result set are parsed and converted to Wordpress post IDs, thus the display process is the same your theme implements for regular searches.<br>
* Works with built-in post types. Custom post types are supported as well

You can also choose to cache search engine's results for a defined period of time, which will save your Transaction amount for the same search query.

In order to use it, you must have a valid [Windows Account key][1] and you will need to activate at least the [free subscription here][3] or [here][2]

Easy, simple and fast!

If this is your way and fits your needs, you could consider [Smart Search Pro][4] and its extended features.

**Note:** *your blog content must be indexed by BING in order to see the matching result set.*

[1]: https://datamarket.azure.com/account/keys "Create Windows Account key"
[2]: https://datamarket.azure.com/dataset/bing/search "Activate your Bing Search API plan"
[3]: https://datamarket.azure.com/dataset/8818F55E-2FE5-4CE3-A617-0B8BA8419F65 "Bing Search API | Web Results only plan"
[4]: http://www.contesio.com/wordpress-plugins/smart-search-pro "Go for Smart Search Pro"

== Installation ==

Once you have the Windows Account key and the basic plan active:

1. Upload `smart-search` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Under the Plugins menu click on BING Search Engine or visit http://yourblog.com/wp-admin/plugins.php?page=smart-search
1. Fill in the only 2 options you see there:
	* Insert the Windows Account Key you've obtained from the link above
	* Increase or decrease the cache duration for search results
1. Be sure that your blog is already indexed by http://bing.com typing site:http://yourblog.com in the search input
1. You're now able to search through your posts:
 	* by using <?php get_search_form(); ?> that will display searchform.php in your Theme or the WP built-in form
 	* or simply by URL: _http://yourblog.com?s=sentence_


== Frequently Asked Questions ==

= Why should I install this search goodness? =

* Keep in mind that a large percentage of users, as the first act will scroll the page looking for a search box
* Is not always easy to provide a lean navigation structure, especially when you have a deep content structure
* Sooner or later, users will think "Let me find it" or "Let me see if is there". Allow them to do it then!
* User experience is more pleasant if you can easily find what you need
* A website with a good search engine gains reliability

= Why Bing and not something else? =

* Bing it's free up to 5.000 transactions per month (or search queries)
* No credit card required
* Other plans over this threshold are competitive too
* Bing search results always meet, like the others
* This is the most competitive one I've been able to find out

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 0.9 =
* First release with custom post types support