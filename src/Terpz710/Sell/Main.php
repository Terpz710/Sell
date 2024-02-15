<?php

declare(strict_types=1);

namespace Terpz710\Sell;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use Terpz710\Sell\Commands\SellCommand;
use Terpz710\Sell\Commands\SellAllCommand;
use Terpz710\Sell\Economy\EconomyManager;

class Main extends PluginBase {

    public function onEnable(): void {
        $this->saveResource("items.yml");

        $economyManager = new EconomyManager($this);

        $this->getServer()->getCommandMap()->registerAll("Sell", [
			new SellCommand($this),
			new SellAllCommand($this)
		]);
    }
}
