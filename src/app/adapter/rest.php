<?php
declare(strict_types=1);
namespace bluefin\pattern\app\adapter;
use bluefin\pattern\app\app as appInterface;
class rest extends \injector implements appInterface
{
	public function __construct(string $format='json')
	{
		if(!is_null($format)) {
			if(static::$locator->has('renderer_'.$format)) {
				static::$locator->alias('renderer', 'renderer_'.$format);
			}
		}
	}

	public function route(string $subject=null):appInterface
	{
		if(is_null($subject)) {
			$subject = $_SERVER['REQUEST_METHOD'].':'.($_SERVER['QUERY_STRING'] ?: '/');
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
