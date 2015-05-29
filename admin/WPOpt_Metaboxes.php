<?php

namespace OptAdmin;

class WPOpt_Metaboxes {

    private $custom_fields_object;

    private $metabox_args = array();

    public function __construct( array $args ) {
        $this->metabox_args = $args;
        $this->wpopt_add_metabox();
        add_action( 'save_post', array( $this, 'wpopt_save_metabox_data' ) );
    }

    private function key_is_valid( $key="", $array ) {
        if ( array_key_exists( $key, $array ) ) {
            if ( isset( $array[$key] ) && !empty( $array[$key] ) ) {
                return true;
            } else { return false; }
        } else { return false; }
    }

    private function wpopt_add_metabox() {
        if ( $this->metabox_args['id'] && $this->metabox_args['title'] ) {

            if ( $this->metabox_args['pages'] ) {

                if ( is_array( $this->metabox_args['pages'] ) ) {

                    foreach ( $this->metabox_args['pages'] as $page ) {

                        add_meta_box( $this->metabox_args['id'], $this->metabox_args['title'], array( $this, 'wpopt_metabox_content' ), $page, $this->metabox_args['context'], $this->metabox_args['priority'] );

                    }

                } else { return false; }
            } else { return false; }

        } else {
            return false;
        }
    }

    /**
     * Outputs the fields to the meta box that is created.
     * Function takes the arguments that are passed, checks the types, and outputs the fields according to it.
     * Returns either string with HTML input fields or boolean on failure
     * @param $post - object
     * @return bool|string
     */
    public function wpopt_metabox_content( $post ) {

        // Check of the fields key is valid...
        if ( $this->key_is_valid( 'fields', $this->metabox_args ) ) {

            /**
             * Set the nonce field for the form.
             */
            wp_nonce_field( $this->metabox_args['id'] . '_nonce_action', $this->metabox_args['id'] . '_nonce' );

            /**
             * Check the 'description' field
             * If it's set then, print it out.
             */
            if ( $this->metabox_args['description'] ) {

                echo '<p>'.esc_attr( $this->metabox_args['description'] ).'</p>';

            }

            /**
             * Foreach loops through 'fields' array in metabox_args array.
             * 'fields' contains arrays of data for input fields that will be printed in the metabox
             */
            foreach ( $this->metabox_args['fields'] as $field ) {

                // If the $field['type'] is ok...
                if ( $field['type'] ) {

                    /**
                     * Switch checks $field['type'] value and decides which form element will be printed to the metabox
                     */
                    switch ( $field['type'] ) {

                        /**
                         * Case "text"
                         * Outputs the normal input field with available fields
                         */
                        case 'text': {

                            // Get the post meta for this field
                            $post_meta = get_post_meta( $post->ID, $field['id'], true );

                            if ( $field['label'] ) {
                                echo '<label for="'.$field['id'].'" class="wpopt-label">'.$field['label'].'</label>';
                            }
                            echo '<input type="text" name="'.$field['id'].'" value="'.get_post_meta( $post->ID, $field['id'], true ).'" class="wpopt-text-block '.$field['class'].'" id="'.$field['id'].'" placeholder="'.$field['placeholder'].'">';
                            break;

                        }
                        /**
                         * Case "textarea"
                         * Outputs the <textarea> field with available fields
                         */
                        case 'textarea': {

                            // Get the post meta for this field
                            $post_meta = get_post_meta( $post->ID, $field['id'], true );

                            // Check if the 'label' field is empty
                            if ( $field['label'] ) {
                                echo '<label for="'.$field['id'].'" class="wpopt-label">'.$field['label'].'</label>';
                            }

                            // Check if the description field is set
                            if ( $field['description'] ) {
                                // if it is, then output the description besides the field
                                echo '<textarea id="'.$field['id'].'" name="'.$field['id'].'" class="wpopt-textarea '.$field['class'].'" rows="4" placeholder="'.$field['placeholder'].'">'.get_post_meta( $post->ID, $field['id'], true ).'</textarea><span class="wpopt-description"><p>'.$field['description'].'</p></span>';

                            } else {
                                // if the description is not set, then
                                echo '<textarea id="'.$field['id'].'" name="'.$field['id'].'" class="wpopt-textarea-block '.$field['class'].'" rows="4" placeholder="'.$field['placeholder'].'">'.get_post_meta( $post->ID, $field['id'], true ).'</textarea>';

                            }

                            break;

                        }
                        /**
                         * Default case, returns false
                         */
                        default: { return ""; break; }

                    } // end switch
                // If no 'type' filled, bail
                } else { return false; }

            } // end foreach
        // if 'fields' is empty or not set, then bail...
        } else { return false; }

    }

    public function wpopt_save_metabox_data( $post_id ) {

        // Check if the nonce is set in $_POST because save_post can be triggered on other places
        if ( $_POST[$this->metabox_args['id'] . '_nonce'] ) {
            // Get the current nonce for $_POST
            $current_nonce = $_POST[$this->metabox_args['id'] . '_nonce'];
            // Try to verify the nonce to check if it comes from our page
            if ( wp_verify_nonce( $current_nonce, $this->metabox_args['id'] . '_nonce_action' ) ) {

                /**
                 * If it's doing autosave then don't do anything
                 */
                if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                    return $post_id;
                }
                // Check if the current user can edit posts
                if ( ! current_user_can( 'edit_posts' ) ) {
                    // if not then bail...
                    return false;

                } else {
                    /**
                     * Check if the 'fields' fields is valid
                     */
                    if ( $this->key_is_valid( 'fields', $this->metabox_args ) ) {

                        foreach ( $this->metabox_args['fields'] as $field ) {

                            if ( $_POST[$field['id']] ) {

                                add_post_meta( $post_id, $field['id'], $_POST[$field['id']] );

                            } else { return false; }

                        } // end foreach

                    } else { return false; }

                } //end user capability check

            } else {
                // if can't verify nonce then bail...
                return false;

            } // end nonce verification check

        } else {

            return false;

        } // end $_POST check

    }

}

?>