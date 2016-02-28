<?php

/**
 * @subpackage Transients
 * @package Boilerplate
 * @version 1.0.0
 *
 * @filters: transients_config
 */

plugin_requires_version('transients', '1.5.0');

# Load Classes
require_once __DIR__ . '/classes/Transients.php';

# Create an instance
$_transients = Transients::get_instance();

# Load Functions
require_once __DIR__ . '/functions/transients.php';
