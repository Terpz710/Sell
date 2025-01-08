<?php

declare(strict_types=1);

namespace terpz710\sell\task;

use pocketmine\scheduler\Task;

use pocketmine\Server;

use terpz710\sell\Main;

class AutoSellTask extends Task {

    public function onRun() : void{
        foreach(Server::getInstance()->getOnlinePlayers() as $player) {
            if (Main::getInstance()->getSellManager()->hasAutoSellOn($player)) {
                Main::getInstance()->getSellManager()->autoSell($player);
            }
        }
    }
}