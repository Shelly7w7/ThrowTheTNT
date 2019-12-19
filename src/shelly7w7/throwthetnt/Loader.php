<?php
declare(strict_types=1);

namespace shelly7w7\throwthetnt;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Loader extends PluginBase{

    /** @var Config $config */
    protected $config;
    /** @var self $instance */

    protected static $instance;

    public function onEnable() : void{
        self::$instance = $this;
        $this->getServer()->getCommandMap()->register("throwtnt", new ThrowTntCommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        @mkdir($this->getDataFolder());
        $this->config = $this->saveDefaultConfig();
    }

    public function getTntConfig() : Config{
        return $this->config;
    }

    public static function getInstance() : self{
        return self::$instance;
    }
}