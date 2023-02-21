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

namespace pocketmine\network\mcpe\multiversion\block;

use pocketmine\network\mcpe\multiversion\block\palettes\Palette;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette220;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette221;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette240;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette260;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette270;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette274;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette280;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette310;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette330;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette340;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette354;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette360;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette370;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette389;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette407;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette408;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette419;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette428;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette440;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette448;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette465;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette471;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette486;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette503;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette526;
use pocketmine\network\mcpe\protocol\ProtocolInfo;

class BlockPalette{
	/** @var Palette[] */
    protected static $palettes = null;

    /**
     * @return void
     */
    public static function init() : void{
        $pool = [
            ProtocolInfo::PROTOCOL_526 => new BlockPalette526(),
            ProtocolInfo::PROTOCOL_503 => new BlockPalette503(),
            ProtocolInfo::PROTOCOL_486 => new BlockPalette486(),
            ProtocolInfo::PROTOCOL_471 => new BlockPalette471(),
            ProtocolInfo::PROTOCOL_465 => new BlockPalette465(),
            ProtocolInfo::PROTOCOL_448 => new BlockPalette448(),
            ProtocolInfo::PROTOCOL_440 => new BlockPalette440(),
            ProtocolInfo::PROTOCOL_428 => new BlockPalette428(),
            ProtocolInfo::PROTOCOL_419 => new BlockPalette419(),
            ProtocolInfo::PROTOCOL_408 => new BlockPalette408(),
            ProtocolInfo::PROTOCOL_407 => new BlockPalette407(),
            ProtocolInfo::PROTOCOL_389 => new BlockPalette389(),
            ProtocolInfo::PROTOCOL_370 => new BlockPalette370(),
            ProtocolInfo::PROTOCOL_360 => new BlockPalette360(),
            ProtocolInfo::PROTOCOL_354 => new BlockPalette354(),
            ProtocolInfo::PROTOCOL_340 => new BlockPalette340(),
            ProtocolInfo::PROTOCOL_330 => new BlockPalette330(),
            ProtocolInfo::PROTOCOL_310 => new BlockPalette310(),
            ProtocolInfo::PROTOCOL_280 => new BlockPalette280(),
            ProtocolInfo::PROTOCOL_274 => new BlockPalette274(),
            ProtocolInfo::PROTOCOL_270 => new BlockPalette270(),
            ProtocolInfo::PROTOCOL_260 => new BlockPalette260(),
            ProtocolInfo::PROTOCOL_240 => new BlockPalette240(),
            ProtocolInfo::PROTOCOL_221 => new BlockPalette221(),
            ProtocolInfo::PROTOCOL_220 => new BlockPalette220()
        ];

        self::$palettes = [];
        foreach($pool as $protocol => $blockPalette){
            $blockPalette::init();
            self::$palettes[$protocol] = $blockPalette;
        }
    }

    /**
     * @param int $playerProtocol
     * 
     * @return ?Palette
     */
    public static function getPalette(int $playerProtocol) : ?Palette{
        foreach(self::$palettes as $protocol => $paletteClass){
            if($playerProtocol >= $protocol){
                return clone $paletteClass;
            }
        }
        return null;
    }
}
