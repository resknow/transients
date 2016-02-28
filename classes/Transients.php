<?php

class Transients {

    ### Properties
    private static $instance;
    private $config = array();
    private $transients = array();

    /**
     * Construct
     */
    private function __construct() {

        # Load Config
        $config = plugin_load_config('transients');

        # Filter config
        $this->config = apply_filters('transients_config', $config);

        # Check we can save transients
        if ( !is_writeable(ROOT_DIR . '/' . $this->config['dir']) ) {
            throw new Exception('Your Transients directory is not writeable. Try <code>chmod 775</code>');
        }

        # Load Transient Store
        $this->get_transients();

    }

    /**
     * Get Instance
     */
    public static function get_instance() {

        if ( self::$instance === null ) {
            self::$instance = new Transients();
        }

        return self::$instance;

    }

    private function __clone() {}
    private function __wakeup() {}

    /**
     * Get Transients
     */
    public function get_transients() {

        # Store file
        $file = ROOT_DIR . '/' . $this->config['dir'] . '/_store.txt';

        # Check Store exists
        if ( !is_readable($file) ) {
            return false;
        }

        # Open Transients Store
        $store = file_get_contents($file);

        # Unserialize
        $store = unserialize($store);

        # Save Transients
        $this->transients = $store;

        # See if any need updating
        $this->update_transients();

        return $this->transients;

    }

    /**
     * Add Transient
     *
     * @param $key (string) Transient key
     * @param $data (string) URL/File to cache
     * @param $lifespan (int) Lifespan in seconds
     */
    public function add_transient( $key, $data, $lifespan = 3600 ) {

        # Check $key is a string
        if ( !is_string($key) ) {
            throw new Exception(sprintf(
                '%s is not a transient valid key.', $key
            ));
        }

        # Check $key is not taken
        if ( $this->transient_exists($key) ) {
            throw new Exception(sprintf(
                '%s is already a transient.', $key
            ));
        }

        # Load Data
        if ( !$the_data = file_get_contents($data) ) {
            throw new Exception('Unable to load data location.');
        }

        # Add Transient
        $this->transients[$key] = array(
            'data_location' => $data,
            'data' => $the_data,
            'created' => time(),
            'expires' => strtotime('+' . $lifespan . ' seconds'),
            'lifespan' => $lifespan
        );

        # Save Data
        if ( !$this->save_transient($key, $the_data) ) {
            unset($this->transients[$key]);
            return false;
        }

        # Update Transients Store
        $this->update_store();

        return true;

    }

    /**
     * Remove Transient
     *
     * @param $key (string) Transient to remove
     */
    public function remove_transient( $key ) {

        # Check Transient Exists
        if ( !$this->transient_exists($key) ) {
            return false;
        }

        # Remove from Transient array
        unset($this->transients[$key]);

        # Set path
        $file = ROOT_DIR . '/' . $this->config['dir'] . '/' . $key . '.txt';

        # Delete file
        if ( file_exists($file) ) {
            unlink($file);
        }

        # Update Store
        $this->update_store();

    }

    /**
     * Update Transients
     *
     * @param $force (bool) Force update all if true
     */
    public function update_transients( $force = false ) {

        foreach ( $this->transients as $key => $transient ) {
            $this->update_transient($key, $force);
        }

    }

    /**
     * Update Transient
     *
     * @param $key (string) Transient to update
     * @param $force (bool) Whether to force an update even if
     * Transient has not expired
     */
    public function update_transient( $key, $force = false ) {

        # Check Transient Exists
        if ( !$this->transient_exists($key) ) {
            return false;
        }

        # Only update if it's expired
        # or $force is true
        if ( time() >= $this->transients[$key]['expires'] || $force == true ) {

            # Load Data
            if ( !$the_data = file_get_contents($this->transients[$key]['data']) ) {
                throw new Exception(sprintf(
                    'Unable to load data location. (%s)',
                    $this->transients[$key]['data']
                ));
            }

            # Save Data
            $this->save_transient($key, $the_data);

        }

    }

    /**
     * Transient Exists
     *
     * @param $key (string) Transient to check
     */
    public function transient_exists( $key ) {
        return array_key_exists( $key, $this->transients );
    }

    /**
     * Save Transient
     *
     * @param $key (string) Transient key for filename
     * @param $data (mixed) Data to save
     */
    private function save_transient( $key, $data ) {

        # Serialize objects & arrays
        if ( is_array($data) || is_object($data) ) {
            $data = serialize($data);
        }

        # Create path
        $file = $this->config['dir'] . '/' . $key . '.txt';

        # Save data
        if ( !file_put_contents($file, $data) ) {
            return false;
        }

        return true;

    }

    /**
     * Update Store
     */
    private function update_store() {
        if ( !$this->save_transient('_store', serialize($this->transients)) ) {
            throw new Exception('Unable to update Transient Store');
        }
    }

}
