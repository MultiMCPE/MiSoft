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

namespace pocketmine\block;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\block\utils\ColorBlockMetaHelper;

class StainedGlassPane extends GlassPane{

	protected $id = self::STAINED_GLASS_PANE;

	public function getName() : string{
		return ColorBlockMetaHelper::getColorFromMeta($this->getVariant()) . " Stained Glass Pane";
	}

	public function getBlockProtocol(int $protocol) : ?Block{
		if($protocol < ProtocolInfo::PROTOCOL_110){
	    	return new GlassPane();
		}

		return parent::getBlockProtocol($protocol);
	}
}
