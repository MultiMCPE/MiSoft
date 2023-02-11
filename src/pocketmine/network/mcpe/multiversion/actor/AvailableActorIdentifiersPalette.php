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

namespace pocketmine\network\mcpe\multiversion\actor;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use function file_get_contents;
use const pocketmine\RESOURCE_PATH;

class AvailableActorIdentifiersPalette{
    protected const ACTOR_IDS_CACHE = [
        ProtocolInfo::PROTOCOL_440 => RESOURCE_PATH . '/vanilla/actor/440/entity_identifiers.nbt',
        ProtocolInfo::PROTOCOL_419 => RESOURCE_PATH . '/vanilla/actor/419/entity_identifiers.nbt',
        ProtocolInfo::PROTOCOL_408 => RESOURCE_PATH . '/vanilla/actor/408/entity_identifiers.nbt',
        ProtocolInfo::PROTOCOL_400 => RESOURCE_PATH . '/vanilla/actor/400/entity_identifiers.nbt',
        ProtocolInfo::PROTOCOL_390 => RESOURCE_PATH . '/vanilla/actor/390/entity_identifiers.nbt',
        ProtocolInfo::PROTOCOL_331 => RESOURCE_PATH . '/vanilla/actor/331/entity_identifiers.nbt'
    ];

    /** @var string[] */
    protected static $actorIds = null;

    /**
     * @return void
     */
    public static function init() : void{
        self::$actorIds = [];
        foreach(self::ACTOR_IDS_CACHE as $protocol => $cache){
            self::$actorIds[$protocol] = file_get_contents($cache);
        }
    }

    /**
     * @param int $playerProtocol
     * 
     * @return string
     */
    public static function getActorIdsCache(int $playerProtocol) : string{
        foreach(self::$actorIds as $protocol => $cache){
            if($playerProtocol >= $protocol){
                return $cache;
            }
        }

        return "";
    }
}
