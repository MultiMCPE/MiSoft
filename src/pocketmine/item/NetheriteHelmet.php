<?php

namespace pocketmine\item;

use pocketmine\inventory\ArmorInventory;
use pocketmine\network\mcpe\protocol\ProtocolInfo;

class NetheriteHelmet extends Armor{
    public function __construct(int $meta = 0){
        parent::__construct(ItemIds::NETHERITE_HELMET, $meta, "Netherite Helmet");
    }

    public function getDefensePoints() : int{
        return 3;
    }

    public function getMaxDurability() : int{
        return 408;
    }

    public function getArmorSlot() : int{
        return ArmorInventory::SLOT_HEAD;
    }

    public function getItemProtocol(int $protocol) : ?Item{
        if($protocol < ProtocolInfo::PROTOCOL_406){
            return ItemFactory::get(ItemIds::CHAIN_HELMET, 0);
        }

        return parent::getItemProtocol($protocol);
    }
}