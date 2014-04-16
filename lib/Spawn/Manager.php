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
namespace Spawn;

use Spawn\Process;

/**
 * Core process Manager class
 *
 * @author Shay Anderson 04.14 <http://www.shayanderson.com/contact>
 */
class Manager
{
	/**
	 * Max commands allowed in command queue
	 */
	const MAX_COMMANDS = 10000;

	/**
	 * Array of string commands
	 *
	 * @var array
	 */
	private $__commands = [];

	/**
	 * Max execution time in seconds for string command
	 *
	 * @var array
	 */
	private $__commands_max_time = [];

	/**
	 * Number of running processes
	 *
	 * @var int
	 */
	private $__proc_running = 0;

	/**
	 * Array of \Spawn\Process objects
	 *
	 * @var array (of \Spawn\Process)
	 */
	private $__processes = [];

	/**
	 * Debug mode flag (debug messages displayed when true)
	 *
	 * @var boolean
	 */
	public $debug_mode = false;

	/**
	 * Path to error log (optional, errors direct output when not set and verbose option true)
	 *
	 * @var string
	 */
	public $error_log_path;

	/**
	 * Max allowed concurrent processes
	 *
	 * @var int
	 */
	public $max_processes = 3;

	/**
	 * Step sleep time in milliseconds (must be greater than 100 milliseconds)
	 *
	 * @var int
	 */
	public $sleep_milliseconds = 500;

	/**
	 * Displays process outputs (including errors if not using error file) when set true
	 *
	 * @var boolean
	 */
	public $verbose = true;

	/**
	 * Add command to queue for process
	 *
	 * @param string $command (ex: 'php -r "echo \'my process\';"')
	 * @param int $max_execution_seconds (optional, max execution time in seconds for process)
	 * @return int (internal command ID)
	 * @throws \Exception
	 */
	public function add($command, $max_execution_seconds = 0) // return int (command ID), 0 = unlimited run time
	{
		if(count($this->__commands) >= self::MAX_COMMANDS)
		{
			throw new \Exception('Spawn: Failed to add command, exceeds max commands allowed ('
				. self::MAX_COMMANDS . ')');
		}

		$this->__commands[] = $command;

		$id = max(array_keys($this->__commands));

		if((int)$max_execution_seconds > 0)
		{
			$this->__commands_max_time[$id] = (int)$max_execution_seconds; // limit run
		}

		return $id;
	}

	/**
	 * Execute commands and handle process queue
	 *
	 * @return void
	 */
	public function execute()
	{
		$id = 0;

		if((int)$this->sleep_milliseconds < 100)
		{
			$this->sleep_milliseconds = 100; // must be > 100
		}

		if(!empty($this->error_log_path)) // init error log file
		{
			@file_put_contents($this->error_log_path, null); // truncate
		}

		while(true)
		{
			while($id < count($this->__commands) && $this->__proc_running < $this->max_processes)
			{
				if($this->debug_mode)
				{
					$this->out('Executing command: ' . $this->__commands[$id]);
				}

				// enqueue process
				$this->__processes[] = new Process($this->__commands[$id],
					( isset($this->__commands_max_time[$id]) ? $this->__commands_max_time[$id] : 0 ),
					$this->error_log_path);

				$this->__proc_running++;

				$id++; // next command
			}

			if($id >= count($this->__commands) && $this->__proc_running === 0) // finished running
			{
				break; // stop
			}

			usleep($this->sleep_milliseconds * 1000);

			foreach($this->__processes as $pid => $proc) // cleanup completed processes
			{
				$is_dequeue = false;

				if($proc->isExpired()) // kill
				{
					$proc->close();
					$is_dequeue = true;

					if($this->debug_mode)
					{
						$this->out('Killed: ' . $proc->command);
					}
				}
				else if(!$proc->isRunning()) // finished
				{
					if($this->verbose)
					{
						$this->out(stream_get_contents($proc->pipes[1])); // proc output

						if(!empty($proc->pipes[2])) // error check (pipe, not file)
						{
							$err = stream_get_contents($proc->pipes[2]);

							if(strlen($err) > 0)
							{
								$this->out($err);
							}
						}
					}

					$proc->close();
					$is_dequeue = true;

					if($this->debug_mode)
					{
						$this->out('Done: ' . $proc->command);
					}
				}

				if($is_dequeue)
				{
					unset($this->__processes[$pid]); // dequeue
					$this->__proc_running--;
				}
			}
		}
	}

	/**
	 * Commands array getter
	 *
	 * @return array
	 */
	public function getCommands()
	{
		return $this->__commands;
	}

	/**
	 * Output string
	 *
	 * @param string $str
	 * @return void
	 */
	public function out($str)
	{
		echo $str . PHP_EOL;
	}
}