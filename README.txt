=== Smart Search Lite ===
Contributors: Contesio
Donate link: http://example.com/
Tags: search, archives
Requires at least: 3.5.1
Tested up to: 3.9
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Smart Search replace the native Wordpress search engine using the Microsoft Bing Search API.
Results are displayed according to your current theme.

== Description ==

Providing a real search engine feature within a Wordpress blog is a very complex subject.

Smart Search enhance the Wordpress search engine by querying Microsoft Bing using its Search API. <br>
The search query is performed within the context of your website url, as you type in the http://bing.com search input like so:

*site:http://www.yourblog.com find this or that*

URLs from the BING result set are parsed and converted to Wordpress post IDs, thus the display process is the same your theme implements for regular searches.

You can also choose to cache search engine's results for a defined period of time, which will save your Transaction amount for the same search query.

In order to use it, you must have a valid [Windows Account key][1] and you will need to activate at least the [free subscription here][3] or [here][2]

Easy, simple and fast!

**Note:** *your blog content must be indexed by BING in order to see the matching result set.*

[1]: https://datamarket.azure.com/account/keys "Create Windows Account key"
[2]: https://datamarket.azure.com/dataset/bing/search "Activate your Bing Search API plan"
[3]: https://datamarket.azure.com/dataset/8818F55E-2FE5-4CE3-A617-0B8BA8419F65 "Bing Search API | Web Results only plan"

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

*
*
* User experience is more pleasant
* Websites with a good search engine rocks!

Answer to foo bar dilemma.

= Why Bing and not Google or something else? =

* Bing it's free up to 5.000 transactions per month (or search queries)
* Other plans over this threshold are competitive too
* No credit card required
* Bing search results always meet, like the others
* This is the most competitive one I've been able to find out

= Can I see analytics about performed searches? =


== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* A change since the previous version.
* Another change.

= 0.5 =
* List versions from most recent at top to oldest at bottom.

== Upgrade Notice ==

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 0.5 =
This version fixes a security related bug.  Upgrade immediately.

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`