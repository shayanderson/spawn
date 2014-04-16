# Spawn
Spawn - Process Manager for Handling Multiple Processes in PHP 5.4+

## Quick Start
Use example.php file to execute test:
```php
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
```
Run example.php script using command line (PHP CLI):
```shell
# php example.php
Executing command: php -r "sleep(1); echo 'process 1';"
Executing command: php -r "sleep(2); echo 'process 2';"
Executing command: php -r "sleep(3); echo 'process 3';"
process 1
Done: php -r "sleep(1); echo 'process 1';"
Executing command: php -r "sleep(4); echo 'process 4';"
process 2
Done: php -r "sleep(2); echo 'process 2';"
Executing command: php -r "sleep(5); echo 'process 5';"
process 3
Done: php -r "sleep(3); echo 'process 3';"
process 4
Done: php -r "sleep(4); echo 'process 4';"
process 5
Done: php -r "sleep(5); echo 'process 5';"
```

## Spawn Settings
Settings can be customized for script optimization

### Debug Mode
Debug mode can be turned on to display helpful debugging messages:
```php
$spawn->debug_mode = true;
```