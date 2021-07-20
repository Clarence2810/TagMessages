<?php

namespace Clarence2810\tagmessages;

use pocketmine\{
	Player,
	scheduler\Task,
};
class TagTask extends Task
{
	private $main;
	private $player;
	private $message;
	private $cooldown;
	
	public function __construct(Main $main, Player $player, string $message, int $cooldown = 10){
		$this->main = $main;
		$this->player = $player;
		$this->message = $message;
		$this->cooldown = $cooldown;
	}
	
	public function onRun(int $currentTick){
		$this->cooldown--;
		if(!$this->player->isOnline()) $this->main->getInstance()->getScheduler()->cancelTask($this->getTaskId());
		if(in_array($this->player->getName(), $this->main->datas["upcoming"])){
			unset($this->main->datas["upcoming"][array_search($this->player->getName(), $this->main->datas["upcoming"])]);
			$this->player->setScoreTag($this->message);
			$this->main->datas["stop"][] = $this->player->getName();
		}else{
			if(!in_array($this->player->getName(), $this->main->datas["stop"])){
				$this->player->setScoreTag($this->message);
			}else{
				$this->main->getScheduler()->cancelTask($this->getTaskId());
				unset($this->main->datas["stop"][array_search($this->player->getName(), $this->main->datas["stop"])]);
			}
		}
		if($this->cooldown < 1){
			unset($this->main->datas["upcoming"][array_search($this->player->getName(), $this->main->datas["upcoming"])]);
			unset($this->main->datas["currently"][array_search($this->player->getName(), $this->main->datas["currently"])]);
			$this->main->getScheduler()->cancelTask($this->getTaskId());
			$this->player->setScoreTag("");
		}
	}
}