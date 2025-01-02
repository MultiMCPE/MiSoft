<?php

namespace pocketmine\item;

use pocketmine\network\mcpe\protocol\ProtocolInfo;

class NetheriteAxe extends Axe
{
    public function __construct($meta = 0){
        parent::__construct(self::NETHERITE_AXE, $meta, "Netherite Axe", self::TIER_NETHERITE);
    }

    public function getItemProtocol(int $protocol) : ?Item{
        if($protocol < ProtocolInfo::PROTOCOL_406){
            return ItemFactory::get(ItemIds::DIAMOND_AXE, 0);
        }

        return parent::getItemProtocol($protocol);
    }
}