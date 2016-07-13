<?php

interface QueryListener{
	public function onResult(QueryResult $result);
}
