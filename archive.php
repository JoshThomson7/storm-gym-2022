<?php get_header(); ?>

	<?php if ( have_posts() ) : ?>

		<?php the_archive_title(); ?>
		<?php the_archive_description(); ?>

		<?php while ( have_posts() ) : the_post(); ?>
			<article id="<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php the_title(); ?>
				<?php the_excerpt(); ?>
				<a href="<?php the_permalink(); ?>">Read More</a>
			</article>
		<?php endwhile;?>

		<?php the_posts_pagination(array(
			'prev_text' => 'Previous page',
			'next_text' => 'Next page'
		)); ?>

	<?php  else : ?>
		<?php echo 'No Results'; ?>
	<?php endif; ?>

<?php get_footer(); ?>
