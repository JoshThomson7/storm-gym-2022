<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
	Developments field (Student)
	Form: Book a room
*/
add_filter( 'gform_pre_render_3', 'gf_student_developments' );
add_filter( 'gform_pre_validation_3', 'gf_student_developments' );
add_filter( 'gform_pre_submission_filter_3', 'gf_student_developments' );
add_filter( 'gform_admin_pre_render_3', 'gf_student_developments' );
function gf_student_developments( $form ) {

    foreach ( $form['fields'] as &$field ) {

        if ( $field->type != 'radio' || strpos( $field->cssClass, 'gf__developments' ) === false ) {
            continue;
        }

        // you can add additional parameters here to alter the posts that are retrieved
        // more info: [http://codex.wordpress.org/Template_Tags/get_posts](http://codex.wordpress.org/Template_Tags/get_posts)
        $posts = get_posts( 'post_type=development&development_type=student&numberposts=-1&orderby=name&post_status=publish' );

        $choices = array();

        foreach ( $posts as $post ) {
        	$dev_img_id = get_field('page_banner', $post->ID);
        	$dev_img = vt_resize( $dev_img_id,'' , 500, 300, true );

        	$dev_area = wp_get_post_terms($post->ID, 'development_area', array("fields" => "all"));
        	$dev_area = $dev_area[0]->name;

            $choices[] = array( 'text' => '<img src="'.$dev_img['url'].'"><span>'.$post->post_title.'<strong>'.$dev_area.'</strong></span>', 'value' => $post->post_title );
        }

        // update 'Select a Post' to whatever you'd like the instructive option to be
        $field->placeholder = 'Select a development';
        $field->choices = $choices;

    }

    return $form;
}

/*
	Developments field (All)
	Form: Report a problem
*/
add_filter( 'gform_pre_render_7', 'gf_all_developments' );
add_filter( 'gform_pre_validation_7', 'gf_all_developments' );
add_filter( 'gform_pre_submission_filter_7', 'gf_all_developments' );
add_filter( 'gform_admin_pre_render_7', 'gf_all_developments' );

add_filter( 'gform_pre_render_5', 'gf_all_developments' );
add_filter( 'gform_pre_validation_5', 'gf_all_developments' );
add_filter( 'gform_pre_submission_filter_5', 'gf_all_developments' );
add_filter( 'gform_admin_pre_render_5', 'gf_all_developments' );

function gf_all_developments( $form ) {

    foreach ( $form['fields'] as &$field ) {

        if ( $field->type != 'radio' || strpos( $field->cssClass, 'gf__all__developments' ) === false ) {
            continue;
        }

        // you can add additional parameters here to alter the posts that are retrieved
        // more info: [http://codex.wordpress.org/Template_Tags/get_posts](http://codex.wordpress.org/Template_Tags/get_posts)
        $posts = get_posts( 'post_type=development&numberposts=-1&orderby=name&post_status=publish' );

        $choices = array();

        foreach ( $posts as $post ) {
        	$dev_img_id = get_field('page_banner', $post->ID);
        	$dev_img = vt_resize( $dev_img_id,'' , 500, 300, true );

        	$dev_area = wp_get_post_terms($post->ID, 'development_area', array("fields" => "all"));
        	$dev_area = $dev_area[0]->name;

        	$devs_not_in = array('X1 Plaza Boulevard');

        	if(in_array($post->post_title, $devs_not_in)) { continue; }

            $choices[] = array( 'text' => '<img src="'.$dev_img['url'].'"><span>'.$post->post_title.'<strong>'.$dev_area.'</strong></span>', 'value' => $post->post_title );
        }

        // update 'Select a Post' to whatever you'd like the instructive option to be
        $field->placeholder = 'Select a development';
        $field->choices = $choices;

    }

    return $form;
}
?>
