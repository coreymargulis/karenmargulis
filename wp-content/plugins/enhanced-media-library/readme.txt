=== Enhanced Media Library ===
Contributors: webbistro
Tags: media library, taxonomy, taxonomies, mime, mime type, attachment, media category, media categories, media tag, media tags, media taxonomy, media taxonomies, media filter, media organizer, file types, media types, media uploader, custom, media management, attachment management, files management, ux, user experience, wp-admin, admin, categories, category, filter,  image, images, media, upload
Requires at least: 4.0
Tested up to: 4.2.1
Stable tag: 2.0.4.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html



A better management for WordPress Media Library



== Description ==

This plugin will be handy for those who need to manage a lot of media files.


= Media Taxonomies (Categories for Media Files) =

* create unlimited amount of media taxonomies (like categories and tags),
* be in total control of your custom taxonomy parameters via admin,
* edit and delete your custom media taxonomies,
* assign existed taxonomies to Media Library (for example, you can use post categories as a taxonomy for your media files),
* unassign any media taxonomy from Media Library via admin,
* immediately set categories to any media file during upload in Media Popup,
* filter media files in Media Library / Media Popup by your custom categories,
* get you media category archive page (front-end) working with EML activated


= MIME Types (Media File Types) =

* create new MIME types (media file types, for example, PDFs, Documents, V-Cards, etc),
* delete any MIME type,
* allow/disallow uploading for any file type,
* filter media files by file types in Media Library / Media Popup,
* be in total control of the names of your file type filters


= Compatiblity with Other Plugins =

* Advanced Custom Fields / Advanced Custom Fields PRO
* WooCommerce
* Meta Slider
* Search & Filter

Please let us know if you find any issue with the plugins from the list above or others.


> #### Enhanced Media Library PRO

> * The free version of Enhanced Media Library does NOT support bulk features.
> * The PRO version requires at least WordPress 4.0
> * [Learn more &raquo;](http://wpuxsolutions.com/plugins/enhanced-media-library/)

= Bulk Attachment for Media Taxonomies (PRO only) =

* Set/unset multiple taxonomies to multiple media files at a time:
    * during uploading process
    * in Media Popup during post/page editing
    * in Media Library Grid View
    
= Bulk selection/deletion of media files (PRO only) =

* Select/deselect all media files (within a category) with a single click
* Delete all media files (within a category) right in media popup window


= Useful Links =

* [Where to start? (The complete beginners guide)](http://wpuxsolutions.com/documents/enhanced-media-library/eml-where-to-start/)



== Installation ==

1. Upload plugin folder to '/wp-content/plugins/' directory

2. Activate the plugin through 'Plugins' menu in WordPress admin

3. Adjust plugin's settings on **Media Settings >> Taxonomies** or **Media Settings >> MIME Types**

4. Enjoy Enhanced Media Library!



== Frequently Asked Questions ==

= Why my custom media taxonomy's page is 404? =

Try to just re-save permalinks settings. Go to Settings >> Permalinks and push "Save Changes" button.

= Why Media Popup of some theme/plugin does not show taxonomy filters? =

[**UPD:** Since EML 2.0.4 there is an option 'Force filters' (see Media Settings > Taxonomies) that allows forcing media filters for ANY Media Popup regardless of what was intended by the author of a third-party plugin or theme.]

EML adds its filters to ANY media popup that already contains native WordPress filters. We chose NOT to force adding filters to ANY media popup because there are a lot of cases when filters are not acceptable and theme's/plugin's author did not add them intentionally.

If you believe that a third-party plugin shoud have filters in its Media Popup please contact its author with a request to add NATIVE WordPress filters ([example of the code](http://wordpress.org/support/topic/how-can-we-use-this-plugin-features-in-my-custom-plugin-media-uploader?replies=15#post-5753212) for theme's/plugin's authors).

= How to show images per media category on a webpage =

Right now it is possible via WP_Query ([example of the code](http://wordpress.org/support/topic/php-displaying-an-array-of-images-per-category-or-categories)). We are working on a gallery based on EML taxonomies. 



== Screenshots ==

1. Media Taxonomies Settings

2. MIME types manager

3. Create media categories just like any others

4. Use media categories in Nav Menu

5. Attach media categories to media files like to posts

6. Attach media categories to media files from Media Library grid view

7. Attach media categories to media files in Media Popup while editing posts and pages. Filter media files by categories and file types

8. Filter media files by categories and file types in Media Library list view

9. Filter media files by categories and file types in Media Library grid view



== Changelog ==

= 2.0.4.6 =
*Release Date - April 29, 2015*

= Bugfixes =
* wp_dropdown_categories bug fixed [Support Request](https://wordpress.org/support/topic/wp_dropdown_categories-broken-with-2045)
* Few minor bug fixes


&nbsp;
= 2.0.4.5 =
*Release Date - April 24, 2015*

= Compatibility =
* Wordpress 4.2 compatibility ensured

= Bugfixes =
* Blank (empty) media library screen fixed for List and Grid views


&nbsp;
= 2.0.4.3 =
*Release Date - March 21, 2015*

= Bugfixes =
* List View: Incorrect filtration for post tags fixed
* Media > Add New: Incorrect media files selection (multiple uploading) fixed (PRO only)
* Edit details for all media file types fixed (PRO only) [Support Request](https://wordpress.org/support/topic/edit-video-detail-missing)
* Other minor bug fixes


&nbsp;
= 2.0.4.2 =
*Release Date - March 09, 2015*

= Bugfixes =
* The bug with "Fatal error: Call to undefined function get_userdata() in /wp-includes/user.php on line 360" fixed [Support Request](https://wordpress.org/support/topic/error-message-after-updating-3)
* The bug with incorrect filtering in Media Library List View fixed (when clicking directly on a media category name)


&nbsp;
= 2.0.4.1 =
*Release Date - March 07, 2015*

= Bugfixes =
* Plugin's options incorrect setting during update fixed. If your front-end media taxonomy archive pages were broken by the recent two updates, please re-save your Settings > Permalinks options
* The bug with "Fatal error: Call to undefined function ..." for Media Library List View fixed


&nbsp;
= 2.0.4 =
*Release Date - March 05, 2015*

= New =
* Filters by 'All Uncategorized', 'All Media Categories', 'Not in Media Category' added to both List and Grid views and to Media Popup
* New Media Taxonomy option: 'Edit in Media Popup' - Allows to show/hide taxonomy checkboxes in Media Popup per taxonomy.
* New Media Taxonomy option: 'Taxonomy archive pages' - Turn on/off taxonomy archive pages on the front-end
* New Media Taxonomy option: 'Assign all like hierarchical' - Allows editing of non-hierarchical taxonomies like hierarchical (checkbox list) in Grid View / Media Popup
* New Media Taxonomy option: 'Force filters' - Shows media filters for ANY Media Popup. May be useful for those who need to force filters for third-party plugins or themes

= Improvements =
* Admin Menu: All Media Settings including native 'Settings > Media' are now under common 'Media Settings' admin menu
* Bulk Edit options are now on 'Media Settings > Taxonomies' admin page
* CSS: Media taxonomies column extended for Media Popup, and few more minor CSS improvements
* Taxonomy Saving: Saving changes on the fly displays changes more correctly now (PRO only)
* Taxonomy Saving: Improved media taxonomies saving mechanism (better compatibility with other plugins, etc.)
* Numerous minor code and performance improvements

= Bugfixes =
* Taxonomy Saving: Fixed a big issue with incorrect bulk saving for media taxonomies (wrong clean-up for non-heirarchical taxonomies, PRO only)
* Taxonomy slug: Bug with the incorrect saving of a taxonomy slug fixed [Support Request](https://wordpress.org/support/topic/not-work-slug)
* Meta Slider plugin compatibility ensured [Support Request](https://wordpress.org/support/topic/filter-not-appearing-when-adding-slides-to-meta-slider)
* Localization: small localization bug fixed (PRO only)
* CSS: Admin notices layout fixed for the Grid View (PRO only)
* CSS: Incorrect checkbox styling for mobile devices fixed
* CSS: Few more minor CSS fixes


&nbsp;
= 2.0.3 =
*Release Date - December 19, 2014*

= Improvements (PRO only) =
* Bulk Edit added to the List View of Media Library
* Bulk Edit Media Popup (for Media Library > List View and Media Library > Add New page) is now being opened with pre-selected multiple media files

= Compatibility =
* Wordpress 4.1 compatibility ensured
* Improved compatibility with other plugins and custom taxonomies

= Bugfixes =
* "PHP Fatal Error: Call to undefined function get_current_screen()" issue fixed
* Minor bugs fixed


&nbsp;
= 2.0.2.3 (PRO only) =
*Release Date - November 27, 2014*

= Bugfixes =
* ACF: Fixed the bug with ACF < 5.0 compatibility


&nbsp;
= 2.0.2.2 =
*Release Date - November 23, 2014*

= Bugfixes =
* Minor JS bug of v2.0 fixed [Support Request](https://wordpress.org/support/topic/upload-hangs-2)


&nbsp;
= 2.0.2.1 =
*Release Date - November 20, 2014*

= Bugfixes =
* Minor JS bug of v2.0.2 fixed


&nbsp;
= 2.0.2 =
*Release Date - November 19, 2014*

= Improvements =
* Taxonomy Settings: you can now rewrite taxonomy slug and permalinks front base

= Bugfixes =
* PRO: fixed a bug preventing repeat saving categories with "Save Changes" button for same set of media files


&nbsp;
= 2.0.1 =
*Release Date - November 16, 2014*

= Bugfixes =
* Front-end: scripts conflict fixed, update if EML breaks your front-end features



&nbsp;
= 2.0 =
*Release Date - November 15, 2014*

= New =
* [PRO vesrion](http://wpuxsolutions.com/plugins/enhanced-media-library/) with long-awaited bulk edit feature is finally released!

= Improvements =
* Media Popup: Filters reset automatically as soon as new media files upload process started
* Media Popup: Selection resets automatically as soon as filter is changed
* Media Popup: WordPress 4.0 date filter added  
* Compatibility: general compatibility with other plugins improved, please [let me know](http://wpuxsolutions.com/support/create-new-ticket/) if you have any issue with EML and other plugins

= Bugfixes =
* Media Popup: No delay or glitches anymore when checking media taxonomy checkboxes [Support Request](https://wordpress.org/support/topic/any-way-to-bulk-edit-images/page/2#post-6051963)
* Media Popup: Fixed the bug with non-hierarchical taxonomies (accidentally, only in 1.1.2)
* Media Popup: Filters added to custom posts media popup
* Media Trash: Fixed the incorrect work with MEDIA_TRASH (WordPress 4.0)
* Advanced Custom Fields: Fixed the bug with ACF compatibility [Support Request](https://wordpress.org/support/topic/acf-file-field-conflict-with-eml) and some other minor bugs



&nbsp;
= 1.1.2 =

= Improvements =
* Wordpress 4.0 compatibility ensured


&nbsp;
= 1.1.1 =

= Improvements =
* Filters added for Appearance -> Header and Appearance -> Background [Support Request](https://wordpress.org/support/topic/missing-category-filter-on-media-select-window)

= Bugfixes =
* Fixed EML 1.1 bug with disappearing widgets on Appearance -> Customize [Support Request](http://wordpress.org/support/topic/customize-missing-widgets)
* Fixed EML 1.1 bug with disappearing scrollbar [Support Request](http://wordpress.org/support/topic/scroll-bar-disappeared-in-media-window)


&nbsp;
= 1.1 =

= Improvements =
* Filters added to /wp-admin/customize.php page [Support Request](https://wordpress.org/support/topic/missing-category-filter-on-media-select-window)
* Reconsidered the mechanism of checkboxes' checking in Media Uploader for more stable operation [Support Request](https://wordpress.org/support/topic/instability-in-the-media-insertion-panel)
* Media Uploader filters now work without page refreshing when you change category for you images

= Bugfixes =
* Fixed "Uploads not showing" issue [Support Request](http://wordpress.org/support/topic/uploads-not-showing)
* Reconsidered CSS for filters area [Support Request](http://wordpress.org/support/topic/missing-search-box)
* Fixed CSS and JS files wrong path definitions [Support Request](http://wordpress.org/support/topic/little-bug-2)


&nbsp;
= 1.0.5 =

= Bugfixes =
* Fixed disappearing filter in Media Uploader [Support Request](https://wordpress.org/support/topic/any-chance-of-adding-a-drop-down-in-the-insert-media-screen)
* Added WP 3.9 compatibility [Support Request](https://wordpress.org/support/topic/great-plugin-but-breaks-the-new-add-media-in-39)


&nbsp;
= 1.0.4 =

= Bugfixes =
* Fixed filter mechanism in Media Library [Support Request](http://wordpress.org/support/topic/filter-in-media-not-working-properly)
* Fixed the bug with saving of assigned post categories and tags in Media Uploader


&nbsp;
= 1.0.3 =

= Improvements =
* Better term sorting in Media Uploader
* Minor code improvements

= Bugfixes =
* Fixed the bug with sorting of post categories and tags assigned to Media Library


&nbsp;
= 1.0.2 =

= Bugfixes =
* Fixed assigned non-media taxonomies archive page [Support Request](http://wordpress.org/support/topic/plugin-woocommerce-products-stopped-displaying)


&nbsp;
= 1.0.1 =

= Bugfixes =
* Media Uploader filter now shows nested terms.
* Media Uploader filter now works correctly with multiple taxonomies.


&nbsp;
= 1.0 =

= New =
* Enhanced Media Library initial release.
