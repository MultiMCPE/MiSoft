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

namespace pocketmine\network\mcpe\multiversion\biomes;

use function array_diff;
use function scandir;
use function file_get_contents;
use function krsort;
use const pocketmine\RESOURCE_PATH;

class BiomeDefinitionPalette{
    /** @var string[] */
    protected static $biomeDefs = null;

    /**
     * @return void
     */
    public static function init() : void{
        self::$biomeDefs = [];
        $biomeDefsDirectory = RESOURCE_PATH . '/vanilla/biomes/';
        foreach(array_diff(scandir($biomeDefsDirectory), ["..", "."]) as $protocol){
            self::$biomeDefs[$protocol] = file_get_contents($biomeDefsDirectory . $protocol . '/biome_definitions.nbt');
        }
        krsort(self::$biomeDefs);
    }

    /**
     * @param int $playerProtocol
     * 
     * @return string
     */
    public static function getBiomesCache(int $playerProtocol) : string{
        foreach(self::$biomeDefs as $protocol => $cache){
            if($playerProtocol >= $protocol){
                return $cache;
            }
        }
        return "";
    }
}
