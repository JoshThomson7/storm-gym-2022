<?php
/**
 * APM Locations Filters
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$locations = new APM_Locations();
//$areas = $locations->get_areas();

$services = new APM_Services();
$services = $services->get_services();

$practitioners = new APM_Practitioners();
$practitioners = $practitioners->get_practitioners();

$posted = $filtered['posted'];
$radius = $posted['radius'];
$geolocate = $posted['geolocate'];
$service_id = $posted['service'];
$practitioner_id = $posted['practitioner'];

$radiusRange = array(0.5, 5, 10, 15, 25, 50, 100, 200, 500);
?>

<div class="apm__filters">
    <form id="apm_filters">
        <article class="filter-all">
            <a href="#" class="filters-reset">See all</a>
        </article>

        <article class="is-input-text tooltip" title="Enter your area/postcode to find locations near you">
            <input type="text" placeholder="Enter your area/postcode" id="geolocate" name="geolocate" value="<?php echo $geolocate; ?>" />
        </article>

        <article class="is-select is-radius">
            <select name="apm_radius" class="chosen-select colored">
                <option value="874">Any distance</option>
                <?php 
                    foreach($radiusRange as $rad):
                    $selected_radius = $radius && $radius == $rad ? ' selected' : '';
                ?>
                    <option value="<?php echo $rad; ?>"<?php echo $selected_radius; ?>><?php echo $rad; ?> miles</option>
                <?php endforeach; ?>
            </select>
        </article>
        
        <?php if($services): ?>
            <article class="is-select">
                <select name="apm_service" class="chosen-select colored">
                    <option value="">Any Service</option>
                    <?php
                        foreach($services as $service):
                        $selected_service = $service_id && $service_id == $service ? ' selected' : '';
                    ?>
                        <option value="<?php echo $service; ?>"<?php echo $selected_service; ?>><?php echo get_the_title($service); ?></option>
                    <?php endforeach; ?>
                </select>
            </article>
        <?php endif; ?>

        <?php if($practitioners): ?>
            <article class="is-select">
                <select name="apm_practitioner" class="chosen-select colored">
                    <option value="">Any Practitioner</option>
                    <?php
                        foreach($practitioners as $practitioner):
                        $practitioner = new APM_Practitioner($practitioner);
                        $selected_practitioner = $practitioner_id && $practitioner_id == $practitioner->id() ? 'selected' : '';
                    ?>
                        <option value="<?php echo $practitioner->id(); ?>"<?php echo $selected_practitioner; ?>><?php echo $practitioner->title(); ?></option>
                    <?php endforeach; ?>
                </select>
            </article>
        <?php endif; ?>
    </form>
</div>