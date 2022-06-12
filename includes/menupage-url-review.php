<?php
/**
 * Menu page for link review.
 */

/**
 * Use the hook "admin_menu" to add a menu page 
 */

add_action( 'admin_menu', 'crl_create_menu' );
function crl_create_menu() {
    add_menu_page(
        'Revisión de Links',
        'Revisión de Links',
        'manage_options',
        'menulist',
        'crl_showing_content',
        plugin_dir_url( __FILE__ ) . '../assets/img/icon.png',
        '1'
    );
}

/**
 * Function to show the content in "ctl_urls" table 
 */

function crl_showing_content() {
    global $wpdb;

    // Execute query to get all the records of "ctl_urls" table
    $sql = "SELECT * FROM `ctl_urls` ORDER BY `id` ASC";
    $results = $wpdb->get_results( $sql, ARRAY_A ); 

    echo "
    <h2>Revisión de Links</h2>
    <div style='padding-right:20px'>
        <table class='wp-list-table widefat fixed striped table-view-list posts'>
            <thead>
                <tr>
                    <td>URl</td>
                    <td>Estado</td>
                    <td>Origen</td>
                </tr>
            </thead>
            <tbody>";

        // Loop through the results one by one
        foreach( $results as $row ) {
            // Show '%20' instead of space
            $url = str_replace(' ', '%20', $row[ "url" ]);
            echo "
            <tr>
                <td><strong>" . $url . "</strong></td>
                <td><strong><span style='color: orange'>" . $row[ "status" ] . "</span></strong></td>
                <td><strong>" .  get_the_title( $row[ "post_id" ] ) . "</strong></td>
            </tr>";
        }
        
    echo "
            </tbody>
        </table>
    </div>
    ";
}
