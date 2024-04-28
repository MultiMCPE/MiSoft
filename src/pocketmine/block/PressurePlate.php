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

namespace pocketmine\block;

use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\sound\GenericSound;
use pocketmine\math\Vector3;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;

class PressurePlate extends RedstoneSource{
	/** @var int */
	protected $activateTime = 0;
	/** @var bool */
	protected $canActivate = true;

	/**
	 * @return bool
	 */
	public function hasEntityCollision() : bool{
		return true;
	}

	/**
	 * @param Entity $entity
	 */
	public function onEntityCollide(Entity $entity) : void{
		if($this->canActivate){
			if(!$this->isActivated()){
				$this->meta = 1;
				$this->getLevel()->setBlock($this, $this, true, false);
				$this->getLevel()->addSound(new GenericSound($this, 1000));
			}
			if(!$this->isActivated() or ($this->isActivated() and ($this->getLevel()->getServer()->getTick() % 30) === 0)){
				$this->activate();
			}

			$this->getLevel()->scheduleDelayedBlockUpdate($this, 20);
		}
	}

	/**
	 * @param Block|null $from
	 *
	 * @return bool
	 */
	public function isActivated(Block $from = null) : bool{
		return ($this->meta === 0) ? false : true;
	}

	public function onNearbyBlockChange() : void{
		$below = $this->getSide(Vector3::SIDE_DOWN);
		if($below instanceof Transparent){
			$this->getLevel()->useBreakOn($this);
		}
	}

	public function onScheduledUpdate() : void{
		$this->checkActivation();
	}

	public function checkActivation() : void{
		if(!$this->isActivated()){
			return;
		}

        $entities = $this->getLevel()->getNearbyEntities($this->getHitCollision());
        $entities = array_filter($entities, fn($entity) => $entity instanceof Living);
        if(count($entities) !== 0){
			$this->getLevel()->scheduleDelayedBlockUpdate($this, 20);
			return;
		}

		if((($this->getLevel()->getServer()->getTick() - $this->activateTime)) >= 3){
			$this->meta = 0;
			$this->getLevel()->setBlock($this, $this, true, false);
			$this->deactivate();
		}
	}


    protected function getHitCollision() : AxisAlignedBB{
        return new AxisAlignedBB(
            $this->getX() + 0.0625,
            $this->getY(),
            $this->getZ() + 0.0625,
            $this->getX() + 0.9375,
            $this->getY() + 0.0625,
            $this->getZ() + 0.9375
        );
    }

	/**
	 * @param Item        $item
	 * @param Block       $blockReplace
	 * @param Block       $blockClicked
	 * @param int         $face
	 * @param Vector3     $clickVector
	 * @param Player|null $player
	 *
	 * @return bool
	 */
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$below = $this->getSide(Vector3::SIDE_DOWN);
		if($below instanceof Transparent){
			return false;
		}else{
		    return $this->getLevel()->setBlock($this, $this, true, true);
		}
	}

	/**
	 * @param Item        $item
	 * @param Player|null $player
	 *
	 * @return bool
	 */
	public function onBreak(Item $item, Player $player = null) : bool{
		if($this->isActivated()){
			$this->meta = 0;
			$this->deactivate();
		}
		$this->canActivate = false;

		return $this->getLevel()->setBlock($this, new Air(), true);
	}
}
