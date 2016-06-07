<?php
declare(strict_types=1);
namespace bluefin\pattern\dispatcher\adapter;
use bluefin\pattern\dispatcher\dispatcher as dispatcherInterface;
class dispatcher implements dispatcherInterface
{
	private $_aborted    = false;
	private $_forwarding = false;
	private $_forwarded  = false;
	private $_content    = null;

	/**
	* dispatch
	*
	* @usage
	* $dispatcher->dispatch(array('user', 'login'));//实例方法调用
	* $dispatcher->dispatch('user::login');//静态方法调用
	* $dispatcher->dispatch('login');//函数调用
	* $dispatcher->dispatch(function(){return rand(0, 99);});//闭包调用
	*/
	public function dispatch($handle, array $params=[]):bool
	{
		if(is_array($handle) and is_string($handle[0]) and !$this->_forwarded and !$this->_aborted) {
			$handle[0] = new $handle[0];
		}

		if($this->_forwarded) {
			return true;
		}

		if($this->_aborted) {
			return false;
		}

		$this->_content = call_user_func_array($handle, $params);

		if($this->_forwarding) {
			$this->_forwarded = true;
		}

		return true;
	}

	public function abort():bool
	{
		return $this->_aborted = true;
	}

	public function forward($handle, array $params=[]):bool
	{
		if($this->_forwarded or $this->_forwarding) {
			return false;
		}

		$this->_forwarding = true;
		return $this->dispatch($handle, $params);
	}

	public function getContent()
	{
		return $this->_content;
	}
}
