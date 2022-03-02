<?php
/*
----------------------------------------------------
    ______                  ______          __
   / ____/_______  ___     /_  __/__  _  __/ /_
  / /_  / ___/ _ \/ _ \     / / / _ \| |/_/ __/
 / __/ / /  /  __/  __/    / / /  __/>  </ /_
/_/   /_/   \___/\___/    /_/  \___/_/|_|\__/

----------------------------------------------------
Free Text
*/

$max_width = '';
if(get_sub_field('max_width')) {
    $percentage = (get_sub_field('max_width') * 1400) / 100;
    $max_width = 'style="max-width:'.$percentage.'px; margin:0 auto;"';
}
?>

<div <?php echo $max_width; ?>>
    <?php echo apply_filters('the_content', get_sub_field('free_text', false, false)); ?>
</div>
