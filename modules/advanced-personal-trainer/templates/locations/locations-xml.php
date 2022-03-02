<?php
$locations = explode(',', $_GET['locations']);

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<markers>';
?>
    <?php
        foreach($locations as $location_id):
        
        $location = new APM_Location($location_id);

        $name = htmlentities(utf8_decode($location->title()));
        $permalink = $location->url();
        $lat = $location->location('lat');
        $lng = $location->location('lng');
        $address = htmlentities(utf8_decode($location->location('address')));

        // Image
        // if(get_field('page_banner')) {
        //     $attachment_id = get_field('page_banner');
        // } else {
        //     $attachment_id = get_post_thumbnail_id();
        // }

        // $photo = vt_resize($attachment_id, '', 600, 500, true);

        // Address
        
    ?>
        <marker name="<?php echo $name; ?>" permalink="<?php echo $permalink; ?>" lat="<?php echo $lat; ?>" lng="<?php echo $lng; ?>" address="<?php echo $address; ?>" />
    <?php endforeach; ?>
</markers>