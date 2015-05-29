<?php

namespace OptAdmin;

class WPOpt_Metaboxes {

    private $custom_fields_object;

    private $metabox_args = array();

    public function __construct( array $args ) {
        $this->metabox_args = $args;
        $this->wpopt_add_metabox();
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

                        add_meta_box( $this->metabox_args['id'], $this->metabox_args['title'], array( $this, 'wpopt_metabox_content' ), $page, 'advanced', 'high' );

                    }

                } else { return false; }
            } else { return false; }

        } else {
            return false;
        }
    }

    /**
     * wpopt_metabox_content function outputs the content to the metabox created
     */
    public function wpopt_metabox_content( $post ) {

        // Check of the fields key is valid...
        if ( $this->key_is_valid( 'fields', $this->metabox_args ) ) {

            /**
             * Set the nonce field for the form.
             */
            wp_nonce_field( $this->metabox_args['id'] . '_nonce_action', $this->metabox_args['id'] . '_nonce' );

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

                            if ( $field['label'] ) {
                                echo '<label for="'.$field['id'].'" class="wpopt-label">'.$field['label'].'</label>';
                            }
                            echo '<input type="text" class="wpopt-text-block '.$field['class'].'" id="'.$field['id'].'" >';
                            break;

                        }
                        /**
                         * Case "textarea"
                         * Outputs the <textarea> field with available fields
                         */
                        case 'textarea': {

                            if ( $field['label'] ) {
                                echo '<label for="'.$field['id'].'" class="wpopt-label">'.$field['label'].'</label>';
                            }

                            if ( $field['description'] ) {

                                echo '<textarea id="'.$field['id'].'" class="wpopt-textarea '.$field['class'].'" rows="4" ></textarea><span class="wpopt-description"><p>'.$field['description'].'</p></span>';

                            } else {

                                echo '<textarea id="'.$field['id'].'" class="wpopt-textarea-block '.$field['class'].'" rows="4" ></textarea>';

                            }

                            break;

                        }
                        default: { return ""; break; }

                    }

                } else { return false; }

            }
        // if not, then bail...
        } else { return false; }

    }

}

?>