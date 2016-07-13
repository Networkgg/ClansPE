<?php

class QueryResult{
	public $src;
	public $error;
	public $rows;
	public $insertId;
	public function __construct(QueryRequest $src){
		$this->src = $src;
	}
}
