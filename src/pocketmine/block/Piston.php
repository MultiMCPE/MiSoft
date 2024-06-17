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
use pocketmine\level\sound\PistonInSound;
use pocketmine\level\sound\PistonOutSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\PistonHead as PistonHeadTile;
use pocketmine\tile\Tile;
use function abs;
use function in_array;

class Piston extends RedstoneSource{
	protected $id = self::PISTON;

	/** @var int */
	public $maxMoveBlocks = 13;

    /**  @var int[] */
	public $noMoveBlocks = [
		BlockIds::AIR,
		BlockIds::OBSIDIAN,
		BlockIds::BEDROCK,
		BlockIds::COMMAND_BLOCK
	];

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function isSticky() : bool{
	    return false;
	}

	public function getHardness() : float{
		return 1;
	}

	public function getToolType() : int{
		return BlockToolType::TYPE_AXE;
	}

	public function getName() : string{
		return "Piston";
	}

	public function getFace() : int{
		return $this->meta & 0x07; // first 3 bits is face
	}

	public function activate(array $ignore = []){
		if($this->moveNextBlocks()){
			$this->getLevel()->addSound(new PistonInSound($this));

			$tile = $this->getLevel()->getTile($this);
			if($tile instanceof Tile){
				$tile->close();
			}

			$nbt = PistonHeadTile::createNBT($this);
			$nbt->setFloat(PistonHeadTile::TAG_PROGRESS, 1.0);
			$nbt->setByte(PistonHeadTile::TAG_STATE, 0);
			$nbt->setByte(PistonHeadTile::TAG_STICKY, $this->isSticky() ? 1 : 0);

			$tile = Tile::createTile(Tile::PISTON_HEAD, $this->getLevel(), $nbt);
			$tile->spawnToAll();
		}
	}

	public function deactivate(array $ignore = []){
		$this->getLevel()->addSound(new PistonOutSound($this));

		if($this->isSticky()){
			$this->moveBlockBackwards();
		}

		$tile = $this->getLevel()->getTile($this);
		if($tile instanceof Tile){
			$tile->close();
		}

		$nbt = PistonHeadTile::createNBT($this);
		$nbt->setFloat(PistonHeadTile::TAG_PROGRESS, 0.0);
		$nbt->setByte(PistonHeadTile::TAG_STATE, 0);
		$nbt->setByte(PistonHeadTile::TAG_STICKY, $this->isSticky() ? 1 : 0);

		$tile = Tile::createTile(Tile::PISTON_HEAD, $this->getLevel(), $nbt);
		$tile->spawnToAll();
	}

	public function moveNextBlocks() : bool{
		$oldBlock = $this->getSide($this->getExtendSide());
		for($i = 1; $i <= $this->maxMoveBlocks; $i++){
			$nextBlock = $this->getSide($this->getExtendSide(), $i);
			if(in_array($nextBlock->getId(), $this->noMoveBlocks) or !$nextBlock->isSolid()){
				if(!$nextBlock->isSolid() and !$nextBlock->isTransparent()){
					$drops = $nextBlock->getDrops(); //Fixes tile entities being deleted before getting drops
                    foreach($drops as $item){
						if($item->getCount() > 0) {
							$this->getLevel()->dropItem($nextBlock->add(0.5, 0.5, 0.5), $item);
						}
                    }

					$nextBlock->onBreak();
					return true;
				}elseif($nextBlock->getId() !== 0){
					return false;
				}

				$this->getLevel()->setBlock($nextBlock, $oldBlock);
				break;
			}

			$this->getLevel()->setBlock($nextBlock, $oldBlock);
			$oldBlock = $nextBlock;
		}

		if($i > 1 and $i < $this->maxMoveBlocks + 1){
			$this->getLevel()->setBlock($this->getSide($this->getExtendSide()), new Air());
		}

		if($i === $this->maxMoveBlocks + 1){
			return false;
		}

		return true;
	}

	public function moveBlockBackwards(): void{
		$nextBlock = $this->getSide($this->getExtendSide(), 2);
		if(!in_array($nextBlock->getId(), $this->noMoveBlocks) and $nextBlock->isSolid()){
			$this->getLevel()->setBlock($this->getSide($this->getExtendSide()), $nextBlock);
			$this->getLevel()->setBlock($nextBlock, new Air());
		}
	}

	public function getExtendSide() : ?int{
		$faces = [
			0 => self::SIDE_DOWN,
			1 => self::SIDE_UP,
			2 => self::SIDE_SOUTH,
			3 => self::SIDE_NORTH,
			4 => self::SIDE_EAST,
			5 => self::SIDE_WEST,
		];

		return $faces[$this->getFace()] ?? null;
	}

	public function createPistonArm() : void{
		$nbt = PistonHeadTile::createNBT($this);
		$nbt->setFloat(PistonHeadTile::TAG_PROGRESS, 0.0);
		$nbt->setByte(PistonHeadTile::TAG_STATE, 0);
		$nbt->setByte(PistonHeadTile::TAG_STICKY, $this->isSticky() ? 1 : 0);

		Tile::createTile(Tile::PISTON_HEAD, $this->getLevel(), $nbt);
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		if($player instanceof Player){
			$pitch = $player->getPitch();
			if(abs($pitch) >= 45){
				if($pitch < 0){
					$f = 4;
				}else{
					$f = 5;
				}
			}else{
				$f = $player->getDirection();
			}
		}else{
			$f = 0;
		}

		$faces = [
			0 => 5,
			1 => 3,
			2 => 4,
			3 => 2,
			4 => 0,
			5 => 1,
		];
		$this->meta = $faces[$f];

		$this->createPistonArm();
		$this->getLevel()->setBlock($this, $this, true, true);

		return true;
	}
}
