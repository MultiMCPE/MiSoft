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

abstract class ParticleEnums {

	private static $particleIds = [
		ProtocolInfo::PROTOCOL_360 => [
			"TYPE_BUBBLE" => 1,
			"TYPE_CRITICAL" => 3,
			"TYPE_SMOKE" => 5,
			"TYPE_EXPLODE" => 6,
			"TYPE_WHITE_SMOKE" => 7,
			"TYPE_FLAME" => 8,
			"TYPE_LAVA" => 9,
			"TYPE_LARGE_SMOKE" => 10,
			"TYPE_REDSTONE" => 11,
			"TYPE_ITEM_BREAK" => 13,
			"TYPE_SNOWBALL_POOF" => 14,
			"TYPE_LARGE_EXPLODE" => 15,
			"TYPE_HUGE_EXPLODE" => 16,
			"TYPE_MOB_FLAME" => 17,
			"TYPE_HEART" => 18,
			"TYPE_TERRAIN" => 19,
			"TYPE_TOWN_AURA" => 20,
			"TYPE_PORTAL" => 21,
			"TYPE_WATER_SPLASH" => 23,
			"TYPE_WATER_WAKE" => 25,
			"TYPE_DRIP_WATER" => 26,
			"TYPE_DRIP_LAVA" => 27,
			"TYPE_DUST" => 28,
			"TYPE_MOB_SPELL" => 29,
			"TYPE_MOB_SPELL_AMBIENT" => 30,
			"TYPE_MOB_SPELL_INSTANTANEOUS" => 31,
			"TYPE_INK" => 32,
			"TYPE_SLIME" => 33,
			"TYPE_RAIN_SPLASH" => 34,
			"TYPE_VILLAGER_ANGRY" => 35,
			"TYPE_VILLAGER_HAPPY" => 36,
			"TYPE_ENCHANTMENT_TABLE" => 37,
			"TYPE_NOTE" => 39,
			"TYPE_WITCH_MAGIC" => 40,
			"TYPE_ICE_CRYSTAL" => 43
		],
		ProtocolInfo::PROTOCOL_110 => [
			"TYPE_BUBBLE" => 1,
			"TYPE_CRITICAL" => 2,
			"TYPE_SMOKE" => 4,
			"TYPE_EXPLODE" => 5,
			"TYPE_WHITE_SMOKE" => 6,
			"TYPE_FLAME" => 7,
			"TYPE_LAVA" => 8,
			"TYPE_LARGE_SMOKE" => 9,
			"TYPE_REDSTONE" => 10,
			"TYPE_ITEM_BREAK" => 12,
			"TYPE_SNOWBALL_POOF" => 13,
			"TYPE_LARGE_EXPLODE" => 14,
			"TYPE_HUGE_EXPLODE" => 15,
			"TYPE_MOB_FLAME" => 16,
			"TYPE_HEART" => 17,
			"TYPE_TERRAIN" => 18,
			"TYPE_TOWN_AURA" => 19,
			"TYPE_PORTAL" => 20,
			"TYPE_WATER_SPLASH" => 21,
			"TYPE_WATER_WAKE" => 22,
			"TYPE_DRIP_WATER" => 23,
			"TYPE_DRIP_LAVA" => 24,
			"TYPE_DUST" => 25,
			"TYPE_MOB_SPELL" => 26,
			"TYPE_MOB_SPELL_AMBIENT" => 27,
			"TYPE_MOB_SPELL_INSTANTANEOUS" => 28,
			"TYPE_INK" => 29,
			"TYPE_SLIME" => 30,
			"TYPE_RAIN_SPLASH" => 31,
			"TYPE_VILLAGER_ANGRY" => 32,
			"TYPE_VILLAGER_HAPPY" => 33,
			"TYPE_ENCHANTMENT_TABLE" => 34,
			"TYPE_NOTE" => 36,
			"TYPE_WITCH_MAGIC" => 37,
			"TYPE_ICE_CRYSTAL" => 40,			
		]
	];

    /**
     * @param int $playerProtocol
     * 
     * @return array
     */
    public static function getParticleArrayByProtocol(int $playerProtocol) : array{
		switch ($playerProtocol) {
			case ProtocolInfo::PROTOCOL_361:
			case ProtocolInfo::PROTOCOL_360:
			    return self::$particleIds[ProtocolInfo::PROTOCOL_360];
			case ProtocolInfo::PROTOCOL_354:
			case ProtocolInfo::PROTOCOL_351:
			case ProtocolInfo::PROTOCOL_350:
			case ProtocolInfo::PROTOCOL_342:
			case ProtocolInfo::PROTOCOL_340:
			case ProtocolInfo::PROTOCOL_332:
			case ProtocolInfo::PROTOCOL_331:
			    return self::$particleIds[ProtocolInfo::PROTOCOL_110];
			default:
				return self::$particleIds[ProtocolInfo::PROTOCOL_360];
		}
    }

}
