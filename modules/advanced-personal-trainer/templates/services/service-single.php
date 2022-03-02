<?php
/**
 * APM Single Service
 */

get_header();

global $post;
$post_id = $post->ID;
$post_type = get_post_type($post_id);

$service = new APM_Service($post_id);
$service_specialists = $service->specialists();

include get_stylesheet_directory().'/modules/inner-banner.php';
?>

<section class="apm__<?php echo $post_type; ?>--single">

    <div class="max__width">

        <div class="apm__content--top-nav">
            <div>
                <a href="<?php echo esc_url(get_permalink(get_page_by_path('services'))) ?>"><i class="fa fa-chevron-left"></i> Back to Services</a>
            </div>
        </div>

        <div class="apm__<?php echo $post_type; ?>--single-wrap apm__content--with-sidebar">
            
            <div class="apm__<?php echo $post_type; ?>--single-content apm__content--content">

                <?php flexible_content(); ?>

            </div><!-- apm__<?php echo $post_type; ?>--single-content -->

            <aside class="apm__<?php echo $post_type; ?>--single-sidebar apm__content--sidebar">

                <?php if(!empty($service_specialists)): ?>
                    <article class="service__specialists">
                        <h4>Our specialists in this area</h4>

                        <?php
                            foreach($service_specialists as $service_specialist_id):
                                $practitioner = new APM_Practitioner($service_specialist_id);
                                $practitioner_img = $practitioner->profile_picture(800, 600);
                                $practitioner_locations = $practitioner->locations();
                        ?>
                            <div class="service__specialist">

                                <h2><?php echo $practitioner->title(); ?></h2>
                                <h5><?php echo $practitioner->job_title(); ?></h5>
                            
                                <?php if(!empty($practitioner_img) && isset($practitioner_img['url'])): ?>
                                    <div class="specialist__image">
                                        <div class="specialist__image--shape">
                                            <img src="<?php echo $practitioner_img['url']; ?>" alt="<?php echo $practitioner->title(); ?>">
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if(!empty($practitioner_locations)): ?>
                                    <div class="specialist__locations">
                                        <ul>
                                            <?php
                                                foreach($practitioner_locations as $practitioner_location_id):
                                                    $location = new APM_Location($practitioner_location_id);
                                            ?>
                                                <li><i class="fas fa-map-marker-alt fa-fw"></i> <?php echo $location->title(); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <?php if($practitioner->short_bio()): ?>
                                    <div class="specialist__bio">
                                        <?php echo $practitioner->short_bio(); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="specialist__actions">
                                    <a href="<?php echo $practitioner->url(); ?>" class="button primary_solid">See full profile</a>
                                    <!-- <a href="#" class="button orange">Book appointment</a> -->
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </article>
                <?php endif; ?>
                

            </aside><!-- apm__<?php echo $post_type; ?>--single-sidebar -->

        </div>
        
    </div><!-- max__width -->
</section>

<?php get_footer(); ?>