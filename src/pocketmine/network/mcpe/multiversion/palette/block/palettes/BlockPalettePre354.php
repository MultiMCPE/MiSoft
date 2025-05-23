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

class BlockPalettePre354 extends BlockPalettePre340{

	public function init(string $directory) : void{
		/** @var mixed[] $runtimeIdMap */
		$runtimeIdMap = json_decode(file_get_contents($directory . "/BlockPalette.json"), true);
		$legacyIdMap = json_decode(file_get_contents(RESOURCE_PATH . "/vanilla/palette/block/block_id_map340.json"), true);
		foreach($runtimeIdMap as $k => $obj){
			//this has to use the json offset to make sure the mapping is consistent with what we send over network, even though we aren't using all the entries
			if(!isset($legacyIdMap[$obj["name"]])){
				continue;
			}
			$this->registerMapping($k, $legacyIdMap[$obj["name"]], $obj["data"]);
		}

		$stream = new NetworkBinaryStream();
		$stream->putUnsignedVarInt(count($runtimeIdMap));
		foreach($runtimeIdMap as $v){
			$stream->putString($v["name"]);
			$stream->putLShort($v["data"]);
		}
		$this->encodedPalette = $stream->getBuffer();
	}
}
