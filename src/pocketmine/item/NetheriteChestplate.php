<?php

namespace pocketmine\item;

use pocketmine\inventory\ArmorInventory;
use pocketmine\network\mcpe\protocol\ProtocolInfo;

class NetheriteChestplate extends Armor{
    public function __construct(int $meta = 0){
        parent::__construct(ItemIds::NETHERITE_CHESTPLATE, $meta, "Netherite Chestplate");
    }

    public function getDefensePoints() : int{
        return 8;
    }

    public function getMaxDurability() : int{
        return 593;
    }

    public function getArmorSlot() : int{
        return ArmorInventory::SLOT_CHEST;
    }

    public function getItemProtocol(int $protocol) : ?Item{
        if($protocol < ProtocolInfo::PROTOCOL_406){
            return ItemFactory::get(ItemIds::CHAIN_CHESTPLATE, 0);
        }

        return parent::getItemProtocol($protocol);
    }
}