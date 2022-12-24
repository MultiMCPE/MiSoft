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

namespace pocketmine\network\mcpe\protocol;

use pocketmine\network\mcpe\protocol\PacketsIds\PacketMagicNumbers;

/**
 * Version numbers and packet IDs for the current Minecraft PE protocol
 */
final class ProtocolInfo implements PacketMagicNumbers{

	/**
	 * NOTE TO DEVELOPERS
	 * Do not waste your time or ours submitting pull requests changing game and/or protocol version numbers.
	 * Pull requests changing game and/or protocol version numbers will be closed.
	 *
	 * This file is generated automatically, do not edit it manually.
	 */

	/**
	 * Actual Minecraft: PE protocol version
	 */
	public const CURRENT_PROTOCOL = ProtocolInfo::PROTOCOL_110;
	public const ACCEPTED_PROTOCOLS = [ProtocolInfo::PROTOCOL_110, ProtocolInfo::PROTOCOL_111, ProtocolInfo::PROTOCOL_112, ProtocolInfo::PROTOCOL_113, ProtocolInfo::PROTOCOL_130, ProtocolInfo::PROTOCOL_131, ProtocolInfo::PROTOCOL_132, ProtocolInfo::PROTOCOL_133, ProtocolInfo::PROTOCOL_134, ProtocolInfo::PROTOCOL_135, ProtocolInfo::PROTOCOL_136, ProtocolInfo::PROTOCOL_137, ProtocolInfo::PROTOCOL_140, ProtocolInfo::PROTOCOL_141, ProtocolInfo::PROTOCOL_150, ProtocolInfo::PROTOCOL_160, ProtocolInfo::PROTOCOL_200, ProtocolInfo::PROTOCOL_201, ProtocolInfo::PROTOCOL_220, ProtocolInfo::PROTOCOL_221, ProtocolInfo::PROTOCOL_222, ProtocolInfo::PROTOCOL_223, ProtocolInfo::PROTOCOL_224, ProtocolInfo::PROTOCOL_240, ProtocolInfo::PROTOCOL_250, ProtocolInfo::PROTOCOL_260, ProtocolInfo::PROTOCOL_261, ProtocolInfo::PROTOCOL_270, ProtocolInfo::PROTOCOL_271, ProtocolInfo::PROTOCOL_273, ProtocolInfo::PROTOCOL_274, ProtocolInfo::PROTOCOL_280, ProtocolInfo::PROTOCOL_281, ProtocolInfo::PROTOCOL_282, ProtocolInfo::PROTOCOL_290, ProtocolInfo::PROTOCOL_291, ProtocolInfo::PROTOCOL_310, ProtocolInfo::PROTOCOL_311, ProtocolInfo::PROTOCOL_312, ProtocolInfo::PROTOCOL_313, ProtocolInfo::PROTOCOL_330, ProtocolInfo::PROTOCOL_331, ProtocolInfo::PROTOCOL_332, ProtocolInfo::PROTOCOL_340, ProtocolInfo::PROTOCOL_342, ProtocolInfo::PROTOCOL_350, ProtocolInfo::PROTOCOL_351, ProtocolInfo::PROTOCOL_352, ProtocolInfo::PROTOCOL_353, ProtocolInfo::PROTOCOL_354, ProtocolInfo::PROTOCOL_360, ProtocolInfo::PROTOCOL_361];
	public const PROTOCOL_110 = 110; // 1.1.0.0, 1.1.0.1, 1.1.0.2, 1.1.0.3, 1.1.0.4, 1.1.0.5
	public const PROTOCOL_111 = 111; // 1.1.0.8
	public const PROTOCOL_112 = 112; // 1.1.0.9
	public const PROTOCOL_113 = 113; // 1.1.0, 1.1.1.0, 1.1.1.1, 1.1.1, 1.1.2, 1.1.3.0, 1.1.3.1, 1.1.3, 1.1.4, 1.1.5, 1.1.7
	public const PROTOCOL_130 = 130; // 1.2.0.2
	public const PROTOCOL_131 = 131; // 1.2.0.7
	public const PROTOCOL_132 = 132; // 1.2.0.15
	public const PROTOCOL_133 = 133; // 1.2.0.18
	public const PROTOCOL_134 = 134; // 1.2.0.20, 1.2.0.22
	public const PROTOCOL_135 = 135; // 1.2.0.24, 1.2.0.25
	public const PROTOCOL_136 = 136; // 1.2.0.31
	public const PROTOCOL_137 = 137; // 1.2.0, 1.2.1, 1.2.2, 1.2.3.3, 1.2.3, 1.2.5.0
	public const PROTOCOL_140 = 140; // 1.2.5.11
	public const PROTOCOL_141 = 141; // 1.2.5, 1.2.5.15
	public const PROTOCOL_150 = 150; // 1.2.6, 1.2.6.1
	public const PROTOCOL_160 = 160; // 1.2.7, 1.2.8, 1.2.9
	public const PROTOCOL_200 = 200; // 1.2.10.1
	public const PROTOCOL_201 = 201; // 1.2.10, 1.2.11
	public const PROTOCOL_220 = 220; // 1.2.13.5, 1.2.13.6
	public const PROTOCOL_221 = 221; // 1.2.13.8
	public const PROTOCOL_222 = 222; // 1.2.13.10
	public const PROTOCOL_223 = 223; // 1.2.3, 1.2.4, 1.2.5, 1.2.13.60
	public const PROTOCOL_224 = 224; // 1.2.13.11
	public const PROTOCOL_240 = 240; // 1.2.14.2, 1.2.14.3
	public const PROTOCOL_250 = 250; // 1.2.15.1
	public const PROTOCOL_260 = 260; // 1.2.20.1, 1.2.20.2
	public const PROTOCOL_261 = 261; // 1.4.0, 1.4.1, 1.4.2, 1.4.3, 1.4.4
	public const PROTOCOL_270 = 270; // 1.5.0.0
	public const PROTOCOL_271 = 271; // 1.5.0.1, 1.5.0.4
	public const PROTOCOL_273 = 273; // 1.5.0.7
	public const PROTOCOL_274 = 274; // 1.5.0.10, 1.5.0, 1.5.1, 1.5.2, 1.5.3
	public const PROTOCOL_280 = 280; // 1.6.0.1
	public const PROTOCOL_281 = 281; // 1.6.0.5, 1.6.0.6
	public const PROTOCOL_282 = 282; // 1.6.0.8, 1.6.0.30, 1.6.0, 1.6.1, 1.6.2
	public const PROTOCOL_290 = 290; // 1.7.0.2, 1.7.0.3
	public const PROTOCOL_291 = 291; // 1.7.0.5, 1.7.0.7, 1.7.0.9, 1.7.0, 1.7.1
	public const PROTOCOL_310 = 310; // 1.8.0.4, 1.8.0.8
	public const PROTOCOL_311 = 311; // 1.8.0.9, 1.8.0.10
	public const PROTOCOL_312 = 312; // 1.8.0.11, 1.8.0.13, 1.8.0.14
	public const PROTOCOL_313 = 313; // 1.8.0, 1.8.1
	public const PROTOCOL_330 = 330; // 1.9.0.0
	public const PROTOCOL_331 = 331; // 1.9.0.2
	public const PROTOCOL_332 = 332; // 1.9.0.3, 1.9.0.5, 1.9.0
	public const PROTOCOL_340 = 340; // 1.10.0.3, 1.10.0.4, 1.10.0, 1.10.1
	public const PROTOCOL_342 = 342; // 1.10.0
	public const PROTOCOL_350 = 350; // 1.11.0.1
	public const PROTOCOL_351 = 351; // 1.11.0.3
	public const PROTOCOL_352 = 352; // 1.11.0.4
	public const PROTOCOL_353 = 353; // 1.11.0.5
	public const PROTOCOL_354 = 354; // 1.11.0.7, 1.11.0.8, 1.11.0.9, 1.11.0.10, 1.11.0, 1.11.1, 1.11.2, 1.11.3, 1.11.4
	public const PROTOCOL_360 = 360; // 1.12.0.2
	public const PROTOCOL_361 = 361; // 1.12.0.3, 1.12.0.4, 1.12.0.6, 1.12.0.9, 1.12.0.10, 1.12.0.11, 1.12.0.12, 1.12.0.13, 1.12.0.14
	/**
	 * Current Minecraft PE version reported by the server. This is usually the earliest currently supported version.
	 */
	public const MINECRAFT_VERSION = 'v1.X';
	/**
	 * Version number sent to clients in ping responses.
	 */
	public const MINECRAFT_VERSION_NETWORK = '';

}