<?php

/*--------------------------------------------*
 * Meta boxes
 *--------------------------------------------*/

//overall metaboxes array - we'll add arrays to this to pass to add_meta_box
$meta_boxes = array();
//Init an array to start some callback args for field rendering
$optimized_headings_fields = array();
//Extra arguments for the sp_marketing_block metabox
$optimized_headings_fields['fields'] = array(
                                'display title' => array(
                                                'type' => 'text',
                                                'default_value' => get_bloginfo( 'url' ),
                                                'name' => 'optimized_heading_display_title',
                                                'label' => 'Display Title',
                                                'size' => '35',
                                                'description' => 'This heading will be used in place of the_title value.'
                                            ),
                            );
//Add a box to the meta_boxes array
$meta_boxes['optimized-heading'] = array(
                                        'id' => 'optimized-heading-data',
                                        'title' => 'Optimized Display Title',
                                        'callback' => array( &$this, 'render_box' ),
                                        'post_type' => 'page',
                                        'context' => 'normal',
                                        'priority' => 'high',
                                        'callback_args' => array( 
                                                                  'path' => 'views/admin.php', 
                                                                  'variables' => $optimized_headings_fields
                                                                )
                                    );


?>
