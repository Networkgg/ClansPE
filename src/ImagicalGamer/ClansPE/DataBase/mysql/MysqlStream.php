<?php

class MysqlStream extends Thread{
	/** @var mysqli */
	private $db;

	/** @noinspection PhpUndefinedClassInspection */
	/** @var \Threaded */
	private $input, $output;

	/** @var string */
	private $host, $username, $password, $schema;
	/** @var int */
	private $port;

	/** @var int */
	private $myId;
	/** @var bool */
	private $stopped = false;

	public function __construct($host, $username, $password, $schema, $port){
		$this->host = $host;
		$this->username = $username;
		$this->password = $password;
		$this->schema = $schema;
		$this->port = $port;
		$this->input = \ThreadedFactory::create();
		$this->output = \ThreadedFactory::create();
		$this->start();
	}

	public function run(){
		$this->myId = $this->getCurrentThreadId();
		$this->db = new mysqli($this->host, $this->username, $this->password, $this->schema, $this->port);
		while(!$this->stopped){
			$req = $this->nextQuery();
			$result = $this->db->query($req->getQuery());
			$out = new QueryResult($req);
			if($result instanceof mysqli_result){
				$out->rows = [];
				while(is_array($row = $result->fetch_assoc())){
					$out->rows[] = $row;
				}
			}
			if($this->db->error){
				$out->error = $this->db->error;
			}
			$out->insertId = $this->db->insert_id;
			$this->pushResult($out);
		}
		$this->db->close();
	}

	public function stop(){
		$this->stopped = true;
	}

	/**
	 * Queues a query to be executed.
	 *
	 * @param QueryRequest $entry a non-thread-safe {@link QueryEntry}
	 */
	public function addQuery(QueryRequest $entry){
		$this->input[] = $entry->getThreadSafeClone();
	}
	/**
	 * Reads the next query from the input stream to be executed.
	 *
	 * @internal Do <strong>not</strong> use this method outside this thread!
	 *
	 * @return bool|QueryRequest
	 */
	public function nextQuery(){
		if(Thread::getCurrentThreadId() !== $this->myId){
			throw new \InvalidStateException("Attempt to call a thread-private method " . __METHOD__);
		}
		return $this->input->shift();
	}
	/**
	 * Commits a result into the output stream
	 *
	 * @internal Do <strong>not</strong> use this method outside this thread!
	 *
	 * @param QueryResult $result
	 */
	public function pushResult(QueryResult $result){
		if(Thread::getCurrentThreadId() !== $this->myId){
			throw new \InvalidStateException("Attempt to call a thread-private method " . __METHOD__);
		}
		$this->output[] = $result;
	}
	/**
	 * @return bool|QueryResult
	 */
	public function nextResult(){
		return $this->output->shift();
	}

	public function tick(Clans $main){
		while(($result = $this->nextResult()) instanceof QueryResult){
			$result->src->makeThreadUnsafe($main);
			$l = $result->src->getListener();
			if($l !== null){
				$l->onResult($result);
			}
		}
	}

	public function getThreadName(){
		return "WEA-MySQL-Stream";
	}
}
