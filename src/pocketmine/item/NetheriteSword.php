<?php

namespace pocketmine\item;

use pocketmine\network\mcpe\protocol\ProtocolInfo;

class NetheriteSword extends Sword {
    public function __construct($meta = 0){
        parent::__construct(self::NETHERITE_SWORD, $meta, "Netherite Sword", self::TIER_NETHERITE);
    }

    public function getItemProtocol(int $protocol) : ?Item{
        if($protocol < ProtocolInfo::PROTOCOL_406){
            return ItemFactory::get(ItemIds::DIAMOND_SWORD, 0);
        }

        return parent::getItemProtocol($protocol);
    }
}