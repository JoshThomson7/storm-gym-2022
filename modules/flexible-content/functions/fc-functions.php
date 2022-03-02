<?php
/**
 * Flexible Content Functions
 *
 * @package flexible-content/
 * @version 1.0
 * @dependencies
 *    - ACF PRO: https://www.advancedcustomfields.com/pro/
*/

function flexible_content() {
    include(get_stylesheet_directory().'/modules/flexible-content/flexible-content.php');
}

function fc_field_section($row_layout, $open_close) {

    $row_layout = get_row_layout();

    $fc_classes = array('fc-layout', $row_layout);
    $layout_container_classes = array('fc-layout-container');

    // section heading
    $options = get_sub_field('fc_options');
    $option_top_heading = $options['top_heading'];
    $option_heading = $options['heading'];
    $option_heading_center = $options['heading_center'];
    $option_caption = $options['caption'];
    $option_tab = $options['tab_name'];

    // generate section ID
    $tab_id = '';
    if($option_tab) {
        $tab_id = ' id="'.strtolower(preg_replace("#[^A-Za-z0-9]#", "", $option_tab)).'_section"';
    }

    // Styles
    $style = array();
    $fc_styles = get_sub_field('fc_styles');

    // Padding
    $padding = $fc_styles['fc_padding'];
    $padding_style = 'padding:';
    $padding_style .= !empty($padding['fc_padding_top']) ? ' '.$padding['fc_padding_top'].'px' : ' 0';
    $padding_style .= !empty($padding['fc_padding_right']) ? ' '.(($padding['fc_padding_right']*100)/1200).'%' : ' 0';
    $padding_style .= !empty($padding['fc_padding_bottom']) ? ' '.$padding['fc_padding_bottom'].'px' : ' 0';
    $padding_style .= !empty($padding['fc_padding_left']) ? ' '.(($padding['fc_padding_left']*100)/1200).'%' : ' 0';

    $style[] = $padding_style;

    // Background class
    $bk_classes = array('fc-layout-mask');
    
    $has_bk = $fc_styles['fc_background'] ? 'fc-bk-'.$fc_styles['fc_background'] : null;
    if($has_bk) {
        $bk_classes[] = $has_bk;
        $layout_container_classes[] = 'fc-bk-'.$fc_styles['fc_background'];
    }
    
    // Skewed Edges
    $skewed_edges = $fc_styles['fc_skewed_edges'] ?? null;

    if($skewed_edges && $has_bk) {

        $bk_classes[] = 'fc-skewed-edge';

        $angle_direction = $skewed_edges['angle_direction'];
        if($angle_direction) {
            $bk_classes[] = 'fc-skewed-edge-angle-direction-'.$angle_direction;
        }

        $angle_placement = $skewed_edges['angle_placement'];
        if($angle_placement) {
            $bk_classes[] = 'fc-skewed-edge-angle-placement-'.$angle_placement;
        }

    }

    $bk_classes = join(' ', $bk_classes);
    $fc_classes = join(' ', $fc_classes);
    $layout_container_classes = join(' ', $layout_container_classes);

    // full width
    $full_width = $fc_styles['fc_full_width'] == true ? true : false;

    // open/close
    $html = '';
    if($open_close === 'open') {

        if($row_layout === 'fc_carousel_open') {

            $html .= '<div class="fc-layout-carousel '.$fc_classes.'">';

        } else {

            $html .= '<section'.$tab_id.' class="'.$fc_classes.'">';

            $bk_img = $fc_styles['fc_background_image'];
            if($bk_img) {
                $bk_bottom_gradient = $fc_styles['fc_background_bottom_gradient'];
                if($bk_bottom_gradient) {
                    $html .='<div class="fc-layout-bk-btm-gradient '.$has_bk.'"></div>';    
                }

                $bk_img_styles = $fc_styles['fc_background_image_styles'];
                $html .='<div class="fc-layout-bk-img" style="background-image:url('.$bk_img.'); '.$bk_img_styles.'"></div>';
            }

            if($has_bk) {
                $html .='<div class="'.$bk_classes.'"><div class="fc-layout-bk"></div></div>';
            }

            $html .='<div class="'.$layout_container_classes.'" style="'.$padding_style.'">';

            // check if full with
            if(!$full_width) {
                $html .='<div class="max__width">';
            }

            if($option_top_heading || $option_heading || $option_caption) {
                $centre_heading = '';
                if($option_heading_center) {
                    $centre_heading = ' centred';
                }

                $section_top_heading = '';
                if($option_top_heading) {
                    $section_top_heading = '<h5>'.$option_top_heading.'</h5>';
                }

                $section_heading = '';
                if($option_heading) {
                    $section_heading = '<h2>'.$option_heading.'</h2>';
                }

                $section_caption = '';
                if($option_caption) {
                    $section_caption = $option_caption;
                }

                $html .= '<div class="fc-layout-heading'.$centre_heading.'">'.$section_top_heading.$section_heading.$section_caption.'</div>';
            }
        }


    } elseif($open_close === 'close') {

        if($row_layout === 'fc_carousel_close') {

            $html .= '</div>';

        } else {

            // check if full with
            if(!$full_width) {
                $html .= '</div><!-- max__width -->';
                $html .='</div><!-- fc-layout-container -->';
                $html .= '</section><!-- '.$row_layout.' -->';
            } else {
                $html .= '</div><!-- fc-layout-container -->';
                $html .= '</section><!-- '.$row_layout.' -->';
            }

        }

    }

    switch ($row_layout) {
        case 'fc_carousel_open':
        case 'fc_wrapper_open':
            $skip_close = true;
            $skip_open = false;
            break;

        case 'fc_carousel_close':
        case 'fc_wrapper_close':
            $skip_close = false;
            $skip_open = true;
            break;
        
        default:
            $skip_close = false;
            $skip_open = false;
            break;
    }

    return array(
        'html' => $html,
        'skip_open' => $skip_open,
        'skip_close' => $skip_close,
    );
}

// Apply filter
function multisite_body_classes($classes) {
    global $post;
    
    if(have_rows('fc_content_types', $post->ID)) { 
        $classes[] = 'page-has-flexible-content';
    }
    
    return $classes;
}
add_filter('body_class', 'multisite_body_classes');

// function acf_select_post_type($field){
//
// 	//reset
// 	$field['choices'] = array();
//
// 	$args = array(
// 	   'public'   => true,
// 	   'publicly_queryable' => true,
// 	   '_builtin' => false,
// 	);
//
// 	$post_types = get_post_types( $args, 'objects');
// 	$post_types[] = get_post_type_object( 'post' );
//
// 	foreach($post_types as $pt) {
// 		$label = $pt->label;
// 		$value = $pt->name;
//
// 		$field['choices'][$value] = $label;
// 	}
//
// 	return $field;
//
// }
//
// add_filter('acf/load_field/name=select_post_type', 'acf_select_post_type');
