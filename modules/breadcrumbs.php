<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Dimox Breadcrumbs
 *
 * Very nice function to display breadcrumbs
 *
 * @see     https://gist.github.com/Dimox/5654092
 * @author  Dimox
 * @package modules/breadcrumbs.php
 * @version 2015.09.14
 *
 * @todo    Check link above as it seems there's a new version!
*/

function dimox_breadcrumbs() {

	/*
	/	If using Custom Post Types with Taxonomies, we need
		to run through the array of terms first so we can display them later

	if ( get_post_type() == 'project' ) { // Projects
		// Get terms for taxonomy project_cat
		$terms = get_the_terms($post->id, 'project_cat');
		//print_r($terms);
		$counterios='0';
		$project_cat_id='';
		foreach($terms as $array){
			foreach($array as $key=>$value){
				if($counterios=='0'){
					$project_cat_id =$value;
				}else{ }
				$counterios++;
			}
		}
	}*/

  $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
  $delimiter = '<span class="separator"></span>'; // delimiter between crumbs
  $home = 'Home'; // text for the 'Home' link
  $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
  $before = '<span class="current">'; // tag before the current crumb
  $after = '</span>'; // tag after the current crumb

  global $post;
  $homeLink = get_bloginfo('url');

  if (is_home() || is_front_page()) {

    if ($showOnHome == 1) echo '<div class="breadcrumbs"><a href="' . $homeLink . '">' . $home . '</a></div>';

  } else {

    echo '<div class="breadcrumbs"><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';

    if ( is_category() ) {
      global $wp_query;
      $cat_obj = $wp_query->get_queried_object();
      $thisCat = $cat_obj->term_id;
      $thisCat = get_category($thisCat);
      $parentCat = get_category($thisCat->parent);
      if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
      echo '<a href="' . $homeLink . '/news/" title="News">News</a> ' . $delimiter . ' ';
      echo $before . 'Archive by category "' . single_cat_title('', false) . '"' . $after;

    } elseif ( is_search() ) {
      echo $before . 'Search results for "' . get_search_query() . '"' . $after;

    } elseif ( is_day() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('d') . $after;

    } elseif ( is_month() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('F') . $after;

    } elseif ( is_year() ) {
      echo $before . get_the_time('Y') . $after;

    } elseif ( is_attachment() ) {
      $parent = get_post($post->post_parent);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
      echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
      if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;

    } elseif ( is_page() && !$post->post_parent ) {
      if ($showCurrent == 1) echo $before . get_the_title() . $after;

    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
        $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
      foreach ($breadcrumbs as $crumb) echo $crumb;
      if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;

    } elseif ( is_tag() ) {
      echo '<a href="' . $homeLink . '/news/" title="News">News</a> ' . $delimiter . ' ';
      echo $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;

    } elseif ( is_author() ) {
       global $author;
      $userdata = get_userdata($author);
      echo $before . 'Articles posted by ' . $userdata->display_name . $after;

    } elseif ( is_404() ) {
      echo $before . 'Error 404' . $after;

	/*------------------------------------------------------------------------*/
	/*                 FL1 Additions - Custom Post Types                      */
	/*------------------------------------------------------------------------*/
	} elseif ( is_single() ) {
      if ( get_post_type() == 'clinic' ) { // Clinics
		global $post;
		echo '<a href="' . $homeLink . '/clinics/" title="Clinics">Clinics</a> ' . $delimiter . ' ';
        echo $before . get_the_title() . $after;

      } elseif ( get_post_type() == 'partner' ) { // Team
		global $post;
		echo '<a href="' . $homeLink . '/partners/" title="Partners">Partners</a> ' . $delimiter . ' ';
        echo $before . get_the_title() . $after;

      } elseif ( get_post_type() == 'team' ) { // Team
		global $post;
		echo '<a href="' . $homeLink . '/team/" title="Team">Team</a> ' . $delimiter . ' ';
        echo $before . get_the_title() . $after;

      } elseif ( get_post_type() == 'event' ) { // Event
		global $post;
		echo '<a href="' . $homeLink . '/events/" title="Team">Events</a> ' . $delimiter . ' ';
        echo $before . get_the_title() . $after;

	  } else { // Normal posts
        $cat = get_the_category(); $cat = $cat[0];
        echo '<a href="' . $homeLink . '/news/" title="News">News</a> ' . $delimiter . ' ';
        echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
        echo $before . get_the_title() . $after;
      }

	} elseif ( is_tax() ) {
		global $wp_query;
		$term_obj =	$wp_query->get_queried_object();

		if($term_obj->taxonomy == 'feature') {
			$term = get_term_by('id', $term_obj->term_id , 'clinic');
			echo '<a href="' . $homeLink . '/clinics/" title="Clinics">Clinics</a> ' . $delimiter . ' ';
			echo '<a href="' . $homeLink . '/clinics/" title="Feature">Feature</a> ' . $delimiter . ' ';

		} elseif($term_obj->taxonomy == 'field') {
			$term = get_term_by('id', $term_obj->term_id , 'clinic');
			echo '<a href="' . $homeLink . '/team/" title="Team">Team</a> ' . $delimiter . ' ';
			echo '<a href="' . $homeLink . '/team/" title="Field">Field</a> ' . $delimiter . ' ';
		}

        echo $before . $term_obj->name . $after;
	}
 	/*-----------------------------------------------*/


    if ( get_query_var('paged') ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_page() || is_tax() ) echo ' <span class="breadcrumb-pagecount">(';
      echo __('Page') . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_page() || is_tax() ) echo ')</span>';
    }

    echo '</div>';

  }
} // end dimox_breadcrumbs()
