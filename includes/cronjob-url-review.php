<?php

/**
 * When registering the cronjob run this function to create a new scheduled task
 */

function ctl_cronjob_activation() {
    // Check if it already exists
    if( ! wp_next_scheduled( 'ctl_cron_hook' ) ) {
        wp_schedule_event( current_time( 'timestamp' ), '60seconds', 'ctl_cron_hook' );
        // As soon as the cronjob is activated, the first execution will run
        // The time interval is 60 seconds
        // ctl_cron_hook is also be a hook
        
    }
}

/**
 * We remove the scheduled task when the plugin is deactivated
 */

function ctl_cronjob_desativation() {
    wp_clear_scheduled_hook( 'ctl_cron_hook' );
}

/**
 * We use the new hook "ctl_cron_hook" to add an action (ctl_search_links_with_errors)
 */

add_action( 'ctl_cron_hook', 'ctl_search_links_with_errors' );
function ctl_search_links_with_errors() {

    global $wpdb;
    // Find the post table
    $post_table = $wpdb->prefix . "posts";

    // Execute query to select the posts with status 'publish' and 'draft'
    $sql = "SELECT * FROM `" . $post_table . "` WHERE `post_status` IN ('publish','draft') AND `post_type` LIKE 'post' ORDER BY `ID` ASC";
    $results = $wpdb->get_results( $sql, ARRAY_A );

    // Loop through the results row by row
    foreach( $results as $result ) {

        $post_content = $result["post_content"];
        $post_id = $result["ID"];

        // Delete the urls of this post id, previously created 
        $sql = "DELETE FROM `ctl_urls` WHERE `post_id` = " . $post_id;
        $wpdb->query($sql);

        // Search for urls in post content
        $pattern = '/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i';
        preg_match_all( $pattern, $post_content, $result );

        // Urls found?
        if ( ! empty( $result ) ) {

            // Loop through the urls one by one
            foreach ( $result[ "href" ] as $url ) {

                $url_to_validate = strtolower( $url );
                $url_status = '';

                // Does this url have blank spaces?
                if ( ! hasBlanks( $url_to_validate ) ) {
                    // Is it a valid url for PHP?
                    if ( filter_var( $url_to_validate, FILTER_VALIDATE_URL ) ) {
                        // Does this url have a secure protocol
                        if ( hasSecureProtocol( $url_to_validate ) ) {
                            // Does this url exist?
                            if ( existsUrl( $url_to_validate ) ) {
                                // Status: valid 
                                $url_status = "VALID";
                            } else {
                                // Status: doesn't exist
                                $url_status = "No existe";
                            }
                        } else {
                            // Status: insecure url
                            $url_status = "Enlace inseguro";
                        }
                    } else {
                        // Does this url have protocol?
                        if ( ! hasProtocol( $url_to_validate ) ) {
                            // Is it a valid url for PHP?
                            if ( filter_var( "https://" . $url_to_validate, FILTER_VALIDATE_URL ) ) {
                                // This URL is OK!, just need a protocol
                                // Status: Unspecified protocol
                                $url_status = "Protocolo no especificado";
                            } else {
                                // In addition to not having a protocol, it's not a valid URL
                                // Status: Syntax error
                                $url_status = "Enlace malformado";
                            }
                        } else {
                            // Status: Unspecified protocol
                            $url_status = "Protocolo no especificado";
                        }
                    }
                } else {
                    // Status: Syntax error
                    $url_status = "Enlace malformado";
                }

                // If the status is different from "valid" save it
                if ($url_status != "VALID") {
                    $wpdb->insert( 
                        'ctl_urls', 
                        array( 
                            'post_id' => $post_id, 
                            'url'     => $url_to_validate, 
                            'status'  => $url_status, 
                        ) 
                    );
                }
            }
        }
    }
}

/**
 * We use the cron_schedules hook to save time interval '60seconds'
 */

add_filter( 'cron_schedules', 'ctl_schedule' );
function ctl_schedule( $schedules ) {
     $schedules['60seconds'] = array(
        'interval' => 60,
        'display'  => '60 segundos'
     );
     return $schedules;
}
