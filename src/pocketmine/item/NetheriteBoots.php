<?php

namespace pocketmine\item;

use pocketmine\inventory\ArmorInventory;
use pocketmine\network\mcpe\protocol\ProtocolInfo;

class NetheriteBoots extends Armor {
    public function __construct($meta = 0){
        parent::__construct(ItemIds::NETHERITE_BOOTS, $meta, "Netherite Boots");
    }

    public function getDefensePoints() : int{
        return 3;
    }

    public function getMaxDurability() : int{
        return 482;
    }

    public function getArmorSlot() : int{
        return ArmorInventory::SLOT_FEET;
    }

    public function getItemProtocol(int $protocol) : ?Item{
        if($protocol < ProtocolInfo::PROTOCOL_406){
            return ItemFactory::get(ItemIds::CHAIN_BOOTS, 0);
        }

        return parent::getItemProtocol($protocol);
    }
}