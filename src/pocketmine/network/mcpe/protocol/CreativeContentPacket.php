<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use pocketmine\item\Item;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\types\CreativeGroup;
use function count;

class CreativeContentPacket extends DataPacket{
    public const NETWORK_ID = ProtocolInfo::CREATIVE_CONTENT_PACKET;

    /** @var array|CreativeGroup */
    public $groups = [];
    /** @var array */
    public $items = [];

	public function mayHaveUnreadBytes() : bool{
		return true;
	}

    protected function decodePayload(){
    }

    protected function encodePayload(){
        if($this->getProtocol() < ProtocolInfo::PROTOCOL_406){
            $this->putUnsignedVarInt(count($this->groups));
            foreach($this->groups as $group){
                $this->putString($group->getGroupName());
                $this->putLInt($group->getItemLegacyId());
                $this->putUnsignedVarInt(0);
            }

            static $knownGroups = null;
            static $groupsList = [
                0x5 => "itemGroup.name.planks",
                0x8b => "itemGroup.name.walls",
                0x55 => "itemGroup.name.fence",
                0x71 => "itemGroup.name.fence",
                0x6b => "itemGroup.name.fenceGate",
                0xb7 => "itemGroup.name.fenceGate",
                0xb8 => "itemGroup.name.fenceGate",
                0xba => "itemGroup.name.fenceGate",
                0xb9 => "itemGroup.name.fenceGate",
                0xbb => "itemGroup.name.fenceGate",
                0x43 => "itemGroup.name.stairs",
                0x35 => "itemGroup.name.stairs",
                0x86 => "itemGroup.name.stairs",
                0x87 => "itemGroup.name.stairs",
                0x88 => "itemGroup.name.stairs",
                0xa3 => "itemGroup.name.stairs",
                0xa4 => "itemGroup.name.stairs",
                0x6c => "itemGroup.name.stairs",
                0x6d => "itemGroup.name.stairs",
                0x72 => "itemGroup.name.stairs",
                0x80 => "itemGroup.name.stairs",
                0x9c => "itemGroup.name.stairs",
                0xb4 => "itemGroup.name.stairs",
                0x144 => "itemGroup.name.door",
                0x60 => "itemGroup.name.trapdoor",
                0x14 => "itemGroup.name.glass",
                0xf1 => "itemGroup.name.glass",
                0x66 => "itemGroup.name.glassPane",
                0xa0 => "itemGroup.name.glassPane",
                0x2c => "itemGroup.name.slab",
                0x9e => "itemGroup.name.slab",
                0xb6 => "itemGroup.name.slab",
                0x62 => "itemGroup.name.stoneBrick",
                0xce => "itemGroup.name.stoneBrick",
                0x18 => "itemGroup.name.sandstone",
                0x23 => "itemGroup.name.wool",
                0xab => "itemGroup.name.woolCarpet",
                0x52 => "itemGroup.name.stainedClay",
                0xac => "itemGroup.name.stainedClay",
                0x9f => "itemGroup.name.stainedClay",
                0x1 => "itemGroup.name.stone",
                0xf => "itemGroup.name.ore",
                0xe => "itemGroup.name.ore",
                0x38 => "itemGroup.name.ore",
                0x15 => "itemGroup.name.ore",
                0x49 => "itemGroup.name.ore",
                0x10 => "itemGroup.name.ore",
                0x81 => "itemGroup.name.ore",
                0x11 => "itemGroup.name.log",
                0xa2 => "itemGroup.name.log",
                0x12 => "itemGroup.name.wood",
                0xa1 => "itemGroup.name.wood",
                0x6 => "itemGroup.name.sapling",
                0x127 => "itemGroup.name.seed",
                0x169 => "itemGroup.name.seed",
                0x16a => "itemGroup.name.seed",
                0x1ca => "itemGroup.name.seed",
                0x128 => "itemGroup.name.crop",
                0x104 => "itemGroup.name.crop",
                0x142 => "itemGroup.name.crop",
                0x67 => "itemGroup.name.crop",
                0x168 => "itemGroup.name.crop",
                0x56 => "itemGroup.name.crop",
                0x5b => "itemGroup.name.crop",
                0x1f => "itemGroup.name.grass",
                0x25 => "itemGroup.name.flower",
                0x26 => "itemGroup.name.flower",
                0x15f => "itemGroup.name.dye",
                0x27 => "itemGroup.name.mushroom",
                0x28 => "itemGroup.name.mushroom",
                0x17f => "itemGroup.name.mobEgg",
                0x12a => "itemGroup.name.helmet",
                0x12e => "itemGroup.name.helmet",
                0x132 => "itemGroup.name.helmet",
                0x136 => "itemGroup.name.helmet",
                0x13a => "itemGroup.name.helmet",
                0x12b => "itemGroup.name.chestplate",
                0x12f => "itemGroup.name.chestplate",
                0x133 => "itemGroup.name.chestplate",
                0x137 => "itemGroup.name.chestplate",
                0x13b => "itemGroup.name.chestplate",
                0x12c => "itemGroup.name.leggings",
                0x130 => "itemGroup.name.leggings",
                0x134 => "itemGroup.name.leggings",
                0x138 => "itemGroup.name.leggings",
                0x13c => "itemGroup.name.leggings",
                0x12d => "itemGroup.name.boots",
                0x131 => "itemGroup.name.boots",
                0x135 => "itemGroup.name.boots",
                0x139 => "itemGroup.name.boots",
                0x13d => "itemGroup.name.boots",
                0x10c => "itemGroup.name.sword",
                0x110 => "itemGroup.name.sword",
                0x10b => "itemGroup.name.sword",
                0x114 => "itemGroup.name.sword",
                0x11b => "itemGroup.name.sword",
                0x10f => "itemGroup.name.axe",
                0x113 => "itemGroup.name.axe",
                0x102 => "itemGroup.name.axe",
                0x117 => "itemGroup.name.axe",
                0x11e => "itemGroup.name.axe",
                0x10e => "itemGroup.name.pickaxe",
                0x112 => "itemGroup.name.pickaxe",
                0x101 => "itemGroup.name.pickaxe",
                0x116 => "itemGroup.name.pickaxe",
                0x11d => "itemGroup.name.pickaxe",
                0x10d => "itemGroup.name.shovel",
                0x111 => "itemGroup.name.shovel",
                0x100 => "itemGroup.name.shovel",
                0x115 => "itemGroup.name.shovel",
                0x11c => "itemGroup.name.shovel",
                0x122 => "itemGroup.name.hoe",
                0x123 => "itemGroup.name.hoe",
                0x124 => "itemGroup.name.hoe",
                0x125 => "itemGroup.name.hoe",
                0x126 => "itemGroup.name.hoe",
                0x106 => "itemGroup.name.arrow",
                0x15e => "itemGroup.name.cookedFood",
                0x162 => "itemGroup.name.miscFood",
                0x129 => "itemGroup.name.miscFood",
                0x163 => "itemGroup.name.bed",
                0x91 => "itemGroup.name.anvil",
                0x36 => "itemGroup.name.chest",
                0x82 => "itemGroup.name.chest",
                0x143 => "itemGroup.name.sign",
                0x42 => "itemGroup.name.rail",
                0x148 => "itemGroup.name.minecart"
            ];

            if($knownGroups === null){
                $knownGroups = [];
                foreach($groupsList as $itemId => $groupName){
                    $knownGroups[$itemId] = CreativeGroup::fromString($groupName)->getMagicNumber();
                }
            }
        }

        $index = 1;
        $this->putUnsignedVarInt(count($this->items));
        foreach($this->items as $item){
            $this->putUnsignedVarInt($index++);
            if($this->getProtocol() < ProtocolInfo::PROTOCOL_406){
                $this->putUnsignedVarInt($knownGroups[$item->getId()] ?? 0xffffffff);
            }

            $this->putSlot($item, $this->getProtocol());
        }
    }

	public function handle(NetworkSession $session) : bool{
		return $session->handleCreativeContent($this);
	}

}