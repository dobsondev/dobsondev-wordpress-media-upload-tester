<?php
/**
 * Plugin Name: DobsonDev WordPress Media Library Upload Tester
 * Plugin URI: http://dobsondev.com
 * Description: A simple plugin for testing/illustrating you can use the WordPress media library in your own plugin/theme.
 * Version: 0.666
 * Author: Alex Dobson
 * Author URI: http://dobsondev.com/
 * License: GPLv2
 *
 * Copyright 2014  Vital Effect  (email : alex@dobsondev.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


/* Define the images table name */
define( 'DD_IMG_UPLOAD_TBL_NAME', 'dobdev_image_upload_tester' );


/* Create database table for the ads */
function dobdev_image_upload_tester_create_database_table() {
  global $wpdb;

  // Check if the ads table already exists in the database
  if ( $wpdb->get_var( "SHOW TABLES LIKE '" . DD_IMG_UPLOAD_TBL_NAME . "'" ) != DD_IMG_UPLOAD_TBL_NAME )  {
    $table_sql = "CREATE TABLE " . DD_IMG_UPLOAD_TBL_NAME . " (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `imagesrc` varchar(255) NOT NULL,
      UNIQUE KEY id (id)
      );";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $table_sql );
  }
}
register_activation_hook( __FILE__, 'dobdev_image_upload_tester_create_database_table' );


/* Add the media upload tester uploader script and the CSS */
function register_dobdev_image_upload_tester_scripts() {
   wp_enqueue_media();
   wp_register_script('dobsondev-wordpress-media-upload-tester-js', plugins_url( 'dobsondev-wordpress-media-upload-tester-script.js' , __FILE__ ), array('jquery'));
   wp_enqueue_script('dobsondev-wordpress-media-upload-tester-js');
   wp_enqueue_style( 'dobdev-image-upload-tester-css', plugins_url( 'dobsondev-wordpress-media-upload-tester.css' , __FILE__ ) );
}
add_action('admin_enqueue_scripts', 'register_dobdev_image_upload_tester_scripts');


/* Create the administration menu link */
function dobdev_image_upload_tester_admin_menu() {
  add_menu_page( 'Upload Tester', 'Upload Tester', 'manage_options', 'dobdev-image-upload-tester', 'dobdev_image_upload_tester_admin_menu_page', 'dashicons-clipboard', 50 );
}
add_action( 'admin_menu', 'dobdev_image_upload_tester_admin_menu' );


/* The actual administration page */
function dobdev_image_upload_tester_admin_menu_page() {
  global $wpdb;

  if ( !empty( $_POST ) ) {
    // Should you want to see the output - simply remove the comments from the line bellow
    // var_dump($_POST);
  }

  // Add an image
  if ( !empty( $_POST['submit-image'] )
    && !empty( $_POST['image-url'] ) ) {
    $image_src = $_POST['image-url'];
    $wpdb->insert( DD_IMG_UPLOAD_TBL_NAME, array( 'imagesrc' => $image_src ), array( '%s' ) );
  }

  // Delete an image
  if ( !empty( $_POST['delete-image'] )
    && !empty( $_POST['image-id'] ) ) {
    $image_id = $_POST['image-id'];

    $wpdb->delete( DD_IMG_UPLOAD_TBL_NAME, array( 'id' => $image_id ), array( '%d' ) );
  }

  // Get all current images
  $images = $wpdb->get_results( "SELECT * FROM " . DD_IMG_UPLOAD_TBL_NAME . " ORDER BY id DESC", OBJECT );
  ?>
  <h1> Add an Image </h1>
  <hr />
  <p>
    Here you can upload images and add their URL path to the custom database table we've created for this plugin. This is meant to illustrate how you could reuse the images again by getting their path from the database.
  </p>
  <div id="image-preview"></div>
  <form method="POST">
    <input id="image-url" type="text" name="image-url" />
    <input id="upload-button" class="button" type="button" value="Upload Image" />
    <br />
    <input class="button" type="submit" name="submit-image" value="Submit" />
  </form>

  <!-- SPACE IT OUT -->
  <br /><br /><br />

  <h1> Current Images Image </h1>
  <hr />
  <p>
    Please note that deleting these images will NOT remove them from the Media Library - it will only remove them from the custom database table we've created for this plugin. Also please note that the images shown below will be smaller than the originals. They have been scaled down to a maximum width of 250px.
  </p>
  <!-- Display each image -->
  <?php foreach( $images as $image ) { ?>
    <img class="dobdev-image-upload-tester" src="<?php echo $image->imagesrc; ?>" />
    <form method="POST">
      <input type="hidden" name="image-id" value="<?php echo $image->id; ?>" />
      <input class="button" type="submit" name="delete-image" value="Delete" />
    </form>
    <hr />
  <?php } ?>
  <?php
}

?>