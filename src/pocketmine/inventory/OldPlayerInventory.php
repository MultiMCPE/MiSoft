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

namespace pocketmine\inventory;

use InvalidArgumentException;
use pocketmine\item\Item;
use pocketmine\network\mcpe\cache\CreativePacketCache;
use pocketmine\network\mcpe\protocol\ContainerSetContentPacket;
use pocketmine\network\mcpe\protocol\types\ContainerIds;
use pocketmine\Player;

class OldPlayerInventory extends PlayerInventory{

    /**
     * @param int $hotbarSlot
     * @param bool $send
     * @param ?int $inventorySlot
     * 
     * @throws InvalidArgumentException
     */
    public function setHeldItemIndex(int $hotbarSlot, bool $send = true, ?int $inventorySlot = null) : void{
        $this->throwIfNotHotbarSlot($hotbarSlot);

        if($inventorySlot === null){
            $inventorySlot = $this->getHotbarSlotIndex($hotbarSlot);
        }

        $this->setHotbarSlotIndex($hotbarSlot, $inventorySlot);
        parent::setHeldItemIndex($hotbarSlot, $send, $inventorySlot);
    }

    /**
     * @return int
     */
    public function getHeldItemSlot() : int{
        return $this->getHotbarSlotIndex($this->itemInHandIndex);
    }

	public function onSlotChange(int $index, Item $before, bool $send) : void{
		if($send){
			$holder = $this->holder;
			if(!($holder instanceof Player) or !$holder->spawned){
				return;
			}
			parent::onSlotChange($index, $before, $send);
		}
		if($index === $this->itemInHandIndex){
			$this->sendHeldItem($this->holder->getViewers());
			if($send){
				$this->sendHeldItem($this->holder);
			}
		}
	}

	public function sendCreativeContents(){
		$pk = new ContainerSetContentPacket();
		$pk->windowid = ContainerIds::CREATIVE;
		if($this->getHolder()->getGamemode() === Player::CREATIVE){
            $creativeItemEntries = CreativePacketCache::getInstance()->getItems($this->getHolder()->getProtocol());
            foreach($creativeItemEntries as $i => $creativeItemEntry){
                $pk->slots[$i] = clone $creativeItemEntry->getItem();
            }
		}
		$pk->targetEid = $this->getHolder()->getId();
		$this->getHolder()->dataPacket($pk);
	}
}
