<?php
/**
* Blog Archive
*
* @package Blog
* @version 1.0
*/

get_header();

include get_stylesheet_directory().'/modules/inner-banner.php';

$blog_cat_id = null;
$cat_id = get_queried_object_id();

$blogs = new FL1_Blogs();
$get_blogs = $blogs->get_blogs(array(
    'cat' => $cat_id,
    'posts_per_page' => -1
));
?>

<section class="blog">
    <div class="max__width">
        <div class="apm__content--top-nav">
            <div>
                <a href="<?php echo esc_url(get_permalink(get_page_by_path('blog'))); ?>"><i class="fa fa-chevron-left"></i> Back to Blog</a>
            </div>
        </div>

        <div class="blog__loop grid">
            <?php include blog_path().'templates/blog-loop.php'; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>