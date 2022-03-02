<?php
/**
 * iCal Global Search Filters.
 *
 * @author  Various
 * @package Advanced Physio Module
 *
*/

// get clinics returned by radius
$clinics_dropdown = apm_clinics_by_radius($product_id, $lat, $lng, $radius);

// get practitioners return by radius (also filtered by product)
$practitioners = apm_practitioners_by_radius($clinics_dropdown, $product_id);

// get practitioners return by radius (also filtered by product)
$gender_dropdown = apm_genders_by_radius($practitioners);
?>
<div class="apm__global__search__advanced">
    <h5>Advanced filters <i class="ion-ios-arrow-down"></i></h5>

    <div class="advanced__filters on">

        <div class="advanced__filter">
            <label>Date</label>
            <input type="text" class="apm__filter__date" <?php if(isset($date) && !empty($date)) { echo 'value="'.date('j M Y', $date).'"'; } else { echo ' placeholder="Select a date"'; } ?>>
        </div><!-- advanced__filter -->

        <div class="advanced__filter">
            <label>Practitioner gender</label>
            <select class="chosen-select apm__filter__select">
                <option value="all">Any gender</option>
                <?php foreach($gender_dropdown as $gender): ?>
                    <option value="<?php echo $gender['slug'] ?>"<?php if(isset($practitioner_gender) && $practitioner_gender == ['slug']) { echo ' selected="selected"'; } ?>><?php echo $gender['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div><!-- advanced__filter -->

        <div class="advanced__filter">
            <label>Clinic</label>
            <select class="chosen-select apm__filter__select" data-placeholder="Filter by clincs">
                <option value="">Any Clinic</option>
                <?php foreach($clinics_dropdown as $clinic): ?>
                
                    <option value="<?php echo $clinic['ID']; ?>" <?php if(isset($clinic_ids) && in_array($clinic['ID'], $clinic_ids)) { echo ' selected'; } ?>><?php echo $clinic['post_title']; ?></option>

                <?php endforeach; ?>

            </select>
        </div><!-- advanced__filter -->

        <div class="advanced__filter">
            <label>Practitioner</label>
            <select class="chosen-select apm__filter__select" data-placeholder="Filter by practitioners">
                <option value="">Any Practitioner</option>
                <?php foreach($practitioners as $practitioner): ?>
                
                    <option value="<?php echo $practitioner['ID']; ?>"<?php if(isset($practitioner_ids) && in_array($practitioner['ID'], $practitioner_ids)) { echo ' selected'; } ?> data-gender="<?php echo $practitioner['gender'] ?>"><?php echo get_the_title($practitioner['ID']); ?></option>
                
                <?php endforeach; ?>
            </select>
        </div><!-- advanced__filter -->

        <div class="advanced__filter apply">
            <label>&nbsp;</label>
            <a href="#" class="apm__apply__filters" data-action="filter">Apply</a>
        </div><!-- advanced__filter -->

    </div><!-- advanced__filters -->

</div><!-- apm__global__search__advanced -->
