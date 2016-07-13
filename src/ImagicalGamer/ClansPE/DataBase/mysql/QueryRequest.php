<?php

class QueryRequest{
	/** @var Clans */
	private $main;
	/** @var string */
	private $query;
	/** @var QueryListener|int|null */
	private $listener;

	/**
	 * QueryEntry constructor.
	 *
	 * @param Clans     $main
	 * @param string             $query
	 * @param QueryListener|null $listener
	 */
	public function __construct(Clans $main, $query, $listener = null){
		$this->main = $main;
		$this->query = $query;
		$this->listener = $listener;
	}
	/**
	 * Returns a clone of this object that is thread-safe (can be stored as a field in a {@link Threaded}
	 *
	 * @return QueryRequest
	 */
	public function getThreadSafeClone(){
		$clone = clone $this;
		$clone->makeThreadSafe();
		return $clone;
	}
	private function makeThreadSafe(){
		if($this->listener !== null){
			$this->listener = $this->main->getObjectPool()->store($this->listener);
		}
		unset($this->main);
	}
	public function makeThreadUnsafe(Clans $main){
		$this->main = $main;
		if($this->listener !== null){
			$this->listener = $main->getObjectPool()->get($this->listener);
		}
	}

	public function getMain(){
		return $this->main;
	}
	public function getQuery(){
		return $this->query;
	}
	public function getListener(){
		return $this->listener;
	}

}
