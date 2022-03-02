<?php
/**
 * APM Contact
 */

get_header();

global $post;
$post_id = $post->ID;

include get_stylesheet_directory().'/modules/inner-banner.php';
?>

<section class="apm__contact--page">

    <div class="max__width">

        <div class="apm__contact--page-wrap apm__content--with-sidebar two-col">
            
            <div class="apm__contact--page-content apm__content--content">

                <?php flexible_content(); ?>

            </div><!-- apm__contact--page-content -->

            <aside class="apm__contact--page-sidebar apm__content--sidebar">
                
                <article class="grey-box">
                    <h4>Join our team</h4>
                    <p><a href="<?php echo esc_url(get_permalink(get_page_by_path('careers'))); ?>" class="button primary_plain">Apply</a></p>
                </article>

                <article class="grey-box">
                    <h4>Partnerships</h4>
                    <p><?php echo hide_email('clientcare@bodyset.co.uk', 'Make an enquiry', 'button primary_plain'); ?></p>
                </article>

                <article class="grey-box">
                    <h4>GP &amp; Consultant Referrals</h4>
                    <p>Looking to refer? Our team work closely with GPs, specialists and consultants throughout the UK.</p>
                    <p><a href="<?php echo esc_url(get_permalink(get_page_by_path('gp-and-consultant-referrals'))); ?>" class="button primary_plain">Find out more</a></p>
                </article>

                <article class="grey-box">
                    <h4>Buy my practice</h4>
                    <p>Looking to sell? We are interested in buying physio, fitness and wellness related buisnesses across the UK.</p>
                    <p><a href="<?php echo esc_url(get_permalink(get_page_by_path('buy-my-practice'))); ?>" class="button primary_plain">Find out more</a></p>
                </article>                

            </aside><!-- apm__contact--page-sidebar -->

        </div>
        
    </div><!-- max__width -->
</section>

<?php get_footer(); ?>