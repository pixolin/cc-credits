<?php
/*
Plugin Name: CC Credits
Description: Adds a meta box for credits for Creative Commons content.
Author: Bego Mario Garde
Author URI: http://garde-medienberatung.de
Version: 1.0
License: GPL2
Text Domain: cc-credits
Domain Path: /languages/
*/

/*

    Copyright (C) 2014  Bego Mario Garde <pixolin@gmx.com>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// No direct execution of plugin
defined('ABSPATH') or die();


load_plugin_textdomain( 'cc-credits', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


// require file with meta box fields
require_once('cc-fields.php');

// Add the Meta Box
function ccc_add_metabox() {

    // define Post Types that will show Meta Box
    $post_types = array( 'post', 'page' ); //could be extended for Custom Post Types

    foreach( $post_types as $post_type ) {
        add_meta_box(
            'ccc_meta_box', // $id
            'Creative Commons Credits', // $title
            'ccc_meta_box', // $callback
            $post_type, // $page
            'normal', // $context
            'high' // $priority
        );
    }
}

add_action('add_meta_boxes', 'ccc_add_metabox');

// Callback function for meta box – shows the form in Back End
function ccc_meta_box() {

    global $custom_meta_fields, $post;

    // Use nonce for verification
    //echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
    wp_nonce_field( basename( __FILE__ ), 'custom_meta_box_nonce' );

    // Begin the field table and loop
    echo '<table class="form-table">';
    foreach ($custom_meta_fields as $field) {
        // get value of this field if it exists for this post
        $meta = get_post_meta($post->ID, $field['id'], true);
        // begin a table row with
        echo '<tr>
                <th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
                <td>';
                switch($field['type']) {

                    // text
                    case 'text':
                    echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />';
                    break;

                    // select
                    case 'select':
                    echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
                    foreach ($field['options'] as $option) {
                        echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
                    }
                    break;

                } //end switch
        echo '</td></tr>';
    } // end foreach
    echo '</table>'; // end table

    echo ccc_get_post_custom( $post->ID );
}

// Save the Data
function ccc_save_meta($post_id) {
    global $custom_meta_fields;

    // verify nonce
    if (!isset($_POST['custom_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__)))
        return $post_id;
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;
    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
    }

    // loop through fields and save the data
    foreach ($custom_meta_fields as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], wp_kses( $new, $allowed_html ) );
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    } // end foreach
}
add_action('save_post', 'ccc_save_meta');

/*
 * If a license was chosen, return a line with name of image, link to source,
 * copyright date and author, Creative Commons License, link to human readable
 * summary of license and license version.
 */
function ccc_get_post_custom($post_id) {


   $ccc = get_post_custom($post_id);
    if(!empty($ccc['ccc_license'][0])) {
        $ccc_url     = $ccc['ccc_url'][0];
        $ccc_image   = $ccc['ccc_image'][0];
        $ccc_year    = $ccc['ccc_year'][0];
        $ccc_author  = $ccc['ccc_author'][0];
        $ccc_license = $ccc['ccc_license'][0];
        $ccc_version = $ccc['ccc_version'][0];

        //check if URL contains http:// or https:// and add, if it doesn't
        $ccc_url_scheme = parse_url( $ccc_url );
        if ( empty( $ccc_url_scheme['scheme'] ) ) {
            $ccc_url = 'http://' . $ccc_url;
        }



        return '<hr>'.
                __('Image', 'cc-credits').': <a href="'.esc_url($ccc_url).'">'.$ccc_image.'</a>
                &copy' .$ccc_year.' '.$ccc_author.',
                <a href="http://creativecommons.org/licenses/'.strtolower($ccc_license).'/'.$ccc_version.'" target="_blank" rel="nofollow">CC '.$ccc_license.'</a>,
                '.$ccc_version;
    }

}

// Provide Credit under each Blog Post and Page, only for main query.
function ccc_credit($content) {
    if(is_main_query()) {
      return $content . ccc_get_post_custom($post_id);
    }
}
add_filter('the_content', 'ccc_credit');