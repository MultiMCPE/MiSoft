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

namespace pocketmine\palette;

use pocketmine\scheduler\AsyncTask;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\multiversion\BlockPallet;
use pocketmine\Server;

class PaletteInitTask extends AsyncTask{

	public function __construct(){
	}

	public function onRun(){
		$this->setResult(BlockPallet::initAll());
	}

	public function onCompletion(Server $server){
	    if($this->hasResult()) {
	        DataPacket::initPalletes($this->getResult());
	    }else{
	        $server->getLogger()->error("PaletteInit request doesn't have any result data");
	    }
	}
}
