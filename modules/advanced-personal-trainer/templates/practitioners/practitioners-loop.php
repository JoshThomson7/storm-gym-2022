<?php
/**
 * APM Practitioners Loop
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

foreach($get_practitioners as $practitioner_id):
    $practitioner = new APM_Practitioner($practitioner_id);
    $practitioner_job_title = $practitioner->job_title();
?>
    <article>
        <a href="<?php echo $practitioner->url(); ?>" class="card__inner">
            <h3><?php echo $practitioner->title(); ?></h3>
            <p><?php echo $practitioner_job_title; ?></p>
        </a>
    </article>
<?php endforeach; ?>