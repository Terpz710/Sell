<?php

declare(strict_types=1);

namespace Terpz710\Sell\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;
use pocketmine\player\Player;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\utils\Config;

class SellCommand extends Command implements PluginOwned {

    private $plugin;
    private $itemsConfig;

    public function __construct(Plugin $plugin) {
        parent::__construct("sell", "Sell the item you are holding");
        $this->plugin = $plugin;
        $this->setPermission("sell.sell");
        $this->itemsConfig = new Config($this->plugin->getDataFolder() . "items.yml", Config::YAML);
    }

    public function getOwningPlugin(): Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender instanceof Player) {
            $itemInHand = $sender->getInventory()->getItemInHand();

            if ($itemInHand->equals(VanillaItems::AIR())) {
                $sender->sendMessage("§l§c(§f!§c) §r§fYou are not holding any items to sell.");
                return true;
            }

            $sellableItems = $this->itemsConfig->get("items", []);

            foreach ($sellableItems as $sellableItem) {
                if ($itemInHand->equals(StringToItemParser::getInstance()->parse($sellableItem))) {
                    $sender->getInventory()->removeItem($itemInHand);
                    $sender->sendMessage("You have sold the item:§b " . $itemInHand->getName());
                    return true;
                }
            }

            $sender->sendMessage("§l§c(§f!§c) §r§fThis item cannot be sold!");
        } else {
            $sender->sendMessage("This command can only be used by players.");
        }

        return true;
    }
}
