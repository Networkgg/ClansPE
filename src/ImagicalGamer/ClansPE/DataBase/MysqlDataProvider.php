<?php

class MysqlDataProvider extends CachedDataProvider{
	/** @var MysqlStream */
	private $db;

	public function __construct(Clans $main){
		parent::__construct($main);
		$details = $main->getPFConfig()->clansDbMysqlDetails;
		$this->db = new MysqlStream($details["host"], $details["username"], $details["password"], $details["schema"], isset($details["port"]) ? (int) $details["port"] : 3306);
		// TODO init queries
	}

	protected function getClanByNameImpl($name, $callbackId){
	}
	protected function getClanByIdImpl($id, $callbackId){
		// TODO: Implement getClanByIdImpl() method.
	}
	protected function getClanByPlayerImpl($name, $callbackId){
		// TODO: Implement getClanByPlayerImpl() method.
	}

	public function close(){
		$this->db->stop();
	}
}
