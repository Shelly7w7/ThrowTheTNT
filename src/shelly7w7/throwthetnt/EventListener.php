<?php
declare(strict_types=1);

namespace shelly7w7\throwthetnt;

use pocketmine\entity\Entity;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use function str_replace;
use function time;

class EventListener implements Listener
{

    /** @var Loader $plugin */
    private $plugin;
    /** @var array $cooldowns */
    private $cooldowns = [];

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onInteract(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $item = $event->getItem();
        if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK ||$event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR) {
            if ($item->getId() === Item::TNT) {
                $nbt = $item->getCustomBlockData();
                if ($nbt !== null && $nbt->hasTag("throwabletnt")) {
                    if(isset($this->cooldowns[$player->getName()]) && $this->cooldowns[$player->getName()] >= time() && !$player->hasPermission("throwthetnt.bypass.cooldown")){
                        $message = $this->plugin->getTntConfig()->getNested("cooldown.message");
                        $message = str_replace("{timer}", gmdate("H:i:s", $this->cooldowns[$player->getName()] - time()), $message);
                        $player->sendMessage($message);
                        return;
                    }
                    /* @var PrimedTNT $entity */
                    $event->setCancelled();
                    $this->cooldowns[$player->getName()] = time() + (int)$this->plugin->getTntConfig()->getNested("cooldown.timer");
                    $entity = Entity::createEntity("PrimedTNT", $player->getLevel(), Entity::createBaseNBT($player));
                    $entity->setMotion($player->getDirectionVector()->normalize()->multiply((float)$this->plugin->getTntConfig()->getNested("configuration.speed")));
                    $entity->spawnToAll();
                }
            }
        }
    }
}