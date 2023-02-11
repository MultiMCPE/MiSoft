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

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use function file_get_contents;
use const pocketmine\RESOURCE_PATH;

class BiomeDefinitionPalette{
    protected const BIOMES_CACHE = [
		ProtocolInfo::PROTOCOL_475 => RESOURCE_PATH . '/vanilla/biomes/475/biome_definitions.nbt',
		ProtocolInfo::PROTOCOL_471 => RESOURCE_PATH . '/vanilla/biomes/471/biome_definitions.nbt',
        ProtocolInfo::PROTOCOL_428 => RESOURCE_PATH . '/vanilla/biomes/428/biome_definitions.nbt',
        ProtocolInfo::PROTOCOL_400 => RESOURCE_PATH . '/vanilla/biomes/400/biome_definitions.nbt',
        ProtocolInfo::PROTOCOL_331 => RESOURCE_PATH . '/vanilla/biomes/331/biome_definitions.nbt'
    ];

    /** @var string[] */
    protected static $biomeDefs = null;

    /**
     * @return void
     */
    public static function init() : void{
        self::$biomeDefs = [];
        foreach(self::BIOMES_CACHE as $protocol => $cache){
            self::$biomeDefs[$protocol] = file_get_contents($cache);
        }
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
