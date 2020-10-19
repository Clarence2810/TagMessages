<?php

namespace Clarence2810\tagmessages;

use Clarence2810\tagmessages\Main;
use pocketmine\scheduler\Task;
use pocketmine\Player;
class TagTask extends Task
{
	public $show = 10;
	
	public function __construct(Player $player, $message){
		$this->player = $player;
		$this->message = $message;
	}
	public function onRun(int $currentTick){
		$this->show--;
		$this->player->setScoreTag($this->message);
		if($this->show <= 1){
			Main::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}
	}
}