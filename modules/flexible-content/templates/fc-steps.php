<?php
/**
 * Steps
 */

$steps = get_sub_field('steps');
$cta = get_sub_field('cta');

$hide_form = get_field('landing_hide_form') ?? false;
?>

<div class="fc_steps--wrapper">

    <?php
        $step_num = 1;
        foreach($steps as $step): ?>
            <article>
                <div class="inner">
                    <div class="fc_step-count">
                        <figure><?php echo $step_num; ?></figure>
                    </div>
                    
                    <div class="fc_step-content">
                        <?php if($step['heading']): ?><h3><?php echo $step['heading']; ?></h3><?php endif; ?>
                        <?php if($step['content']) { echo $step['content']; } ?>
                    </div>
                </div>
            </article>
    <?php $step_num++; endforeach; ?>

    <?php if($cta && !$hide_form): ?>
        <article class="fc_step-cta">
            <div class="inner">
                <div class="fc_step-content">
                    <h3>Get started below with your free, no obligation quote now.</h3>

                    <?php postcode_form('tiny'); ?>
                </div>
            </div>
        </article>
    <?php endif; ?>

</div>