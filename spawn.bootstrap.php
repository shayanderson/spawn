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
 * Spawn bootstrap
 */

// set no time limit
set_time_limit(0);

// load classes
require_once './lib/Spawn/Process.php';
require_once './lib/Spawn/Manager.php';