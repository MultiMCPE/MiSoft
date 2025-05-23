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
use function json_decode;
use const pocketmine\RESOURCE_PATH;

class BlockPalettePre370 extends BlockPalettePre360{

	public function init(string $directory) : void{
		$legacyIdMap = json_decode(file_get_contents(RESOURCE_PATH . "/vanilla/palette/block/block_id_map360.json"), true);

		$compressedTable = json_decode(file_get_contents($directory . "/BlockPalette.json"), true);
		$decompressed = [];

		foreach($compressedTable as $prefix => $entries){
			foreach($entries as $shortStringId => $states){
				foreach($states as $state){
				    $name = "$prefix:$shortStringId";
					$decompressed[] = [
						"name" => $name,
						"data" => $state,
						"legacy_id" => $legacyIdMap[$name]
					];
				}
			}
		}
		$this->bedrockKnownStates = $this->randomizeTable($decompressed);

		foreach($this->bedrockKnownStates as $k => $obj){
			if($obj["data"] > 15){
				//TODO: in 1.12 they started using data values bigger than 4 bits which we can't handle right now
				continue;
			}
			//this has to use the json offset to make sure the mapping is consistent with what we send over network, even though we aren't using all the entries
			$this->registerMapping($k, $obj["legacy_id"], $obj["data"]);
		}

		$stream = new NetworkBinaryStream();
		$stream->putUnsignedVarInt(count($this->bedrockKnownStates));
		foreach($this->bedrockKnownStates as $v){
			$stream->putString($v["name"]);
			$stream->putLShort($v["data"]);
			$stream->putLShort($v["legacy_id"]);
		}
		$this->encodedPalette = $stream->getBuffer();
	}
}
