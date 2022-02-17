<?php
/**
 * LifterLMS Loop main template
 *
 * @since 5.8.0
 * @version 5.8.0
 */

defined( 'ABSPATH' ) || exit;
?>

<?php do_action( 'lifterlms_before_main_content' ); ?>

<?php if ( apply_filters( 'lifterlms_show_page_title', true ) ) : ?>

<h1 class="page-title"><?php lifterlms_page_title(); ?></h1>

<?php endif; ?>

<?php do_action( 'lifterlms_archive_description' ); ?>

<?php
/**
 * Hook: lifterlms_loop
 *
 * @hooked lifterlms_loop - 10
 */
do_action( 'lifterlms_loop' );
?>

<?php do_action( 'lifterlms_after_main_content' ); ?>

<?php do_action( 'lifterlms_sidebar' ); ?>
