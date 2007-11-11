<?php
/*
Plugin Name: Exclude Pages from Navigation
Plugin URI: http://www.simonwheatley.co.uk/wordpress-plugins/exclude-pages/
Description: Provides a checkbox on the editing page which you can check to exclude pages from the primary navigation. IMPORTANT NOTE: This will remove the pages from any "consumer" side page listings, which may not be limited to your page navigation listings.
Version: 1.1
Author: Simon Wheatley

Copyright 2007 Simon Wheatley

This script is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This script is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

// Full filesystem path to this dir
define('EP_PLUGIN_DIR', dirname(__FILE__));

// Option name for exclusion data
define('EP_OPTION_NAME', 'ep_exclude_pages');
// Separator for the string of IDs stored in the option value
define('EP_OPTION_SEP', ',');

// Take the pages array, and return the pages array without the excluded pages
function ep_exclude_pages( $pages )
{
	$excluded_ids = ep_get_excluded_ids();
	$length = count($pages);
	for ( $i=0; $i<$length; $i++ ) {
		$page = & $pages[$i];
		if ( in_array($page->ID, $excluded_ids ) ) {
			unset( $pages[$i] );
		}
	}
	// Reindex the array, for neatness
	// SWFIXME: Is reindexing the array going to create a memory optimisation problem for large arrays of WP post/page objects?
	$pages = array_values( $pages );
	return $pages;
}

// Is this page currently NOT excluded,
// returns true if NOT excluded (i.e. included)
// returns false is it IS excluded.
// (Tricky this upside down flag business.)
function ep_include_this_page()
{
	global $post_ID;
	// New post? Must be included then.
	if ( ! $post_ID ) return true;
	$excluded_ids = ep_get_excluded_ids();
	// If there's no exclusion array, we can return true
	if ( empty($excluded_ids) ) return true;
	// Check if our page is in the exclusion array
	// The bang (!) reverses the polarity [1] of the boolean
	return ! in_array( $post_ID, $excluded_ids );
	// [1] (of the neutron flow)
}

function ep_get_excluded_ids()
{
	$exclude_ids_str = get_option( EP_OPTION_NAME );
	// No excluded IDs? Return an empty array
	if ( empty($exclude_ids_str) ) return array();
	// Otherwise, explode the separated string into an array, and return that
	return explode( EP_OPTION_SEP, $exclude_ids_str );
}

// This function gets all the exclusions out of the options
// table, updates them, and resaves them in the options table.
// We're avoiding making this a postmeta (custom field) because we
// don't want to have to retrieve meta for every page in order to
// determine if it's to be excluded. Storing all the exclusions in
// one row seems more sensible.
function ep_update_exclusions( $post_ID )
{
	// Bang (!) to reverse the polarity of the boolean, turning include into exclude
	$exclude_this_page = ! (bool) $_POST['ep_include_this_page'];
	// SWTODO: Also check for a hidden var, which confirms that this checkbox was present
	// If hidden var not present, then default to including the page in the nav (i.e. bomb out here rather
	// than add the page ID to the list of IDs to exclude)
	$ctrl_present = (bool) @ $_POST['ep_ctrl_present'];
	if ( ! $ctrl_present ) return;
	
	$excluded_ids = ep_get_excluded_ids();
	// If we need to EXCLUDE the page from the navigation...
	if ( $exclude_this_page ) {
		// Add the post ID to the array of excluded IDs
		array_push( $excluded_ids, $post_ID );
		// De-dupe the array, in case it was there already
		$excluded_ids = array_unique( $excluded_ids );
	}
	// If we need to INCLUDE the page in the navigation...
	if ( ! $exclude_this_page ) {
		// Find the post ID in the array of excluded IDs
		$index = array_search( $post_ID, $excluded_ids );
		// Delete any index found
		if ( $index !== false ) unset( $excluded_ids[$index] );
	}
	$excluded_ids_str = implode( EP_OPTION_SEP, $excluded_ids );
	ep_set_option( EP_OPTION_NAME, $excluded_ids_str, "Comma separated list of post and page IDs to exclude when returning pages from the get_pages function." );
}

// Take an option, delete it if it exists, then add it.
function ep_set_option( $name, $value, $description )
{
	// Delete option	
	delete_option($name);
	// Insert option
	add_option($name, $value, $description);
}

// Add some HTML for the DBX sidebar control into the edit page page
function ep_admin_sidebar()
{
	echo <<<END
		<fieldset id="excludepagediv" class="dbx-box">
		<h3 class="dbx-handle">Navigation</h3>
		<div class="dbx-content">
		<label for="ep_include_this_page" class="selectit">
		<input 
			type="checkbox" 
			name="ep_include_this_page" 
			id="ep_include_this_page" 
END;
		if ( ep_include_this_page() ) echo 'checked="checked"';
		echo <<<END
 />
			Include this page in menus</label>
		<input type="hidden" name="ep_ctrl_present" value="1" />
		</div>
		</fieldset>
END;
}

// Add our ctrl to the list of controls which AREN'T hidden
function ep_hec_show_dbx( $to_show )
{
	array_push( $to_show, 'excludepagediv' );
	return $to_show;
}

// HOOK IT UP TO WORDPRESS

// Add panels into the editing sidebar(s)
add_action('dbx_page_sidebar', 'ep_admin_sidebar');

// Set the exclusion when the post is saved
add_action('save_post', 'ep_update_exclusions');

// Call this function on the get_pages filter
// (get_pages filter appears to only be called on the "consumer" side of WP,
// the admin side must use another function to get the pages. So we're safe to
// remove these pages every time.)
add_filter('get_pages','ep_exclude_pages');

// Call this function on our very own hec_show_dbx filter
// This filter is harmless to add, even if we don't have the 
// Hide Editor Clutter plugin installed as it's using a custom filter
// which won't be called except by the HEC plugin.
// Uncomment to show the control by default
// add_filter('hec_show_dbx','ep_hec_show_dbx');

?>