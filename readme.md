<b>WP Glossary - Encyclopedia / Lexicon / Knowledge Base / Wiki / Dictionary</b>
<hr>
Tags: A-Z, A to Z, alphabets, categories, custom post type, index, glossary, encyclopedia, lexicon, knowledge base, wiki, dictionary, tooptip, linkify, auto link
Requires at least: 4.0

## Description

The "WP Glossary" plugin  helps you to create your own glossary of terms for Encyclopedia / Lexicon / Knowledge Base / Wiki / Dictionary in your website. This plugin works based on a custom post type and so you have a full editor at your disposal. Want to use already existing post type? Worry not, you are still on the correct page. This plugin allows you to choose the existing post type over plugin's default one.

## Installation

1. Upload the `wp_glossary` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. OR simply upload the plugin from WordPress admin section under Plugins >> Add New >> Upload Plugin.
4. Visit the "Glossary Terms" Menu.

## Contribute 
<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore -->
| [<img src="https://avatars1.githubusercontent.com/u/3042584?s=460&v=4" width="100px;"/><br /><sub><b>Willian Ganzert</b></sub>](https://willianganzert.github.io)<br />|
| :---: |
<!-- ALL-CONTRIBUTORS-LIST:END -->


## Changelog 

= 1.0 =
* Launched the initial version of the plugin

= 1.1 =
* Added more options to the shortcode eg: include/exclude specific categories from glossary list
* Added support for taxonomies with existing post types
* Added option to filter terms by categories

= 1.2 =
* Added option for custom colours in glossary list
* Added option for custom font sizes in glossary list

= 1.3 =
* Added linkify module to auto hyper link terms/phrases in your pages/posts
* Added tooltip module to show info box when hover glossary terms

= 1.4 =
* Added option to add multiple tags per glossary term
* Fixed few PHP warnings

= 1.5 =
* Added option to add custom set of filter letters/alphabets from settings panel. This helps you to create filters list for other languages too (eg: Hebrew, Greek )
* Added option to hide "All" filter from list
* Added option to hide "0-9" filter from list

= 1.6 =
* Fixed small case letters issue with filter
* Fixed filter by showing only first letter terms in case if enable the "Hide 'All' Filter?"
* Added option to add multi set of term filters in different rows

= 1.7 =
* Added option to auto hyper link terms/phrases within glossary post type itself as well
* Added option to use custom post title for glossary post term over main one
* Fixed hide "All" option with "category" template

= 1.8 =
* Improved the linkify module to work more accurately
* Optimized the plugin speed
* Fixed auto changing letters case issue for glossary terms
* Fixed double click issue for glossary terms having tooltips
* Added option to enable/disable auto hyper link term tags
* Added option to format the tooltip title
* Fixed "Linkify Limit per Term" option to work properly

= 1.9 =
* Fixed "New Tab" issue from version 1.8 with linkify module
* Fixed linkify impact from glossary page itself

= 2.0 =
* Optimized plugin code and improved plugin speed
* Added option to execute shortcodes within tooltip for glossary term content
* Added a widget for glossary term details page which shows all the page/post links where the glossary term is actually found. A very nice option for internal linking.
* Added option to open glossary term links in new tab on "Glossary Index Page" and "Linkify Terms"
* Added option to disable the links permanently from glossary terms on "Glossary Index Page" and "Linkify Terms"
* Added option to show a "Back to Glossary Index Page" link on glossary term details page
* Added option to show a "Read More" link in tooltip for glossary term
* Added help tooltip with each option on "Plugin Settings" which means no more need to look into plugin documentation from differently
* Added "User Guide" for other documentation
* Added support of "<!--more-->" tag for tooltip excerpt in same way WordPress does
* Added an option in the shortcode to show uncategorized posts/terms under default category with "category" template
* Added an option to redirect visitors to external link while clicks on glossary term rather than going to glossary term details page
* Changed "Plugin Settings" panel to more user friendly interface
* Fixed typo errors
* Fixed terms list to have the same sorting order which filter list has
* Fixed "Category" template to work with other languages too

= 2.1 =
* Updated jquery mixitup library
* Added WordPress filters for args with custom post type and taxonomy
* Added option to change the title for "Glossary" wholeover the website
* Added option to change the label for glossary phrases like All, Read More, Back to Glossary Index Page
* Added "author" support for custom post type "Glossary"
* Added option to disable Glossary Archive in order to have same slug with "Glossary Index Page" and "Glossary Slug"
* Added option to disable animation on Glossary Index Page
* Added option to exclude specific HTML tags from linkified
* Added option to change the colours/styles for tooltip
* Fixed conflicts when having multiple indexes on same page
* Added "Tags" sub menu in "Glossary Terms" main menu
* Sort terms by title on archive pages
* Replaced PHP function strtolower with mb_strtolower
* Fixed few PHP warnings

= 2.2 =
* Updated jquery mixitup library
* Fixed PHP 7.x conflicts
* Fixed BuddyPress conflicts
* Fixed few programming scripts ro work more smoothly
* Added option to limit linkify per term on whole page or per section only ( eg: post content, comments, widgets )
* Added option to disable tooltip only on "Glossary Index Page"
* Fixed conflict with linkify when a large term contains another small term