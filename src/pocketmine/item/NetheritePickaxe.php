<?php

namespace pocketmine\item;

use pocketmine\network\mcpe\protocol\ProtocolInfo;

class NetheritePickaxe extends Pickaxe
{
    public function __construct($meta = 0){
        parent::__construct(self::NETHERITE_PICKAXE, $meta, "Netherite Pickaxe", self::TIER_NETHERITE);
    }

    public function getItemProtocol(int $protocol) : ?Item{
        if($protocol < ProtocolInfo::PROTOCOL_406){
            return ItemFactory::get(ItemIds::DIAMOND_PICKAXE, 0);
        }

        return parent::getItemProtocol($protocol);
    }
}