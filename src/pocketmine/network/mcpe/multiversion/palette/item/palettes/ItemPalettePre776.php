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

namespace pocketmine\network\mcpe\multiversion\palette\item\palettes;

use InvalidStateException;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\nbt\tag\CompoundTag;
use function file_get_contents;
use function json_decode;
use const pocketmine\RESOURCE_PATH;

class ItemPalettePre776 extends ItemPalette{

	public function init(
        string $requiredItemList,
        string $r16ToCurrentItemMap
    ) : void{
		/** @var int[] $itemPalette */
		$itemMap = json_decode(file_get_contents($r16ToCurrentItemMap), true);

		/** @var int[] $itemPalette */
        $itemIdMapPath = ($this->getProtocol() >= ProtocolInfo::PROTOCOL_712 ?
            RESOURCE_PATH . "/vanilla/palette/item/item_id_map712.json" :
            RESOURCE_PATH . "/vanilla/palette/item/item_id_map.json"
        );
		$stringToIntMap = json_decode(file_get_contents($itemIdMapPath), true);

		$simpleMappings = [];
		foreach($itemMap["simple"] as $oldId => $newId){
			if(!isset($stringToIntMap[$oldId])){
				//new item without a fixed legacy ID - we can't handle this right now
				continue;
			}
			$simpleMappings[$newId] = $stringToIntMap[$oldId];
		}
		foreach($stringToIntMap as $stringId => $intId){
            if(isset($simpleMappings[$stringId])){
                throw new InvalidStateException("Old ID $stringId collides with new ID");
            }
            $simpleMappings[$stringId] = $intId;
        }

		$complexMappings = [];
		foreach($itemMap["complex"] as $oldId => $map){
			foreach($map as $meta => $newId){
				if(!isset($stringToIntMap[$oldId])){
					//new item without a fixed legacy ID - we can't handle this right now
					continue;
				}
				$intId = $stringToIntMap[$oldId];
				if(isset($complexMappings[$newId]) && $complexMappings[$newId][0] === $intId && $complexMappings[$newId][1] <= $meta){
					//TODO: HACK! Multiple legacy ID/meta pairs can be mapped to the same new ID (see minecraft:log)
					//Assume that the first one is the most relevant for now
					//However, this could catch fire in the future if this assumption is broken
					continue;
				}
				$complexMappings[$newId] = [$intId, (int) $meta];
			}
		}

		$itemList = json_decode(file_get_contents($requiredItemList), true);
		foreach($itemList as $stringId => $entry){
			$runtimeId = $entry["runtime_id"];

			$this->stringToRuntimeIdMap[$stringId] = $runtimeId;
			$this->runtimeToStringIdMap[$runtimeId] = $stringId;

			if(isset($complexMappings[$stringId])){
				[$id, $meta] = $complexMappings[$stringId];
				$this->complexLegacyToRuntimeIdMap[$id][$meta] = $runtimeId;
				$this->complexRuntimeToLegacyIdMap[$runtimeId] = [$id, $meta];
			}elseif(isset($simpleMappings[$stringId])){
				$this->simpleLegacyToRuntimeIdMap[$simpleMappings[$stringId]] = $runtimeId;
				$this->simpleRuntimeToLegacyIdMap[$runtimeId] = $simpleMappings[$stringId];
			}else{
				//not all items have a legacy mapping - for now, we only support the ones that do
				continue;
			}
		}

		$entries = [];
		$emptyNBT = new CompoundTag();
		foreach($itemList as $stringId => $entry){
			$entries[] = new ItemTypeEntry(
				$stringId,
				$entry["runtime_id"],
				$entry["component_based"],
				0,
				$emptyNBT
			);
		}

		$this->entries = $entries;
	}
}
