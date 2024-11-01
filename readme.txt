=== Tagnetic Poetry ===
Contributors: weefselkweekje
Donate link: http://www.roytanck.com/about-my-themes/donations/
Tags: tag cloud, flash, magnetic poetry, categories, widget
Requires at least: 2.3
Tested up to: 2.8
Stable tag: 1.0

Tagnetic Poetry displays your blog's tags and/or categories as draggable (and clickable) magnetic poetry.

== Description ==

Tagnetic Poetry allows you to display your site's tags, categories or both using a Flash movie that turns them into magnetic poetry. It works just like a regular tags cloud, but is more visually exciting. You can either drag to form sentences or arrange them any way you like, or click them.

The sources code for the Flash movie are available from subversion.

== Installation ==

= Installation =
1. Make sure you're running WordPress version 2.3 or better. It won't work with older versions. Really.
1. Download the zip file and extract the contents.
1. Upload the 'tagnetic-poetry' folder to your plugins directory (wp-content/plugins/).
1. Activate the plugin through the 'plugins' page in WP.
1. See 'Options->Tagnetic Poetry' to adjust things like display size, etc...

= In order to actually display the tag cloud, you have three options. =
1. Create a page or post and type [TAGNETICPOETRY] anywhere in the content. This 'tag' will be replaced by the flash movie when viewing the page.
1. Add the following code anywhere in your theme to display the cloud. `<?php tagneticpoetry_insert(); ?>` This can be used to add Tagnetic Poetry to your sidebar, although it may not actually be wide enough in many cases to keep the tags readable.
1. The plugin adds a widget, so you can place it on your sidebar through 'Design'->'Widgets'. The widget uses a separate set of settings, so it's possible to have different background colors, sizes, etc.

If you're using version 1.0 or higher and WordPress 2.5+ you'll also be able to use shortcodes to place the Tag Cloud. This allows you to use different settings for each instance.

Examples

`[tagneticpoetry args="number=5"]` (shows only five tags)
`[tagneticpoetry width="400"]` (displays a 400 pixels wide tag cloud)
`[tagneticpoetry bgcolor="999999"]` (sets the background color to medium grey)

== Frequently Asked Questions ==

= I see a regular tag could and a line of of text about how I need the Flash plugin =
It appears you either do not have Flash Player 9 or better installed, or you've disabled javascript.

= Some of my tags aren't showing =
If you haven't changed this using the options panel, the plugin will attempt to display 45 tags. It'll try very hard to find an empty spot for all of them, but if the movie's dimensions are small it might fail with some tags. You should play with the movie's width and heigh, the tag size and the number of tags until it looks right for your site. I would recommend against using it on sidebars unless your theme has very wide ones.

= Hey, but what about SEO? =
I’m not sure how beneficial tag clouds are when it comes to SEO, but just in case Tagnetic Poetry outputs the regular tag cloud (and/or categories listing) for non-flash users. This means that search engines will see the same links.

= I’d like to change something in the Flash movie, will you release the .fla? =
The source code is included in the 'development release download from wordpress.org/extend.

= My theme/site appears not to like this plugin. It's not displaying correctly. =
If you're having trouble getting Tagnetic Poetry to work. please make sure you're running the latest version, try a different theme, and check for HTML markup errors. If this does not help, please contact me with as much info as you can.

= Some characters are not showing up =
Because of the way Flash handles text, only Latin characters are supported in the current version. This is due to a limitation where in order to be able to animate text fields smoothly the glyphs need to be embedded in the movie. The Flash movie's source code is available for download through Subversion. Doing so will allow you to create a version for your language. There's a text field in the root of the movie that you can use to embed more characters. If you change to another font, you'll need to edit the Tag class as well.

= When I click on tags, nothing happens. =
This is usually caused by a Flash security feature that affects movies served from another domain as the surrounding page. If your blog is http://yourblog.com, but you have http://www.youblog.com listed as the ‘WordPress address’ under Settings -> General this issue can occur. In this case you should adjust this setting to match your blog’s actual URL. If you haven’t already, I recommend you decide on a single URL for your blog and redirect visitors using other options. This will increase your search engine ranking and in the process help solve this issue :).

== Screenshots ==

1. Tagnetic Poetry.
2. The options panel.
3. There's a separate one for the widget.

== Options ==

The options page allows you to change the Flash movie’s dimensions, change the text color as well as the background.

= How wide is your fridge? =
The movie will scale itself to fit inside whatever dimensions you decide to give it. If you make it really small, chances are some tags will not fit and will be left out.

= And how high? =
This sets the height of the display area. Again, if you choose very small dimension, tags will be left out. It probably best to play with these numbers until it looks nice.

= How big should the magnets be? =
To accommodate smaller size, you can choose to make the tags smaller. Below 70% lesser-used tags will become pretty much unreadable.

= Background color =
The hex value for the background color you’d like to use. This options has no effect when 'Use transparent mode' is selected.

= Use transparent mode =
Turn on/off background transparency. Enabling this might cause issues with some (mostly older) browsers. Under Linux, transparency doesn't work in at all due to a known limitation in the Flash player.

= Display =
Choose whether to show tags only, categories only, or both mixed together. Choosing 'both' can result in 'duplicate tags' if you have categories and tags with the same name. These words will appear twice, with one linking to the tag and the other to the category overview.

= wp_tag_cloud parameters = 
This setting allows you to pass parameters to the wp\_tag\_cloud function, which is used to fetch the tag cloud. Use caution with this setting. Everything you enter will be passed to the function. Be sure to read the function’s manual. Please also note that these parameters affect tags only. If you’ve chosen to show categories or both, the category 'tags' will not be affected.


== Version history ==

= Version 1.0 =
* Adds shortcode support.
* Allows for multiple tag clouds on a page.
* Adds xmlpath flashvar support (for setting the path to read XML from).
* Several minor fixed borrowed from WP-Cumulus.

= Version 0.8 =
* Initial release version.
