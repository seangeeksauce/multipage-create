<?php

/**
 * Plugin Name: Multipage create
 * Plugin URI: #
 * Description: Simple plugin to create multiple wordpress pages from a single options panel page and submit.
 * Version: 1.0.0
 * Author: Sean Fitzpatrick
 * Author URI: 
 * License: GPL2
 */
class Multipagecreate {

    static $hidden_field_name = 'multipage_hidden_field';
    static $array_field_name = 'multipage_array';
    static $table_header_array = array('Page Name', 'Page URL');
    public static $match_msg;
    public static $list_page_info;
    public static $table_headers;
    public static $userPageArray = array();
    public static $error_array = array();
    private static $valid_submission = false;
    private static $validated = false;
    
    

    public static function Init() {
        add_action('admin_menu', array(self::class, 'multipage_plugin_menu'));
    }

    public static function multipage_plugin_menu() {
        add_options_page('Create array of pages', 'Multipage Create', 'manage_options', 'geek-multipage-create', array(self::class, 'multipage_create_options'));
    }

    public static function multipage_create_options() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Sorry! You dont have the correct user access rights to use this plugin!'));
        }


        // Setup data
        self::controls();
        
        // Insert pages into database
        self::insert_pages_db();
        
        // set view
        self::get_view();
    }

    public static function controls() {

        if (isset($_POST[self::$hidden_field_name]) && $_POST[self::$hidden_field_name] == '1') {
            self::$valid_submission = true;

            // Read their posted value
            $userPageArray_initial = $_POST[self::$array_field_name];

            if (preg_match("/[,]/", $userPageArray_initial)) :
                self::$userPageArray = explode(',', $userPageArray_initial);
                self::$match_msg = "Your String was valid - inserting pages into database.";

                // setup creation info and sets validation property for insert
                self::set_creation_info();


            

            else:
                self::$match_msg = "No commas in string..... Please use commas to seperate your values.";

            endif;
        }
    }

    public static function set_creation_info() {
        $headers = "<thead><tr>";
        for ($i = 0; $i < count(self::$table_header_array); $i++) {
            $headers .= sprintf('<th class="manage-column column-title">%s</th>', self::$table_header_array[$i]);
        }
        $headers .= "</tr></thead>";
        self::$table_headers = $headers;

        if (!empty(self::$userPageArray) && is_array(self::$userPageArray)):
            $i = 0;
            foreach (self::$userPageArray as $pageName) {
                $trimmed = trim($pageName);
                if (trim($trimmed == "")):
                    unset($pageName);
                    continue;
                else:
                    self::$list_page_info[$i]['pagename'] = $pageName;
                    
                    $i++;
                endif;
            }

            //sets validation property if array is valid and can be inserted
            if (!empty(self::$userPageArray) && is_array(self::$userPageArray)) {
                if (!empty(self::$list_page_info) && is_array(self::$list_page_info)):
                    
                self::$validated = true;
                 endif;
            }

        endif;
    }

    public static function display_creation_info() {
        if (self::$valid_submission === true):
            if (!empty(self::$list_page_info) && is_array(self::$list_page_info)) {


                $list = '<table class="wp-list-table widefat fixed posts">';
                $list .= self::$table_headers;
                $list .= " <tbody>";
                $alternateColor = "";
                $i = 0;

                foreach (self::$list_page_info as $details) :
                    $alternateColor = $i % 2 == 1 ? "" : "alternate";
                    $list .= sprintf('<tr class="%s"> <td>%s</td><td>%s</td> </tr>', $alternateColor, $details['pagename'], $details['pageurl']);
                    $i++;
                endforeach;
                $list .= " </tbody> </table>";
                return $list;
            }
        endif;
    }

    private static function insert_pages_db() {
        if (self::$valid_submission === true && self::$validated === true):
            // running database insert
            error_log("inserting into database!");
            $i = 0;
            foreach (self::$userPageArray as $page) {
                // Create post object
                $my_post = array(
                    'post_title' => wp_strip_all_tags($page),
                    //'post_content'  => $_POST['post_content'],
                    'post_status' => 'publish',
                    'post_type' => 'page'
                        //'post_author'   => 1,
                        //'post_category' => array( 8,39 )
                );
                // Insert the post into the database
                $post_data = wp_insert_post($my_post, true);
//                wp_insert_post($my_post, true);
                self::$list_page_info[$i]['pageurl'] = get_permalink( $post_data);
                $i++;
            }
        endif;
    }
 
  
    
    private static function view_updated_msg() {

        if (self::$valid_submission === true && self::$validated === true):
            echo '<div class="updated"><p><strong>Pages Created.</strong></p></div>';
        endif;
    }

    public static function get_view() {
        self::view_updated_msg();
        require __DIR__ . '/view.php';
    }

}

Multipagecreate::Init(); //Call my static constructor
