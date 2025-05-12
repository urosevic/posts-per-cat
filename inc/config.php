<?php
/**
 * ReduxFramework Posts per Cat Config File
 */

if ( ! class_exists( 'ReduxFramework' ) ) {
	return;
}

if ( ! class_exists( 'Redux_Framework_Posts_Per_Cat' ) ) {
	class Redux_Framework_Posts_Per_Cat {

		public $args     = array();
		public $sections = array();
		public $redux_framework;

		public function __construct() {

			// Set the default arguments
			$this->set_arguments();

			// Create the sections and fields
			$this->set_sections();

			if ( ! isset( $this->args['opt_name'] ) ) { // No errors please
				return;
			}
			// If Redux is running as a plugin, this will remove the demo notice and links
			add_action( 'redux/plugin/hooks', array( $this, 'remove_demo' ) );

			$this->redux_framework = new ReduxFramework( $this->sections, $this->args );
		}

		/**
		 * Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.
		 */
		public function change_arguments( $args ) {
			$args['dev_mode'] = true;
			return $args;
		}

		/**
		 * Remove the demo link and the notice of integrated demo from the redux-framework plugin
		 */
		public function remove_demo() {
			// Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
			if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
				remove_filter(
					'plugin_row_meta',
					array( ReduxFrameworkPlugin::get_instance(), 'plugin_meta_demo_mode_link' ),
					null,
					2
				);
				// Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
				remove_action(
					'admin_notices',
					array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' )
				);
			}
		} // END function remove_demo()

		public function set_sections() {

			ob_start();
			?>
<p>You can implement <?php echo POSTS_PER_CAT_NAME; ?> to your theme in couple different ways.</p>
<ol>
	<li>
		Insert code below to template file, just in place where you wish to display PPC boxes (but not inside Loop!):
		<pre>&lt;?php do_action('ppc'); ?&gt;</pre>
	</li>
	<li>Insert <a href="widget.php"><?php echo POSTS_PER_CAT_NAME; ?> Widget</a> in preferred Widget Area, and configure it there.</li>
	<li>Insert shortcode <code>[ppc]</code> to your page or widget (avoid posts!), and even modify default settings by shortcode parameters listed in section below.</li>
</ol>
			<?php
			$usage_html = ob_get_contents();
			ob_end_clean();

			ob_start();
			?>
<ul>
	<li><code>columns</code>=2 - Number of columns (1, 2, 3 or 4)</li>
	<li><code>minh</code>=0 - Minimal height of box (in px, set to 0 for auto)</li>

	<li><code>include</code>=category_ID's - Include category (comma separated category ID's)</li>
	<li><code>exclude</code>=category_ID's - Exclude category (comma separated category ID's)</li>
	<li><code>parent</code>=0 - Only top level categories (0 or 1)</li>
	<li><code>order</code>=ID - Order categories by (ID, name or custom)</li>
	<li><code>catonly</code>=0 - Only from displayed category archive (0 or 1)</li>
	<li><code>noctlink</code>=0 - Do not link category name (0 or 1)</li>
	<li><code>more</code>=0 - Standalone link to archives (0 or 1)</li>
	<li><code>moretxt</code>="More from" - Archive link prefix</li>

	<li><code>posts</code>=5 - Number of headlines per category block</li>
	<li><code>porderby</code>=date - Order articles by (ID, author, title, date, modified, comment_count, rand)</li>
	<li><code>porder</code>=DESC - Sort articles (DESC or ASC)</li>
	<li><code>titlelen</code>=34 - Headline length (in characters)</li>
	<li><code>shorten</code>=0 - Shorten headline (0 or 1)</li>
	<li><code>commnum</code>=0 - Display comment number (0 or 1)</li>
	<li><code>nosticky</code>=0 - Hide sticky posts (0 or 1)</li>

	<li><code>excerpts</code>=none - Show excerpt (none, first or all)</li>
	<li><code>content</code>=0 - Use post content as excerpt (0 or 1)</li>
	<li><code>excleng</code>=100 - Excerpt length</li>
	<li><code>thumb</code>=0 - Show thumbnail with excerpt (0 or 1)</li>
	<li><code>tsize</code>=60 - Thumbnail size, set size in px for thumbnail width (height is same)</li>
</ul>

<h4>Example</h4>
<pre>[ppc columns=3 minh=200 include=1,16,4 order=custom posts=10 nosticky=1 excerpts=all excleng=150]</pre>
<p><strong>Explanation:</strong> render three columns per row, minimal height of box is 200px, get 10 posts ffrom categories with ID 1, 16 and 4, order categories as defined in array, exclude sticky posts, show excerpts for all posts andshorten excerpt to 150 characters.</p>

<h4>Template</h4>
<p>Since version 1.4.0 you can use template to display custom formatted output (post line element).</p>
<pre>[ppc tsize=ppc content=1]
%thumbnail%
&lt;strong&gt;&lt;a href="%link%"&gt;%title_short%&lt;/a&gt;&lt;/strong&gt;&lt;br /&gt;
&lt;small class="date-meta"&gt;Published on %datetime%&lt;/small&gt;
&lt;small class="author-meta"&gt;by &lt;a href="%author_posts_url%"&gt;%author_displayname%&lt;/a&gt;&lt;/small&gt;&lt;br /&gt;
%excerpt% &lt;a href="%link%"&gt;[read more]&lt;/a&gt;
[/ppc]</pre>

<p>Supported macros:
<ul>
	<li><code>%title%</code></li>
	<li><code>%title_short%</code></li>
	<li><code>%post_content%</code></li>
	<li><code>%excerpt%</code></li>
	<li><code>%thumbnail%</code></li>
	<li><code>%link%</code></li>
	<li><code>%comments_num%</code></li>
	<li><code>%comments_link%</code></li>
	<li><code>%comments_form_link%</code></li>
	<li><code>%datetime%</code></li>
	<li><code>%date%</code></li>
	<li><code>%time%</code></li>
	<li><code>%author_displayname%</code></li>
	<li><code>%author_firstname%</code></li>
	<li><code>%author_lastname%</code></li>
	<li><code>%author_posts_url%</code></li>
</ul></p>
			<?php
			$shortcode_html = ob_get_contents();

			ob_end_clean();

			// ACTUAL DECLARATION OF SECTIONS

			$this->sections[] = array(
				'title'  => __( 'Boxes', 'ppc' ),
				'icon'   => 'el-icon-th',
				'fields' => array(
					array(
						'id'      => 'columns',
						'type'    => 'radio',
						'title'   => __( 'Columns', 'ppc' ),
						'desc'    => __( 'Number of columns per row.', 'ppc' ),
						'options' => array(
							'1' => 'One column (full width)',
							'2' => 'Two columns',
							'3' => 'Three columns',
							'4' => 'Four columns',
							'5' => 'Five columns',
						),
						'default' => 2,
					),

					array(
						'id'      => 'minh',
						'type'    => 'dimensions',
						'units'   => false,
						'width'   => false,
						'height'  => true,
						'title'   => __( 'Minimal height of box', 'ppc' ),
						'desc'    => __( 'in pixels (leave empty to disable min-height)', 'ppc' ),
						'default' => 0,
					),
				),
			);

			$this->sections[] = array(
				'icon'   => 'el-icon-tags',
				'title'  => __( 'Categories', 'ppc' ),
				'fields' => array(
					array(
						'id'      => 'include',
						'type'    => 'sorter',
						'title'   => __( 'Included categories', 'ppc' ),
						'desc'    => __( 'Categories that will be included by default. Drag them to enabled block and order as you wish for Custom ordering.', 'ppc' ),
						'options' => array(
							'enabled'  => array(
								'placebo' => 'placebo', // REQUIRED!
							),
							'disabled' => au_get_categories(),
						),
					),
					array(
						'id'      => 'exclude',
						'type'    => 'sorter',
						'title'   => __( 'Excluded categories', 'ppc' ),
						'desc'    => __( 'Categories that will be excluded by default', 'ppc' ),
						'options' => array(
							'enabled'  => array(
								'placebo' => 'placebo', // REQUIRED!
							),
							'disabled' => au_get_categories(),
						),
					),

					array(
						'id'      => 'parent',
						'type'    => 'checkbox',
						'title'   => __( 'Only top level categories', 'ppc' ),
						'desc'    => __( 'Show only top level categories (exclude subcategories)', 'ppc' ),
						'default' => 0,
					),

					array(
						'id'      => 'order',
						'type'    => 'radio',
						'title'   => __( 'Order categories by', 'ppc' ),
						'options' => array(
							'ID'     => 'Category ID',
							'name'   => 'Category Name',
							'custom' => 'Custom, as listed in Include category',
						),
						'default' => 'ID',
					),

					array(
						'id'      => 'catonly',
						'type'    => 'checkbox',
						'title'   => __( 'Only from displayed category archives', 'ppc' ),
						'desc'    => __( 'exclude categories different from currently displayed on category archive and ignore first category rules on category archive', 'ppc' ),
						'default' => 0,
					),

					array(
						'id'      => 'noctlink',
						'type'    => 'checkbox',
						'title'   => __( 'Do not link category name', 'ppc' ),
						'desc'    => __( 'leave unchecked to link category title to archive', 'ppc' ),
						'default' => 0,
					),

					array(
						'id'      => 'more',
						'type'    => 'checkbox',
						'title'   => __( 'Standalone link to archives', 'ppc' ),
						'desc'    => __( 'check to print "read more" link bellow list of headlines', 'ppc' ),
						'default' => 0,
					),

					array(
						'id'      => 'moretxt',
						'type'    => 'text',
						'title'   => __( 'Archive link prefix', 'ppc' ),
						'default' => __( 'More from', 'ppc' ),
					),
				),
			);

			$this->sections[] = array(
				'icon'   => 'el-icon-th-list',
				'title'  => __( 'Headlines', 'ppc' ),
				'fields' => array(
					array(
						'id'      => 'posts',
						'type'    => 'spinner',
						'title'   => __( 'Number of headlines', 'ppc' ),
						'default' => 5,
						'min'     => 1,
						'max'     => 50,
						'step'    => 1,
					),
					array(
						'id'      => 'porderby',
						'type'    => 'radio',
						'title'   => __( 'Order articles by', 'ppc' ),
						'options' => array(
							'ID'            => 'ID',
							'author'        => 'Author',
							'title'         => 'Title',
							'date'          => 'Date',
							'modified'      => 'Modification Date',
							'comment-count' => 'Number of comments',
							'rand'          => 'Random',
						),
						'default' => 'date',
					),
					array(
						'id'      => 'porder',
						'type'    => 'radio',
						'title'   => __( 'Sort articles', 'ppc' ),
						'options' => array(
							'DESC' => 'Descending',
							'ASC'  => 'Ascending',
						),
						'default' => 'DESC',
					),

					array(
						'id'      => 'titlelen',
						'type'    => 'text',
						'title'   => __( 'Headline length', 'ppc' ),
						'desc'    => __( 'leave blank for full post title length, optimal 34 characters', 'ppc' ),
						'default' => '',
					),

					array(
						'id'      => 'shorten',
						'type'    => 'checkbox',
						'title'   => __( 'Shorten headline', 'ppc' ),
						'default' => 0,
					),

					array(
						'id'      => 'commnum',
						'type'    => 'checkbox',
						'title'   => __( 'Display comment number', 'ppc' ),
						'default' => 0,
					),
					array(
						'id'      => 'nosticky',
						'type'    => 'checkbox',
						'title'   => __( 'Hide sticky posts', 'ppc' ),
						'default' => 0,
					),
				),
			);

			$this->sections[] = array(
				'icon'   => 'el-icon-edit',
				'title'  => __( 'Content', 'ppc' ),
				'fields' => array(
					array(
						'id'      => 'excerpts',
						'type'    => 'radio',
						'title'   => __( 'Show excerpt', 'ppc' ),
						'options' => array(
							'none'  => 'Don\'t display',
							'first' => 'For first article only',
							'all'   => 'For all articles',
						),
						'default' => 'none',
					),
					array(
						'id'      => 'content',
						'type'    => 'checkbox',
						'title'   => __( 'Use post content as excerpt', 'ppc' ),
						'desc'    => __( 'use post content in stead of post excerpt', 'ppc' ),
						'default' => 0,
					),
					array(
						'id'      => 'excleng',
						'type'    => 'text',
						'title'   => __( 'Excerpt length', 'ppc' ),
						'desc'    => __( 'leave empty for full excerpt length', 'ppc' ),
						'default' => 100,
					),

					array(
						'id'      => 'thumb',
						'type'    => 'checkbox',
						'title'   => __( 'Show thumbnail with excerpt', 'ppc' ),
						'desc'    => __( 'thumbnail is shown only if theme support it, and excerpt is enabled', 'ppc' ),
						'default' => 0,
					),
					array(
						'id'      => 'tsize',
						'type'    => 'text',
						'title'   => __( 'Thumbnail size', 'ppc' ),
						'desc'    => __( 'enter size in pixels for thumbnail width (height is same) or WIDTHxHEIGHT or image size name (thumbnail, small, medium, large, full)', 'ppc' ),
						'default' => 60,
						/*,
						'min'     => 16,
						'max'     => 250,
						'step'    => 1,
						*/
					),
				),
			);

			$this->sections[] = array(
				'icon'   => 'el-icon-brush',
				'title'  => __( 'Styling', 'ppc' ),
				'fields' => array(
					array(
						'id'      => 'ppccss',
						'type'    => 'checkbox',
						'title'   => __( 'Use PPC for styling list?', 'ppc' ),
						'desc'    => __( 'enable this option if you see ugly lists in PPC boxes', 'ppc' ),
						'default' => 0,
					),
				),
			);

			$this->sections[] = array(
				'type' => 'divide',
			);

			$this->sections[] = array(
				'icon'   => 'el-icon-question-sign',
				'title'  => __( 'Usage', 'ppc' ),
				'fields' => array(
					array(
						'id'      => 'implementation',
						'title'   => 'How to implement ' . POSTS_PER_CAT_NAME,
						'type'    => 'raw',
						'content' => $usage_html,
					),
					array(
						'id'       => 'shortcode',
						'title'    => 'How to use shortcode',
						'subtitle' => 'Shortcode parameters with default values',
						'type'     => 'raw',
						'content'  => $shortcode_html,
					),
				),
			);

			$this->sections[] = array(
				'icon'   => 'el-icon-group',
				'title'  => __( 'Support', 'ppc' ),
				'fields' => array(
					array(
						'id'      => 'support',
						'type'    => 'raw',
						'content' => '<p>'
						. sprintf(
							// translators: %s is replaced with link to community forum
							__( 'For all questions, feature request and communication with other users of this plugin, use %s.', 'ppc' ),
							'<a href="https://wordpress.org/support/plugin/posts-per-cat">' . __( 'community forum', 'ppc' ) . '</a>'
						)
						. '<p>'
						. sprintf(
							// translators: %1$s plugin name, %2$s localized string 'review it'
							__( 'If you like %1$s, consider to %2$s', 'ppc' ),
							POSTS_PER_CAT_NAME,
							sprintf(
								'<a href="https://wordpress.org/support/plugin/posts-per-cat/reviews/#new-post" target="_review">%s</a>',
								__( 'review it', 'ppc' )
							)
						)
						. '</p>',
					),
				),
			);
		}

		/**
		 * All the possible arguments for Redux.
		 * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
		 */
		public function set_arguments() {

			$this->args = array(

				// TYPICAL -> Change these values as you need/desire
				'opt_name'           => 'postspercat',       // This is where your data is stored in the database and also becomes your global variable name.
				'display_name'       => POSTS_PER_CAT_NAME,  // Name that appears at the top of your panel
				'display_version'    => POSTS_PER_CAT_VER,   // Version that appears at the top of your panel
				'menu_type'          => 'submenu',           // Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
				'allow_sub_menu'     => true,                // Show the sections below the admin menu item or not
				'menu_title'         => POSTS_PER_CAT_NAME_I18N,
				'page'               => POSTS_PER_CAT_NAME_I18N . ' ' . __( 'Options', 'ppc' ),
				'google_api_key'     => '',                                           // Must be defined to add google fonts to the typography module
				'global_variable'    => '',                                           // Set a different name for your global variable other than the opt_name
				'dev_mode'           => false,                                        // Show the time the page took to load, etc
				'customizer'         => true,                                         // Enable basic customizer support

				// OPTIONAL -> Give you extra features
				'page_priority'      => null,                   // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
				'page_parent'        => 'options-general.php',  // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
				'page_permissions'   => 'manage_options',       // Permissions needed to access the options panel.
				'menu_icon'          => '',                     // Specify a custom URL to an icon
				'last_tab'           => '',                     // Force your panel to always open to a specific tab (by id)
				'page_icon'          => 'icon-settings',        // Icon displayed in the admin panel next to your menu_title
				'page_slug'          => 'posts-per-cat',        // Page slug used to denote the panel
				'save_defaults'      => true,                   // On load save the defaults to DB before user clicks save or not
				'default_show'       => true,                  // If true, shows the default value next to each field that is not the default value.
				'default_mark'       => '',                     // What to print by the field's title if the value shown is default. Suggested: *

				// CAREFUL -> These options are for advanced use only
				'transient_time'     => 60 * MINUTE_IN_SECONDS,
				'output'             => true,                     // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
				'output_tag'         => true,                     // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head

				// FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
				'database'           => '',  // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
				'show_import_export' => false,   // REMOVE
				'system_info'        => false,   // REMOVE
				'help_tabs'          => array(),
				'help_sidebar'       => '',
			);

			// SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
			$this->args['share_icons'][] = array(
				'url'   => 'https://www.facebook.com/urosevic',
				'title' => 'Author on Facebook',
				'icon'  => 'el-icon-facebook',
			);
			$this->args['share_icons'][] = array(
				'url'   => 'https://x.com/urosevic',
				'title' => 'Add me on Twitter',
				'icon'  => 'el-icon-twitter',
			);
			$this->args['share_icons'][] = array(
				'url'   => 'https://rs.linkedin.com/in/aurosevic',
				'title' => 'Find me on LinkedIn',
				'icon'  => 'el-icon-linkedin',
			);
			$this->args['share_icons'][] = array(
				'url'   => 'https://www.youtube.com/AleksandarUrosevic',
				'title' => 'Subscribe to my YouTube',
				'icon'  => 'el-icon-youtube',
			);
			$this->args['share_icons'][] = array(
				'url'   => 'https://wordpress.org/plugins/posts-per-cat/',
				'title' => 'Visit official plugin site',
				'icon'  => 'el-icon-home-alt',
			);

			// Panel Intro text -> before the form
			if ( ! isset( $this->args['global_variable'] ) || false !== $this->args['global_variable'] ) {
				if ( ! empty( $this->args['global_variable'] ) ) {
					$v = $this->args['global_variable'];
				} else {
					$v = str_replace( '-', '_', $this->args['opt_name'] );
				}
				$this->args['intro_text'] = __( '<p>List latest articles from all, top level only or manually defind categories and group them in category boxes organized to one, two, three, four or five columns.</p><p>Here you can set default options that will be used as defaults for new widgets, and for shortcode.</p>', 'ppc' );
			}
		}
	}
	global $redux_config;
	$redux_config = new Redux_Framework_Posts_Per_Cat();
}

/**
 * Custom function for the callback referenced above
 */
// TODO Remove?
if ( ! function_exists( 'redux_my_custom_field' ) ) :
	function redux_my_custom_field( $field, $value ) {
		print_r( $field );
		print_r( $value );
	}
endif;

/**
 * Custom function for the callback validation referenced above
 */
// TODO Remove?
if ( ! function_exists( 'redux_validate_callback_function' ) ) :
	function redux_validate_callback_function( $field, $value, $existing_value ) {
		$error = false;
		$value = 'just testing';
		/*
		do your validation

		if(something) {
				$value = $value;
		} elseif(something else) {
				$error = true;
				$value = $existing_value;
				$field['msg'] = 'your custom error message';
		}
		*/

		$return['value'] = $value;
		if ( true === $error ) {
			$return['error'] = $field;
		}
		return $return;
	}
endif;

/**
 * Custom function for category sorter
 */
function au_get_categories() {
	$categories      = get_categories( 'hide_empty=0&orderby=id&order=asc' );
	$cats['placebo'] = 'placebo';
	foreach ( $categories as $category ) {
		$cats[ '_' . $category->cat_ID ] = $category->name;
	}
	return $cats;
}
