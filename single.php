<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

	<article id="<?php the_ID(); ?>" <?php post_class(); ?>>
		<!-- Happy coding -->
	</article>

	<?php // If comments are open or we have at least one comment, load up the comment template.
	//if ( comments_open() || get_comments_number() ) : comments_template(); endif;
	?>

	<?php the_post_navigation( array(
		'screen_reader_text' => 'Navigation',
		'next_text' => 'Next Post: %title',
		'prev_text' => 'Previous Post: %title'
	)); ?>

<?php endwhile;?>

<?php get_footer();
?>
