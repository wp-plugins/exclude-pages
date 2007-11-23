=== Exclude Pages ===
Contributors: simonwheatley
Donate link: http://www.simonwheatley.co.uk/wordpress-plugins/
Tags: get_pages, navigation, menu, exclude pages, hide pages
Requires at least: 2.2.3
Tested up to: 2.3.1
Stable tag: 1.2

This plugin adds a checkbox, “include this page in menus”, uncheck this to exclude pages from the 
page navigation that users see on your site.

== Description ==

This plugin adds a checkbox, “include this page in menus”, uncheck this to exclude pages from the 
page navigation that users see on your site.

Any issues: [contact me](http://www.simonwheatley.co.uk/contact-me/).

== Change Log ==

= v1.2 2007/11/21 =

* Enhancement: Child pages of an excluded page are now also hidden. There is also a warning message in the edit screen for any child page with a hidden ancestor, informing the person editing that the page is effectively hidden; a link is provided to edit the ancestor affecting the child page.

= v1.1 2007/11/10 =

* Fix: Pages not created manually using "Write Page" were always excluded from the navigation, meaning the admin has to edit the page to manually include them. Pages created by other plugins are not always included in the navigation, if you want to exclude them (a less common scenario) you have to edit them and uncheck the box. ([Reported by Nudnik](http://wordpress.org/support/topic/140017 "Wordpress forum topic"))

== Description ==

This plugin adds a checkbox, “include this page in menus”, which is checked by default. If you uncheck 
it, the page will not appear in any listings of pages (which includes, and is *usually* limited to, your 
page navigation menus).

Pages which are children of excluded pages also do not show up in menu listings. (An alert in the editing screen, 
underneath the "include" checkbox allows you to track down which ancestor page is affecting child pages 
in this way.)

== Installation ==

1. Upload `exclude_pages.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create or edit a page, and enjoy the frisson of excitement as you exclude it from the navigation

== Screenshots ==

1. Showing the control on the editing screen to exclude a page from the navigation
2. Showing the control and warning for a page which is the child of an excluded page
