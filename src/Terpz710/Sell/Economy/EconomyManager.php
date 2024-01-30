<?php

declare(strict_types=1);

namespace Terpz710\Sell\Economy;

use Closure;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

use cooldogedev\BedrockEconomy\libs\cooldogedev\libSQL\context\ClosureContext;
use onebone\economyapi\EconomyAPI;
use Terpz710\Sell\Main;

class EconomyManager {
    /** @var Plugin|null $eco */
    private ?Plugin $eco;
    /** @var Main $plugin */
    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $manager = $plugin->getServer()->getPluginManager();
        $this->eco = $manager->getPlugin("EconomyAPI") ?? $manager->getPlugin("BedrockEconomy") ?? null;
        unset($manager);
    }

    public function getMoney(Player $player, Closure $callback): void {
        switch ($this->eco->getName()) {
            case "EconomyAPI":
                $money = $this->eco->myMoney($player->getName());
                assert(is_float($money));
                $callback($money);
                break;
            case "BedrockEconomy":
                $this->eco->legacy()->getPlayerBalance($player->getName(), ClosureContext::create(static function(?int $balance) use($callback) : void {
                    $callback($balance ?? 0);
                }));
                break;
            default:
                $this->eco->legacy()->getPlayerBalance($player->getName(), ClosureContext::create(static function(?int $balance) use($callback) : void {
                    $callback($balance ?? 0);
                }));
        }
    }

    public function reduceMoney(Player $player, int $amount, Closure $callback): void {
        if ($this->eco == null) {
            $this->plugin->getLogger()->warning("You don't have an Economy plugin");
            return;
        }
        switch ($this->eco->getName()) {
            case "EconomyAPI":
                $callback($this->eco->reduceMoney($player->getName(), $amount) === EconomyAPI::RET_SUCCESS);
                break;
            case "BedrockEconomy":
                $this->eco->legacy()->subtractFromPlayerBalance($player->getName(), (int) ceil($amount), ClosureContext::create(static function(bool $success) use($callback) : void {
                    $callback($success);
                }));
                break;
        }
    }

    public function addMoney(Player $player, int $amount, Closure $callback): void {
        if ($this->eco == null) {
            $this->plugin->getLogger()->warning("You don't have an Economy plugin");
            return;
        }
        switch ($this->eco->getName()) {
            case "EconomyAPI":
                $callback($this->eco->addMoney($player->getName(), $amount, EconomyAPI::RET_SUCCESS));
                break;
            case "BedrockEconomy":
                $this->eco->legacy()->addToPlayerBalance($player->getName(), (int) ceil($amount), ClosureContext::create(static function(bool $success) use($callback) : void {
                    $callback($success);
                }));
                break;
        }
    }
}
