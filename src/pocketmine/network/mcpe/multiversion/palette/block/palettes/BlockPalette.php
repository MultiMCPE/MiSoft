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

namespace pocketmine\network\mcpe\multiversion\palette\block\palettes;

use pocketmine\block\BlockIds;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\network\mcpe\multiversion\palette\block\palettes\interface\BlockPaletteInterface;
use function file_get_contents;

class BlockPalette implements BlockPaletteInterface{
	/** @var int[] */
	protected $legacyToRuntimeMap = [];
	/** @var int[] */
	protected $runtimeToLegacyMap = [];
	/** @var CompoundTag[]|null */
	protected $bedrockKnownStates = null;

	/** @var int */
	protected $protocol;

	public function init(string $directory) : void{
		$runtimeBlockStatesFile = file_get_contents($directory . "/runtime_block_states.dat");
		if($runtimeBlockStatesFile === false){
			throw new AssumptionFailedError("Missing required resource file");
		}

		$stream = (new BigEndianNBTStream())->readCompressed($runtimeBlockStatesFile)->getValue();
		$this->bedrockKnownStates = $stream;

		foreach($stream as $tag){
			$id = $tag->getInt("id");
			$meta = $tag->getTag("data")->getValue();
			if($meta > 15){
				continue;
			}
			$runtimeId = $tag->getInt("runtimeId");

			$this->registerMapping($runtimeId, $id, $meta);
		}
	}

	protected function lazyInit() : void{
		if($this->bedrockKnownStates === null){
			$this->init();
		}
	}

	public function toStaticRuntimeId(int $id, int $meta = 0) : int{
		$this->lazyInit();
		/*
		 * try id+meta first
		 * if not found, try id+0 (strip meta)
		 * if still not found, return update! block
		 */
		return $this->legacyToRuntimeMap[($id << 4) | $meta] ?? $this->legacyToRuntimeMap[$id << 4] ?? $this->legacyToRuntimeMap[BlockIds::INFO_UPDATE << 4];
	}

	/**
	 * @return int[] [id, meta]
	 */
	public function fromStaticRuntimeId(int $runtimeId) : array{
		$this->lazyInit();
		if(isset($this->runtimeToLegacyMap[$runtimeId])){
	    	$v = $this->runtimeToLegacyMap[$runtimeId];
	    	return [$v >> 4, $v & 0xf];
		}else{
		    return [0, 0];
		}
	}

	protected function registerMapping(int $staticRuntimeId, int $legacyId, int $legacyMeta) : void{
		$this->legacyToRuntimeMap[($legacyId << 4) | $legacyMeta] = $staticRuntimeId;
		$this->runtimeToLegacyMap[$staticRuntimeId] = ($legacyId << 4) | $legacyMeta;
	}

	/**
	 * @return CompoundTag[]
	 */
	public function getBedrockKnownStates() : array{
		$this->lazyInit();
		return $this->bedrockKnownStates;
	}

    /**
     * @param int $protocol
     */
    public function setProtocol(int $protocol) : void{
		$this->protocol = $protocol;
	}

    /**
     * @return int
     */
    public function getProtocol() : int{
		return $this->protocol;
	}
}
