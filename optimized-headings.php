<?php
/*
Plugin Name: Optimized Headings
Plugin URI: http://www.bluetentmarketing.com/
Description: A plugin that provides a meta box and a filter for overriding the the_title when used in a template
Version: 1.0
Author: EthanHinson
Author URI: http://www.bluetentmarketing.com/
Author Email: ethan@bluetent.com
License:

  Copyright 2013 Blue Tent Marketing (ethan@bluetent.com)

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

class OptimizedHeadings {

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {
            
            // Load plugin text domain
            add_action( 'init', array( $this, 'plugin_textdomain' ) );            
            //Add META boxes
            add_action( 'add_meta_boxes', array( $this, 'add_boxes' ) );
            add_action( 'save_post', array( $this, 'save_meta_data' ) );
            //Alter the on screen title on pages
            add_filter( 'the_title',  array( $this, 'optimize' ) );
	} // end constructor


	/**
	 * Loads the plugin text domain for translation
	 */
	public function plugin_textdomain() {

            $domain = 'optimized-headings';
            $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
            load_textdomain( $domain, WP_LANG_DIR.'/'.$domain.'/'.$domain.'-'.$locale.'.mo' );
            load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

	} // end plugin_textdomain
        
        
        /**
         * Add META box
         */
        
        public function add_boxes() {
            //Require extra files
            require( 'includes/metaboxes.php' );
            //Loops $meta_boxes and adds
            foreach( $meta_boxes as $box ) {
                add_meta_box( $box['id'], $box['title'], $box['callback'], $box['post_type'], $box['context'], $box['priority'], $box['callback_args'] );
            }
        }
        
        public function render_box($post, $meta_box) {
            if( is_array($meta_box['args']) ) {
                $meta_box['args']['variables']['post'] = $post;
                print $this->theme( $meta_box['args']['path'], $meta_box['args']['variables']  );
            } else {
                wp_die( 'You must pass $path and $variables via the add_meta_box() callback arguments. ' );
            }
        }
        
        /**
         * Save Meta Data
         */
        
        public function save_meta_data( $post_id ) {
            global $post;
            switch($post->post_type) {
               case 'page' : if( wp_verify_nonce( $_POST['optimized_headings_data'], 'save_data' ) &&  current_user_can( 'edit_post', $post_id ) ) {
                                                if( isset( $_POST['fields'] ) && is_array( $_POST['fields'] ) ) {
                                                    foreach( $_POST['fields'] as $field => $value ) {
                                                        update_post_meta( $post_id, $field, wp_kses( $value, array() ) );
                                                    }
                                                }
                                            } else {
                                                wp_die( 'You do not have sufficient permission to perform this operation.' );
                                            }
                                            
                                            break;
            
            }
        }

        
        /**
         * Function which fetches an HTML template
         * @param string $path The path to the HTML template which will be used
         * @param array  $variables The data which will be themed
         */
        
        public function theme( $path, $variables ) {
            // init a var to return with markup init
            $markup = "";
            //Filter hooks for filtering templates or META boxes
            $path = apply_filters('template_path_override', $path, $path);
            $variables = apply_filters('template_vars_override', $variables, $variables);
            ob_start();
            include($path);
            $markup = ob_get_clean();
            return $markup;
        } // End theme

        // Override our title by filtering on the_title
        function optimize($title) {
                //Init Vars
                global $id, $post;
                $display_title = '';
                //Logic for determining what the Display Title should be
                //If we are on a page and not in the admin area
                if( $id && $post->post_type == 'page' && !is_admin( ) ) {
                        //If it is not empty then we will set the $title var to the field
                        $display_title = get_post_meta($id, 'optimized_heading_display_title', true);
                        if( $display_title != '') {
                                $title = $display_title;
                        }
                }
                return $title;
        }
        

} // end class

// Fire Away
$optimized_headings = new OptimizedHeadings();
