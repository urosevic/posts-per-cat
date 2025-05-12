<?php
/**
 * Plugin Name: Posts per Cat
 * Plugin URI: http://urosevic.net/wordpress/plugins/posts-per-cat/
 * Description: Group latest posts by selected category and show post titles w/ or w/o excerpt, featured image and comments number in boxes organized to columns. Please note, for global settings you need to have installed and active <strong>Redux Framework Plugin</strong>.
 * Version: 1.5.0
 * Requires PHP: 7.4
 * Author: Aleksandar Urošević
 * Author URI: https://urosevic.net
 * License: GNU GPLv3
 */

/*
	WP Posts per Cat list titles of recent posts in boxes for all single categories
	Copyright (C) 2009-2025 Aleksandar Urošević <urke.kg@gmail.com>

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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Predefined constants
define( 'POSTS_PER_CAT_NAME', 'Posts per Cat' );
define( 'POSTS_PER_CAT_NAME_I18N', __( 'Posts per Cat', 'ppc' ) );
define( 'POSTS_PER_CAT_VER', '1.5.0' );
define( 'POSTS_PER_CAT_URL', plugin_dir_url( __FILE__ ) );

if ( ! class_exists( 'POSTS_PER_CAT' ) ) {

	class POSTS_PER_CAT {

		public function __construct() {

			// Init textdomain for localisation
			add_action( 'init', array( $this, 'load_textdomain' ) );

			// Initialize Plugin Settings Magic
			if ( is_admin() ) {
				add_action( 'init', array( $this, 'settings_init' ), 900 );
			}

			// Load tool functions
			require_once __DIR__ . '/inc/tools.php';

			// Load widget definition
			require_once __DIR__ . '/inc/widget.php';

			// Add 'ppc' action
			add_action( 'ppc', array( $this, 'echo_shortcode' ) );

			// Add 'ppc' shortcode
			add_shortcode( 'ppc', array( $this, 'shortcode' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		} // END public function __construct()

		/**
		 * Load localization textdomain
		 *
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain(
				'ppc',
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages'
			);
		}

		public function settings_init() {
			// Load Redux Framework
			if ( class_exists( 'ReduxFramework' ) ) {
				// Add Settings link on Plugins page if Redux is installed
				add_filter(
					'plugin_action_links_' . plugin_basename( __FILE__ ),
					array( $this, 'add_settings_link' )
				);

				// Load Settings Page configuration
				if ( file_exists( __DIR__ . '/inc/config.php' ) ) {
					require_once __DIR__ . '/inc/config.php';
				}
			} else {
				// Add admin notice for Redux Framework
				add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			}
		} // END public function settings_init()

		public function admin_notice() {
			echo '<div class="error"><p>'
			. sprintf(
				'To configure global <strong>%s</strong> options, you need to install and activate <strong>%s</strong>.',
				POSTS_PER_CAT_NAME,
				'Redux Framework Plugin'
			)
			. '</p></div>';
		} // END public function admin_notice()

		public function add_settings_link( $links ) {
			$settings_link = '<a href="options-general.php?page=posts-per-cat">' . __( 'Settings' ) . '</a>';
			array_unshift( $links, $settings_link );
			return $links;
		} // END public function add_settings_link()

		public function echo_shortcode() {
			echo do_shortcode( '[ppc]' );
		} // END public function echo_shortcode()

		public static function shortcode( $attr, $template = null ) {
			// Get global plugin options
			$options         = get_option( 'postspercat' );
			$include_default = '';
			$exclude_default = '';

			// Deal with placebo category from ReduxFramework
			if ( ! empty( $options ) ) {
				if ( ! empty( $options['include']['enabled'] ) ) {
					$include = $options['include']['enabled'];
					unset( $include['placebo'] );
					$include_default = str_replace( '_', '', implode( ',', array_keys( $include ) ) );
				} // END ! empty($options['include']['enabled'])

				if ( ! empty( $options['exclude']['enabled'] ) ) {
					$exclude = $options['exclude']['enabled'];
					unset( $exclude['placebo'] );
					$exclude_default = str_replace( '_', '', implode( ',', array_keys( $exclude ) ) );
				} // END ! empty($options['exclude']['enabled'])
			} // END ! empty($options)

			// Prepare shortcode attributes
			$defaults = array(
				'posts'    => $options['posts'],
				'porderby' => $options['porderby'],
				'porder'   => $options['porder'],
				'shorten'  => $options['shorten'],
				'titlelen' => $options['titlelen'],
				'parent'   => $options['parent'],
				'excerpts' => $options['excerpts'],
				'content'  => $options['content'],
				'excleng'  => $options['excleng'],
				'order'    => $options['order'],
				'nosticky' => $options['nosticky'],
				'noctlink' => $options['noctlink'],
				'more'     => $options['more'],
				'moretxt'  => $options['moretxt'],
				'include'  => $include_default,
				'exclude'  => $exclude_default,
				'catonly'  => $options['catonly'],
				'commnum'  => $options['commnum'],
				'thumb'    => $options['thumb'],
				'tsize'    => $options['tsize'],
				'columns'  => ! empty( $options['columns'] ) ? $options['columns'] : $options['column'],
				'minh'     => $options['minh']['height'],
			);
			$atts     = shortcode_atts( $defaults, $attr );

			// Define valid values for custom sanizization
			$valid = array(
				'porderby' => array( 'ID', 'author', 'title', 'date', 'modified', 'comment-count', 'rand' ),
				'porder'   => array( 'ASC', 'DESC' ), // post sorting order
				'order'    => array( 'ID', 'name', 'custom' ), // how to order categories
				'excerpts' => array( 'none', 'first', 'all' ),
			);

			// Sanitize shortcode values
			$atts['posts']    = (int) $atts['posts'];
			$atts['porderby'] = in_array( $atts['porderby'], $valid['porderby'], true ) ? $atts['porderby'] : 'date';
			$atts['porder']   = in_array( strtoupper( $atts['porder'] ), $valid['porder'], true ) ? strtoupper( $atts['porder'] ) : 'DESC';
			$atts['shorten']  = (bool) $atts['shorten'];
			$atts['titlelen'] = (int) $atts['titlelen'];
			$atts['parent']   = (bool) $atts['parent'];
			$atts['excerpts'] = in_array( $atts['excerpts'], $valid['excerpts'], true ) ? $atts['excerpts'] : 'none'; //
			$atts['content']  = (bool) $atts['content'];
			$atts['excleng']  = ! empty( $atts['excleng'] ) ? (int) $atts['excleng'] : 500;
			$atts['order']    = in_array( $atts['order'], $valid['order'], true ) ? $atts['order'] : 'ID';
			$atts['nosticky'] = (bool) $atts['nosticky'];
			$atts['noctlink'] = (bool) $atts['noctlink'];
			$atts['more']     = (bool) $atts['more'];
			$atts['moretxt']  = wp_strip_all_tags( $atts['moretxt'], true );
			$atts['include']  = isset( $atts['include'] ) ? preg_replace( '/[^0-9,]/', '', $atts['include'] ) : '';
			$atts['exclude']  = isset( $atts['exclude'] ) ? preg_replace( '/[^0-9,]/', '', $atts['exclude'] ) : '';
			$atts['catonly']  = (bool) $atts['catonly'];
			$atts['commnum']  = (bool) $atts['commnum'];
			$atts['thumb']    = (bool) $atts['thumb'];
			$atts['columns']  = (int) $atts['columns'];
			$atts['minh']     = (int) $atts['minh'];

			// Define thumbnail size
			if ( empty( $atts['tsize'] ) ) {
				$ppc_tsize = array( 60, 60 );
			} elseif ( preg_match_all( '/^([0-9]+)x([0-9]+)$/', $atts['tsize'], $matches ) ) {
				$ppc_tsize = array( $matches[1][0], $matches[2][0] );
			} elseif ( preg_match( '/^([0-9]+)$/', $atts['tsize'] ) ) {
				$ppc_tsize = array( $atts['tsize'], $atts['tsize'] );
			} else {
				$ppc_tsize = preg_replace( '/[^a-zA-Z0-9_-]/', '', $atts['tsize'] );
			}

			// Prepare reusable variables
			switch ( $atts['columns'] ) { // setup number of columns
				case 1:
					$ppc_column = 'one';
					break;
				case 3:
					$ppc_column = 'three';
					break;
				case 4:
					$ppc_column = 'four';
					break;
				case 5:
					$ppc_column = 'five';
					break;
				default:
					$ppc_column = 'two';
			}

			// do we need to display only current category on archive?
			if ( $atts['catonly'] && is_category() && ! is_home() ) {
				$cats = get_categories( 'orderby=' . $atts['order'] . '&include=' . get_query_var( 'cat' ) );
			} else {
				// custom or other category ordering?
				if ( 'custom' === $atts['order'] ) {
					$cats         = array();
					$custom_order = explode( ',', $atts['include'] );
					foreach ( $custom_order as $custom_order_cat_id ) {
						$custom_order_cat_object = get_categories( 'include=' . $custom_order_cat_id );
						$cats[]                  = $custom_order_cat_object[0];
					}
				} else { // by cat_ID or name
					$cats = get_categories( 'orderby=' . $atts['order'] . '&include=' . $atts['include'] . '&exclude=' . $atts['exclude'] );
				}
			}

			// set number of boxes for clear fix
			$boxnum  = 0;
			$ppc_str = '';

			// print PPC body header
			if ( WP_DEBUG ) {
				$ppc_str .= '<!-- start of ' . POSTS_PER_CAT_NAME . ' -->';
			}
			$ppc_str .= "\n" . '<div id="ppc-box" class="' . $ppc_column . '">' . "\n";

			foreach ( $cats as $cat ) { // process all category

				// get only non-empty categories
				if (
					$cat->count > 0
					&& (
						(
							true === (bool) $atts['parent']
							&& 0 === $cat->category_parent
						)
						|| false === (bool) $atts['parent']
						|| ( true === (bool) $atts['catonly'] && is_category() )
					)
				) {
					$cat_link = get_category_link( $cat->cat_ID );
					// link on category title
					$ppc_cattitle = $atts['noctlink'] ? $cat->cat_name : '<a href="' . $cat_link . '">' . $cat->cat_name . '</a>';

					// add more link
					$ppc_moreadd = $atts['more'] ? "\t\t\t" . '<div class="ppc-more"><a href="' . $cat_link . '">' . $atts['moretxt'] . ' ' . __( '&#8220;', 'ppc' ) . $cat->cat_name . __( '&#8221;', 'ppc' ) . '</a></div>' : '';

					// start category box
					// <!-- start of Category Box -->
					$ppc_minh = $atts['minh'] > 0
						? ' style="min-height: ' . $atts['minh'] . 'px!important"'
						: '';
					$ppc_str .= sprintf(
						"\t<div class='ppc-box'>\n\t\t<div class='ppc'%1\$s>\n\t\t\t<h3>%2\$s</h3>\n\t\t\t<ul>",
						$ppc_minh,
						$ppc_cattitle
					);

					// get latest N posts from category $cat
					if ( $atts['nosticky'] ) { // exclude sticky posts
						$posts_arr = get_posts(
							array(
								'post__not_in' => get_option( 'sticky_posts' ),
								'numberposts'  => $atts['posts'],
								'order'        => $atts['porder'],              // DSC
								'orderby'      => $atts['porderby'],            // date
								'category'     => $cat->cat_ID,
							)
						);
					} else { // include sticky posts
						$posts_arr = get_posts( 'numberposts=' . $atts['posts'] . '&order=' . $atts['porder'] . '&orderby=' . $atts['porderby'] . '&category=' . $cat->cat_ID );
					}

					$br = 0; // control number for number of excerpts

					// process all posts from category
					foreach ( $posts_arr as $post ) {

						// Define Post Link
						$link = get_permalink( $post->ID );

						// Define Full Title
						$title_full  = $post->post_title;
						$title_short = $title_full;
						$title_full  = htmlspecialchars( str_replace( '"', '', $title_full ) );

						// Define Short Title
						if ( $atts['titlelen'] && mb_strlen( $post->post_title ) > ( $atts['titlelen'] + 1 ) ) {
							$title_short = mb_substr( $post->post_title, 0, $atts['titlelen'] );
							$title_short = htmlspecialchars( str_replace( '"', '', $title_short ) ) . '&hellip;';
						}

						// Define Date
						$date = get_the_date( get_option( 'date_format' ), $post->ID );
						// Define Time
						$time = get_the_time( get_option( 'time_format' ), $post->ID );
						// Define DateTime
						$datetime = $date . ' ' . $time;

						// Define Comments Number
						$comments_num = get_comments_number( $post->ID );
						// Define Comments Link
						$comments_link = $link . '#comments';
						// Define Comments Form Link
						$comments_form_link = $link . '#respond';

						// Define Post Author
						$author_displayname = get_the_author_meta( 'display_name', $post->post_author );
						$author_firstname   = get_the_author_meta( 'user_firstname', $post->post_author );
						$author_lastname    = get_the_author_meta( 'user_lastname', $post->post_author );
						$author_posts_url   = get_author_posts_url( $post->post_author );

						// Define Post Content
						$post_content = $post->post_content;

						// Define Excerpt
						if ( $atts['content'] ) {
							$excerpt = strip_tags( $post->post_content );
							$excerpt = mb_substr( $excerpt, 0, $atts['excleng'] ) . '&hellip;';
							if ( $atts['excleng'] && mb_strlen( $excerpt ) > ( $atts['excleng'] + 1 ) ) {
								$excerpt = mb_substr( $excerpt, 0, $atts['excleng'] ) . '&hellip;';
							}
						} else {
							if ( $atts['excleng'] && mb_strlen( $post->post_excerpt ) > ( $atts['excleng'] + 1 ) ) {
								$excerpt = mb_substr( $post->post_excerpt, 0, $atts['excleng'] ) . '&hellip;';
							} else {
								$excerpt = $post->post_excerpt;
							}
						}

						// Define Thumbnail
						$thumbnail = '';
						if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post->ID ) ) {
							$thumbnail = wp_get_attachment_image( get_post_thumbnail_id( $post->ID ), $ppc_tsize );
						}

						// start post line
						$ppc_str .= "\n\t\t\t\t<li>";

						// Use automated output format or template?
						if ( empty( $template ) ) {
							// automated output
							$title_text = $atts['shorten'] ? $title_short : $title_full;
							$title_attr = sprintf(
								/* translators: 1: Post title, 2: Post publish date */
								__( 'Article %1$s published at %2$s', 'ppc' ),
								$title_full,
								date_i18n( 'F j, Y g:i a', strtotime( $post->post_date ) )
							);

							$ppc_str .= sprintf(
								'<a href="%1$s" class="ppc-post-title" title="%2$s">%3$s</a>',
								esc_url( get_permalink( $post->ID ) ),
								esc_attr( $title_attr ),
								esc_html( $title_text )
							);

							// Now maybe append comment number
							if ( $atts['commnum'] ) {
								$comments_link = get_permalink( $post->ID ) . ( 0 === $comments_num ? '#respond' : '#comments' );

								$title_attr = sprintf(
									0 === $comments_num
										/* translators: %s: Post title */
										? __( 'Be first to comment %s', 'ppc' )
										/* translators: %s: Post title */
										: __( 'Read comments on %s', 'ppc' ),
									$title_full
								);

								$ppc_str .= sprintf(
									' <span class="ppc-comments-num">(<a href="%s" title="%s">%d</a>)</span>',
									esc_url( $comments_link ),
									esc_attr( $title_attr ),
									(int) $comments_num
								);
							}

							$ppc_excerpt  = '';
							$show_excerpt = (
								( 0 === $br && 'first' === $atts['excerpts'] )
								|| 'all' === $atts['excerpts']
							);
							++$br; // increment once

							if ( $show_excerpt ) {
								if ( ! empty( $atts['thumb'] ) && function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post->ID ) ) {
									$ppc_excerpt .= wp_get_attachment_image( get_post_thumbnail_id( $post->ID ), $ppc_tsize );
								}
								$ppc_excerpt .= $excerpt;
							}
							if ( ! empty( $ppc_excerpt ) ) {
								$ppc_str .= '<p>' . $ppc_excerpt . '</p>';
							}
						} else {
							// tempalte output
							$template_str = $template;
							$template_str = str_replace( '%title%', $title_full, $template_str );
							$template_str = str_replace( '%title_short%', $title_short, $template_str );
							$template_str = str_replace( '%post_content%', $post_content, $template_str );
							$template_str = str_replace( '%excerpt%', $excerpt, $template_str );
							$template_str = str_replace( '%thumbnail%', $thumbnail, $template_str );
							$template_str = str_replace( '%link%', $link, $template_str );
							$template_str = str_replace( '%comments_num%', $comments_num, $template_str );
							$template_str = str_replace( '%comments_link%', $comments_link, $template_str );
							$template_str = str_replace( '%comments_form_link%', $comments_form_link, $template_str );
							$template_str = str_replace( '%datetime%', $datetime, $template_str );
							$template_str = str_replace( '%date%', $date, $template_str );
							$template_str = str_replace( '%time%', $time, $template_str );
							$template_str = str_replace( '%author_displayname%', $author_displayname, $template_str );
							$template_str = str_replace( '%author_firstname%', $author_firstname, $template_str );
							$template_str = str_replace( '%author_lastname%', $author_lastname, $template_str );
							$template_str = str_replace( '%author_posts_url%', $author_posts_url, $template_str );
							$ppc_str     .= $template_str;
							unset( $template_str );
						}

						$ppc_str .= '</li>';
					} // end of processing every post from category $cat

					// close category box
					$ppc_str .= "\n\t\t\t</ul>\n{$ppc_moreadd}\n\t\t</div>\n\t</div>\n";
					// <!-- end of Category Box -->
				} // end of processing non-empty categories
			} // end foreach $cats as $cat

			// close PPC container
			$ppc_str .= "\n" . '</div>';
			if ( WP_DEBUG ) {
				$ppc_str .= "\n" . '<!-- end of ' . POSTS_PER_CAT_NAME . ' -->';
			}

			return $ppc_str;
		} // posts_per_cat()

		// inject PPC CSS in page head
		public function enqueue_scripts() {
			wp_enqueue_style(
				'ppc-main',
				POSTS_PER_CAT_URL . 'assets/css/ppc.min.css',
				array(),
				POSTS_PER_CAT_VER
			);
			$options = get_option( 'postspercat' );
			if ( $options['ppccss'] ) {
				wp_enqueue_style(
					'ppc-list',
					POSTS_PER_CAT_URL . 'assets/css/ppc-list.min.css',
					array(),
					POSTS_PER_CAT_VER
				);
			}
			$tsize = $options['tsize'];
			if ( preg_match_all( '/([0-9]+)x([0-9]+)/', $tsize, $matches ) ) {
				$custom_css = ".ppc .attachment-{$matches[0]}x{$matches[1]} { width: {$matches[0]}px !important; height: {$matches[1]}px !important; }";
			} elseif ( preg_match( '/^([0-9])+$/', $tsize ) ) {
				$custom_css = ".ppc .attachment-{$tsize}x{$tsize} { width: {$tsize}px !important; height: {$tsize}px !important; }";
			}
			if ( ! empty( $custom_css ) ) {
				wp_add_inline_style( 'ppc-main', $custom_css );
			}
		}
	} // end class
} // end class check

new POSTS_PER_CAT();
