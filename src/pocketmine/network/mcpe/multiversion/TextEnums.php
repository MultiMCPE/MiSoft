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

namespace pocketmine\network\mcpe\multiversion;

use pocketmine\network\mcpe\protocol\ProtocolInfo;

abstract class TextEnums {

	private const GROUP_1 = 1;
	private const GROUP_2 = 2;

    private static $textPacketType = [
        self::GROUP_2 => [
            "TYPE_RAW" => 0,
            "TYPE_CHAT" => 1,
            "TYPE_TRANSLATION" => 2,
            "TYPE_POPUP" => 3,
            "TYPE_JUKEBOX_POPUP" => 4,
            "TYPE_TIP" => 5,
            "TYPE_SYSTEM" => 6,
            "TYPE_WHISPER" => 7,
            "TYPE_ANNOUNCEMENT" => 8,
            "TYPE_JSON" => 9
        ],
        self::GROUP_1 => [
            "TYPE_RAW" => 0,
            "TYPE_CHAT" => 1,
            "TYPE_TRANSLATION" => 2,
            "TYPE_POPUP" => 3,
            "TYPE_TIP" => 4,
            "TYPE_SYSTEM" => 5,
            "TYPE_WHISPER" => 6,
            "TYPE_ANNOUNCEMENT" => 7
        ]
    ];

    /**
     * @param int $playerProtocol
     * @param int $typeId
     * 
     * @return string
     */
	public static function getMessageType(int $playerProtocol, int $typeId) : string{
		$groupKey = self::getTextKeyByProtocol($playerProtocol);
		foreach (self::$textPacketType[$groupKey] as $key => $value) {
		    if ($value === $typeId) {
		        return $key;
		    }
		}

		return self::$textPacketType[$groupKey][$typeId];
	}

    /**
     * @param int $playerProtocol
     * @param string $typeName
     * 
     * @return int
     */
	public static function getMessageTypeId(int $playerProtocol, string $typeName) : int{
		$groupKey = self::getTextKeyByProtocol($playerProtocol);
		if (isset(self::$textPacketType[$groupKey][$typeName])) {
		    return self::$textPacketType[$groupKey][$typeName];
		}

		return 0;
	}

    /**
     * @param int $playerProtocol
     * 
     * @return int
     */
    private static function getTextKeyByProtocol(int $playerProtocol) : int{
		switch ($playerProtocol) {
			case ProtocolInfo::PROTOCOL_361:
			case ProtocolInfo::PROTOCOL_360:
			case ProtocolInfo::PROTOCOL_354:
			case ProtocolInfo::PROTOCOL_351:
			case ProtocolInfo::PROTOCOL_350:
			case ProtocolInfo::PROTOCOL_342:
			case ProtocolInfo::PROTOCOL_340:
			case ProtocolInfo::PROTOCOL_332:
			case ProtocolInfo::PROTOCOL_331:
			case ProtocolInfo::PROTOCOL_330:
			case ProtocolInfo::PROTOCOL_311:
			case ProtocolInfo::PROTOCOL_310:
			case ProtocolInfo::PROTOCOL_290:
			case ProtocolInfo::PROTOCOL_282:
			case ProtocolInfo::PROTOCOL_280:
			case ProtocolInfo::PROTOCOL_274:
			case ProtocolInfo::PROTOCOL_273:
			case ProtocolInfo::PROTOCOL_271:
			case ProtocolInfo::PROTOCOL_260:
			case ProtocolInfo::PROTOCOL_240:
			case ProtocolInfo::PROTOCOL_221:
			case ProtocolInfo::PROTOCOL_220:
			case ProtocolInfo::PROTOCOL_200:
			case ProtocolInfo::PROTOCOL_140:
			case ProtocolInfo::PROTOCOL_135:
			case ProtocolInfo::PROTOCOL_134:
			case ProtocolInfo::PROTOCOL_133:
			    return self::GROUP_2;
			case ProtocolInfo::PROTOCOL_132:
			case ProtocolInfo::PROTOCOL_131:
			case ProtocolInfo::PROTOCOL_130:
				return self::GROUP_1;
			default:
				return self::GROUP_2;
		}
	}

}
