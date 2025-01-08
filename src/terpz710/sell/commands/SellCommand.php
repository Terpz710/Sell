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

class SellCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct() {
        parent::__construct("sell");
        $this->setDescription("Sell a specific type of item from your inventory");
        $this->setPermission(Permission::SELL_ITEM);

        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(Error::TYPE_USE_COMMAND_INGAME);
            return true;
        }

        $itemInHand = $sender->getInventory()->getItemInHand();
        if ($itemInHand->isNull()) {
            $sender->sendMessage((string) new Message("must-hold-item"));
            return true;
        }

        $this->plugin->getSellManager()->sellItems($sender, $itemInHand);
        return true;
    }

        public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}