<?php

if ( ! defined('ABSPATH') ) {
    die('Direct access not permitted.');
}

foreach ( glob( (__DIR__) . '/hooks/*.php' ) as $filename ) {
    require_once $filename;
}

foreach ( glob( (__DIR__) . '/functions/*.php' ) as $filename ) {
    require_once $filename;
}

/**
 * Function to create the table "ctl_urls"
 */

function ctl_table_creation() {
    global $wpdb;

    $sql = "CREATE TABLE  IF NOT EXISTS ctl_urls (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) UNSIGNED NOT NULL,
        url varchar(250) DEFAULT '' NOT NULL,
        status varchar(25) DEFAULT '' NOT NULL,
        PRIMARY KEY  (id)
    )";

    $wpdb->query( $sql );
}

/**
 * Function to check is a url has protocol
 */

function hasProtocol( $url ) {
    if ( strripos( $url, 'http:' ) === 0 || strripos( $url, 'https:' ) === 0 ) { 
        return true;
    }

    return false;
}

/**
 * Function to check is a url has secure protocol
 */

function hasSecureProtocol( $url ) {
    if ( strripos( $url, 'https:' ) === 0) { 
        return true;
    }

    return false;
}

/**
 * Function to check is a url has blanks
 */

function hasBlanks( $url ) {
    if ( $url[ 0 ] == ' ' || $url[ strlen( $url ) - 1 ] == ' ' ) {
        return true;
    } 

    return false;
}

/**
 * Function to check is a url exist
 */

function existsUrl( $url ) {
    $options[ 'http' ] = array(
        'method'        => "HEAD",
        'ignore_errors' => 1,
        'max_redirects' => 0
    );

    $body = @file_get_contents( $url, NULL, stream_context_create( $options ) );
    
    if ( isset( $http_response_header ) ) {
        sscanf( $http_response_header[ 0 ], 'HTTP/%*d.%*d %d', $httpcode );
 
        $accepted_response = array( 200, 301, 302 );
        if ( in_array( $httpcode, $accepted_response ) ) {
            return true;
        } else {
            return false;
        }
     } else {
         return false;
     }
}
