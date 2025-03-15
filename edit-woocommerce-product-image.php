<?php
/**
 * Plugin Name: Edit WooCommerce Product Images
 * Plugin URI: https:/miladjafarigavzan.ir
 * Description: Edit WooCommerce product gallery images' Alt Text, Title, Caption, and Description directly from the admin product page.
 * Version: 1.0
 * Author: milad jafari agvzan
 * Author URI:  https:/miladjafarigavzan.ir
 * License: GPL
 * Text Domain: edit-woocommerce-product-images
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Add a custom metabox to WooCommerce product pages
function eiwi_add_image_meta_box() {
    add_meta_box(
        'eiwi_image_meta_box',
        __('ویرایش متا دیتای گالری تصاویر', 'edit-woocommerce-product-images'),
        'eiwi_render_image_meta_box',
        'product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'eiwi_add_image_meta_box');

// Render the custom metabox
function eiwi_render_image_meta_box($post) {
    $gallery_images_ids = get_post_meta($post->ID, '_product_image_gallery', true);
    $gallery_images_ids = explode(',', $gallery_images_ids);

    echo '<div id="eiwi-gallery-meta">';

    if (!empty($gallery_images_ids)) {
        foreach ($gallery_images_ids as $image_id) {
            $image = wp_get_attachment_image_src($image_id, 'thumbnail');

            if ($image) {
                $alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                $title = get_the_title($image_id);
                $caption = wp_get_attachment_caption($image_id);
                $description = get_post_field('post_content', $image_id);

                echo '<div class="eiwi-image-meta" style="margin-bottom: 10px;">';
                echo '<img src="' . esc_url($image[0]) . '" style="max-width: 100px; margin-bottom: 5px;" />';
                echo '<input type="text" name="eiwi_alt[' . esc_attr($image_id) . ']" value="' . esc_attr($alt) . '" placeholder="متن جایگزین" style="width: 100%; margin-bottom: 5px;" />';
                echo '<input type="text" name="eiwi_title[' . esc_attr($image_id) . ']" value="' . esc_attr($title) . '" placeholder="عنوان" style="width: 100%; margin-bottom: 5px;" />';
                echo '<input type="text" name="eiwi_caption[' . esc_attr($image_id) . ']" value="' . esc_attr($caption) . '" placeholder="توضیحات کوتاه" style="width: 100%; margin-bottom: 5px;" />';
                echo '<textarea name="eiwi_description[' . esc_attr($image_id) . ']" placeholder="توضیحات مطول " style="width: 100%;">' . esc_textarea($description) . '</textarea>';
                echo '</div>';
            }
        }
    } else {
        echo '<p>' . __('هیچ تصویری در گالری تصاویر این محصول پیدا نشد', 'edit-woocommerce-product-images') . '</p>';
    }

    echo '</div>';
}

// Save custom image metadata
function eiwi_save_image_meta($post_id) {
    if (isset($_POST['eiwi_alt']) && is_array($_POST['eiwi_alt'])) {
        foreach ($_POST['eiwi_alt'] as $image_id => $alt) {
            update_post_meta($image_id, '_wp_attachment_image_alt', sanitize_text_field($alt));
        }
    }

    if (isset($_POST['eiwi_title']) && is_array($_POST['eiwi_title'])) {
        foreach ($_POST['eiwi_title'] as $image_id => $title) {
            wp_update_post([
                'ID' => intval($image_id),
                'post_title' => sanitize_text_field($title),
            ]);
        }
    }

    if (isset($_POST['eiwi_caption']) && is_array($_POST['eiwi_caption'])) {
        foreach ($_POST['eiwi_caption'] as $image_id => $caption) {
            update_post_meta($image_id, '_wp_attachment_image_caption', sanitize_text_field($caption));
        }
    }

    if (isset($_POST['eiwi_description']) && is_array($_POST['eiwi_description'])) {
        foreach ($_POST['eiwi_description'] as $image_id => $description) {
            wp_update_post([
                'ID' => intval($image_id),
                'post_content' => sanitize_textarea_field($description),
            ]);
        }
    }
}
add_action('save_post', 'eiwi_save_image_meta');
