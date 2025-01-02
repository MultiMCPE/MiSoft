<?php

namespace pocketmine\item;

use pocketmine\network\mcpe\protocol\ProtocolInfo;

class NetheriteHoe extends Hoe
{
    public function __construct($meta = 0){
        parent::__construct(self::NETHERITE_HOE, $meta, "Netherite Hoe", self::TIER_NETHERITE);
    }

    public function getItemProtocol(int $protocol) : ?Item{
        if($protocol < ProtocolInfo::PROTOCOL_406){
            return ItemFactory::get(ItemIds::DIAMOND_HOE, 0);
        }

        return parent::getItemProtocol($protocol);
    }
}