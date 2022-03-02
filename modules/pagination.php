<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
  PAGINATION

  Usage: <?php pagination (); ?>
  More info: http://www.kriesi.at/archives/how-to-build-a-wordpress-post-pagination-without-plugin
*/

function pagination($pages = '', $range = 4, $count = null) {
    $showitems = ($range * 2)+1;

    global $paged;
    if(empty($paged)) $paged = 1;

    if($pages == '') {
        global $wp_query;
        $pages = $wp_query->max_num_pages;
        if(!$pages) {
            $pages = 1;
        }
    }

    if(1 != $pages) {

        echo "<div class=\"pagination\">";
        echo ($count === true) ? "<div class=\"page__count\">Page ".$paged." of ".$pages."</div>" : '';
        echo "<div class=\"page__numbers\">";
        if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
        if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";

        for ($i=1; $i <= $pages; $i++) {
            if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
            {
                echo ($paged == $i)? "<span class=\"current__page\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
            }
        }

        if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">&rsaquo;</a>";
        if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
        echo "</div></div>\n";
    }
}
