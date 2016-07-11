<?php
namespace ImagicalGamer\ClansPE;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;

use pocketmine\item\Item;

use pocketmine\utils\TextFormat as C;
use pocketmine\utils\Config;

use pocketmine\command\{Command, CommandSender};

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, July 2016
 */

class Main extends PluginBase implements Listener{

  public $format = C::RESET . C::AQUA . "ClansPE" . C::GRAY . " >> "; 
  public $banned_names = array("admin", "owner", "mod", "moderator", "co-owner", "coowner", "administrator", "builder");

  public function onEnable(){
    @mkdir($this->getDataFolder());
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    //$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
  }

  public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
    if($cmd->getName() == "clan" or "c"){
      if(count($args) == 2){
        if($args[0] == "create" or "make"){
           $this->createClan($sender,$args[1]);
        }
        if($args[0] == "delete"){
          $this->deleteClan($sender, $args[1]);
        }
      }
      if(count($args) == 1){
        if($args[0] == "help" or "menu"){
          $sender->sendMessage($this->helpMenu);
        }
      }
    }
  }

  public function createClan(Player $leader, String $clan){
    if($this->clanExists($clan) == "Exists"){
      $leader->sendMessage($this->format . "Clan Exists!");
    }
    else{
      if(in_array($clan, $this->banned_names)){
        $sender->sendMessage($this->format . "You Cannot Use That Name!");
      }
      else{
        $data = new Config($this->getDataFolder() . "Clans.yml", Config::YAML);
        $data->set($clan, array($leader->getName(),"Steve"));
        $data->save();
      }
    }
  }

  public function deleteClan(Player $leader, String $clan){
    $data = new Config($this->getDataFolder() . "Clans.yml", Config::YAML);
    $clan = $data->get($clan);
    if($clan[0] == $leader->getName()){

    }
    else{
      $leader->sendMessage($this->format . " Your not the Leader of your Clan!");
    }
  }

  public function clanExists(String $clan){
    $data = new Config($this->getDataFolder() . "Clans.yml", Config::YAML);
    if($data->get($clan) == null){
      return "Doesnt Exist";
    }
    else{
      return "Exists";
    }
  }

  public function helpMenu(){
    $menu = C::AQUA . "ClansPE " . C::GRAY . "Help\n" . C::AQUA . "/c delete" . C::GRAY . " - Delete your Clan (must be leader)!";
    return $menu;
    }
}
