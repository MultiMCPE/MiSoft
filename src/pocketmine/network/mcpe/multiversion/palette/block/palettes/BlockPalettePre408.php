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

use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\ListTag;
use pocketmine\utils\AssumptionFailedError;
use function file_get_contents;

class BlockPalettePre408 extends BlockPalettePre407{

	public function init(string $directory) : void{
		$runtimeBlockStatesFile = file_get_contents($directory . "/runtime_block_states.dat");
		if($runtimeBlockStatesFile === false){
			throw new AssumptionFailedError("Missing required resource file");
		}

		$stream = (new BigEndianNBTStream())->readCompressed($runtimeBlockStatesFile)->getValue();
		$this->bedrockKnownStates = $stream;
		$this->encodedPalette = (new NetworkLittleEndianNBTStream())->write(new ListTag("", $this->bedrockKnownStates));

		foreach($stream as $runtimeId => $tag){
			$id = $tag->getInt("id");
			$meta = $tag->getShort("data");
			if($meta > 15){
				continue;
			}

			$this->registerMapping($runtimeId, $id, $meta);
		}
	}
}
