<?php

namespace pocketmine\item;

use pocketmine\network\mcpe\protocol\ProtocolInfo;

class NetheriteIngot extends Item
{
    public function __construct(int $meta = 0)
    {
        parent::__construct(self::NETHERITE_INGOT, $meta, "Netherite Ingot");
    }

    public function getItemProtocol(int $protocol) : ?Item{
        if($protocol < ProtocolInfo::PROTOCOL_406){
            return ItemFactory::get(ItemIds::DIAMOND, 0);
        }

        return parent::getItemProtocol($protocol);
    }
}