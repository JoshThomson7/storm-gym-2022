<?php
/**
 * Default template
 */

get_header();

global $post;

AVB::avb_banners();
flexible_content();

get_footer(); ?>
