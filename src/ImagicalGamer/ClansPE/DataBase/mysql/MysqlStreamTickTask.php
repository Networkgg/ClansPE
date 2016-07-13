<?php

class MysqlStreamTickTask extends PluginTask{
	/** @var Clans */
	private $main;
	/** @var MysqlStream */
	private $stream;

	public function __construct(Clans $main, MysqlStream $stream){
		parent::__construct($this->main = $main);
		$this->stream = $stream;
	}
	public function onRun($currentTick){
		$this->stream->tick($this->main);
	}
}
