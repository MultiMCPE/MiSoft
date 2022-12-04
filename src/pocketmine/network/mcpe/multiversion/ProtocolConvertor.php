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

abstract class ProtocolConvertor {

    /**
     * @param int $playerProtocol
     * 
     * @return int
     */
	public final static function convertProtocol(int $playerProtocol) : int{
		switch ($playerProtocol) {
			case ProtocolInfo::PROTOCOL_361:
				return ProtocolInfo::PROTOCOL_361;
			case ProtocolInfo::PROTOCOL_360:
				return ProtocolInfo::PROTOCOL_360;
			case ProtocolInfo::PROTOCOL_354:
			    return ProtocolInfo::PROTOCOL_354;
			case ProtocolInfo::PROTOCOL_353:
			case ProtocolInfo::PROTOCOL_352:
			case ProtocolInfo::PROTOCOL_351:
			    return ProtocolInfo::PROTOCOL_351;
			case ProtocolInfo::PROTOCOL_350:
			    return ProtocolInfo::PROTOCOL_350;
			case ProtocolInfo::PROTOCOL_342:
			    return ProtocolInfo::PROTOCOL_342;
			case ProtocolInfo::PROTOCOL_340:
			    return ProtocolInfo::PROTOCOL_340;
			case ProtocolInfo::PROTOCOL_332:
			    return ProtocolInfo::PROTOCOL_332;
			case ProtocolInfo::PROTOCOL_331:
			    return ProtocolInfo::PROTOCOL_331;
			case ProtocolInfo::PROTOCOL_330:
			    return ProtocolInfo::PROTOCOL_330;
			case ProtocolInfo::PROTOCOL_313:
			case ProtocolInfo::PROTOCOL_312:
			case ProtocolInfo::PROTOCOL_311:
			    return ProtocolInfo::PROTOCOL_311;
			case ProtocolInfo::PROTOCOL_310:
			    return ProtocolInfo::PROTOCOL_310;
			case ProtocolInfo::PROTOCOL_291:
			case ProtocolInfo::PROTOCOL_290:
			    return ProtocolInfo::PROTOCOL_290;
			case ProtocolInfo::PROTOCOL_282:
			    return ProtocolInfo::PROTOCOL_282;
			case ProtocolInfo::PROTOCOL_281:
			case ProtocolInfo::PROTOCOL_280:
			    return ProtocolInfo::PROTOCOL_280;
			case ProtocolInfo::PROTOCOL_274:
			    return ProtocolInfo::PROTOCOL_274;
			case ProtocolInfo::PROTOCOL_273:
			    return ProtocolInfo::PROTOCOL_273;
			case ProtocolInfo::PROTOCOL_271:
			    return ProtocolInfo::PROTOCOL_271;
			case ProtocolInfo::PROTOCOL_270:
			case ProtocolInfo::PROTOCOL_261:
			case ProtocolInfo::PROTOCOL_260:
			    return ProtocolInfo::PROTOCOL_260;
			case ProtocolInfo::PROTOCOL_250:
			case ProtocolInfo::PROTOCOL_240:
				return ProtocolInfo::PROTOCOL_240;
			case ProtocolInfo::PROTOCOL_224:
			case ProtocolInfo::PROTOCOL_223:
			case ProtocolInfo::PROTOCOL_222:
			case ProtocolInfo::PROTOCOL_221:
				return ProtocolInfo::PROTOCOL_221;
			case ProtocolInfo::PROTOCOL_220:
				return ProtocolInfo::PROTOCOL_220;
			case ProtocolInfo::PROTOCOL_200:
				return ProtocolInfo::PROTOCOL_200;
			case ProtocolInfo::PROTOCOL_201:
			case ProtocolInfo::PROTOCOL_160:
			case ProtocolInfo::PROTOCOL_150:
			case ProtocolInfo::PROTOCOL_141:
			case ProtocolInfo::PROTOCOL_140:
			    return ProtocolInfo::PROTOCOL_140;
			case ProtocolInfo::PROTOCOL_137:
			case ProtocolInfo::PROTOCOL_136:
			case ProtocolInfo::PROTOCOL_135:
			    return ProtocolInfo::PROTOCOL_135;
			case ProtocolInfo::PROTOCOL_134:
			    return ProtocolInfo::PROTOCOL_134;
			case ProtocolInfo::PROTOCOL_133:
			    return ProtocolInfo::PROTOCOL_133;
			case ProtocolInfo::PROTOCOL_132:
				return ProtocolInfo::PROTOCOL_132;
			case ProtocolInfo::PROTOCOL_131:
			    return ProtocolInfo::PROTOCOL_131;
			case ProtocolInfo::PROTOCOL_130:
			    return ProtocolInfo::PROTOCOL_130;
			default:
				return ProtocolInfo::PROTOCOL_130;
		}
	}

    /**
     * @param int $playerProtocol
     * 
     * @return int
     */
	public static function convertChunkProtocol(int $playerProtocol) : int{
		switch ($playerProtocol) {
		    case ProtocolInfo::PROTOCOL_361:
			case ProtocolInfo::PROTOCOL_360:
				return ProtocolInfo::PROTOCOL_360;
			case ProtocolInfo::PROTOCOL_354:
			case ProtocolInfo::PROTOCOL_351:
			case ProtocolInfo::PROTOCOL_350:
			case ProtocolInfo::PROTOCOL_342:
			case ProtocolInfo::PROTOCOL_340:
			case ProtocolInfo::PROTOCOL_332:
			case ProtocolInfo::PROTOCOL_331:
			    return ProtocolInfo::PROTOCOL_331;
			case ProtocolInfo::PROTOCOL_330:
			case ProtocolInfo::PROTOCOL_311:
			case ProtocolInfo::PROTOCOL_310:
			    return ProtocolInfo::PROTOCOL_310;
			case ProtocolInfo::PROTOCOL_290:
			case ProtocolInfo::PROTOCOL_282:
			case ProtocolInfo::PROTOCOL_280:
			    return ProtocolInfo::PROTOCOL_280;
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
			case ProtocolInfo::PROTOCOL_132:
			case ProtocolInfo::PROTOCOL_131:
			case ProtocolInfo::PROTOCOL_130:
			    return ProtocolInfo::PROTOCOL_130;
			default:
				return ProtocolInfo::PROTOCOL_130;
		}
	}

    /**
     * @param int $playerProtocol
     * 
     * @return int
     */
	public static function convertCraftingProtocol(int $playerProtocol) : int{
		switch ($playerProtocol) {
		    case ProtocolInfo::PROTOCOL_361:
				return ProtocolInfo::PROTOCOL_361;
			case ProtocolInfo::PROTOCOL_360:
				return ProtocolInfo::PROTOCOL_360;
			case ProtocolInfo::PROTOCOL_354:
			case ProtocolInfo::PROTOCOL_351:
			case ProtocolInfo::PROTOCOL_350:
			    return ProtocolInfo::PROTOCOL_350;
			case ProtocolInfo::PROTOCOL_342:
			case ProtocolInfo::PROTOCOL_340:
			case ProtocolInfo::PROTOCOL_332:
			case ProtocolInfo::PROTOCOL_331:
			    return ProtocolInfo::PROTOCOL_331;
			case ProtocolInfo::PROTOCOL_330:
			    return ProtocolInfo::PROTOCOL_330;
			case ProtocolInfo::PROTOCOL_311:
			case ProtocolInfo::PROTOCOL_310:
			    return ProtocolInfo::PROTOCOL_310;
			case ProtocolInfo::PROTOCOL_290:
			case ProtocolInfo::PROTOCOL_282:
			case ProtocolInfo::PROTOCOL_280:
			    return ProtocolInfo::PROTOCOL_280;
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
			case ProtocolInfo::PROTOCOL_132:
			case ProtocolInfo::PROTOCOL_131:
			case ProtocolInfo::PROTOCOL_130:
			    return ProtocolInfo::PROTOCOL_130;
			default:
				return ProtocolInfo::PROTOCOL_130;
		}
	}

}