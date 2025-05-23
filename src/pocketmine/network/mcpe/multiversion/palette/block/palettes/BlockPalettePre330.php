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

class BlockPalettePre330 extends BlockPalettePre280{

	public function init(string $directory) : void{
		/** @var mixed[] $runtimeIdMap */
		$runtimeIdMap = json_decode(file_get_contents($directory . "/BlockPalette.json"), true);
		foreach($runtimeIdMap as $k => $obj){
			$this->registerMapping($k, $obj["id"], $obj["data"]);
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
