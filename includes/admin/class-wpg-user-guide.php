<?php
/**
 * Settings Panel
 *
 * @class WPG_User_Guide
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPG_User_Guide
 */
class WPG_User_Guide {

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
	
		// Add User Guide Menu
		add_action( 'admin_menu', array( $this, 'add_user_guide_menu' ) );		
	}

	/**
	 * Add Admin Sub Menu: User Guide
	 */
	public function add_user_guide_menu() {
		add_submenu_page( 'edit.php?post_type=glossary', __( 'WP Glossary - User Guide', WPG_TEXT_DOMAIN ), __( 'User Guide', WPG_TEXT_DOMAIN ), 'manage_options', 'wpg-user-guide', array( $this, 'add_user_guide' ) );
	}
	
	/**
	 * Add User Guide Menu
	 */
	public function add_user_guide() {
		?><div class="wrap">
			<div id="icon-tools" class="icon32"></div>
			<h2><?php _e( 'User Guide', WPG_TEXT_DOMAIN ); ?></h2>
			
			<div class="wpg-user-guide-wrapper">
				
				<div class="wpg-info-section">
					<h3>Add New Glossary Term</h3>
					<ol>
						<li>Click on <a href="post-new.php?post_type=glossary" target="_blank"><strong>Add New</strong></a> under <strong>Glossary Terms</strong> menu.</li>
						<li>Fill out the required details for term:
							<ul>
								<li>Glossary Term Title</li>
								<li><strong>Full Description</strong> through editor</li>
								<li><strong>Post Title</strong> under <strong>Custom Attributes</strong> section: This option allows you to use custom title all-over the website for current glossary term.</li>
								<li><strong>External URL</strong> under <strong>Custom Attributes</strong> section: This option allows you to use external/custom URL for current glossary term. This option is useful when you want user to redirect on wikipedia or some other dictionary URL for this particular term rather than having complete description on your website.</li>
								<li><strong>Excerpt</strong>: This option is good in case if you are using <strong>Short Description</strong> as <strong>Content Type</strong> for tooltip.</li>
								<li>Featured Image</li>
							</ul>
						</li>
						<li>Once done with these details, click on <strong>Publish</strong> button from the right side area.</li>
					</ol>
				</div>
				
				<div class="wpg-info-section">
					<h3>Edit Glossary Term</h3>
					<ol>
						<li>Click on <a href="edit.php?post_type=glossary" target="_blank"><strong>All Glossary Terms</strong></a> under <strong>Glossary Terms</strong> menu.</li>
						<li>Click <strong>Edit</strong> against glossary term you need to edit.</li>
						<li>Next screen is same of <strong>Add New Glossary Term</strong> screen so refer to <strong>Add New Glossary Term</strong> section for further steps.</li>
						<li>In this case, you will see <strong>Update</strong> button over <strong>Publish</strong> button.</li>
					</ol>
				</div>
				
				<div class="wpg-info-section">
					<h3>Create Glossary Index Page</h3>
					<ol>
						<li>Go to <strong>Pages/Posts</strong> menu under WordPress dashboard.</li>
						<li>Create a new page/post.</li>
						<li>Add the <strong>[wpg_list]</strong> shortcode in page/post content. You can find more about shortcode on the same user guide under <strong>Shortcode</strong> section.</li>
						<li>Note down this page/post ID.</li>
						<li>Visit  <a href="edit.php?post_type=glossary&page=wpg-settings" target="_blank"><strong>Plugin Settings</strong></a> and enter the ID from previous step under <strong>Glossary Page/Post ID</strong> setting.</li>
						<li>Checkout the other settings from <a href="edit.php?post_type=glossary&page=wpg-settings" target="_blank"><strong>Plugin Settings</strong></a> page as per your requirement.</li>
					</ol>
				</div>
				
				<div class="wpg-info-section">
					<h3>Shortcode</h3>
					<ol>
						<li><strong>Basic shortcode:</strong> [wpg_list]
							<p>Use this shortcode anywhere in your page or post and it will start showing glossary terms in the same style you are expecting plugin to do. There are some attributes available with the same shortcode and you can find those in next step. Not all of those attributes are required to add with the shortcode and add only as per your need. If you don't use those attributes, shortcode uses <a href="edit.php?post_type=glossary&page=wpg-settings" target="_blank"><strong>Plugin Settings</strong></a> for the purpose.</p>
						</li>
						
						<li><strong>Full Shortcode:</strong> [wpg_list title="" layout="one_column/two_column/three_column/four_column/five_column" alphabet_set="A,B,C,D,E,..." hide_empty="yes/no" hide_all="yes/no" hide_numeric="yes/no" post_type="glossary" template="alphabet/category" taxonomy="glossary_cat" taxonomy_terms_to_include="optional/1,2,3.." taxonomy_terms_to_exclude="optional/1,2,3.." uncategorized_term_name="" thumbnail=yes]
							<ul>
								<li><strong>title:</strong> This option is for nothing at the time with the plugin and is here just for future references/updates.</li>
								
								<li><strong>layout:</strong> Select the number of columns you want per row on Glossary Index Page. By default, it shows three columns per row but you can change it to any from one to five columns. This option allows you to choose one from following five options:
									<ul>
										<li>one_column</li>
										<li>two_column</li>
										<li>three_column</li>
										<li>four_column</li>
										<li>five_column</li>
									</ul>
								</li>

								<li><strong>alphabet_set:</strong> By default, plugin uses A to Z alphabet set for filter on Glossary Index Page. Suppose you want to: <br /><ul><li>change the order of the alphabets</li><li>show only few alphabets</li><li>show a different set of characters</li><li>show characters list for other language (eg: Hebrew, Greek )</li><li>show multiple rows rather than all the alphabets in single row or all English alphabets in first row and Hebrew in next row</ul>You can use this option for all these. Use comma (,) for multiple alphabets. For multiple rows, simply use new line (ENTER from keyboard). <br /><strong>EG:</strong> <br />A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z <br />А,Б,В,Г,Д,Е,Ж,З,И,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Э,Ю,Я</li>

								<li><strong>hide_empty:</strong> In case, some of the alphabets from filter list, on Glossary Index Page, doesn't have any glossary term/phrase. These alphabets will show as disabled in the list. Selecting this option will remove these disabled alphabets from the list completely. This option allows you to choose one from following two options:
									<ul>
										<li>yes</li>
										<li>no</li>
									</ul>
								</li>

								<li><strong>hide_all:</strong> Plugin, by default, shows <strong>ALL</strong> at the starting of the filter list on Glossary Index Page which shows all the glossary terms/phrases when clicked. For example, you click on alphabet <strong>A</strong> from the filter list, it will come up with all the glossary terms starting with <strong>A</strong>. Now you want to see back the whole list of terms. Clicking <strong>ALL</strong> does this for you. If you want to show it no more in the filter list, you can enable this option. This option allows you to choose one from following two options:
									<ul>
										<li>yes</li>
										<li>no</li>
									</ul>
								</li>

								<li><strong>hide_numeric:</strong> This option works almost in the same way <strong>Hide "All" Filter</strong> works but for the glossary terms starting with number. Plugin shows  <strong>0-9</strong> just after <strong>All</strong> in the filter list on Glossary Index Page and you can remove it by enabling this option. This option allows you to choose one from following two options:
									<ul>
										<li>yes</li>
										<li>no</li>
									</ul>
								</li>

								<li><strong>post_type:</strong> Plugin uses its own custom post type for Glossary Index Page. In case if you want to show WordPress's default post type ( Page, Post ) or any other custom post type you can choose under this option.</li>

								<li><strong>template:</strong> Plugin comes up with two type of glossaries: <strong>Alphabet Glossary</strong> and <strong>Taxonomy/Category Glossary</strong>. Few times, you want to list all the categories with their posts in glossary style and this option does it for you. By default, template uses <strong>alphabet</strong> to works as <strong>Alphabet Glossary</strong> and you can change it to <strong>category</strong> in case you need to show <strong>Taxonomy/Category Glossary</strong>. This option allows you to choose one from following two options:
									<ul>
										<li>alphabet</li>
										<li>category</li>
									</ul>
								</li>

								<li><strong>taxonomy:</strong> This option works better with <strong>template="category"</strong> from previous option. That way you can decide which taxonomy you want to load the posts for.<br />
								<strong>EG:</strong> You want to list all the products with WooCommerce categories, you can set it like <strong>taxonomy="product_cat"</strong>.</li>

								<li><strong>taxonomy_terms_to_include:</strong> By default, Previous option ( <strong>taxonomy</strong> ) loads posts for all the categories. This option allows you to include only specific categories to load the posts for. For this you have to enter comma separated category ID's with this option.</li>

								<li><strong>taxonomy_terms_to_exclude:</strong> By default, Previous option ( <strong>taxonomy</strong> ) loads posts for all the categories. This option allows you to exclude specific categories to load the posts from rest of the categories. For this you have to enter comma separated category ID's with this option.</li>

								<li><strong>uncategorized_term_name:</strong> Its not necessary that all the posts will be categorized properly and few of those can still be without having any category assigned. Using <strong>template="category"</strong> and it shows only those posts which have at-least one category assigned. This option allows you to show uncategorized posts as well under same name category you pass with this option.</li>
								<li><strong>thumbnail:</strong> This option is used to show or not thumbnails for items of the list. This option allows you to choose one from following two options:
									<ul>
										<li>yes</li>
										<li>no</li>
									</ul>
								</li>
							</ul>
						</li>
					</ol>
				</div>

			</div>
		</div><?php
	}
}

new WPG_User_Guide();
