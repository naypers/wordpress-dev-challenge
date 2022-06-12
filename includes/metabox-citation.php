<?php

/**
 * We use the hook "add_meta_boxes" to add an extra fields to the post edit page
 */

add_action( 'add_meta_boxes',  function() {
    add_meta_box( 'citation_post', 'Citación', 'crl_citation_display' );
});

function crl_citation_display() {
    // Get the ID of the post being edited
    $post_id = get_the_ID();
    $crl_citation = '';

    // If exists, get the content of custom field "citacion"
    if ( metadata_exists( 'post', $post_id, 'citacion' ) ) {
        $crl_citation = get_post_meta( $post_id, 'citacion', true );
    }

    // Create de WYSIWYG editor with the content of $crl_citation
    wp_editor( $crl_citation, 'meta_box_citation', array( 'textarea_name' => 'citacion' ) );
}

/**
 * We use the hook "save_post" to add function to save citation
 */

add_action( 'save_post', 'crl_save_citation');
function crl_save_citation() {
    // Get the ID of the post being edited
    $post_id = get_the_ID();

    // Save a citacion if it receive content in the metabox "citation_post"
    if ( isset( $_POST['citacion'] ) ) {
        $crl_new_citation = $_POST[ 'citacion' ];
        update_post_meta( $post_id, 'citacion', $crl_new_citation );
    }
}
