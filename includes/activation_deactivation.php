<?php
register_activation_hook(__FILE__, 'sat_activate');
register_deactivation_hook(__FILE__, 'sat_deactivate');

// Activation function
function sat_activate() {}

// Deactivation function
function sat_deactivate() {}