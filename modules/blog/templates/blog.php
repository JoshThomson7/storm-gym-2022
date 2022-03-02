<?php
/**
 * Blog
 */

get_header();

$featured_blog = FL1_Blogs::get_blogs(array(
    'posts_per_page' => 1
));
$featured_blog_id = reset($featured_blog);

$blog_cats = FL1_Blogs::get_categories();
?>

<section class="blog__header">
    <div class="max__width">
        <h1 class="blog__title">Blog</h1>

        <article class="blog__article blog__featured">
            <?php
                if($featured_blog_id):
                
                    $blog = new FL1_Blog($featured_blog_id);

                    // Image
                    $blog_image = $blog->image(900, 500, true);
                    $banner_image = '';
                    if(!empty($blog_image)) {
                        $banner_image = ' style="background-image: url('.$blog_image['url'].')"';
                    } else {
                        $banner_image = ' style="background-image: url('.get_stylesheet_directory_uri().'/img/sq-blog-placeholder.jpg)"';
                    }

                    // Main category
                    $blog_cat = $blog->main_category('id=>name');
            ?>
                <div class="blog__content">

                    <?php if($blog_cat): ?><h5><?php echo $blog_cat; ?></h5><?php endif; ?>
                    <h2><a href="<?php echo $blog->url(); ?>" title="<?php echo $blog->title(); ?>"><?php echo $blog->title(); ?></a></h2>

                    <date><?php echo $blog->date('M jS Y') ?></date>
                    
                    <p><?php echo $blog->excerpt(55); ?></p>

                    <div class="blog__more">
                        <a href="<?php echo $blog->url(); ?>">
                            <span>Read more</span>
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </div><!-- blog__more -->
                </div>

                <a class="blog__img" href="<?php echo $blog->url(); ?>" <?php echo $banner_image; ?>></a>
            <?php endif; ?>
        </article>
    </div>
</section>

<section class="blog has-deps" data-deps='{"js":["blog"]}' data-deps-action="blog_filters">
    <div class="max__width">
        <div class="blog__filters">
            <form id="blog_filters">
                <article class="is-radio">
                    <input type="radio" id="filter-all" value="" name="blog_cat" checked>
                    <label for="filter-all">
                        <span>Recent articles</span>
                    </label>
                </article>

                <?php if(!empty($blog_cats)): ?>

                    <?php
                        foreach($blog_cats as $blog_cat):
                            
                            $checked = '';
                            if($blog_cat->slug === 'core') {
                                $checked = ' checked';
                            }

                            $label_id = $blog_cat->slug.'_'.$blog_cat->term_id;
                    ?>
                        <article class="is-radio">
                            <input type="radio" id="<?php echo $label_id; ?>" value="<?php echo $blog_cat->term_id; ?>" name="blog_cat"<?php echo $checked; ?>>
                            <label for="<?php echo $label_id; ?>">
                                <span><?php echo $blog_cat->name; ?></span>
                            </label>
                        </article>
                    <?php endforeach; ?>

                <?php endif; ?>
            </form>
        </div>
        
        <div id="blog_response" class="blog__loop grid"></div>
    </div><!-- max__width -->
</section><!-- blog -->

<?php get_footer(); ?>
