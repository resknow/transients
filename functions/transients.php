<?php

/**
 * Get Transients
 *
 * @global $_transients
 */
function get_transients() {
    global $_transients;
    return $_transients->get_transients();
}

/**
 * Add Transient
 *
 * @global $_transients
 *
 * @param $key (string) Transient key
 * @param $data (string) URL/File to cache
 * @param $lifespan (int) Lifespan in seconds
 */
function add_transient( $key, $data, $lifespan = 3600 ) {
    global $_transients;
    return $_transients->add_transient( $key, $data, $lifespan );
}

/**
 * Remove Transient
 *
 * @global $_transients
 *
 * @param $key (string) Transient to remove
 */
function remove_transients( $key ) {
    global $_transients;
    return $_transients->remove_transient( $key );
}

/**
 * Update Transients
 *
 * @global $_transients
 *
 * @param $force (bool) Force update all if true
 */
function update_transients( $force = false ) {
    global $_transients;
    return $_transients->update_transients( $force );
}

/**
 * Update Transient
 *
 * @param $key (string) Transient to update
 * @param $force (bool) Whether to force an update even if
 * Transient has not expired
 */
function update_transient( $key, $force = false ) {
    global $_transients;
    return $_transients->update_transient( $key, $force );
}

/**
 * Transient Exists
 *
 * @global $_transients
 *
 * @param $key (string) Transient to check
 */
function transient_exists( $key ) {
    global $_transients;
    return $_transients->transient_exists( $key );
}
