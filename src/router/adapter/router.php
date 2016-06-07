<?php
declare(strict_types=1);
namespace bluefin\pattern\router\adapter;
use bluefin\base\registry\registry;
use bluefin\pattern\router\router as routerInterface;
class router extends \injector implements routerInterface
{
	private $_registry = null;
	private $_handle   = null;
	private $_matches  = [];

	public function __construct(string $prefix='routes')
	{
		$this->_registry = static::$locator->make('registry', [$prefix]);
	}

	public function add(string $pattern, $handle):routerInterface
	{
		$pattern = preg_replace('/\{[a-z0-9]+\}/i', '*', $pattern);

		if(strpos($pattern, '*')===false) {
			$this->_registry->set("STATIC:{$pattern}", $handle);
		} else {
			$nodes = explode('/', ltrim($pattern, '/'));
			$key   = 'MATCH:'.next($nodes).':'.count($nodes);
			$tree  = $this->_registry->exists($key) ? $this->_registry->get($key) : [];
			$curr  = &$tree;
			foreach($nodes as $node) {
				if(!isset($curr[$node])) {
					$curr[$node] = [];
				}

				$curr = &$curr[$node];
			}
			$curr['#handle'] = $handle;

			$this->_registry->set($key, $tree);
		}

		return $this;
	}

	public function remove(string $pattern):routerInterface
	{
		return $this;
	}

	public function flush():routerInterface
	{
		$this->_registry->flush();
		return $this;
	}

	public function route(string $subject):bool
	{
		$this->_handle  = null;
		$this->_matches = [];

		if($handle=$this->_registry->get("STATIC:{$subject}")) {
			$this->_handle = $handle;
			return true;
		} else {
			$nodes = explode('/', ltrim($subject, '/'));
			$key   = 'MATCH:'.next($nodes).':'.count($nodes);
			$rules = $this->_registry->get($key);
			if(!$rules) {
				$key   = 'MATCH:*:'.count($nodes);
				$rules = $this->_registry->get($key);
			}

			if($rules) {
				$last    = &$rules;
				$matches = [];
				foreach($nodes as $node) {
					if(isset($last[$node])) {
						$last = &$last[$node];
					} elseif(isset($last['*']) and ctype_alnum(strtr($node, array(':'=>'', '_'=>'')))) {
						$matches[] = $node;
						$last      = &$last['*'];
					} else {
						return false;
					}
				}

				if(isset($last['#handle'])) {
					$this->_handle = $last['#handle'];
					$this->_matches= $matches;
					return true;
				}
			}
		}

		return false;
	}

	public function getHandle()
	{
		return $this->_handle;
	}

	public function getMatches():array
	{
		return $this->_matches;
	}
}
