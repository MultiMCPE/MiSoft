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

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use function in_array;

class RedstoneTorch extends RedstoneSource{

	protected $id = self::LIT_REDSTONE_TORCH;

	/** @var string */
	protected $ignore = "";

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Redstone Torch";
	}

	public function getLightLevel() : int{
		return 7;
	}

	public function getLastUpdateTime() : int{
		return $this->getLevel()->getBlockTempData($this);
	}

	public function setLastUpdateTimeNow() : void{
		$this->getLevel()->setBlockTempData($this, $this->getLevel()->getServer()->getTick());
	}

	public function canCalcTurn(){
		if(!parent::canCalc()){
			return false;
		}

		if($this->getLevel()->getServer()->getTick() !== $this->getLastUpdateTime()){
			return true;
		}

		return ($this->canScheduleUpdate() ? 1 : false);
	}

	public function canScheduleUpdate() : bool{
		return $this->getLevel()->getServer()->allowFrequencyPulse;
	}

	public function getFrequency() : int{
		return $this->getLevel()->getServer()->pulseFrequency;
	}

	public function turnOn(string $ignore = "") : bool{
		$result = $this->canCalcTurn();
		$this->setLastUpdateTimeNow();
		if($result === true){
			$faces = [
				0 => Vector3::SIDE_DOWN,
				1 => Vector3::SIDE_WEST,
				2 => Vector3::SIDE_EAST,
				3 => Vector3::SIDE_NORTH,
				4 => Vector3::SIDE_SOUTH,
				5 => Vector3::SIDE_DOWN
			];
			$this->id = self::REDSTONE_TORCH;
			$this->getLevel()->setBlock($this, $this, true, true);
			$this->activateTorch([$faces[$this->meta]], [$ignore]);
			return true;
		}elseif($result === 1){
			$this->ignore = $ignore;
			$this->getLevel()->scheduleUpdate($this, 20 * $this->getFrequency());
			return true;
		}
		return false;
	}

	public function turnOff(string $ignore = "") : bool{
		$result = $this->canCalcTurn();
		$this->setLastUpdateTimeNow();
		if($result === true){
			$faces = [
				0 => Vector3::SIDE_DOWN,
				1 => Vector3::SIDE_WEST,
				2 => Vector3::SIDE_EAST,
				3 => Vector3::SIDE_NORTH,
				4 => Vector3::SIDE_SOUTH,
				5 => Vector3::SIDE_DOWN
			];
			$this->id = self::UNLIT_REDSTONE_TORCH;
			$this->getLevel()->setBlock($this, $this, true, true);
			$this->deactivateTorch([$faces[$this->meta]], [$ignore]);
			return true;
		}elseif($result === 1){
			$this->ignore = $ignore;
			$this->getLevel()->scheduleUpdate($this, 20 * $this->getFrequency());
			return true;
		}
		return false;
	}

	public function activateTorch(array $ignore = [], array $notCheck = []) : void{
		if($this->canCalc()){
			$this->activated = true;
			/** @var Door $block */

			$sides = [
				Vector3::SIDE_EAST,
				Vector3::SIDE_WEST,
				Vector3::SIDE_SOUTH,
				Vector3::SIDE_NORTH,
				Vector3::SIDE_UP,
				Vector3::SIDE_DOWN
			];

			foreach($sides as $side){
				if(!in_array($side, $ignore)){
					$block = $this->getSide($side);
					if(!in_array($hash = Level::chunkBlockHash($block->x, $block->y, $block->z), $notCheck)){
						$this->activateBlock($block);
					}
				}
			}
		}
	}

	public function activate(array $ignore = []){
		$this->activateTorch($ignore);
	}

	public function deactivate(array $ignore = []){
		$this->deactivateTorch($ignore);
	}

	public function deactivateTorch(array $ignore = [], array $notCheck = []) : void{
		if($this->canCalc()){
			$this->activated = false;
			/** @var Door $block */

			$sides = [
				Vector3::SIDE_EAST,
				Vector3::SIDE_WEST,
				Vector3::SIDE_SOUTH,
				Vector3::SIDE_NORTH
			];

			foreach($sides as $side){
				if(!in_array($side, $ignore)){
					$block = $this->getSide($side);
					if(!in_array($hash = Level::chunkBlockHash($block->x, $block->y, $block->z), $notCheck)){
						$this->deactivateBlock($block);
					}
				}
			}

			if(!in_array(Vector3::SIDE_DOWN, $ignore)){
				$block = $this->getSide(Vector3::SIDE_DOWN);
				if(!in_array($hash = Level::chunkBlockHash($block->x, $block->y, $block->z), $notCheck)){
					if(!$this->checkPower($block)){
						/** @var $block ActiveRedstoneLamp */
						if($block->getId() === Block::LIT_REDSTONE_LAMP){
							$block->turnOff();
						}
					}

					$block = $this->getSide(Vector3::SIDE_DOWN, 2);
					$this->deactivateBlock($block);
				}
			}
		}
	}

	public function onBreak(Item $item, Player $player = null) : bool{
		$this->getLevel()->setBlock($this, new Air(), true, false);

		$faces = [
			0 => Vector3::SIDE_DOWN,
			1 => Vector3::SIDE_WEST,
			2 => Vector3::SIDE_EAST,
			3 => Vector3::SIDE_NORTH,
			4 => Vector3::SIDE_SOUTH,
			5 => Vector3::SIDE_DOWN
		];

		$this->deactivate([$faces[$this->meta]]);
		$this->getLevel()->setBlockTempData($this);

		return true;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$below = $this->getSide(Vector3::SIDE_DOWN);

		if(!$blockClicked->isTransparent() and $face !== Vector3::SIDE_DOWN){
			$faces = [
				Vector3::SIDE_UP => 5,
				Vector3::SIDE_NORTH => 4,
				Vector3::SIDE_SOUTH => 3,
				Vector3::SIDE_WEST => 2,
				Vector3::SIDE_EAST => 1
			];
			$this->meta = $faces[$face];
			$this->getLevel()->setBlock($this, $this, true, true);

			return true;
		}elseif(!$below->isTransparent() or $below->getId() === self::FENCE or $below->getId() === self::COBBLESTONE_WALL or $below->getId() === self::REDSTONE_LAMP or $below->getId() === self::LIT_REDSTONE_LAMP){
			$this->meta = 0;
			$this->getLevel()->setBlock($this, $this, true, true);

			return true;
		}

		return false;
	}

	public function getVariantBitmask() : int{
		return 0;
	}

	public function onNearbyBlockChange() : void{
		$below = $this->getSide(Vector3::SIDE_DOWN);
		$meta = $this->getDamage();
		static $faces = [
			0 => Vector3::SIDE_DOWN,
			1 => Vector3::SIDE_WEST,
			2 => Vector3::SIDE_EAST,
			3 => Vector3::SIDE_NORTH,
			4 => Vector3::SIDE_SOUTH,
			5 => Vector3::SIDE_DOWN
		];
		$face = $faces[$meta] ?? Vector3::SIDE_DOWN;

		if($this->getSide($face)->isTransparent() and !($face === Vector3::SIDE_DOWN and ($below->getId() === self::FENCE or $below->getId() === self::COBBLESTONE_WALL))){
			$this->getLevel()->useBreakOn($this);

			return;
		}

		$this->activate([$face]);
	}

    public function onScheduledUpdate() : void{
		if($this->id === self::UNLIT_REDSTONE_TORCH){
			$this->turnOn($this->ignore);
		}else{
			$this->turnOff($this->ignore);
		}
	}

	public function isActivated(Block $from = null) : bool{
		return true;
	}
}
