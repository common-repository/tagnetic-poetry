<?php
/*
	Plugin Name: Tagnetic Poetry
	Plugin URI: http://www.roytanck.com/2008/08/18/wordpress-plugin-tagnetic-poetry/
	Description: Fridge magnets for WordPress
	Version: 1.0
	Author: Roy Tanck & Merel Zwart
	Author URI: http://www.roytanck.com
	
	Copyright 2008, Roy Tanck & Merel Zwart

	This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//initially set the options
function tagpo_install () {
	$newoptions = get_option('tagpo_options');
	$newoptions['width'] = '550';
	$newoptions['height'] = '375';
	$newoptions['tagsize'] = '100';
	$newoptions['bgcolor'] = '990000';
	$newoptions['trans'] = 'false';
	$newoptions['mode'] = 'tags';
	$newoptions['args'] = '';
	add_option('tagpo_options', $newoptions);
	// widget options
	$widgetoptions = get_option('tagpo_widget');
	$newoptions['width'] = '160';
	$newoptions['height'] = '160';
	$newoptions['tagsize'] = '100';
	$newoptions['bgcolor'] = 'ffffff';
	$newoptions['trans'] = 'false';
	$newoptions['mode'] = 'tags';
	$newoptions['args'] = '';
	add_option('tagpo_widget', $newoptions);
}

// add the admin page
function tagpo_add_pages() {
	add_options_page('Tagnetic Poetry', 'Tagnetic Poetry', 8, __FILE__, 'tagpo_options');
}

// replace tag in content with tag cloud
function tagpo_init($content){
	if( strpos($content, '[TAGNETICPOETRY]') === false ){
		return $content;
	} else {
		$code = tagpo_createflashcode(false);
		$content = str_replace( '[TAGNETICPOETRY]', $code, $content );
		return $content;
	}
}

// template function
function tagneticpoetry_insert(){
	echo tagpo_createflashcode(false);
}

// shortcode function
function tagpo_shortcode( $atts=NULL ){
	return tagpo_createflashcode( false, $atts );
}

// piece together the flash code
function tagpo_createflashcode( $widget=false, $atts=NULL ){
	// get the options
	if( $widget == true ){
		$options = get_option('tagpo_widget');
		$soname = "tagpowidget_so";
		$divname = "tagpowidgetcontent";
	} else if( $atts != NULL ){
		$options = shortcode_atts( get_option('tagpo_options'), $atts );
		$soname = "tagposhortcode_so";
		$divname = "tagpocontent";
	} else {
		$options = get_option('tagpo_options');
		$soname = "tagpo_so";
		$divname = "tagpocontent";
	}
	// get the tag cloud...
	if( $options['mode'] != "cats" ){
		ob_start();
		wp_tag_cloud( $options['args'] );
		$tagcloud = urlencode( str_replace( "&nbsp;", " ", ob_get_clean() ) );	
	}
	// get categories
	if( $options['mode'] != "tags" ){
		ob_start();
		wp_list_categories('title_li=&show_count=1&hierarchical=0&style=none');
		$cats = urlencode( ob_get_clean() );
	}
	// get some paths
	if( function_exists('plugins_url') ){ 
		// 2.6 or better
		$movie = plugins_url('tagnetic-poetry/tagcloud.swf');
		$path = plugins_url('tagnetic-poetry/');
	} else {
		// pre 2.6
		$movie = get_bloginfo('wpurl') . "/wp-content/plugins/tagnetic-poetry/tagcloud.swf";
		$path = get_bloginfo('wpurl')."/wp-content/plugins/tagnetic-poetry/";
	}
	// add random seeds to so name and movie url to avoid collisions and force reloading (needed for IE)
	$soname .= rand(0,9999999);
	$movie .= '?r=' . rand(0,9999999);
	$divname .= rand(0,9999999);
	// write flash tag
	$flashtag = '<!-- SWFObject embed by Geoff Stearns geoff@deconcept.com http://blog.deconcept.com/swfobject/ -->';	
	$flashtag .= '<script type="text/javascript" src="'.$path.'swfobject.js"></script>';
	$flashtag .= '<div id="'.$divname.'"><p style="display:none">';
	// alternate content
	if( $options['mode'] != "cats" ){ $flashtag .= urldecode($tagcloud); }
	if( $options['mode'] != "tags" ){ $flashtag .= urldecode($cats); }
	$flashtag .= '</p><p>Tagnetic Poetry by <a href="http://www.roytanck.com">Roy Tanck</a> and <a href="http://zeroblack.org">Merel Zwart</a> requires Flash Player 9 or better.</p></div>';
	$flashtag .= '<script type="text/javascript">';
	$flashtag .= 'var rnumber = Math.floor(Math.random()*9999999);'; // force loading of movie to fix IE weirdness
	$flashtag .= 'var '.$soname.' = new SWFObject("'.$movie.'?r="+rnumber, "tagcloudflash", "'.$options['width'].'", "'.$options['height'].'", "9", "#'.$options['bgcolor'].'");';
	if( $options['trans'] == 'true' ){
		$flashtag .= $soname.'.addParam("wmode", "transparent");';
	}
	$flashtag .= $soname.'.addParam("allowScriptAccess", "always");';
	$flashtag .= $soname.'.addVariable("tagsize", "'.$options['tagsize'].'");';
	$flashtag .= $soname.'.addVariable("mode", "'.$options['mode'].'");';
	// put tags in flashvar
	if( $options['mode'] != "cats" ){
		$flashtag .= $soname.'.addVariable("tagcloud", "'.urlencode('<tags>') . $tagcloud . urlencode('</tags>').'");';
	}
	// put categories in flashvar
	if( $options['mode'] != "tags" ){
		$flashtag .= $soname.'.addVariable("categories", "' . $cats . '");';
	}
	$flashtag .= $soname.'.write("'.$divname.'");';
	$flashtag .= '</script>';
	return $flashtag;
}

// function to display the options page
function tagpo_options(){
	$options = $newoptions = get_option('tagpo_options');
	// if submitted, process results
	if ( $_POST["tagpo_submit"] ) {
		$newoptions['width'] = strip_tags(stripslashes($_POST["width"]));
		$newoptions['height'] = strip_tags(stripslashes($_POST["height"]));
		$newoptions['tagsize'] = strip_tags(stripslashes($_POST["tagsize"]));
		$newoptions['bgcolor'] = strip_tags(stripslashes($_POST["bgcolor"]));
		$newoptions['trans'] = strip_tags(stripslashes($_POST["trans"]));
		$newoptions['mode'] = strip_tags(stripslashes($_POST["mode"]));
		$newoptions['args'] = strip_tags(stripslashes($_POST["args"]));
	}
	// any changes? save!
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('tagpo_options', $options);
	}
	// options form
	echo '<form method="post">';
	echo "<div class=\"wrap\"><h2>Tagnetic Poetry options</h2>";
	echo '<table class="form-table">';
	// width
	echo '<tr valign="top"><th scope="row">How wide is your fridge?</th>';
	echo '<td><input type="text" name="width" value="'.$options['width'].'" size="5"></input><br />Width in pixels (500 or more is recommended)</td></tr>';
	// height
	echo '<tr valign="top"><th scope="row">And how high?</th>';
	echo '<td><input type="text" name="height" value="'.$options['height'].'" size="5"></input><br />Height in pixels</td></tr>';
	// tag size
	echo '<tr valign="top"><th scope="row">How big should the magnets be?</th>';
	echo '<td><input type="text" name="tagsize" value="'.$options['tagsize'].'" size="5"></input><br />Percentage (default is 100)</td></tr>';
	// background color
	echo '<tr valign="top"><th scope="row">Background color</th>';
	echo '<td><input type="text" name="bgcolor" value="'.$options['bgcolor'].'" size="8"></input><br />6 character hex color value</td></tr>';
	// transparent
	echo '<tr valign="top"><th scope="row">Use transparent mode</th>';
	echo '<td><input type="checkbox" name="trans" value="true"';
	if( $options['trans'] == "true" ){ echo ' checked="checked"'; }
	echo '></input> Switches on Flash\'s wmode-transparent setting</td></tr>';
	// end table
	echo '</table>';
	// tags, cats?
	echo '<h3>Output options</h3>';
	echo '<table class="form-table">';
	echo '<tr valign="top"><th scope="row">Display:</th>';
	echo '<td><input type="radio" name="mode" value="tags"';
	if( $options['mode'] == 'tags' ){ echo ' checked="checked" '; }
	echo '></input> Tags<br /><input type="radio" name="mode" value="cats"';
	if( $options['mode'] == 'cats' ){ echo ' checked="checked" '; }
	echo '></input> Categories<br /><input type="radio" name="mode" value="both"';
	if( $options['mode'] == 'both' ){ echo ' checked="checked" '; }
	echo '></input> Both (you may want to consider lowering the number of tags , using the advanced options below)';
	// end table
	echo '</table>';
	// advanced options
	echo '<h3>Advanced options</h3><p>Please leave this setting empty unless you know what you\'re doing.</p>';
	echo '<table class="form-table">';
	// arguments
	echo '<tr valign="top"><th scope="row">wp_tag_cloud parameters</th>';
	echo '<td><input type="text" name="args" value="'.$options['args'].'" size="60"></input><br />Parameter string for wp_tag_cloud (see the <a href="http://codex.wordpress.org/Template_Tags/wp_tag_cloud#Parameters" target="_blank">codex</a> for more details)<br /><br /><strong>Example uses</strong><br />number=20 - limit the number of tags to 20<br />smallest=5&largest=50 - specify custom font sizes<br /><br /><strong>Known issues</strong><ul><li>Currently, the \'units\', \'orderby\' and \'order\' parameters are not supported.</li><li>Setting \'format\' to anything but \'flat\' will cause the plugin to fail.</li></ul></td></tr>';
	// close stuff
	echo '<input type="hidden" name="tagpo_submit" value="true"></input>';
	echo '</table>';
	echo '<p class="submit"><input type="submit" value="Update Options &raquo;"></input></p>';
	echo "</div>";
	echo '</form>';
	
}

//uninstall all options
function tagpo_uninstall () {
	delete_option('tagpo_options');
	delete_option('tagpo_widget');
}


// widget
function widget_init_tagpo_widget() {
	// Check for required functions
	if (!function_exists('register_sidebar_widget'))
		return;

	function tagpo_widget($args){
	    extract($args);
		$options = get_option('tagpo_widget');
		$title = empty($options['title']) ? __('Tag Cloud') : $options['title'];
		?>
	        <?php echo $before_widget; ?>
				<?php echo $before_title . $title . $after_title; ?>
				<?php 
					if( !stristr( $_SERVER['PHP_SELF'], 'widgets.php' ) ){
						echo tagpo_createflashcode(true);
					}
				?>
	        <?php echo $after_widget; ?>
		<?php
	}
	
	function tagpo_widget_control() {
		$options = $newoptions = get_option('tagpo_widget');
		if ( $_POST["tagpo_widget_submit"] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST["tagpo_widget_title"]));
			$newoptions['width'] = strip_tags(stripslashes($_POST["tagpo_widget_width"]));
			$newoptions['tagsize'] = strip_tags(stripslashes($_POST["tagpo_widget_tagsize"]));
			$newoptions['height'] = strip_tags(stripslashes($_POST["tagpo_widget_height"]));
			$newoptions['bgcolor'] = strip_tags(stripslashes($_POST["tagpo_widget_bgcolor"]));
			$newoptions['trans'] = strip_tags(stripslashes($_POST["tagpo_widget_trans"]));
			$newoptions['args'] = strip_tags(stripslashes($_POST["tagpo_widget_args"]));
			$newoptions['mode'] = strip_tags(stripslashes($_POST["tagpo_widget_mode"]));
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('tagpo_widget', $options);
		}
		$title = attribute_escape($options['title']);
		$width = attribute_escape($options['width']);
		$height = attribute_escape($options['height']);
		$tagsize = attribute_escape($options['tagsize']);
		$bgcolor = attribute_escape($options['bgcolor']);
		$trans = attribute_escape($options['trans']);
		$args = attribute_escape($options['args']);
		$mode = attribute_escape($options['mode']);
		?>
			<p><label for="tagpo_widget_title"><?php _e('Title:'); ?> <input class="widefat" id="tagpo_widget_title" name="tagpo_widget_title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p><label for="tagpo_widget_width"><?php _e('Width:'); ?> <input class="widefat" id="tagpo_widget_width" name="tagpo_widget_width" type="text" value="<?php echo $width; ?>" /></label></p>
			<p><label for="tagpo_widget_height"><?php _e('Height:'); ?> <input class="widefat" id="tagpo_widget_height" name="tagpo_widget_height" type="text" value="<?php echo $height; ?>" /></label></p>
			<p><label for="tagpo_widget_tagsize"><?php _e('Tag size:'); ?> <input class="widefat" id="tagpo_widget_tagsize" name="tagpo_widget_tagsize" type="text" value="<?php echo $tagsize; ?>" /></label></p>
			<p><label for="tagpo_widget_bgcolor"><?php _e('Backgroound color:'); ?> <input class="widefat" id="tagpo_widget_bgcolor" name="tagpo_widget_bgcolor" type="text" value="<?php echo $bgcolor; ?>" /></label></p>
			<p><label for="tagpo_widget_trans"><input class="checkbox" id="tagpo_widget_trans" name="tagpo_widget_trans" type="checkbox" value="true" <?php if( $trans == "true" ){ echo ' checked="checked"'; } ?> > Background transparency</label></p>
			<p>
				<input class="radio" id="tagpo_widget_mode" name="tagpo_widget_mode" type="radio" value="tags" <?php if( $mode == "tags" ){ echo ' checked="checked"'; } ?> > Tags<br />
				<input class="radio" id="tagpo_widget_mode" name="tagpo_widget_mode" type="radio" value="cats" <?php if( $mode == "cats" ){ echo ' checked="checked"'; } ?> > Categories<br />
				<input class="radio" id="tagpo_widget_mode" name="tagpo_widget_mode" type="radio" value="both" <?php if( $mode == "both" ){ echo ' checked="checked"'; } ?> > Both
			</p>
			<p><label for="tagpo_widget_args"><?php _e('wp_tag_cloud parameters:'); ?> <input class="widefat" id="tagpo_widget_args" name="tagpo_widget_args" type="text" value="<?php echo $args; ?>" /></label></p>
			<input type="hidden" id="tagpo_widget_submit" name="tagpo_widget_submit" value="1" />
		<?php
	}
	
	register_sidebar_widget( "Tagnetic Poetry", tagpo_widget );
	register_widget_control( "Tagnetic Poetry", "tagpo_widget_control" );
}

// Delay plugin execution until sidebar is loaded
add_action('widgets_init', 'widget_init_tagpo_widget');

// add the actions
add_action('admin_menu', 'tagpo_add_pages');
register_activation_hook( __FILE__, 'tagpo_install' );
register_deactivation_hook( __FILE__, 'tagpo_uninstall' );

if( function_exists('add_shortcode') ){
	add_shortcode('tagneticpoetry', 'tagpo_shortcode');
	add_shortcode('TAGNETICPOETRY', 'tagpo_shortcode');
} else {
	add_filter('the_content','tagpo_init');
}


?>