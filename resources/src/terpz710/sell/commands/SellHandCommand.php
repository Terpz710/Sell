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

class SellHandCommand extends Command implements PluginOwned {

    private $plugin;

    public function __construct() {
        parent::__construct("sellhand");
        $this->setDescription("Sell the item currently in your hand");
        $this->setPermission(Permission::SELL_HAND);

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

        $amount = 1;

        if (!empty($args) && is_numeric($args[0])) {
            $amount = (int)$args[0];

            if ($amount <= 0) {
                $sender->sendMessage((string) new Message("must-be-positive-amount"));
                return true;
            }
        }

        if ($amount > 64) {
            $sender->sendMessage((string) new Message("must-not-exceed-stack-limit"));
            return true;
        }

        $this->plugin->getSellManager()->sellHand($sender, $itemInHand, $amount);
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}