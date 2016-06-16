<?php
declare(strict_types=1);
namespace bluefin\pattern\app\adapter;
use bluefin\pattern\app\app as appInterface;
class cli extends \injector implements appInterface
{
	public function __construct()
	{
		if(php_sapi_name()!=='cli') {
			throw new \exception('cli', 1002);
		}

		defined('STDIN')  or define('STDIN',  fopen('php://stdin',  'r'));
		defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));
		defined('STDERR') or define('STDIN',  fopen('php://stderr', 'w'));
	}

	public function route(string $subject=null):appInterface
	{
		if(is_null($subject)) {
			$subject = 'CLI:'.(isset($argv[1]) ? $argv[1] : '/');
		}

		$route = static::$locator->router->route($subject);
		if(!$route) {
			throw new \exception('route', 1000);
		}

		return $this;
	}

	public function dispatch($handle=null, array $params=[]):appInterface
	{
		if(is_null($handle)) {
			$handle = static::$locator->router->getHandle();
			$params = static::$locator->router->getMatches();
		}

		$content = static::$locator->dispatcher->dispatch($handle, $params);
		if(!$content) {
			throw new \exception('dispatch', 1001);
		}

		return $this;
	}

	public function render($content=null):appInterface
	{
		if(is_null($content)) {
			$content = static::$locator->dispatcher->getContent();
		}

		if(!is_null($content)) {
			static::$locator->renderer->render($content);
		}

		return $this;
	}
}
