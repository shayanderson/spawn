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

/**
 * Process class
 *
 * @author Shay Anderson 04.14 <http://www.shayanderson.com/contact>
 */
class Process
{
	/**
	 * Expire time
	 *
	 * @var int
	 */
	private $__exp_time = 0;

	/**
	 * Process command
	 *
	 * @var string
	 */
	public $command;

	/**
	 * Process pipes
	 *
	 * @var array
	 */
	public $pipes;

	/**
	 * Process resource
	 *
	 * @var resource
	 */
	public $proc;

	/**
	 * Init
	 *
	 * @param string $command
	 * @param int $max_execution_seconds
	 * @param null|string $error_log_path
	 */
	public function __construct($command, $max_execution_seconds = 0, $error_log_path = null)
	{
		$this->command = $command;

		$ds = [
			0 => ['pipe', 'r'],
			1 => ['pipe', 'w'],
			2 => ( empty($error_log_path) ? ['pipe', 'w'] : ['file', $error_log_path, 'a'] )
		];

		$this->proc = proc_open($this->command, $ds, $this->pipes, null, null);

		if($max_execution_seconds > 0)
		{
			$this->__exp_time = strtotime('+' . $max_execution_seconds . ' seconds');
		}
	}

	/**
	 * Close process
	 *
	 * @return void
	 */
	public function close()
	{
		fclose($this->pipes[0]);
		fclose($this->pipes[1]);
		if(!empty($this->pipes[2]))
		{
			fclose($this->pipes[2]);
		}
		proc_close($this->proc);
	}

	/**
	 * Process expired flag getter
	 *
	 * @return boolean
	 */
	public function isExpired()
	{
		if($this->__exp_time > 0)
		{
			return $this->__exp_time < mktime();
		}

		return false;
	}

	/**
	 * Process is running flag getter
	 *
	 * @return boolean
	 */
	public function isRunning()
	{
		return proc_get_status($this->proc)['running'];
	}
}