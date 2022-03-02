<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <title><?php wp_title( ' - ', true, 'right' ); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <?php wp_head(); ?>

    <script src="https://player.vimeo.com/api/player.js"></script>
</head>

<body <?php body_class(); ?>>

    <?php include get_stylesheet_directory().'/modules/mega-menu-mobile.php'; ?>

	<div id="page">
		<header class="header">

            <?php if(get_field('sitewide_notice_enable', 'option')): ?>
                <div class="header--site__wide__notice">
                    <div class="max__width">
                        <?php the_field('sitewide_notice', 'option'); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="header__main">

                <div class="max__width">
                    
                    <div class="header__main--left">

                        <a href="#nav_mobile" class="burger__menu">
                            <i class="fal fa-bars"></i>
                        </a>

                        <div class="logo">
                            <a href="<?php echo esc_url(home_url()); ?>" title="<?php bloginfo('name'); ?>">
                               <img src="<?php echo esc_url(get_stylesheet_directory_uri().'/img/jd-logo.png'); ?>" alt="">
                            </a>
                        </div><!-- logo -->

                        <nav class="header__nav">
                            <?php include get_stylesheet_directory().'/modules/mega-menu.php'; ?>
                        </nav><!-- header__nav -->

                    </div><!-- left -->

                    <div class="header__main--right">
                        

                        <nav class="header__actions">
                            <ul>
                                <li>
                                    <a href="#" class="button primary medium"><span>Book a session</span></a>
                                </li>
                            </ul>
                        </nav><!-- header__nav -->
                    </div><!-- right -->

                </div><!-- max__width -->
            </div><!-- header__main -->
		</header><!-- header -->
