<?php
declare(strict_types=1);
namespace bluefin\pattern\renderer\adapter;
use bluefin\pattern\renderer\renderer as rendererInterface;
class html extends \injector implements rendererInterface
{
	private $_tplPath = '';

	public function __construct(string $tplPath='../view/%s.tpl')
	{
		$this->_tplPath = $tplPath;
	}

	public function render($content):bool
	{
		if(strpos($this->_tplPath, '%s')) {
			$handle = static::$locator->router->getHandle();
			if(is_null($handle)) {
				return false;
			}

			if(is_array($handle)) {
				$handle = implode('\\', $handle);
			}

			$handle  = strtr($handle, ['::'=>'\\', '\\'=>'/']);
			$handle  = substr($handle, strpos($handle, '/')+1);
			$tplPath = sprintf($this->_tplPath, $handle);
		} else {
			$tplPath = $this->_tplPath;
		}

		if(is_file($tplPath)) {
			extract($content);
			include($tplPath);
			return true;
		}

		return false;
	}
}
