<?php
/**
 * Spawn - Process Manager for Handling Multiple Processes in PHP 5.4+
 *
 * @package Spawn
 * @version 1.0.b - Apr 16, 2014
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 * @link <https://github.com/shayanderson/spawn>
 */

/**
 * Spawn example
 */

// load Spawn
require_once './spawn.bootstrap.php';

try
{
	// set Spawn object
	$spawn = new \Spawn\Manager;

	// turn on debug messages for testing
	$spawn->debug_mode = true;

	// allow 3 processes to run concurrently (default is 3)
	$spawn->max_processes = 3;

	// add test processes
	$spawn->add('php -r "sleep(1); echo \'process 1\';"');
	$spawn->add('php -r "sleep(2); echo \'process 2\';"');
	$spawn->add('php -r "sleep(3); echo \'process 3\';"');
	$spawn->add('php -r "sleep(4); echo \'process 4\';"');
	$spawn->add('php -r "sleep(5); echo \'process 5\';"');

	// start processes
	$spawn->execute();
}
catch(\Exception $ex)
{
	echo $ex->getMessage();
}