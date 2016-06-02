<?php
declare(strict_types=1);
namespace bluefin\pattern\renderer\adapter;
use XMLWriter;
use bluefin\pattern\renderer\renderer as rendererInterface;
class xml implements rendererInterface
{
	private $_charset = null;
	private $_xml     = null;

	public function __construct($charset='utf-8')
	{
		$this->_charset = $charset;
	}

	public function render($content):bool
	{
		header("Content-type: text/xml; charset={$this->_charset}");

		$data = (!is_array($content) or count($content)>1) ? array('root'=>$content) : $content;

		$this->_xml = new XMLWriter;
		$this->_xml->openMemory();
		$this->_xml->startDocument('1.0', $this->_charset);

		$this->_build($data);

		$this->_xml->endDocument();
		echo $this->_xml->outputMemory(true);

		return true;
	}

	private function _build($data, $node='node')
	{
		foreach($data as $key=>$value) {
			$key = is_numeric($key) ? 'item' : $key;

			$this->_xml->startElement($key);
			if(is_array($value)) {
				$this->{__FUNCTION__}($value, $key);
			} else {
				$this->_xml->text(strval($value));
			}
			$this->_xml->endElement();
		}
	}
}
