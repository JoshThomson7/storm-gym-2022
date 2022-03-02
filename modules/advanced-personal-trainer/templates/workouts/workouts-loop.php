<?php
/**
 * APT Workouts Loop
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if($get_workouts):
    foreach($get_workouts as $workout_id):
        $workout = new APT_Workout($workout_id);
        $workout_image = $workout->image(800, 800);
        $levels = $workout->get_terms('workout_level', 'id=>name');
        $good_for = $workout->get_terms('workout_good_for', 'id=>name');
        $equipment = $workout->get_terms('workout_equipment', 'id=>name');
    ?>
        <article class="card workout w-1-3">
            <div class="card__inner">
                <?php if(!empty($workout_image) && isset($workout_image['url'])): ?>
                    <a href="<?php echo $workout->url(); ?>" title="<?php echo $workout->title(); ?>">
                        <figure style="background-image: url(<?php echo $workout_image['url']; ?>);"></figure>
                    </a>
                <?php endif; ?>

                <div class="workout--info pad-20">
                    <h2><a href="<?php echo $workout->url(); ?>" title="<?php echo $workout->title(); ?>"><?php echo $workout->title(); ?></a></h2>

                    <ul>
                        <li class="tooltip" title="Duration and level">
                            <i class="fal fa-fw fa-stopwatch"></i>
                            <?php echo $workout->get_workout('totalTimeMinutes'); ?> min
                            <?php
                                if(!empty($levels)) {
                                    $level_output = array();
                                    foreach($levels as $level) {
                                        $level_output[] = $level;
                                    }
                                    echo '&mdash; '.join(', ', $level_output);
                                }
                            ?>
                        </li>
                        
                        <?php if(!empty($good_for)): ?>
                            <li class="tooltip" title="Good for">
                                <i class="fal fa-fw fa-running"></i>
                                <?php
                                    $good_for_output = array();
                                    foreach($good_for as $good_for_item) {
                                        $good_for_output[] = $good_for_item;
                                    }
                                    echo join(', ', $good_for_output);
                                ?>
                            </li>
                        <?php endif; ?>
                        
                        <?php if(!empty($equipment)): ?>
                            <li class="tooltip" title="Advised equipment">
                                <i class="fal fa-fw fa-dumbbell"></i>
                                <?php
                                    $equipment_output = array();
                                    foreach($equipment as $equipment_item) {
                                        $equipment_output[] = $equipment_item;
                                    }
                                    echo join(', ', $equipment_output);
                                ?>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="workout--footer">
                    
                </div>
            </div>
        </article>
    <?php endforeach; ?>

<?php else: ?>
    <div class="apt-workouts--not-found">
        <i class="fal fa-dumbbell"></i>
        <h2>No workouts found</h2>
        <p>Try searching for something else</p>
    </div>
<?php endif; ?>