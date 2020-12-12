<?php

namespace Clarence2810\tagmessages;

use Clarence2810\tagmessages\Main;
use pocketmine\scheduler\Task;
use pocketmine\Player;

class TagTask extends Task
{
	
	public function __construct(Player $player, $message, $show = 10){
		$this->player = $player;
		$this->message = $message;
		$this->show = $show;
	}
	
	public function onRun(int $currentTick){
		$this->show--;
		if($this->player instanceof Player){
			if(in_array($this->player->getName(), Main::$new)) {
				unset(Main::$new[array_search($this->player->getName(), Main::$new)]);
				unset(Main::$first[array_search($this->player->getName(), Main::$first)]);
				$this->player->setScoreTag($this->message);
				Main::$stop[] = $this->player->getName();
			}else{
				if(!in_array($this->player->getName(), Main::$stop)){
					$this->player->setScoreTag($this->message);
				}else{
					Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
					unset(Main::$stop[array_search($this->player->getName(), Main::$stop)]);
				}
			}
			if($this->show <= 1){
				unset(Main::$new[array_search($this->player->getName(), Main::$new)]);
				unset(Main::$first[array_search($this->player->getName(), Main::$first)]);
				Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
				$this->player->setScoreTag("");
			}
		}else{
			Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}
	}
}