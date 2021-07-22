<?php

namespace Clarence2810\tagmessages;

use pocketmine\{
	Player,
	command\Command,
	command\CommandSender,
	event\Listener,
	event\player\PlayerChatEvent,
	plugin\PluginBase,
	utils\Textformat as C,
};
class Main extends PluginBase implements Listener
{
	public $datas = ["currently" => [], "upcoming" => [], "showtag" => [], "stop" => []];
	private const PREFIX = C::BOLD . C::AQUA . "TagMessages >> " . C::RESET;
	
	public function onEnable(){
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if($this->getConfig()->get("config-version") < 1 or $this->getConfig()->get("config-version") === null){
            $this->getLogger()->error("Your config file is outdated delete it and a new one will automatically added!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool{
		if(!$sender instanceof Player){
			$sender->sendMessage(self::PREFIX . $this->getConfig()->get("player-only"));
			return false;
		}
		if(empty($args)){
			$sender->sendMessage(self::PREFIX . C::RED . "Usage: /tagmessages <on:off>");
			return false;
		}
		switch(strtolower($args[0])){
			case "on":
			case "enable":
				if(!in_array($sender->getName(), $this->datas["showtag"])){
					$sender->sendMessage(self::PREFIX . $this->getConfig()->get("not-even-off"));
					return false;
				}
				unset($this->datas["showtag"][array_search($sender->getName(), $this->datas["showtag"])]);
				$sender->sendMessage(self::PREFIX . $this->getConfig()->get("tagmessages-on"));
			break;
			case "off":
			case "disable":
				if(in_array($sender->getName(), $this->datas["showtag"])){
					$sender->sendMessage(self::PREFIX . $this->getConfig()->get("not-even-on"));
					return false;
				}
				$sender->sendMessage(self::PREFIX . $this->getConfig()->get("tagmessages-off"));
				$this->datas["showtag"][] = $sender->getName();
			break;
			default;
				$sender->sendMessage(self::PREFIX . C::RED . "Usage: /tagmessages <on:off>");
			return false;
		}
		return true;
	}
	/*** @var TagTask[] */
	public $tasks = [];
	public function onChat(PlayerChatEvent $event):void{
		$player = $event->getPlayer();
		if(in_array($player->getName(), $this->datas["showtag"])) return;
		// cancel if there is a task still
		if(($this->tasks[$player->getName()] ?? null) instanceof TagTask && $this->tasks[$player->getName()]->getHandler())
			$this->tasks[$player->getName()]->getHandler()->cancel();
		$task = new TagTask($this, $player, $event->getMessage(), $this->getConfig()->get("tag-cooldown"));
		$this->tasks[$player->getName()] = $task;
		$this->getScheduler()->scheduleRepeatingTask($task, 20);
		if(in_array($player->getName(), $this->datas["currently"])) $this->datas["upcoming"][] = $player->getName();
		if(!in_array($player->getName(), $this->datas["currently"])) $this->datas["currently"][] = $player->getName();
	}
}
