<?php
declare(strict_types=1);
namespace bluefin\pattern\app;
interface app
{
	public function route(string $subject=null):app;
	public function dispatch($handle=null, array $params=[]):app;
	public function render($content=null):app;
}
