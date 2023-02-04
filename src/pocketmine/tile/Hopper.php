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

namespace pocketmine\tile;

use pocketmine\inventory\HopperInventory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\item\Item;
use pocketmine\inventory\InventoryHolder;
use pocketmine\math\Vector3;

class Hopper extends Spawnable implements Container, Nameable{

	use ContainerTrait;
	use NameableTrait;

	private const TAG_TRANSFER_COOLDOWN = "TransferCooldown";

	/** @var HopperInventory */
	private $inventory;

	/** @var int */
	private $transferCooldown = 0;

	public function readSaveData(CompoundTag $nbt) : void{
	    $this->inventory = new HopperInventory($this);

		$this->loadItems($nbt);
		$this->loadName($nbt);

		$this->transferCooldown = $nbt->getInt(self::TAG_TRANSFER_COOLDOWN, 0);

		$this->scheduleUpdate();
	}

	protected function writeSaveData(CompoundTag $nbt) : void{
		$this->saveItems($nbt);
		$this->saveName($nbt);

		$nbt->setInt(self::TAG_TRANSFER_COOLDOWN, $this->transferCooldown);
	}

	public function close() : void{
		if(!$this->closed){
			$this->inventory->removeAllViewers(true);
			$this->inventory = null;

			parent::close();
		}
	}

	public function getDefaultName() : string{
		return "Hopper";
	}

	/**
	 * @return HopperInventory
	 */
	public function getInventory(){
		return $this->inventory;
	}

	/**
	 * @return HopperInventory
	 */
	public function getRealInventory(){
		return $this->inventory;
	}

    public function canUpdate(){
        return $this->namedtag->TransferCooldown->getValue() === 0;
    }

    public function resetCooldownTicks(){
        $this->namedtag->TransferCooldown->setValue(8);
    }

    public function onUpdate(): bool{
        //Pickup dropped items
        //This can happen at any time regardless of cooldown
        $area = clone $this->getBlock()->getBoundingBox(); //Area above hopper to draw items from
        $area->maxY = ceil($area->maxY) + 1; //Account for full block above, not just 1 + 5/8
        foreach($this->getLevel()->getChunkEntities($this->getBlock()->x >> 4, $this->getBlock()->z >> 4) as $entity){
            if(!($entity instanceof Item) or !$entity->isAlive()){
                continue;
            }
            if(!$entity->boundingBox->intersectsWith($area)){
                continue;
            }

            $item = $entity->getItem();
            if(!$item instanceof Item){
                continue;
            }
            if($item->getCount() < 1){
                $entity->kill();
                continue;
            }

            if($this->inventory->canAddItem($item)){
                $this->inventory->addItem($item);
                $entity->kill();
            }
        }

        if(!$this->canUpdate()){ //Hoppers only update CONTENTS every 8th tick
            $this->namedtag->TransferCooldown->setValue($this->namedtag->TransferCooldown->getValue() - 1);
            return true;
        }
 
        //дюперы, я ебал вашу маму.
        $source = $this->getLevel()->getTile($this->getBlock()->getSide(Vector3::SIDE_UP));
        if($source instanceof ShulkerBox) {
            return true;
        }

        //Suck items from above tile inventories
        if($source instanceof Tile and $source instanceof InventoryHolder){
            $inventory = $source->getInventory();
            $item = clone $inventory->getItem($inventory->firstOccupied());
            $item->setCount(1);
            if($this->inventory->canAddItem($item)){
                $this->inventory->addItem($item);
                $inventory->removeItem($item);
                $this->resetCooldownTicks();
                if($source instanceof Hopper){
                    $source->resetCooldownTicks();
                }
            }
        }

        //Feed item into target inventory
        //Do not do this if there's a hopper underneath this hopper, to follow vanilla behaviour
        if(!($this->getLevel()->getTile($this->getBlock()->getSide(Vector3::SIDE_DOWN)) instanceof Hopper)){
            $target = $this->getLevel()->getTile($this->getBlock()->getSide($this->getBlock()->getDamage()));
            if($target instanceof Tile and $target instanceof InventoryHolder){
                $inv = $target->getInventory();
                foreach($this->inventory->getContents() as $item){
                    if($item->getId() === Item::AIR or $item->getCount() < 1){
                        continue;
                    }

                    $targetItem = clone $item;
                    $targetItem->setCount(1);

                    if($inv->canAddItem($targetItem)){
                        $this->inventory->removeItem($targetItem);
                        $inv->addItem($targetItem);
                        $this->resetCooldownTicks();
                        if($target instanceof Hopper){
                            $target->resetCooldownTicks();
                        }
                        break;
                    }

                }
            }
        }

        return true;
    }
}
