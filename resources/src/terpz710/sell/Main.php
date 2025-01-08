<?php

declare(strict_types=1);

namespace terpz710\sell;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

use terpz710\sell\commands\SellCommand;
use terpz710\sell\commands\SellAllCommand;
use terpz710\sell\commands\SellHandCommand;
use terpz710\sell\commands\AutoSellCommand;

use terpz710\sell\manager\SellManager;
use terpz710\sell\manager\EconomyManager;

use terpz710\sell\task\AutoSellTask;

final class Main extends PluginBase {

    protected static self $instance;

    protected SellManager $sellmanager;

    protected EconomyManager $eco;

    public Config $messages;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->saveResource("items.yml");
        $this->saveResource("messages.yml");
        $this->getServer()->getCommandMap()->registerAll("Sell", [
            new SellCommand(),
            new SellAllCommand(),
            new SellHandCommand(),
            new AutoSellCommand()
        ]);

        $this->getScheduler()->scheduleRepeatingTask(new AutoSellTask(), 20);

        $this->messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML);

        $this->sellmanager = new SellManager();
        $this->eco = new EconomyManager();
    }

    public static function getInstance() : self{ 
        return self::$instance;
    }

    public function getSellManager() : SellManager{
        return $this->sellmanager;
    }

    public function getEconomyManager() : EconomyManager{
        return $this->eco;
    }
}