<?php
/*
 * Plugin Name: BP Default user notifications
 * Plugin URI: https://github.com/utan/bp-default-user-notifications
 * Description: Change default notification settings for users, overrides current settings for Buddypress..
 * Tags: Buddypress, Buddypress notifications, Stop Buddypress flooading emails, Buddypress missing options
 * Version: 1.0.0
 * Author: Neumann S Valle Argueta
 * Author URI: https://www.linkedin.com/in/neumann-valle-abb72682/
 * License: GPLv2 or later
 */
if (! defined("ABSPATH")) {

    exit(); // Exit if accessed directly
}

if (! class_exists("UserNotification")) {

    // get our class
    include_once (plugin_dir_path(__FILE__) . "/class/UserNotification.php");

    add_action("admin_menu", function () {

        if (current_user_can("activate_plugins")) {

            add_menu_page("BP Default user notifications", "BP default user notifications", "read", "Set default user notifications for BP", array(
                "UserNotification",
                "createBPcheckboxs"
            ));

            // add our javascript stuff
            add_action("admin_enqueue_scripts", "my_enqueue");

            function my_enqueue($hook)
            {
                $ajax_nonce = wp_create_nonce("bp-default-user-notification");

                wp_enqueue_script("ajax-script", plugins_url("client/js/bp-default-user-notifications.js", __FILE__), array(
                    "jquery"
                ), UserNotification::$ver);

                wp_localize_script("ajax-script", "ajax_object", array(
                    "ajax_url" => admin_url("admin-ajax.php"),
                    "nonce" => $ajax_nonce
                ));

                wp_enqueue_style("main-styles", plugins_url("client/css/bp-user-notifications-style.css", __FILE__), array(), UserNotification::$ver);
            }
        }
    });

    add_action("user_register", function ($user_id) {

        $notifications = UserNotification::getBPnotifications();

        for ($i = 0; $i < count($notifications); $i ++) {

            bp_update_user_meta($user_id, $notifications[$i], "no");
        }
    });

    add_action("wp_ajax_bp_default_notifications", array(
        "UserNotification",
        "init"
    ));
}
