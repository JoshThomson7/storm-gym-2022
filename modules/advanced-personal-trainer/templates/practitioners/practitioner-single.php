<?php
/**
 * APM Single Practitioner
 */

get_header();

global $post;
$post_id = $post->ID;
$post_type = get_post_type($post_id);

$practitioner = new APM_Practitioner($post_id);
$practitioner_img = $practitioner->profile_picture(800, 800, true);
$practitioner_job_title = $practitioner->job_title();
$practitioner_locations = $practitioner->locations();
$practitioner_services = $practitioner->services();
?>

<section class="apm__<?php echo $post_type; ?>--single">

    <div class="max__width">

        <div class="apm__content--top-nav">
            <div>
                <a href="/about-us/our-people"><i class="fa fa-chevron-left"></i> Back to Our People</a>
            </div>
        </div>

        <div class="apm__<?php echo $post_type; ?>--single-wrap apm__content--with-sidebar">
            
            <div class="apm__<?php echo $post_type; ?>--single-content apm__content--content">

                <div class="team__single__info">
                    <h1><?php echo $practitioner->title(); ?></h1>
                    <h3><?php echo $practitioner_job_title; ?></h3>
                </div>

                <?php flexible_content(); ?>

            </div><!-- apm__<?php echo $post_type; ?>--single-content -->

            <aside class="apm__<?php echo $post_type; ?>--single-sidebar apm__content--sidebar">

                <article class="team__single__info">

                    <?php if(!empty($practitioner_img) && isset($practitioner_img['url'])): ?>
                        <div class="team__single__image">
                            <div class="container">
                                <img src="<?php echo $practitioner_img['url']; ?>" alt="">
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($practitioner_locations)): ?>
                        <div class="team__single__location">
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
                                    
                    <?php if(!empty($practitioner_services)): ?>
                        <div class="team__single__categories">
                            <?php
                                foreach($practitioner_services as $practitioner_service_id):
                                    $service = new APM_Service($practitioner_service_id);
                            ?>
                                <p><?php echo $service->title(); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
                    
                <?php if(!empty($practitioner_locations)): ?>
                    <article class="team__single__locations">
                        <?php
                            foreach($practitioner_locations as $practitioner_location_id):
                                $location = new APM_Location($practitioner_location_id);
                        ?>
                            <a href="<?php echo $location->url(); ?>" class="box">
                                <p class="name"><?php echo $location->title(); ?></p>
                                <p><?php echo $location->location('address', true); ?></p>
                            </a>
                        <?php endforeach; ?>
                    </article>
                <?php endif; ?>

            </aside><!-- apm__<?php echo $post_type; ?>--single-sidebar -->

        </div>
        
    </div><!-- max__width -->
</section>

<?php get_footer(); ?>