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

namespace pocketmine\network\mcpe\multiversion\block\palettes;

use pocketmine\utils\MainLogger;
use pocketmine\block\BlockIds;
use pocketmine\network\mcpe\NetworkBinaryStream;
use function file_get_contents;
use function json_decode;
use function count;
use function max;
use const pocketmine\RESOURCE_PATH;

/**
 * @internal
 */
final class BlockPalette221 extends Palette{

	/** @var int[] */
	private static $staticRuntimeIdMap = [];
	/** @var int[] */
	private static $legacyIdMap = [];
	/** @var int */
	private static $lastRuntimeId = 0;
    /** @var string|null */
    private static $encodedPalette = null;

	public function __construct(){
		//NOOP
	}

	public static function init() : void{
		/** @var mixed[] $runtimeIdMap */
		$runtimeIdMap = json_decode(file_get_contents(RESOURCE_PATH . "/vanilla/palette/221/BlockPalette.json"), true);
		foreach($runtimeIdMap as $obj){
			self::registerMapping($obj["runtimeID"], $obj["id"], $obj["data"]);
		}

		$stream = new NetworkBinaryStream();
		$stream->putUnsignedVarInt(count($runtimeIdMap));
		foreach($runtimeIdMap as $v){
			$stream->putString($v["name"]);
			$stream->putLShort($v["data"]);
		}
		self::$encodedPalette = $stream->getBuffer();
	}

	/**
	 * @param int $id
	 * @param int $meta
	 *
	 * @return int
	 */
	public static function toStaticRuntimeId(int $id, int $meta = 0) : int{
		$index = ($id << 4) | $meta;
		if(!isset(self::$staticRuntimeIdMap[$index])){
			self::registerMapping($rtId = ++self::$lastRuntimeId, $id, $meta);
			MainLogger::getLogger()->error("ID $id meta $meta does not have a corresponding block static runtime ID, added a new unknown runtime ID ($rtId)");
			return $rtId;
		}

		return self::$staticRuntimeIdMap[$index];
	}

	/**
	 * @param int $runtimeId
	 *
	 * @return int[] [id, meta]
	 */
	public static function fromStaticRuntimeId(int $runtimeId) : array{
		$v = self::$legacyIdMap[$runtimeId];
		return [$v >> 4, $v & 0xf];
	}

	private static function registerMapping(int $staticRuntimeId, int $legacyId, int $legacyMeta) : void{
		self::$staticRuntimeIdMap[($legacyId << 4) | $legacyMeta] = $staticRuntimeId;
		self::$legacyIdMap[$staticRuntimeId] = ($legacyId << 4) | $legacyMeta;
		self::$lastRuntimeId = max(self::$lastRuntimeId, $staticRuntimeId);
	}

	/**
	 * @return string
	 */
	public static function getEncodedPalette() : string{
		return self::$encodedPalette;
	}
}