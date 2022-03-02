<?php
/**
 * Blog Loop
 *
 * @package Blog
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if(!empty($filtered['blogs'])) {
    $blogs = $filtered['blogs'];

    foreach($blogs as $blog_id) {
    
        require('blog-item.php');
        
    }
}