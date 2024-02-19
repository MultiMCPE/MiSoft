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
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette534;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette544;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette560;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette567;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette575;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette582;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette589;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette594;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette618;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette622;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette630;
use pocketmine\network\mcpe\multiversion\block\palettes\BlockPalette649;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\Server;
use function krsort;

class BlockPalette{
	/** @var Palette[] */
    protected static $palettes = [];

    /**
     * @return void
     */
    public static function init() : void{
        $pools = [
            ProtocolInfo::PROTOCOL_649 => new BlockPalette649(),
            ProtocolInfo::PROTOCOL_630 => new BlockPalette630(),
            ProtocolInfo::PROTOCOL_622 => new BlockPalette622(),
            ProtocolInfo::PROTOCOL_618 => new BlockPalette618(),
            ProtocolInfo::PROTOCOL_594 => new BlockPalette594(),
            ProtocolInfo::PROTOCOL_589 => new BlockPalette589(),
            ProtocolInfo::PROTOCOL_582 => new BlockPalette582(),
            ProtocolInfo::PROTOCOL_575 => new BlockPalette575(),
            ProtocolInfo::PROTOCOL_567 => new BlockPalette567(),
            ProtocolInfo::PROTOCOL_560 => new BlockPalette560(),
            ProtocolInfo::PROTOCOL_544 => new BlockPalette544(),
            ProtocolInfo::PROTOCOL_534 => new BlockPalette534(),
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

        foreach($pools as $poolProtocol => $pool){
            Server::getInstance()->getAsyncPool()->submitTask(new BlockPaletteInitTask(
                $pool,
                $poolProtocol,
                function(Palette $palette, int $poolProtocol) : void{
                    self::$palettes[$poolProtocol] = $palette;
                    krsort(self::$palettes);
                }
            ));
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

    /**
     * @param Palette $palette
     * @param int $paletteProtocol
     * 
     * @return void
     */
    public static function addPalette(Palette $palette, int $paletteProtocol) : void{
        // TODO: как по мне, подойдет для каких-нибудь плагинов. Но вообще, это сделано для CraftingDataBuildTask
        self::$palettes[$paletteProtocol] = $palette;
        krsort(self::$palettes);
    }

}
