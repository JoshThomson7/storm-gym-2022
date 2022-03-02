<?php
/**
 * WP Search ACF
 *
 * Includes ACF fields on Search
 *
 * @see https://gist.github.com/charleslouis/5924863#file-custom-search-acf-wordpress-php
 */


/**
 * [list_searchable_acf list all the custom fields we want to include in our search query]
 * @return [array] [list of custom fields]
 */
function list_searchable_acf(){
    $list_searchable_acf = array('deal_highlights', 'voucher_location', 'deal_display_price', '_regular_price');
    return $list_searchable_acf;
}


/**
 * [advanced_custom_search search that encompasses ACF/advanced custom fields and taxonomies and split expression before request]
 * @param  [query-part/string]      $where    [the initial "where" part of the search query]
 * @param  [object]                 $wp_query []
 * @return [query-part/string]      $where    [the "where" part of the search query as we customized]
 * see https://vzurczak.wordpress.com/2013/06/15/extend-the-default-wordpress-search/
 * credits to Vincent Zurczak for the base query structure/spliting tags section
 */
function advanced_custom_search( $where, $wp_query ) {

    global $wpdb;

    $prefix = $wpdb->prefix;

    if ( empty( $where ))
        return $where;

    // get search expression
    $terms = sanitize_text_field($wp_query->query_vars[ 's' ]);

    // explode search expression to get search terms
    $exploded = explode( ' ', $terms );
    if( $exploded === FALSE || count( $exploded ) == 0 )
        $exploded = array( 0 => $terms );

    // reset search in order to rebuilt it as we whish
    $where = '';

    // get searchable_acf, a list of advanced custom fields you want to search content in
    $list_searchable_acf = list_searchable_acf();

    foreach( $exploded as $tag ) :
        $where .= "
          AND (
            (".$prefix."posts.post_title LIKE '%$tag%')
            OR (".$prefix."posts.post_content LIKE '%$tag%')
            OR EXISTS (
              SELECT * FROM ".$prefix."postmeta
	              WHERE post_id = ".$prefix."posts.ID
	                AND (";

                    foreach ($list_searchable_acf as $searchable_acf) :
                      if ($searchable_acf == $list_searchable_acf[0]):
                        $where .= " (meta_key LIKE '%" . $searchable_acf . "%' AND meta_value LIKE '%$tag%') ";
                      else :
                        $where .= " OR (meta_key LIKE '%" . $searchable_acf . "%' AND meta_value LIKE '%$tag%') ";
                      endif;
                    endforeach;

	        $where .= ")
            )
            OR EXISTS (
              SELECT * FROM ".$prefix."comments
              WHERE comment_post_ID = ".$prefix."posts.ID
                AND comment_content LIKE '%$tag%'
            )
            OR EXISTS (
              SELECT * FROM ".$prefix."terms
              INNER JOIN ".$prefix."term_taxonomy
                ON ".$prefix."term_taxonomy.term_id = ".$prefix."terms.term_id
              INNER JOIN ".$prefix."term_relationships
                ON ".$prefix."term_relationships.term_taxonomy_id = ".$prefix."term_taxonomy.term_taxonomy_id
              WHERE (
          		taxonomy = 'product_cat'
          		)
              	AND object_id = ".$prefix."posts.ID
              	AND ".$prefix."terms.name LIKE '%$tag%'
            )
        )";
    endforeach;
    return $where;
}

add_filter( 'posts_search', 'advanced_custom_search', 500, 2 );



/**
 * Sets the 'no_found_rows' param to true.
 *
 * In the WP_Query class this stops the use of SQL_CALC_FOUND_ROWS in the
 * MySql query it generates.
 *
 * @param  WP_Query $wp_query The WP_Query instance. Passed by reference.
 * @return void
 */
function wp_set_no_found_rows( \WP_Query $wp_query ) {

    if ( $wp_query->is_main_query() && $wp_query->is_search && is_search() && !is_admin()) {
        //$wp_query->set( 'no_found_rows', true );
        $wp_query->set( 'posts_per_page', 24 );
        $wp_query->set( 'post_type', array('product') );
    }
}
add_filter( 'pre_get_posts', 'wp_set_no_found_rows', 10, 1 );


function clean($string) {
  return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}