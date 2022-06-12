<?php
/**
 * Shortcode for citation "mc-citacion" (with or without post id).
 */

// Add shortcode "mc-citacion" with function to get the "citacion" value 
add_shortcode( "mc-citacion", "get_citacion_shortcode" );
function get_citacion_shortcode ( $atts ) {
	// If it gets an ID use it
    if ( isset( $atts['post_id'] ) ) {
        $post_id = $atts['post_id'];
    // If not get the ID of the post being viewed
    } else {
        $post_id = get_the_ID();
    }

    // Get the value of the custom field "citacion" with this post id
    return get_post_meta( $post_id, 'citacion', true );
}
