<?php

abstract class CachedDataProvider implements ClansDataProvider{
	const NO_CLAN = -1;

	/** @var Clan[] $clans indexed by ID */
	private $clans = [self::NO_CLAN => null];

	/** @var int[] array ("clan_name" => fid ) */
	private $nameToFID = [];
	/** @var int[] array ( "player_name" => fid ) */
	private $playerToFID = [];

	/** @var Clans */
	private $main;

	public function __construct(Clans $main){
		$this->main = $main;
	}

	public function getClan($name, callable $callback){
		if(array_key_exists($name, $this->nameToFID)){
			$callback($this->clans[$this->nameToFID[$name]]);
			return;
		}
		$callback2 = function ($clan) use ($callback, $name){
			if($clan instanceof Clan){
				PFUtils::identical($clan->getName(), $name, "fetched clan name", "requested clan name");
				$this->clans[$clan->getId()] = $clan;
				$this->nameToFID[$name] = $clan->getId();
			}
			$callback($clan);
		};
		$callbackId = $this->main->getObjectPool()->store($callback2);
		$this->getClanByNameImpl($name, $callbackId);
	}
	protected abstract function getClanByNameImpl($name, $callbackId);

	public function getClanById($id, callable $callback){
		if(array_key_exists($id, $this->clans)){
			$callback($this->clans[$id]);
			return;
		}
		$callback2 = function ($clan) use ($callback, $id){
			if($clan instanceof Clan){
				PFUtils::identical($clan->getId(), $id, "fetched clan ID", "requested clan ID");
				$this->clans[$id] = $clan;
				$this->nameToFID[$clan->getName()] = $id;
			}else{
				$this->clans[$id] = null;
			}
			$callback($clan);
		};
		$callbackId = $this->main->getObjectPool()->store($callback2);
		$this->getClanByIdImpl($id, $callbackId);
	}
	protected abstract function getClanByIdImpl($id, $callbackId);

	public function getClanForPlayer(Player $player, callable $callback){
		$name = strtolower($player->getName());
		if(array_key_exists($name, $this->playerToFID)){
			$callback($this->clans[$this->playerToFID[$name]]);
		}
		$callback2 = function ($fid) use ($callback, $name){
			$this->playerToFID[$name] = $fid;
			if($fid !== self::NO_CLAN){
				$this->getClanById($fid, $callback);
			}else{
				$callback(null);
			}
		};
		$callbackId = $this->main->getObjectPool()->store($callback2);
		$this->getClanByPlayerImpl($name, $callbackId);
	}
	protected abstract function getClanByPlayerImpl($name, $callbackId);

	public function linkPlayerToClanCache($name, Clan $clan){
		$this->playerToFID[$name] = $clan->getId();
	}
	public function unlinkPlayerFromClanCache($name){
		$this->playerToFID[$name] = -1;
	}

	public function close(){
	}
}
