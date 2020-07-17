=== BP Portfolio Pro ===
Contributors: buddyboss
Requires at least: 3.8
Tested up to: 4.9.6
Stable tag: 1.2.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add Works in Progress, Collections, MP3s and video embeds to portfolios.

== Description ==

Allow your BuddyPress members to share photography, artwork, MP3s, and video embeds with each other, organized into online portfolios, works in progress, and collections.

== Installation ==

1. Make sure BuddyPress is activated.
2. Visit 'Plugins > Add New'
3. Click 'Upload Plugin'
4. Upload the file 'bp-portfolio.zip'
5. Upload the file 'bp-portfolio-pro.zip'
6. Activate BP Portfolio and BP Portfolio Pro from your Plugins page.

= Configuration =

1. Visit 'BuddyBoss > Portfolio Pro' for options and support.
2. Add/Edit Projects from the 'Portfolio > Projects' link in your BuddyPress profile.
3. Add/Edit Works in Progress from the 'Portfolio > WIP' link in your BuddyPress profile.
4. Add/Edit Collections from the 'Portfolio > Collections' link in your BuddyPress profile.

== Changelog ==

= 1.2.4 =
* New - Added support for GDPR
* New - Added support for Gutenberg 

= 1.2.3 =
* New - Ability to edit the collection
* New - Ability to edit work in progress
* Tweak - Replace post type name with the post type slug in filer "bpcp_pro_register_post_type_"
* Tweak - Use placeholder image for Project, WIP and Collections widget's thumbnail
* Fix - Check BuddyPres is active to prevent Fatal error before loading main class
* Fix - Do not increase project/wip view count when author view their own post
* Fix - Sortable image float on left edge while dragging
* Fix - Projects, WIP and Collection widgets are missing inside Admin Panel >> Appearance >> Widgets

= 1.2.2 =
* Fix - Collections Follow functionality doesn’t work
* Fix - Media need to be deleted from media library after deleting project
* Fix - Conflict with Jetpack Social Sharing module

= 1.2.1 =
* Enhancement – License Module Update

= 1.2.0 =
* Fix - Deleted Projects/WIP update still appear on wall
* Fix - php notice fix
* Fix - WIP Revision deletion
* Fix - User can access /add-project/ and /add-wip/ even though settings are disabled
* Fix - WIP without image is not appearing in WIP revision list
* Fix - Activity stream visibility fix
* Fix - WIP Comments sync Stopped working
* Fix - RTL support
* Fix - Tag selection on Add Projects steps
* Fix - Social Portfolio is not working if buddypress is network activated
* Fix - Tag selection on Add Projects steps
* Fix - rtMedia video upload issue by BP Portfolio plugins
* Enhancement - New filter added bpcp_pro_register_post_type_$post_type_name
* Fix - wrong link in a WIP 



= 1.0.2 =
* Enhancement – License Module

= 1.0.1 =
* Fix - BuddyPress network activated error
* Fix - Project should be automatically added to the new collection while adding
* Fix - New select, correct translations
* Fix - Global pages content
* Fix - Javascript issues
* Fix - Wrap shortcodes
* Fix - Light-box just for photos
* Fix - Notice on WIP function
* Fix - Notice on No featured image WIP
* Fix - PHP Notices
* Tweak - Added Metaboxes
* Tweak - Added cover media library
* Localization - French translations added, credits to Jean-Pierre Michaud

= 1.0.0 =
* Initial public release
