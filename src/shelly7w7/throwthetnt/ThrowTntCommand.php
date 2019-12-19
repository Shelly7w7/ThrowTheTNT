<?php
declare(strict_types=1);

namespace shelly7w7\throwthetnt;

use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use function number_format;
use function strtolower;

class ThrowTntCommand extends Command
{
    /** @var Loader */
    private $plugin;
    public function __construct(Loader $plugin)
    {
        parent::__construct("throwtnt", "Give someone throwable tnt!", "/throwtnt", ["tht"]);
        $this->setPermission("throwthetnt.use.command");
        $this->plugin = $plugin;
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermissionSilent($sender)) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command");
            return;
        }
        if(count($args) < 2){
            $sender->sendMessage(TextFormat::RED . "(!) Use " . TextFormat::YELLOW . "/throwtnt (name) (amount)");
            $this->plugin->getTntConfig()->reload();
            return;
        }
        $target = $this->plugin->getServer()->getPlayer($args[0]);
        if($target === null){
            $sender->sendMessage(TextFormat::RED . "(!) Player not found.");

            return;
        }
        $amount = (int) ($args[1] ?? 1);
        if($amount < 0){
            $sender->sendMessage(TextFormat::RED . "(!) Amount must be more than " . TextFormat::YELLOW . "0");
            return;
        }
        $config = $this->plugin->getTntConfig();
        $item = Item::get(Item::TNT, 0, $amount);
        $item->setCustomName((string)($config->getNested("configuration.customname") ?? "Throwable tnt"));
        $item->setLore([$config->getNested("configuration.lore") ?? ""]);
        $item->setCustomBlockData(new CompoundTag("", [
            new StringTag("throwabletnt")
        ]));
        $target->getInventory()->addItem($item);
        $sender->sendMessage(TextFormat::RED . "(!) You gave " . strtolower($target->getName()) . " x" . number_format($amount) . TextFormat::YELLOW . " throwable tnt(s)!");
        if($target->getName() !== $sender->getName()){
            $target->sendMessage(TextFormat::RED . "(!) You have been given x" . number_format($amount) . TextFormat::YELLOW . " throwable tnt(s)!");
        }
    }
}