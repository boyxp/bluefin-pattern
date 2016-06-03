<?php
namespace bluefin\pattern\dispatcher;
interface dispatcher
{
	public function dispatch($handle, array $params=[]);
	public function abort():bool;
	public function forward($handle, array $params=[]):bool;
}