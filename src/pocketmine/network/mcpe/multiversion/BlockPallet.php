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

namespace pocketmine\network\mcpe\multiversion;

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use function array_diff;
use function scandir;
use function strpos;
use function explode;
use function substr;
use function krsort;
use function json_decode;
use function file_get_contents;
use function count;
use function var_dump;

class BlockPallet {

    /** @var array */
    private static $blockNamesIds = [];
    /** @var array */
	private $pallet = [];
	/** @var array */
	private $palletReverted = [];
	/** @var string */
	private $dataForPackets = "";

    /**
     * @return array
     */
	public static function initAll() : array{
	    self::$blockNamesIds = json_decode(file_get_contents(\pocketmine\RESOURCE_PATH . '/vanilla/block_id_map.json'), true);

	 	$result = [];
		$folderPath = \pocketmine\RESOURCE_PATH . "/vanilla/palette/";
		$palletFiles = array_diff(scandir($folderPath), ['..', '.']);
		foreach($palletFiles as $fileName){
			if(strpos($fileName, "BlockPallet") !== 0){
				continue;
			}
			$parts = explode(".", $fileName);
			$protocolNumber = (int) substr($parts[0], 11);
			$pallet = new BlockPallet($folderPath . $fileName, $protocolNumber);
			$result[$protocolNumber] = $pallet;
		}
		krsort($result);

		return $result;
	}

    /**
     * @param string $path
     * @param int $protocolNumber
     */
	public function __construct(string $path, int $protocolNumber){
		$palletData = json_decode(file_get_contents($path), true);

		if($protocolNumber >= ProtocolInfo::PROTOCOL_419){
			$palletData = $palletData['blocks'];
		}

		if($protocolNumber >= ProtocolInfo::PROTOCOL_370){
		    $data = [];
			$lastData = null;
			$currentId = null;
			foreach($palletData as $runtimeID => $blockInfo){
				if($protocolNumber >= ProtocolInfo::PROTOCOL_419){
			        $blockInfo['id'] = self::$blockNamesIds[$blockInfo['name']] ?? -1;
			       	if($blockInfo['id'] !== $currentId){
				        $currentId = $blockInfo['id'];
				    	$lastData = -1;
				    	$blockInfo['data'] = -1;
			        }
			    	$blockInfo['data'] = ++$lastData;
				}

				if(isset($blockInfo['data'])){
					$this->pallet[$blockInfo['id']][$blockInfo['data']] = $runtimeID;
					$this->palletReverted[$runtimeID] = [$blockInfo['id'], $blockInfo['data'], $blockInfo['name']];
				}

				$states = new CompoundTag("states", []);
				foreach($blockInfo['states'] as $stateName => $state){
					switch($state['type']){
						case 'byte':
						case NBT::TAG_Byte:
							$states->offsetSet((string) $stateName, new ByteTag((string) $stateName, $state['val'] ?? $state['value']));
							break;
						case NBT::TAG_Int:
						case 'int':
							$states->offsetSet((string) $stateName, new IntTag((string) $stateName, $state['val'] ?? $state['value']));
							break;
						case NBT::TAG_String:
						case 'string':
							$states->offsetSet((string) $stateName, new StringTag((string) $stateName, $state['val'] ?? $state['value']));
							break;
						default:
							var_dump("Block Pallet Initialization Error");
							break;
					}
				}

				$nbt = new CompoundTag("", [
					"block" => new CompoundTag("block", [
						"name" => new StringTag("name", $blockInfo['name']),
						"states" => $states,
						"version" => new IntTag("version", 17629199)
					]),
					"id" => new ShortTag("id", $blockInfo['id'])
				]);

				$data[$runtimeID] = $nbt;
			}

            $palletTag = new ListTag("", $data);

			$this->dataForPackets = (new NetworkLittleEndianNBTStream())->write($palletTag);
		}else{
	    	$bs = new NetworkBinaryStream();
	    	$bs->putUnsignedVarInt(count($palletData));

	    	foreach($palletData as $runtimeID => $blockInfo){
		    	if(isset($blockInfo['runtimeID'])){
			    	$this->pallet[$blockInfo['id']][$blockInfo['data']] = $blockInfo['runtimeID'];
		    		$this->palletReverted[$blockInfo['runtimeID']] = [$blockInfo['id'], $blockInfo['data'], $blockInfo['name']];
		    	}else{
			    	$this->pallet[$blockInfo['id']][$blockInfo['data']] = $runtimeID;
			     	$this->palletReverted[$runtimeID] = [$blockInfo['id'], $blockInfo['data'], $blockInfo['name']];
		    	}

		    	$bs->putString($blockInfo['name']);
		    	$bs->putLShort($blockInfo['data']);
		    	if($protocolNumber >= ProtocolInfo::PROTOCOL_360){
		        	$bs->putLShort($blockInfo['id']);
		    	}
			}

			$this->dataForPackets = $bs->getBuffer();
		}
	}

    /**
     * @param int $runtimeID
     * 
     * @return array
     */
	public function getBlockDataByRuntimeID(int $runtimeID) : array{
		if(isset($this->palletReverted[$runtimeID])){
			return $this->palletReverted[$runtimeID];
		}

		return [0, 0, ""];
	}

    /**
     * @param int $id
     * @param int $meta
     * 
     * @return int
     */
	public function getBlockRuntimeIDByData(int $id, int $meta) : int{
		if(isset($this->pallet[$id][$meta])){
			return $this->pallet[$id][$meta];
		}

		return 0;
	}

    /**
     * @return string
     */
	public function getDataForPackets() : string{
		return $this->dataForPackets;
	}
}
