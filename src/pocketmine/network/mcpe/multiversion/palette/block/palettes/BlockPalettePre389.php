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

use pocketmine\nbt\NBT;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use function file_get_contents;
use function json_decode;
use const pocketmine\RESOURCE_PATH;

class BlockPalettePre389 extends BlockPalettePre370{

	public function init(string $directory) : void{
		$tag = (new NetworkLittleEndianNBTStream())->read(file_get_contents($directory . "/required_block_states.nbt"));
		if(!($tag instanceof ListTag) or $tag->getTagType() !== NBT::TAG_Compound){ //this is a little redundant currently, but good for auto complete and makes phpstan happy
			throw new \RuntimeException("Invalid blockstates table, expected TAG_List<TAG_Compound> root");
		}

		/** @var CompoundTag[] $list */
		$list = $tag->getValue();
		$this->bedrockKnownStates = $this->randomizeTable($list);
        $this->encodedPalette = (new NetworkLittleEndianNBTStream())->write(new ListTag("", $this->bedrockKnownStates));

		$legacyIdMap = json_decode(file_get_contents(RESOURCE_PATH . "/vanilla/palette/block/block_id_map370.json"), true);
		$legacyStateMap = (new NetworkLittleEndianNBTStream())->read(file_get_contents($directory . "/r12_to_current_block_map.nbt"));
		if(!($legacyStateMap instanceof ListTag) or $legacyStateMap->getTagType() !== NBT::TAG_Compound){
			throw new \RuntimeException("Invalid legacy states mapping table, expected TAG_List<TAG_Compound> root");
		}

		/**
		 * @var int[][] $idToStatesMap string id -> int[] list of candidate state indices
		 */
		$idToStatesMap = [];
		foreach($this->bedrockKnownStates as $k => $state){
			$idToStatesMap[$state->getCompoundTag("block")->getString("name")][] = $k;
		}
		/** @var CompoundTag $pair */
		foreach($legacyStateMap as $pair){
			$oldState = $pair->getCompoundTag("old");
			$id = $legacyIdMap[$oldState->getString("name")];
			$data = $oldState->getShort("val");
			if($data > 15){
				//we can't handle metadata with more than 4 bits
				continue;
			}
			$mappedState = $pair->getCompoundTag("new");

			//TODO HACK: idiotic NBT compare behaviour on 3.x compares keys which are stored by values
			$mappedState->setName("block");
			$mappedName = $mappedState->getString("name");
			if(!isset($idToStatesMap[$mappedName])){
				throw new \RuntimeException("Mapped new state does not appear in network table");
			}
			foreach($idToStatesMap[$mappedName] as $k){
				$networkState = $this->bedrockKnownStates[$k];
				if($mappedState->equals($networkState->getCompoundTag("block"))){
					$this->registerMapping($k, $id, $data);
					continue 2;
				}
			}
			throw new \RuntimeException("Mapped new state does not appear in network table");
		}
	}
}
