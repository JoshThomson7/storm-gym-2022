<?php
/*
-----------------------------
 _    ___     __
| |  / (_)___/ /__  ____
| | / / / __  / _ \/ __ \
| |/ / / /_/ /  __/ /_/ /
|___/_/\__,_/\___/\____/

-----------------------------
Video
*/
?>
<div class="video-wrapper" style="width:<?php the_sub_field('video_width'); ?>%">

    <?php if(get_sub_field('video_text')): ?><p><?php the_sub_field('video_text'); ?></p><?php endif; ?>

    <div class="video-responsive">
        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/<?php the_sub_field('video_id'); ?>?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
    </div><!-- video-responsive -->
</div><!-- video-wrapper -->
