<?php

declare(strict_types=1);

namespace terpz710\sell\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use pocketmine\player\Player;

use terpz710\sell\Main;

use terpz710\sell\utils\Error;
use terpz710\sell\utils\Message;
use terpz710\sell\utils\Permission;

class AutoSellCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct() {
        parent::__construct("autosell");
        $this->setDescription("Enable/Disable auto-sell");
        $this->setPermission(Permission::SELL_AUTO);

        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(Error::TYPE_USE_COMMAND_INGAME);
            return true;
        }

        $sellManager = $this->plugin->getSellManager();
        $autoSellStatus = $sellManager->hasAutoSellOn($sender);

        if ($autoSellStatus) {
            $sellManager->setAutoSellStatus($sender, false);
            $sender->sendMessage((string) new Message("disable-auto-sell"));
        } else {
            $sellManager->setAutoSellStatus($sender, true);
            $sender->sendMessage((string) new Message("enable-auto-sell"));
        }
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}