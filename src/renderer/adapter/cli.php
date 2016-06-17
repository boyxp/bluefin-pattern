<?php
declare(strict_types=1);
namespace bluefin\pattern\renderer\adapter;
use bluefin\pattern\renderer\renderer as rendererInterface;
class cli implements rendererInterface
{
	public function render($content):bool
	{
		fwrite(STDOUT, var_export($content, true));
		return true;
	}
}
