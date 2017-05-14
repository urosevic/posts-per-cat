<?php
/*
    WP Posts per Cat list titles of recent posts in boxes for all single categories
    Copyright (C) 2009-2015 Aleksandar Urošević <urke.kg@gmail.com>

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

Plugin Name: Posts per Cat [Unmaintained]
Plugin URI: http://urosevic.net/wordpress/plugins/posts-per-cat/
Description: <strong>This plugin is unmaintained. If you are interested to acquisition of plugin, please <a href="http://urosevic.net/wordpress/contact/?subject=Posts-per-Cat%20Acquisition" target="_blank">contact us</a>!</strong> Group latest posts by selected category and show post titles w/ or w/o excerpt, featured image and comments number in boxes organized to columns. Please note, for global settings you need to have installed and active <strong>Redux Framework Plugin</strong>.
Version: 1.4.2
Author: Aleksandar Urošević
Author URI: http://urosevic.net
License: GNU GPLv3
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// predefined constants
define( 'POSTS_PER_CAT_NAME', 'Posts per Cat [Unmaintained]' );
define( 'POSTS_PER_CAT_VER', '1.4.2' );
define( 'POSTS_PER_CAT_URL', plugin_dir_url(__FILE__) );

if ( !class_exists('POSTS_PER_CAT') )
{

	class POSTS_PER_CAT
	{

		function __construct()
		{

			// Init textdomain for localisation
			load_plugin_textdomain( 'ppc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

			// Initialize Plugin Settings Magic
			if ( is_admin() ) {
				add_action('init', array($this, 'settings_init'), 900);
			}

			// Load tool functions
			require_once('inc/tools.php');

			// Load widget definition
			require_once('inc/widget.php');

			// Add 'ppc' action
			add_action( 'ppc', array($this, 'echo_shortcode') );

			// Add 'ppc' shortcode
			add_shortcode( 'ppc', array($this,'shortcode') );

			add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );

		} // construct

		function settings_init()
		{

			// Load Redux Framework
			if ( class_exists( "ReduxFramework" ) )
			{
				// Add Settings link on Plugins page if Redux is installed
				add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'add_settings_link') );

				// Load Settings Page configuration
				if ( file_exists(dirname(__FILE__).'/inc/config.php') ){
					require_once( dirname( __FILE__ ) . '/inc/config.php' );
				}
			} else {
				// Add admin notice for Redux Framework
				add_action( 'admin_notices', array($this,'admin_notice') );
			}

		} // settings_init()

		function admin_notice()
		{
			echo '<div class="error"><p>'.sprintf("To configure global <strong>%s</strong> options, you need to install and activate <strong>%s</strong>.",POSTS_PER_CAT_NAME, "Redux Framework Plugin") . '</p></div>';
		} // admin_notice()

		function add_settings_link($links)
		{
			$settings_link = '<a href="options-general.php?page=posts-per-cat">'.__('Settings').'</a>';
			array_unshift( $links, $settings_link );
			return $links;
		} // add_settings_link()

		function echo_shortcode()
		{
			echo do_shortcode('[ppc]');
		} // echo_shortcode()

		public static function shortcode($attr, $template=null) {
			global $blog_url;

			// get global plugin options
			$options		= get_option('postspercat');
			$include_default = $exclude_default = '';

			if ( ! empty($options) ) {

				if ( ! empty($options['include']['enabled']) ) {
					$include = $options['include']['enabled'];
					unset ($include['placebo']);
					$include_default = str_replace("_","",implode(",", array_keys($include)));
				} // END ! empty($options['include']['enabled'])

				if ( ! empty($options['exclude']['enabled']) ) {
					$exclude = $options['exclude']['enabled'];
					unset ($exclude['placebo']);
					$exclude_default = str_replace("_","",implode(",", array_keys($exclude)));
				} // END ! empty($options['exclude']['enabled'])

			} // END ! empty($options)

			// echo "include=$include<br/>exclude=$exclude<br/>";
			extract( shortcode_atts( array(
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
				'columns'  => ( ! empty($options['columns']) ) ? $options['columns'] : $options['column'],
				'minh'     => $options['minh']['height']
			), $attr ) );

			// if exclength has not been set, set 500
			if (empty($excleng)) $excleng = 500;

			// default thumbnail size
			if ( empty($tsize) )
			{
				$ppc_tsize = array(60,60);
			} else if ( preg_match_all('/^([0-9]+)x([0-9]+)$/', $tsize, $matches)) {
				$ppc_tsize = array($matches[1][0],$matches[2][0]);
			} else if ( preg_match('/^([0-9]+)$/', $tsize) ) {
				$ppc_tsize = array($tsize,$tsize);
			} else {
				$ppc_tsize = $tsize;
			}

			switch ( $columns ) { // setup number of columns
				case 1:		$ppc_column = "one"; break;
				case 3:		$ppc_column = "three"; break;
				case 4:		$ppc_column = "four"; break;
				case 5:		$ppc_column = "five"; break;
				default:	$ppc_column = "two";
			}

			// do we need to force minimal height of box?
			$ppc_minh = ( $minh > 0 ) ? 'style="min-height: '.$minh.'px !important;"' : "";

			// do we need to display only current category on archive?
			if ( $catonly && is_category() && !is_home() ) {
				$cats = get_categories('orderby='.$order.'&include='.get_query_var('cat'));
			} else {
				// custom or other category ordering?
				if ( $order == "custom" ) { // custom
					$custom_order = explode(",", $include);
					// print_r($cusom_order);
					foreach ( $custom_order as $custom_order_cat_ID ) {
						$custom_order_cat_object = get_categories('include='.$custom_order_cat_ID);
						$custom_order_cat[] = $custom_order_cat_object[0];
					}
					$cats = $custom_order_cat;
				} else { // by cat_ID or name
					$cats = get_categories('orderby='.$order.'&include='.$include.'&exclude='.$exclude);
				}
			}

			// set number of boxes for clear fix
			$boxnum = 0;
			// print PPC body header
			$ppc_str = '
		<!-- start of '.POSTS_PER_CAT_NAME.' version '.POSTS_PER_CAT_VER.' -->
			<div id="ppc-box">
		';

			foreach ( $cats as $cat ) { // process all category
				// get only non-empty categories
				if ( $cat->count > 0 && ( ($parent == true && $cat->category_parent == 0) || $parent == false || ( $catonly == true && is_category() ) ) ) {

					$cat_link = get_category_link( $cat->cat_ID );
					// link on category title
					$ppc_cattitle = ( $noctlink ) ? $cat->cat_name : '<a href="'.$cat_link.'">'.$cat->cat_name.'</a>';

					// add more link
					$ppc_moreadd = ( $more ) ? '<div class="ppc-more"><a href="'.$cat_link.'">'.$moretxt.' '.__('&#8220;', 'ppc').$cat->cat_name.__('&#8221;', 'ppc').'</a></div>' : '';

					// start category box
					// <!-- start of Category Box -->
					$ppc_str .= '
				<div class="ppc-box '.$ppc_column.'">
					<div class="ppc" '.$ppc_minh.'>
					<h3>'.$ppc_cattitle.'</h3>
					<ul>';
					// get latest N posts from category $cat
					if ( $nosticky ) { // exclude sticky posts
						$posts_arr = get_posts(array(
							'post__not_in' => get_option("sticky_posts"),
							'numberposts' => $posts,
							'order' => $porder, //"DSC",
							'orderby' => $porderby, //"date",
							'category' => $cat->cat_ID
						));
					} else { // include sticky posts
						$posts_arr = get_posts('numberposts='.$posts.'&order='.$porder.'&orderby='.$porderby.'&category='.$cat->cat_ID);
					}

					$br = 0; // control number for number of excerpts

					// process all posts from category
					foreach ( $posts_arr as $post ) {

						// Define Post Link
						$link = get_permalink($post->ID);

						// Define Full Title
						$title_full = $title_short = $post->post_title;
						$title_full = htmlspecialchars (str_replace('"', "", $title_full));

						// Define Short Title
						if ( $titlelen && mb_strlen($post->post_title) > ($titlelen+1) ) {
							$title_short = mb_substr($post->post_title, 0, $titlelen);
							$title_short = htmlspecialchars (str_replace('"', "", $title_short))."&hellip;";
						}

						// Define Date
						$date = get_the_date( get_option( 'date_format' ), $post->ID );
						// Define Time
						$time = get_the_time( get_option( 'time_format' ), $post->ID );
						// Define DateTime
						$datetime = $date.' '.$time;

						// Define Comments Number
						$comments_num = get_comments_number($post->ID);
						// Define Comments Link
						$comments_link = $link."#comments";
						// Define Comments Form Link
						$comments_form_link = $link.'#respond';

						// Define Post Author
						$author_displayname = get_the_author_meta( 'display_name', $post->post_author );
						$author_firstname = get_the_author_meta( 'user_firstname', $post->post_author );
						$author_lastname = get_the_author_meta( 'user_lastname', $post->post_author );
						$author_posts_url = get_author_posts_url( $post->post_author );

						// Define Post Content
						$post_content = $post->post_content;

						// Define Excerpt
						if ( $content ) {
							$excerpt = strip_tags($post->post_content);
							$excerpt = mb_substr($excerpt, 0, $excleng).'&hellip;';
							if ( $excleng && mb_strlen($excerpt) > ($excleng+1) ) {
								$excerpt = mb_substr($excerpt, 0, $excleng).'&hellip;';
							}
						} else {
							if ( $excleng && mb_strlen($post->post_excerpt) > ($excleng+1) ) {
								$excerpt = mb_substr($post->post_excerpt, 0, $excleng).'&hellip;';
							} else {
								$excerpt = $post->post_excerpt;
							}
						}

						// Define Thumbnail
						$thumbnail = "";
						if ( function_exists('has_post_thumbnail') && has_post_thumbnail($post->ID) ) {
							$thumbnail = wp_get_attachment_image( get_post_thumbnail_id($post->ID), $ppc_tsize );
						}

						// start post line
						$ppc_str .= '<li>';

						// Use automated output format or template?
						if ( empty($template) )
						{
							// automated output
							$ppc_str .= '
							<a href="'.get_permalink($post->ID).'" class="ppc-post-title" title="'.sprintf(__('Article %s published at %s', 'ppc'), $title_full, date_i18n(__('F j, Y g:i a'), strtotime($post->post_date)) ).'">'. (($shorten)?$title_short:$title_full) .'</a>';
							if ( $commnum ) {
									$ppc_str .= ' <span class="ppc-comments-num">(<a href="'.get_permalink($post->ID);
								if ( $comments_num == 0 ) {
									$ppc_str .= '#respond" title="'.sprintf(__('Be first to comment %s', 'ppc'), $title_full);
								} else {
									$ppc_str .= '#comments" title="'.sprintf(__('Read comments on %s', 'ppc'), $title_full);
								}
								$ppc_str .= '">'.$comments_num.'</a>)</span>';
							}

							if ( $br++ == 0 && ($excerpts == 'first') ) { // print excerpt for first post
								$ppc_str .= '<p>';
								if ( $thumb ) { // print thumbnail
									if ( function_exists('has_post_thumbnail') && has_post_thumbnail($post->ID) ) {
										$ppc_str .= wp_get_attachment_image( get_post_thumbnail_id($post->ID), $ppc_tsize);
									}
								}
								$ppc_str .= $excerpt.'</p>';
							} elseif ( $br++ > 0 && $excerpts == 'all' ) { // print excerpt for other posts
								$ppc_str .= '<p>';
								if ( $thumb ) { // print thumbnails
									if ( function_exists('has_post_thumbnail') && has_post_thumbnail($post->ID) ) {
										$ppc_str .= wp_get_attachment_image( get_post_thumbnail_id($post->ID), $ppc_tsize );
									}
								}
								$ppc_str .= $excerpt.'</p>';
							}
						} else {
							// tempalte output
							$template_str = $template;
							$template_str = str_replace('%title%', $title_full, $template_str);
							$template_str = str_replace('%title_short%', $title_short, $template_str);
							$template_str = str_replace('%post_content%', $post_content, $template_str);
							$template_str = str_replace('%excerpt%', $excerpt, $template_str);
							$template_str = str_replace('%thumbnail%', $thumbnail, $template_str);
							$template_str = str_replace('%link%', $link, $template_str);
							$template_str = str_replace('%comments_num%', $comments_num, $template_str);
							$template_str = str_replace('%comments_link%', $comments_link, $template_str);
							$template_str = str_replace('%comments_form_link%', $comments_form_link, $template_str);
							$template_str = str_replace('%datetime%', $datetime, $template_str);
							$template_str = str_replace('%date%', $date, $template_str);
							$template_str = str_replace('%time%', $time, $template_str);
							$template_str = str_replace('%author_displayname%', $author_displayname, $template_str);
							$template_str = str_replace('%author_firstname%', $author_firstname, $template_str);
							$template_str = str_replace('%author_lastname%', $author_lastname, $template_str);
							$template_str = str_replace('%author_posts_url%', $author_posts_url, $template_str);
							$ppc_str .= $template_str;
							unset($template_str);
						}

						$ppc_str .= '</li>';
					} // end of processing every post from category $cat

					// close category box
					$ppc_str .= '
					</ul>
					'.$ppc_moreadd.'
					</div>
				</div>
					';
					// <!-- end of Category Box -->

					// print row cleaner
					$boxnum++;
					if ( $boxnum == $columns ) {
						$ppc_str .= '<div class="clear"></div>';
						$boxnum = 0;
					} // boxnum equal to number of columns
				} // end of processing non-empty categories
			} // end foreach $cats as $cat

			// print row cleaner at end of PPC
			if ( $boxnum != $columns ) {
				$ppc_str .= '<div class="clear"></div>';
			}

			// close PPC container
			$ppc_str .= '
			</div>
		<!-- end of '.POSTS_PER_CAT_NAME.' -->
		';

		return $ppc_str;
		} // posts_per_cat()

		// inject PPC CSS in page head
		function enqueue_scripts() {
			wp_enqueue_style( 'ppc-main', plugins_url('assets/css/ppc.min.css', __FILE__) );
			$options = get_option('postspercat');
			if ( $options['ppccss'] ) {
				wp_enqueue_style( 'ppc-list', plugins_url('assets/css/ppc-list.min.css', __FILE__) );
			}
		    $tsize =  $options['tsize'];
		    if ( preg_match_all('/([0-9]+)x([0-9]+)/', $tsize, $matches) )
		    {
		        $custom_css = ".ppc .attachment-{$matches[0]}x{$matches[1]} {
			width: {$matches[0]}px !important;
			height: {$matches[1]}px !important;
		}";
		    }
		    else if ( preg_match('/^([0-9])+$/', $tsize) )
		    {
		        $custom_css = ".ppc .attachment-{$tsize}x{$tsize} {
			width: {$tsize}px !important;
			height: {$tsize}px !important;
		}";
		    }
			if ( !empty($custom_css) )
		    	wp_add_inline_style( 'ppc-main', $custom_css );
		}

	} // end class


} // end class check

new POSTS_PER_CAT();
