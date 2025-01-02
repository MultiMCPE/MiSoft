<?php

namespace pocketmine\item;

use pocketmine\network\mcpe\protocol\ProtocolInfo;

class NetheriteShovel extends Shovel
{

    public function __construct($meta = 0){
        parent::__construct(self::NETHERITE_SHOVEL, $meta, "Netherite Shovel", self::TIER_NETHERITE);
    }

    public function getItemProtocol(int $protocol) : ?Item{
        if($protocol < ProtocolInfo::PROTOCOL_406){
            return ItemFactory::get(ItemIds::DIAMOND_SHOVEL, 0);
        }

        return parent::getItemProtocol($protocol);
    }
}