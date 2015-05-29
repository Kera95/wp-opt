<?php
/**
 * @package WordPress theme options
 * @author Kerim Karalić
 */

define( 'WPOPT_VERSION', '1.0' );
define( 'WPOPT_TEXTDOMAIN', 'wpopt' );

require('vendor/autoload.php');

$opt_loader = new \OptAdmin\WPOpt_Loader();

?>