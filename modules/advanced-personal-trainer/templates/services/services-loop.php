<?php
/**
 * APM Services Loop
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

foreach($get_services as $service_id):
    $service = new APM_Service($service_id);
?>
    <article>
        <a href="<?php echo $service->url(); ?>" class="card__inner service">
            <h3><?php echo $service->title(); ?></h3>
        </a>
    </article>
<?php endforeach; ?>