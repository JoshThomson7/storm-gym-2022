<?php
/*
*	Blog Sidebar New
*
*	@package Blog
*	@version 1.0
*/
?>

	<div class="blog__sidebar">
        <?php
            // Prepare query
            /*$cats = $blog->categories('ids');
            $relatedPosts = new WP_Query(array(
                'post_type'         => 'post',
                'post_status'       => 'publish',
                'posts_per_page'    => 10,
                'orderby'           => 'rand',
                'cat'               => $cats
            ));

            if($relatedPosts->have_posts()):
        ?>
        
            <article class="related__posts">
                
                <h5>Related Blog Posts</h5>

                <ul>
                    <?php while($relatedPosts->have_posts()) : $relatedPosts->the_post(); ?>
                        <li><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
                    <?php endwhile; ?>
                </ul>
                
            </article>

        <?php endif;*/ ?>

	</div><!-- blog__sidebar -->