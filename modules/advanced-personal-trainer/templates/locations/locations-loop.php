<?php
/**
 * APM Locations Loop
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if(!empty($filtered['locations'])):
    $locations = $filtered['locations'];
?>

    <div class="apm__cards">
        <?php
            foreach($locations as $location):

                $location_id = $location;
                $distance = '';
                if(is_array($location) && isset($location['distance'])) {
                    $location_id = $location['ID'];
                    $distance = '<small class="distance"><i class="fas fa-location-arrow"></i> '.round($location['distance'], 2).' miles</small>';
                }

                $_location = new APM_Location($location_id);
        ?>
            <article>
                <a href="<?php echo $_location->url(); ?>" class="card__inner">
                    <h3><?php echo $_location->title(); ?></h3>
                    <p><?php echo $_location->location('address', true); ?></p>
                    <?php echo $distance; ?>
                </a>
            </article>
        <?php endforeach; ?>
    </div>

<?php endif; ?>