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

use pocketmine\math\Vector3;
use pocketmine\item\Item;

class LitRedstoneLamp extends Solid implements ElectricalAppliance{
	protected $id = self::LIT_REDSTONE_LAMP;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Lit Redstone Lamp";
	}

	public function getLightLevel() : int{
		return 15;
	}

	public function isLightedByAround() : bool{
		return ($this->meta === 1);
	}

	protected function checkPower(array $ignore = []) : bool{
		if($this->isLightedByAround()){
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
					/** @var LitRedstoneLamp $block */
					$block = $this->getSide($side);
					if($block->getId() === $this->id){
						if(!$block->isLightedByAround()){
							return true;
						}
					}
				}
			}
		}

		return false;
	}

	public function lightAround() : void{
		$sides = [
			Vector3::SIDE_EAST,
			Vector3::SIDE_WEST,
			Vector3::SIDE_SOUTH,
			Vector3::SIDE_NORTH,
			Vector3::SIDE_UP,
			Vector3::SIDE_DOWN
		];

		foreach($sides as $side){
			/** @var RedstoneLamp $block */
			$block = $this->getSide($side);
			if($block->getId() === self::REDSTONE_LAMP){
				$block->turnOn();
			}
		}
	}

	public function turnAroundOff(array $ignore = []) : void{
		if(!$this->isLightedByAround()){
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
					/** @var LitRedstoneLamp $block */
					$block = $this->getSide($side);
					if($block->getId() === $this->id){
						if($block->isLightedByAround()){
							if(!$block->checkPower([$this->getOppositeSide($side)])){
								$block->turnOff();
							}
						}
					}
				}
			}
		}
	}

	public function turnOn() : bool{
		$this->meta = 0;
		$this->getLevel()->setBlock($this, $this, true, false);

		return true;
	}

	public function turnOff() : bool{
		$this->getLevel()->setBlock($this, new RedstoneLamp(), true, true);

		return true;
	}

	public function getDrops(Item $item) : array{
		return [
			Item::get(Item::REDSTONE_LAMP, 0, 1)
		];
	}
}
