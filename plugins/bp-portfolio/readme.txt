=== BP Portfolio ===
Contributors: buddyboss
Requires at least: 3.8
Tested up to: 5.0
Stable tag: 1.1.5
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Let BuddyPress members create Portfolios to showcase their photos and artwork.

== Description ==

Allow your BuddyPress members to share photography and artwork with each other, organized into beautiful online portfolios.

Watch our [video tutorial](https://www.youtube.com/watch?v=v-qLRodp4C8) for setup and configuration instructions.

BP Portfolio is built by the experienced developers at BuddyBoss who also offer premium [BuddyPress themes](https://www.buddyboss.com/themes/ "BuddyPress themes from BuddyBoss") and [plugins](https://www.buddyboss.com/plugins/ "BuddyPress plugins from BuddyBoss") to build your social network.

== Installation ==

= From your WordPress dashboard =

1. Make sure BuddyPress is activated.
2. Visit 'Plugins > Add New'
3. Search for 'BP Portfolio'
4. Activate BP Portfolio from your Plugins page.

= From WordPress.org =

1. Make sure BuddyPress is activated.
2. Download BP Portfolio.
3. Upload the 'bp-portfolio.zip' file to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, etc...)
4. Activate BP Portfolio from your Plugins page.

= Configuration =

1. Visit 'BuddyBoss > Portfolio' for options and support.
2. Add Projects from the 'Portfolio > Projects' link in your BuddyPress profile.
3. Edit any Project from the Projects link in admin.

== Screenshots ==

1. **Projects Page** - View projects from all BuddyPress members on a single page, as styled with our Social Portfolio theme.
2. **Add Project** - BuddyPress members can create new portfolio projects from their profiles, as styled with our Social Portfolio theme.

== Changelog ==

= 1.1.5 =
* Fix - Fatal error on plugin update


= 1.1.4 =
* Updated Readme

= 1.1.3 =
* New - Added support for GDPR
* New - Added support for Gutenberg

= 1.1.2 =
* Fix - Stop upload progress bar when image upload fail due to inadequate size
* Fix - Editors can no longer see any post types
* Fix - First Project sub navigation item not displaying on the member profile
* Fix - Can not delete project image

= 1.1.1 =
* Fix - Upload fail silently without showing error prompt when user upload large files
* Fix - Conflict with Jetpack Social Sharing module
* Fix - Media is still stored in the media library after deleting project
* Fix - Cant delete the project

= 1.1.0 =
* Fix - Deleted Projects/WIP update still appear on wall
* Fix - WIP Revision deletion
* Fix - User can access /add-project/ and /add-wip/ even though settings are disabled
* Fix - Privacy fails in WIP revision
* Fix - Activity visibility fix
* Fix - MU site fix
* Fix - RTL Fixes
* Fix - Tag selection on Add Projects steps
* Fix - rtMedia video upload issue by BP Portfolio plugins 
* Fix - wrong link in a WIP
* Fix - Warning- when BP is disabled
* Fix - Activity update didnt show up for the subscriber user role activity
* Fix - 500 error on single WIP when Friends component is disable
* Fix - PHP Fatal error: Uncaught Error: [] operator not supported for strings 


= 1.0.4 =
* Fix - Fixed error when Friends component is activated

= 1.0.3 =
* Fix - BuddyPress network enabled issue
* Fix - Global pages content
* Fix - Single WIP access
* Fix - Members visibilty
* Fix - WIP access to friends
* Fix - WIP and Collections listing page loop
* Fix - Privacy issue for collection
* Fix - Shortcodes
* Fix - Replace select script
* Fix - Minify doesn't work for chosen.js
* Fix - Menu position
* Fix - Flush rewrite rules added
* Fix - PHP Notices
* Tweak - New uploader method
* Tweak - Add more media formats
* Tweak - Add Metaboxes
* Tweak - Private project access to non-logged in users
* Tweak - Added a message to empty photo library
* Localization - French translations added, credits to Jean-Pierre Michaud

= 1.0.2 =
* Fix - category not required, if no categories created yet
* Fix - category and tags update when editing project
* Fix - activity images linking to project
* Tweak - size limit for images
* Tweak - Project description is not required

= 1.0.1 =
* Fix - compatibilty with Yoast WordPress SEO plugin
* Fix - Firefox CSS fix
* Tweak - use Large image size in popup

= 1.0.0 =
* Initial public release
