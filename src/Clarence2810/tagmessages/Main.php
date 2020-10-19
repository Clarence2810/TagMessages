<?php

namespace Clarence2810\tagmessages;

use pocketmine\{
	Player,
	Server,
	plugin\PluginBase,
	utils\Textformat as C,
	event\Listener,
	event\player\PlayerChatEvent,
	command\Command,
	command\CommandSender,
};;
class Main extends PluginBase implements Listener
{
	private const PREFIX = C::BOLD . C::AQUA . "TagMessages >> " . C::RESET;
	private $running = [];
	private static $tm = [];
	private static $instance;
	
	public static function getInstance(): Main{
		return self::$instance;
	}
	
	public function onLoad(){
		self::$instance = $this;
	}
	
	public function onEnable()
	{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool{
		if($sender instanceof Player){
			if(strtolower($cmd->getName()) === "tagmessages"){
				if(empty($args)){
					$sender->sendMessage(self::PREFIX . C::RED . "Usage: /tagmessages <on:off:about>");
					return true;
				}
				switch(strtolower($args[0])){
					case "on":
					case "enable":
					if(in_array($sender->getName(), self::$tm)){
						$sender->sendMessage(self::PREFIX . C::GREEN . "Everyone can now see your messages below of your name!");
						unset(self::$tm[array_search($sender->getName(), self::$tm)]);
					}else{
						$sender->sendMessage(self::PREFIX . C::RED . "Your TagMessages is not even turned off");
					}
					break;
					case "off":
					case "disable":
					if(!in_array($sender->getName(), self::$tm)){
						$sender->sendMessage(self::PREFIX . C::GREEN . "Everyone can't now see your messages below of your name!");
						self::$tm[] = $sender->getName();
					}else{
						$sender->sendMessage(self::PREFIX . C::RED . "Your TagMessages is not even turned on");
					}
					break;
					case "about":
					case "info":
					$sender->sendMessage(self::PREFIX . C::YELLOW . "Version: " . C::AQUA . $this->getDescription()->getVersion() . C::YELLOW . " by " . C::AQUA . "Clarence2810");
					$sender->sendMessage(C::GREEN . "Contact on Discord: " . C::AQUA . "Clarence2810#7952");
					break;
					default;
					$sender->sendMessage(self::PREFIX . C::RED . "Usage: /tagmessages <on:off:about>");
					return true;
				}
			}
		}else{
			$sender->sendMessage(self::PREFIX . C::RED . "Im sorry but only players can run this command!");
		}
		return true;
	}
	public function onChat(PlayerChatEvent $event){
		$player = $event->getPlayer();
		$message = $event->getMessage();
		if(!in_array($player->getName(), self::$tm)){
			$this->getScheduler()->scheduleRepeatingTask(new TagTask($player, $message), 20);
		}
	}
}