<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Miscellaneous Functions
 *
 * This file contains useful functions for different things.
 * If you find anything useful, add it here and document it well.
 *
 * @author  Multiple Authors
 * @package modules/
 * @version 1.0
*/

/**
 * Disable the fatal error handler.
 */
add_filter( 'wp_fatal_error_handler_enabled', '__return_false' );

/**
 * Remove version numbers from enqueued files.
 *
 * @since 1.0
 * @param string $src
 */
function pretty_print($var) {
    
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    
}

/* ------------------------------------------*/
/*  Custom WP Title
* -------------------------------------------*/
function custom_wp_title( $title, $sep ) {
    if ( is_feed() ) {
        return $title;
    }

    global $page, $paged;

    // Add the blog name
    $title .= get_bloginfo( 'name', 'display' );

    // Add the blog description for the home/front page.
    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) ) {
        $title = get_bloginfo( 'name', 'display' ) . $sep . $site_description;
    }

    // Add a page number if necessary:
    if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
        $title .= " $sep " . sprintf( __( 'Page %s', '_s' ), max( $paged, $page ) );
    }

    return $title;
}
add_filter( 'wp_title', 'custom_wp_title', 10, 2 );


/* ------------------------------------------*/
/*  Add Support
* -------------------------------------------*/
add_theme_support( 'post-thumbnails' ); // Enable Featured Images
add_theme_support( 'menus' );

function custom_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'custom_mime_types', 11);

/* ------------------------------------------*/
/*  TRUNCATE TEXT
/*  Encrypts email addresses
/*
/*  Usage: <php? echo trunc($text, 25); ?>
* -------------------------------------------*/
function trunc($string, $word_limit) {

    $words = explode(' ', $string, ($word_limit + 1));

    if(count($words) > $word_limit) {
        array_pop($words);
        return $alex = implode(' ', $words)." [...]"; //add a ... at last article when more than limit word count

    } else {
        return $alex = implode(' ', $words);

    }
}

/**
*   wp_spinner()
*
*   displays the built in WordPress spinner graphic
*/
function wp_spinner($echo = true) {
    $html = '<div class="wp__loading"><img src="'.admin_url().'/images/spinner-2x.gif"></div>';
    if($echo === true) {
        echo $html;
    } else {
        return $html;
    }
}

/* ------------------------------------------*/
/* 	ENCRYPT EMAIL
/* 	Encrypts email addresses
/*
/*	Usage: <php? echo hide_email($email); ?>
* -------------------------------------------*/
function hide_email($email, $text = '', $classes = '') {
	$character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
	$key = str_shuffle($character_set); $cipher_text = ''; $id = 'e'.rand(1,999999999);
	for ($i=0;$i<strlen($email);$i+=1) $cipher_text.= $key[strpos($character_set,$email[$i])];
	$script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";';
	$script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));';
	$script.= 'document.getElementById("'.$id.'").innerHTML="<a href=\\"mailto:"+d+"\\" class=\\"'.$classes.'\\">'.($text ? $text : '"+d+"').'</a>"';
	$script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")";
	$script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>'; return '<span id="'.$id.'">[javascript protected email address]</span>'.$script;
}


/* ------------------------------------------*/
/* 	PARENT PAGE ID
/* 	Get the very top parent ID
* -------------------------------------------*/
function get_top_parent_page_id() {
    global $post;
    // Check if page is a child page (any level)
    if ($post->ancestors) {
        //  Grab the ID of top-level page from the tree
        return end($post->ancestors);
    } else {
        // Page is the top level, so use  it's own id
        return $post->ID;
    }
}


/* ------------------------------------------------------------*/
/*  Stops WP wrapping WYSIWYG images in P tags
* -------------------------------------------------------------*/
function img_unautop($pee) {
    $pee = preg_replace('/<p>\\s*?(<a .*?><img.*?><\\/a>|<img.*?>)?\\s*<\\/p>/s', '<figure>$1</figure>', $pee);
    return $pee;
}
add_filter( 'the_content', 'img_unautop', 30 );

/* ------------------------------------------*/
/* 	SEARCH EVERYTHING FIX
/* 	Fixes problem with emtpy $s parameter
/*  not finding anything
* -------------------------------------------*/
function se_wp_search_fix( $query_vars ) {
    if( isset( $_GET['s'] ) && empty( $_GET['s'] ) ) {
        $query_vars['s'] = "&nbsp;";
    }

    return $query_vars;
}
//add_filter( 'request', 'se_wp_search_fix' );

// function wp_custom_mail_from_address($email){
//     return 'no-reply@wpdev2.com';
// }
// add_filter('wp_mail_from', 'wp_custom_mail_from_address');

// function wp_custom_mail_from_name($from_name){
//     return get_bloginfo('description');
// }
// add_filter('wp_mail_from_name', 'wp_custom_mail_from_name');

/* ------------------------------------------*/
/* 	SITE SEARCH
/* 	Posts per page on search results
* -------------------------------------------*/
/*function limit_posts_per_archive_page() {
	if(is_search()) {
		$limit = 30;
	} else {
		$limit = get_option('posts_per_page');
	}

	set_query_var('posts_per_archive_page', $limit);
}
add_filter('pre_get_posts', 'limit_posts_per_archive_page');*/


/* ------------------------------------------*/
/* 	SITE SEARCH
/* 	Include post types in search
* -------------------------------------------*/
/*function search_cpts($query) {
    if ($query->is_search && !is_admin()) {
		$query->set('post_type', array('post', 'page', 'clinic'));
    };
    return $query;
};
add_filter('pre_get_posts', 'search_cpts');*/


/* ------------------------------------------*/
/* 	SHOW POSTS ON TAXONOMIES
/* 	Sometimes Wordpress can't see any posts
/*  under a taxpnomy archive. This fixes it.
* -------------------------------------------*/
/*function wpa82763_custom_type_in_categories( $query ) {
    if ( $query->is_main_query() && !is_admin()
    && ( $query->is_category() || $query->is_tag() || $query->is_tax() ) ) {
        $query->set( 'post_type', array( 'clinic', 'team', 'post', 'page' ) );
    }
}
add_action( 'pre_get_posts', 'wpa82763_custom_type_in_categories' );*/


/*
 * Tests if any of a post's assigned categories are descendants of target categories
 *
 * @param int|array $cats The target categories. Integer ID or array of integer IDs
 * @param int|object $_post The post. Omit to test the current post in the Loop or main query
 * @return bool True if at least 1 of the post's categories is a descendant of any of the target categories
 * @see get_term_by() You can get a category by name or slug, then pass ID to this function
 * @uses get_term_children() Passes $cats
 * @uses in_category() Passes $_post (can be empty)
 * @version 2.7
 * @link http://codex.wordpress.org/Function_Reference/in_category#Testing_if_a_post_is_in_a_descendant_category

if ( ! function_exists( 'post_is_in_descendant_term' ) ) {
	function post_is_in_descendant_term( $terms, $_post = null ) {
		foreach ( (array) $terms as $term ) {
			// get_term_children() accepts integer ID only
			$descendants = get_term_children( (int) $terms, 'resource_type' );
			if ( $descendants && in_category( $descendants, $_post ) )
				return true;
		}
		return false;
	}
} */


/* ------------------------------------------*/
/* 	FILE SIZES
/* 	Convert bytes to a human readable format
* -------------------------------------------*/
// Convert bytes to a human readable format
/*function ByteSize($bytes) {
    $size = $bytes / 1024;
    if($size < 1024)
        {
        $size = number_format($size, 2);
        $size .= ' KB';
        }
    else
        {
        if($size / 1024 < 1024)
            {
            $size = number_format($size / 1024, 2);
            $size .= ' MB';
            }
        else if ($size / 1024 / 1024 < 1024)
            {
            $size = number_format($size / 1024 / 1024, 2);
            $size .= ' GB';
            }
        }
    return $size;
} */


/* ------------------------------------------*/
/* 	WP_LIST_CATEGORIES POST COUNT
/* 	Fix to style count
* -------------------------------------------*/
/*add_filter('wp_list_categories', 'cat_count_span');
function cat_count_span($new_home_types) {
	$new_home_types = str_replace('</a> (', ' <span>', $new_home_types);
	$new_home_types = str_replace(')', '</span></a>', $new_home_types);
	return $new_home_types;
}*/


/* ------------------------------------------*/
/*  FANCYBOX (DO NOT DELETE, TO DO)
/*
/*  Automatically adds the fancybox class to
/*  the link of any image uploaded inside
/*  the_content().
* -------------------------------------------*/
/*add_filter('the_content', 'add_fancybox_class_replace');
function add_fancybox_class_replace ($content) {
    global $post;
    $pattern = "/<a(.*?)href=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
    $replacement = '<a$1href=$2$3.$4$5 class="fancybox"$6>';
    $content = preg_replace($pattern, $replacement, $content);
    $content = str_replace("%LIGHTID%", $post->ID, $content);
    return $content;
}*/
