<?php

declare(strict_types=1);

namespace terpz710\sell\manager;

use pocketmine\player\Player;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\item\StringToItemParser;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

use terpz710\sell\Main;

use terpz710\sell\utils\Error;
use terpz710\sell\utils\Message;

final class SellManager {
    use SingletonTrait;

    public Main $plugin;

    public Config $itemsConfig;
    public Config $autoSellConfig;

    public function __construct() {
        $this->plugin = Main::getInstance();
        $this->itemsConfig = new Config($this->plugin->getDataFolder() . "items.yml", Config::YAML);
        $this->autoSellConfig = new Config($this->plugin->getDataFolder() . "autosell.json", Config::JSON);
    }

    public function sellHand(Player $player, Item $item, int $amount) : void{
        $sellableItems = $this->itemsConfig->get("items", []);
        foreach ($sellableItems as $entry) {
            if (!isset($entry["id"], $entry["price"])) {
                continue;
            }

            $parsedItem = StringToItemParser::getInstance()->parse($entry["id"]);
            if ($parsedItem !== null && $parsedItem->equals($item)) {
                $pricePerItem = $entry["price"];
                $total = $pricePerItem * $amount;

                $playerInventory = $player->getInventory();
                $currentItem = $playerInventory->getItemInHand();
                if ($currentItem->getCount() >= $amount) {
                    $currentItem->setCount($currentItem->getCount() - $amount);
                    $playerInventory->setItemInHand($currentItem);

                    $this->plugin->getEconomyManager()->addMoney($player, $total, function (bool $success) use ($player, $amount, $currentItem, $total) {
                        if ($success) {
                            $player->sendMessage((string) new Message("successfully-sold-item", ["{amount}", "{item_name}", "{total}"], [$amount, $currentItem->getVanillaName(), $total]));
                        } else {
                            $player->sendMessage(Error::TYPE_CANNOT_SELL_ITEM);
                        }
                    });
                } else {
                    $player->sendMessage((string) new Message("not-enough-item"));
                }

                return;
            }
        }

        $player->sendMessage((string) new Message("item-not-sellable"));
    }

    public function sellItems(Player $player, Item $item) : void{
        $inventory = $player->getInventory();
        $items = $inventory->getContents();
        $total = 0;

        foreach ($items as $slot => $inventoryItem) {
            if ($inventoryItem->equals($item)) {
                $sellableItems = $this->itemsConfig->get("items", []);
                foreach ($sellableItems as $entry) {
                    if (!isset($entry["id"], $entry["price"])) {
                        continue;
                    }

                    $parsedItem = StringToItemParser::getInstance()->parse($entry["id"]);
                    if ($parsedItem !== null && $parsedItem->equals($inventoryItem)) {
                        $pricePerItem = $entry["price"];
                        $total += $pricePerItem * $inventoryItem->getCount();

                        $inventory->setItem($slot, VanillaItems::AIR());
                        break;
                    }
                }
            }
        }

        $this->plugin->getEconomyManager()->addMoney($player, $total, function (bool $success) use ($player, $item, $total) {
            if ($success) {
                $player->sendMessage((string) new Message("successfully-sold-all-the-same-item", ["{item_name}", "{total}"], [$item->getVanillaName(), $total]));
            } else {
                $player->sendMessage(Error::TYPE_CANNOT_SELL_ITEM);
            }
        });
    }

    public function sellAll(Player $player) : void{
        $inventory = $player->getInventory();
        $items = $inventory->getContents();
        $total = 0;

        foreach ($items as $slot => $item) {
            $sellableItems = $this->itemsConfig->get("items", []);
            foreach ($sellableItems as $entry) {
                if (!isset($entry["id"], $entry["price"])) {
                    continue;
                }

                $parsedItem = StringToItemParser::getInstance()->parse($entry["id"]);
                if ($parsedItem !== null && $parsedItem->equals($item)) {
                    $pricePerItem = $entry["price"];
                    $total += $pricePerItem * $item->getCount();

                    $inventory->setItem($slot, VanillaItems::AIR());
                    break;
                }
            }
        }

        $this->plugin->getEconomyManager()->addMoney($player, $total, function (bool $success) use ($player, $total) {
            if ($success) {
                $player->sendMessage((string) new Message("successfully-sold-all-item", ["{total}"], [$total]));
            } else {
                $player->sendMessage(Error::TYPE_CANNOT_SELL_ITEM);
            }
        });
    }

    public function autoSell(Player $player) : void{
        $inventory = $player->getInventory();
        $items = $inventory->getContents();
        $total = 0;

        foreach ($items as $slot => $item) {
            $sellableItems = $this->itemsConfig->get("items", []);
            foreach ($sellableItems as $entry) {
                if (!isset($entry["id"], $entry["price"])) {
                    continue;
                }

                $parsedItem = StringToItemParser::getInstance()->parse($entry["id"]);
                if ($parsedItem !== null && $parsedItem->equals($item)) {
                    $pricePerItem = $entry["price"];
                    $total += $pricePerItem * $item->getCount();

                    $inventory->setItem($slot, VanillaItems::AIR());
                    break;
                }
            }
        }

        if ($total > 0) {
            $this->plugin->getEconomyManager()->addMoney($player, $total, function (bool $success) use ($player, $total) {
                if ($success) {
                    $player->sendMessage((string) new Message("successfully-auto-sold-item", ["{total}"], [$total]));
                } else {
                    $player->sendMessage(Error::TYPE_CANNOT_SELL_ITEM);
                }
            });
        }
    }

    public function hasAutoSellOn(Player $player) : bool{
        return $this->autoSellConfig->exists($player->getName());
    }

    public function setAutoSellStatus(Player $player, bool $type) : void{
        if ($type) {
            if (!$this->hasAutoSellOn($player)) {
                $this->autoSellConfig->set($player->getName(), true);
                $this->autoSellConfig->save();
            }
        } else {
            if ($this->hasAutoSellOn($player)) {
                $this->autoSellConfig->remove($player->getName());
                $this->autoSellConfig->save();
            }
        }
    }
}