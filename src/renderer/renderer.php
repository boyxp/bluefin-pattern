<?php
declare(strict_types=1);
namespace bluefin\pattern\renderer;
interface renderer
{
	public function render($content):bool;
}
