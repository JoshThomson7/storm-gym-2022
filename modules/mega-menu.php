<nav class="mega-menu" role="navigation">
    <ul>
        <?php while(have_rows('mega_menu', 'option')) : the_row(); ?>
            <li class="<?php if(get_sub_field('menu_item_columns')): ?>has-columns<?php endif; ?>">
                <a href="<?php the_sub_field('menu_item_link'); ?>">
                    <?php
                        if(get_sub_field('menu_cart_count') && $woocommerce->cart->cart_contents_count > 0) {
                            echo '<span class="cart__count">'.$woocommerce->cart->cart_contents_count.'</span>';
                        }
                    ?>

                    <?php if(get_sub_field('menu_item_icon')): ?><span class="nav__icon <?php the_sub_field('menu_item_icon'); ?>"></span><?php endif; ?>

                    <?php the_sub_field('menu_item_label'); ?>
                </a>

                <?php if(have_rows('menu_item_columns')): ?>
                    <div class="panel">
                        <?php while(have_rows('menu_item_columns')) : the_row(); ?>
                            <?php
                                // -------------- COLUMN --------------
                                if(get_row_layout() == 'submenu_column'):

                                $column_subs = get_sub_field('column_subs');
                            ?>
                                <div class="panel__column<?php if(get_sub_field('column_icon')): ?> has__icon<?php endif; ?>">
                                    <?php if(get_sub_field('column_name')): ?>
                                        <h3>
                                            <?php
                                                if(get_sub_field('column_name_link')):

                                                    $target = '';
                                                    if(get_sub_field('column_name_link_target')) {
                                                        $target = ' target="_blank"';
                                                    }
                                            ?>
                                                <a href="<?php the_sub_field('column_name_link'); ?>"<?php echo $target; ?>>
                                            <?php endif; ?>

                                            <?php if(get_sub_field('column_icon')): ?><span class="<?php the_sub_field('column_icon'); ?>"></span><?php endif; ?>
                                            <?php the_sub_field('column_name'); ?>

                                            <?php if(get_sub_field('column_name_link')): ?></a><?php endif; ?>
                                        </h3>
                                    <?php endif; ?>

                                    <ul class="submenu">
                                        <?php
                                            foreach($column_subs as $column_sub):

                                                $post_img = '';
                                                $post_title = '';
                                                $post_url = '';

                                                if($column_sub['type'] == 'post') {

                                                    $post_id = $column_sub['post'];
                                                 
                                                    $attachment_id = get_field('page_banner', $post_id);
                                                    if($attachment_id) {
                                                        $post_img = vt_resize($attachment_id, '', 150, 150, true);
                                                    }

                                                    $post_title = get_the_title($post_id);
                                                    $post_url = get_permalink($post_id);

                                                } else {

                                                    $custom = $column_sub['custom'];
                                                    $custom_img = $custom['image'];

                                                    $post_title = $custom['label'];
                                                    $post_url = $custom['url'];

                                                    if($custom_img) {
                                                        $post_img = vt_resize($custom_img, '', 150, 150, true);
                                                    }

                                                }
                                        
                                        ?>
                                            <li>
                                                <a href="<?php echo $post_url; ?>">
                                                    <?php if($post_img): ?>
                                                        <figure><img src="<?php echo $post_img['url']; ?>" alt="<?php echo $post_title; ?>"></figure>
                                                    <?php endif; ?>
                                                    <?php echo $post_title; ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>

                                    

                                    <?php if(get_sub_field('column_button_link')): ?>
                                        <a href="<?php the_sub_field('column_button_link'); ?>" title="<?php the_sub_field('column_button_label'); ?>" class="column__button"><?php the_sub_field('column_button_label'); ?></a>
                                    <?php endif; ?>
                                </div><!-- panel__column -->

                            <?php
                                // -------------- Icon Links --------------
                                elseif(get_row_layout() == 'icon_links'):
                                    $column_subs = get_sub_field('column_subs');
                                ?>
                                    <div class="panel__column panel__icon-links<?php if(get_sub_field('column_icon')): ?> has__icon<?php endif; ?>">
                                        <?php if(get_sub_field('column_name')): ?>
                                            <h3>
                                                <?php
                                                    if(get_sub_field('column_name_link')):
    
                                                        $target = '';
                                                        if(get_sub_field('column_name_link_target')) {
                                                            $target = ' target="_blank"';
                                                        }
                                                ?>
                                                    <a href="<?php the_sub_field('column_name_link'); ?>"<?php echo $target; ?>>
                                                <?php endif; ?>
    
                                                <?php if(get_sub_field('column_icon')): ?><span class="<?php the_sub_field('column_icon'); ?>"></span><?php endif; ?>
                                                <?php the_sub_field('column_name'); ?>
    
                                                <?php if(get_sub_field('column_name_link')): ?></a><?php endif; ?>
                                            </h3>
                                        <?php endif; ?>
    
                                        <ul class="submenu icon-links">
                                            <?php
                                                foreach($column_subs as $column_sub):
                                                    $label = $column_sub['label'];
                                                    $url = $column_sub['url'];
                                                    $icon = $column_sub['icon'];
                                            ?>
                                                <li>
                                                    <a href="<?php echo $url; ?>">
                                                        <?php if($icon): ?>
                                                            <figure><i class="<?php echo $icon; ?> fa-fw"></i></figure>
                                                        <?php endif; ?>
                                                        <?php echo $label; ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
    
                                        <?php if(get_sub_field('column_button_link')): ?>
                                            <a href="<?php the_sub_field('column_button_link'); ?>" title="<?php the_sub_field('column_button_label'); ?>" class="column__button"><?php the_sub_field('column_button_label'); ?></a>
                                        <?php endif; ?>
                                    </div><!-- panel__column -->

                            <?php
                                // -------------- CONTACT --------------
                                elseif(get_row_layout() == 'contact'):

                                    $contacts = get_sub_field('contact');

                            ?>
                                <div class="panel__column panel__contact<?php if(get_sub_field('column_icon')): ?> has__icon<?php endif; ?>">

                                    <?php if(get_sub_field('column_name')): ?>
                                        <h3>
                                            <?php
                                                if(get_sub_field('column_name_link')):

                                                    $target = '';
                                                    if(get_sub_field('column_name_link_target')) {
                                                        $target = ' target="_blank"';
                                                    }
                                            ?>
                                                <a href="<?php the_sub_field('column_name_link'); ?>"<?php echo $target; ?>>
                                            <?php endif; ?>

                                            <?php if(get_sub_field('column_icon')): ?><span class="<?php the_sub_field('column_icon'); ?>"></span><?php endif; ?>
                                            <?php the_sub_field('column_name'); ?>

                                            <?php if(get_sub_field('column_name_link')): ?></a><?php endif; ?>
                                        </h3>
                                    <?php endif; ?>

                                    <?php if($contacts): ?>
                                        <div class="contacts">
                                            <?php foreach($contacts as $contact): ?>
                                                <article>
                                                    <h5><?php echo $contact['name']; ?></h5>
                                                    <a href="tel:<?php echo $contact['phone']; ?>"><i class="fal fa-phone"></i> <?php echo $contact['phone']; ?></a>
                                                    <span><i class="fal fa-envelope"></i> <?php echo hide_email($contact['email']); ?></span>
                                                </article>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                </div><!-- panel__column -->

                            <?php endif; ?>

                        <?php endwhile; ?>
                    </div><!-- panel -->
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
    </ul>
</nav>
