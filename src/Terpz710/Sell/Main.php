<?php

declare(strict_types=1);

namespace Terpz710\Sell;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use Terpz710\Sell\Commands\SellCommand;

class Main extends PluginBase {

    public function onEnable(): void {
        $this->saveResource("items.yml");

        $this->getServer()->getCommandMap()->register("sell", new SellCommand($this));
    }
}
