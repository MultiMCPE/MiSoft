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

use InvalidArgumentException;
use InvalidStateException;
use pocketmine\network\mcpe\multiversion\palette\item\palettes\interface\ItemPaletteInterface;
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\nbt\LittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use UnexpectedValueException;
use function array_key_exists;
use function count;
use function file_get_contents;
use function json_decode;
use function base64_decode;
use const pocketmine\RESOURCE_PATH;

class ItemPalette implements ItemPaletteInterface{
	/** @var int[] */
	protected $stringToRuntimeIdMap = [];
	/** @var string[] */
	protected $runtimeToStringIdMap = [];

	/** @var int[] */
	protected $simpleLegacyToRuntimeIdMap = [];
	/** @var int[] */
	protected $simpleRuntimeToLegacyIdMap = [];

	/** @var int[][] */
	protected $complexLegacyToRuntimeIdMap = []; // array[internalID][metadata] = runtimeID
	/** @var int[][] */
	protected $complexRuntimeToLegacyIdMap = []; // array[runtimeID] = [internalID, metadata]

	/** @var ItemTypeEntry[] */
	protected $entries;

	/** @var int */
	protected $protocol;

	public function init(
		string $requiredItemList,
		string $r16ToCurrentItemMap
	) : void{
		/** @var int[] $itemPalette */
		$itemMap = json_decode(file_get_contents($r16ToCurrentItemMap), true);

		/** @var int[] $itemPalette */
		$stringToIntMap = json_decode(file_get_contents(RESOURCE_PATH . "/vanilla/palette/item/item_id_map712.json"), true);

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
		$nbtSerializer = new LittleEndianNBTStream();
		foreach($itemList as $stringId => $entry){
			$entries[] = new ItemTypeEntry(
				$stringId,
				$entry["runtime_id"],
				$entry["component_based"],
				$entry["version"],
				isset($entry["component_nbt"]) ? $nbtSerializer->read(base64_decode($entry["component_nbt"])) : $emptyNBT
			);
		}
		$this->entries = $entries;
	}

	/**
	 * @param int $runtimeId
	 *
	 * @return string
	 */
	public function getStringFromRuntimeId(int $runtimeId) : string{
		return $this->runtimeToStringIdMap[$runtimeId] ?? "minecraft:unknown";
	}

	/**
	 * @param string $stringId
	 *
	 * @return int
	 */
	public function getRuntimeFromStringId(string $stringId) : int{
		return $this->stringToRuntimeIdMap[$stringId] ?? -1;
	}

	/**
	 * @param int $id
	 * @param int $meta
	 *
	 * @return ?int[]
	 */
	public function getRuntimeFromLegacyIdQuiet(int $id, int $meta = 0) : ?array{
		if($meta === -1){
			$meta = 0x7fff;
		}
		if(isset($this->complexLegacyToRuntimeIdMap[$id][$meta])){
			return [$this->complexLegacyToRuntimeIdMap[$id][$meta], 0];
		}
		if(array_key_exists($id, $this->simpleLegacyToRuntimeIdMap)){
			return [$this->simpleLegacyToRuntimeIdMap[$id], $meta];
		}

		return null;
	}

	/**
	 * @param int $id
	 * @param int $meta
	 *
	 * @return int[]
	 */
	public function getRuntimeFromLegacyId(int $id, int $meta = 0) : array{
	    return $this->getRuntimeFromLegacyIdQuiet($id, $meta) ??
		    throw new InvalidArgumentException("Unmapped ID/metadata combination $id:$meta");
	}

	/**
	 * @param int $runtimeId
	 * @param int $runtimeMeta
	 * @param false $isComplex
	 *
	 * @return int[]
	 */
	public function getLegacyFromRuntimeId(int $runtimeId, int $runtimeMeta, &$isComplex = false) : array{
		if(isset($this->complexRuntimeToLegacyIdMap[$runtimeId])){
			if($runtimeMeta !== 0){
				throw new UnexpectedValueException("Unexpected non-zero network meta on complex item mapping");
			}

			$isComplex = true;
			return $this->complexRuntimeToLegacyIdMap[$runtimeId];
		}
		if(isset($this->simpleRuntimeToLegacyIdMap[$runtimeId])){
			return [$this->simpleRuntimeToLegacyIdMap[$runtimeId], $runtimeMeta];
		}
		throw new InvalidArgumentException("Unmapped network ID/metadata combination $runtimeId:$runtimeMeta");
	}

	public function getLegacyFromRuntimeIdWildcard(int $runtimeId, int $runtimeMeta) : array{
		if($runtimeMeta !== 0x7fff){
			return $this->getLegacyFromRuntimeId($runtimeId, $runtimeMeta);
		}

		$isComplex = false;
		[$id, $meta] = $this->getLegacyFromRuntimeId($runtimeId, 0, $isComplex);

		if($isComplex){
			return [$id, $meta];
		}else{
			return [$id, -1];
		}
	}

	/**
	 * @return ItemTypeEntry[]
	 */
	public function getEntries() : array{
		return $this->entries;
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
