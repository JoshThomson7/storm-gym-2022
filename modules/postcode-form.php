<?php
/**
 * Postcode form
 */

/**
 * Outputs postcode forms
 * 
 * @param String $size | Accepts: tiny / ''
 */
function postcode_form($size = '') {
    
    ?>
    <div class="postcode__form <?php echo $size; ?>">
        <input type="text" placeholder="Your postcode">
        <button class="button tertiary pulse">Go</button>
    </div>
    <?php
}