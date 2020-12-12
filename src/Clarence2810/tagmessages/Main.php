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
	public static $tm = []; // Switch on and off
	public static $first = []; // Secret formula
	public static $new = []; // Secret formula
	private const PREFIX = C::BOLD . C::AQUA . "TagMessages >> " . C::RESET;
	private static $instance;
	
	public static function getInstance(): Main{
		return self::$instance;
	}
	
	public function onLoad(){
		self::$instance = $this;
	}
	
	public function onEnable()
	{
		$this->saveResource("config.yml");
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if($this->getConfig()->get("config-version") < 1 or $this->getConfig()->get("config-version") == null){
            $this->getLogger()->error("Your config file is outdated delete it and it will automatically updated!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
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
						$sender->sendMessage(self::PREFIX . $this->getConfig()->get("tagmessages-on"));
						unset(self::$tm[array_search($sender->getName(), self::$tm)]);
					}else{
						$sender->sendMessage(self::PREFIX . $this->getConfig()->get("not-even-off"));
					}
					break;
					case "off":
					case "disable":
					if(!in_array($sender->getName(), self::$tm)){
						$sender->sendMessage(self::PREFIX . $this->getConfig()->get("tagmessages-off"));
						self::$tm[] = $sender->getName();
					}else{
						$sender->sendMessage(self::PREFIX . $this->getConfig()->get("not-even-on"));
					}
					break;
					case "about":
					case "info":
					$sender->sendMessage(self::PREFIX . "TagMessages by Clarence2810");
					$sender->sendMessage(C::GREEN . "Contact on Discord: " . C::AQUA . "Clarence2810#7952");
					break;
					default;
					$sender->sendMessage(self::PREFIX . C::RED . "Usage: /tagmessages <on:off:about>");
					return true;
				}
			}
		}else{
			$sender->sendMessage(self::PREFIX . $this->getConfig()->get("player-only"));
		}
		return true;
	}
	public function onChat(PlayerChatEvent $event){
		$player = $event->getPlayer();
		$message = $event->getMessage();
		if(!in_array($player->getName(), self::$tm)){
			$this->getScheduler()->scheduleRepeatingTask(new TagTask($player, $message, $this->getConfig()->get("tag-cooldown")), 20);
			if(in_array($player->getName(), self::$first)){
				self::$new[] = $player->getName();
			}
		}
	}
}