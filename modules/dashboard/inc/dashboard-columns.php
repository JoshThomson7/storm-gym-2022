<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
	CUSTOM COLUMNS FOR CUSTOM POST TYPES
	Version: 1.0 (Last update: 25 Sep 2012)
	-------------------------------------------------------------------------
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* ===============================================================================================================*/
/*                                                                                                                */
/*                                                    VIDEO CPT                                                   */
/*                                                                                                                */
/* ===============================================================================================================*/

// ------------------------------------------ Manage Columns ------------------------------------------ //

add_filter( 'manage_edit-video_columns', 'my_edit_movie_columns' ) ;

function my_edit_movie_columns( $columns ) {

	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Video' ),
		'speaker' => __( 'Speaker' ),
		'view_time' => __( 'Viewing Time' ),
		'cpd_time' => __( 'CPD Time' ),
		'video_category' => __( 'Video Category' ),
		'date' => __( 'Date' )
	);

	return $columns;
}


// ------------------------------------------ Manage Column Data ------------------------------------------ //

add_action( 'manage_video_posts_custom_column', 'my_manage_video_columns', 10, 2 );

function my_manage_video_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {

		case 'speaker' :

			/* Get the post meta. */
			$speaker = get_field('speaker', $post_id);

			/* If no duration is found, output a default message. */
			if ( empty( $speaker ) )
				echo __( '--' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				echo $speaker;

			break;

		case 'view_time' :

			/* Get the post meta. */
			$view_time = get_field('video_viewing_time', $post_id);

			/* If no duration is found, output a default message. */
			if ( empty( $view_time ) )
				echo __( '--' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				echo $view_time;

			break;

		case 'cpd_time' :

			/* Get the post meta. */
			$cpd_time = get_field('cpd_time', $post_id);

			/* If no duration is found, output a default message. */
			if ( empty( $cpd_time ) )
				echo __( '--' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				echo $cpd_time;

			break;

		case 'video_category' :

			/* Get the genres for the post. */
			$terms = get_the_terms( $post_id, 'video_cat' );

			/* If terms were found. */
			if ( !empty( $terms ) ) {

				$out = array();

				/* Loop through each term, linking to the 'edit posts' page for the specific term. */
				foreach ( $terms as $term ) {
					$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'video_cat' => $term->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'video_cat', 'display' ) )
					);
				}

				/* Join the terms, separating them with a comma. */
				echo join( ', ', $out );
			}

			/* If no terms were found, output a default message. */
			else {
				_e( 'No categories' );
			}

			break;

		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}

// ------------------------------------------ Manage dropdown filtering ------------------------------------------ //
/*
	This adds a dropdown at the top of the list of posts to filter them by taxonomy
*/
add_action( 'restrict_manage_posts', 'my_restrict_manage_resource_posts' );
function my_restrict_manage_resource_posts() {

    // only display these taxonomy filters on desired custom post_type listings
    global $typenow;
    if ($typenow == 'resource') {

        // create an array of taxonomy slugs you want to filter by - if you want to retrieve all taxonomies, could use get_taxonomies() to build the list
        $filters = array('resource_type');

        foreach ($filters as $tax_slug) {
            // retrieve the taxonomy object
            $tax_obj = get_taxonomy($tax_slug);
            $tax_name = $tax_obj->labels->name;
            // retrieve array of term objects per taxonomy
            $terms = get_terms($tax_slug);

            // output html for taxonomy dropdown filter
            echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
            echo "<option value=''>Show All $tax_name</option>";
            foreach ($terms as $term) {
                // output each select option line, check against the last $_GET to show the current option selected
                echo '<option value='. $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
            }
            echo "</select>";
        }
    }
}


// ------------------------------------------ Sortable Columns ------------------------------------------ /

add_filter( 'manage_edit-video_sortable_columns', 'video_sortable_columns' );

function video_sortable_columns( $columns ) {
	$columns['view_time'] = 'view_time';
	$columns['cpd_time'] = 'cpd_time';
	return $columns;
}

/* Only run our customization on the 'edit.php' page in the admin. */
add_action( 'load-edit.php', 'edit_video_load' );

function edit_video_load() {
	add_filter( 'request', 'sort_viewing_time' );
	add_filter( 'request', 'sort_cpd_time' );
}

// ---------------------- Viewing Time ---------------------- //
/* Sorts the viewing time */
function sort_viewing_time( $vars ) {

	/* Check if we're viewing the 'movie' post type. */
	if ( isset( $vars['post_type'] ) && 'video' == $vars['post_type'] ) {

		/* Check if 'orderby' is set to 'duration'. */
		if ( isset( $vars['orderby'] ) && 'view_time' == $vars['orderby'] ) {

			/* Merge the query vars with our custom variables. */
			$vars = array_merge(
				$vars,
				array(
					'meta_key' => 'video_viewing_time',
					'orderby' => 'meta_value_num'
				)
			);
		}
	}

	return $vars;
}

// ---------------------- CPD Time ---------------------- //
/* Sorts the cpd_time. */
function sort_cpd_time( $vars ) {

	/* Check if we're viewing the 'movie' post type. */
	if ( isset( $vars['post_type'] ) && 'video' == $vars['post_type'] ) {

		/* Check if 'orderby' is set to 'duration'. */
		if ( isset( $vars['orderby'] ) && 'cpd_time' == $vars['orderby'] ) {

			/* Merge the query vars with our custom variables. */
			$vars = array_merge(
				$vars,
				array(
					'meta_key' => 'cpd_time',
					'orderby' => 'meta_value_num'
				)
			);
		}
	}

	return $vars;
}
