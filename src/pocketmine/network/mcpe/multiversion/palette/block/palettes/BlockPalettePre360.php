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

use pocketmine\network\mcpe\NetworkBinaryStream;
use function count;
use function file_get_contents;
use function getmypid;
use function json_decode;
use function mt_rand;
use function mt_srand;
use function shuffle;
use const pocketmine\RESOURCE_PATH;

class BlockPalettePre360 extends BlockPalettePre354{
	/** @var mixed[]|null */
	protected $bedrockKnownStates = null;

	public function init(string $directory) : void{
		$legacyIdMap = json_decode(file_get_contents(RESOURCE_PATH . "/vanilla/palette/block/block_id_map354.json"), true);

		$compressedTable = json_decode(file_get_contents($directory . "/BlockPalette.json"), true);
		$decompressed = [];

		foreach($compressedTable as $prefix => $entries){
			foreach($entries as $shortStringId => $states){
				foreach($states as $state){
					$decompressed[] = [
						"name" => "$prefix:$shortStringId",
						"data" => $state
					];
				}
			}
		}
		$this->bedrockKnownStates = $this->randomizeTable($decompressed);

		foreach($this->bedrockKnownStates as $k => $obj){
			//this has to use the json offset to make sure the mapping is consistent with what we send over network, even though we aren't using all the entries
			if(!isset($legacyIdMap[$obj["name"]])){
				continue;
			}
			$this->registerMapping($k, $legacyIdMap[$obj["name"]], $obj["data"]);
		}

		$stream = new NetworkBinaryStream();
		$stream->putUnsignedVarInt(count($this->bedrockKnownStates));
		foreach($this->bedrockKnownStates as $v){
			$stream->putString($v["name"]);
			$stream->putLShort($v["data"]);
		}
		$this->encodedPalette = $stream->getBuffer();
	}

	protected function lazyInit() : void{
		if($this->bedrockKnownStates === null){
			$this->init();
		}
	}

	/**
	 * Randomizes the order of the runtimeID table to prevent plugins relying on them.
	 * Plugins shouldn't use this stuff anyway, but plugin devs have an irritating habit of ignoring what they
	 * aren't supposed to do, so we have to deliberately break it to make them stop.
	 *
	 * @param array $table
	 *
	 * @return array
	 */
	protected function randomizeTable(array $table) : array{
		$postSeed = mt_rand(); //save a seed to set afterwards, to avoid poor quality randoms
		mt_srand(getmypid() ?: 0); //Use a seed which is the same on all threads. This isn't a secure seed, but we don't care.
		shuffle($table);
		mt_srand($postSeed); //restore a good quality seed that isn't dependent on PID
		return $table;
	}
}
