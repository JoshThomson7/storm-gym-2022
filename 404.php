<?php
/*
	404 template
*/
global $post;

get_header();
?>
		<section class="four_o_four">
			<div class="max__width">

				<div class="four_o_four__page">
					<div class="four_o_four__img">
						<i class="fal fa-ghost"></i>
					</div><!-- four_o_four__img -->

					<h2>Oops!</h2>
					<p>The page you're looking isn't here anymore.<br>Please check the spelling on the address bar or try a different page.</p>

					<?php echo get_post_type(); ?>
				</div><!-- four_o_four__page -->
			</div><!-- max__width -->
		</section><!-- four_o_four -->

<?php get_footer(); ?>
