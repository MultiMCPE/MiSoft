<?php

namespace pocketmine\item;

use pocketmine\inventory\ArmorInventory;
use pocketmine\network\mcpe\protocol\ProtocolInfo;

class NetheriteLeggings extends Armor{
    public function __construct(int $meta = 0){
        parent::__construct(ItemIds::NETHERITE_LEGGINGS, $meta, "Netherite Leggings");
    }

    public function getDefensePoints() : int{
        return 6;
    }

    public function getMaxDurability() : int{
        return 556;
    }

    public function getArmorSlot() : int{
        return ArmorInventory::SLOT_LEGS;
    }

    public function getItemProtocol(int $protocol) : ?Item{
        if($protocol < ProtocolInfo::PROTOCOL_406){
            return ItemFactory::get(ItemIds::CHAIN_LEGGINGS, 0);
        }

        return parent::getItemProtocol($protocol);
    }
}