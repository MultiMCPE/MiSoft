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

namespace pocketmine\network\mcpe\multiversion\palette\block;

use RuntimeException;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\multiversion\palette\block\palettes\BlockPalette;
use pocketmine\network\mcpe\multiversion\palette\block\palettes\BlockPalettePre280;
use pocketmine\network\mcpe\multiversion\palette\block\palettes\BlockPalettePre330;
use pocketmine\network\mcpe\multiversion\palette\block\palettes\BlockPalettePre340;
use pocketmine\network\mcpe\multiversion\palette\block\palettes\BlockPalettePre354;
use pocketmine\network\mcpe\multiversion\palette\block\palettes\BlockPalettePre360;
use pocketmine\network\mcpe\multiversion\palette\block\palettes\BlockPalettePre370;
use pocketmine\network\mcpe\multiversion\palette\block\palettes\BlockPalettePre389;
use pocketmine\network\mcpe\multiversion\palette\block\palettes\BlockPalettePre407;
use pocketmine\network\mcpe\multiversion\palette\block\palettes\BlockPalettePre408;
use pocketmine\network\mcpe\multiversion\palette\block\palettes\BlockPalettePre419;
use pocketmine\network\mcpe\multiversion\palette\block\palettes\interface\BlockPaletteInterface;
use function krsort;
use const pocketmine\RESOURCE_PATH;

class BlockPalettePool{
    public const BLOCK_PALETTE_PROTOCOLS = [
        ProtocolInfo::PROTOCOL_818,
        ProtocolInfo::PROTOCOL_800,
        ProtocolInfo::PROTOCOL_786,
        ProtocolInfo::PROTOCOL_776,
        ProtocolInfo::PROTOCOL_766,
        ProtocolInfo::PROTOCOL_748,
        ProtocolInfo::PROTOCOL_729,
        ProtocolInfo::PROTOCOL_712,
        ProtocolInfo::PROTOCOL_685,
        ProtocolInfo::PROTOCOL_671,
        ProtocolInfo::PROTOCOL_662,
        ProtocolInfo::PROTOCOL_649,
        ProtocolInfo::PROTOCOL_630,
        ProtocolInfo::PROTOCOL_622,
        ProtocolInfo::PROTOCOL_618,
        ProtocolInfo::PROTOCOL_594,
        ProtocolInfo::PROTOCOL_589,
        ProtocolInfo::PROTOCOL_582,
        ProtocolInfo::PROTOCOL_575,
        ProtocolInfo::PROTOCOL_567,
        ProtocolInfo::PROTOCOL_560,
        ProtocolInfo::PROTOCOL_544,
        ProtocolInfo::PROTOCOL_534,
        ProtocolInfo::PROTOCOL_526,
        ProtocolInfo::PROTOCOL_503,
        ProtocolInfo::PROTOCOL_486,
        ProtocolInfo::PROTOCOL_471,
        ProtocolInfo::PROTOCOL_465,
        ProtocolInfo::PROTOCOL_448,
        ProtocolInfo::PROTOCOL_440,
        ProtocolInfo::PROTOCOL_428,
        ProtocolInfo::PROTOCOL_419,
        ProtocolInfo::PROTOCOL_408,
        ProtocolInfo::PROTOCOL_407,
        ProtocolInfo::PROTOCOL_389,
        ProtocolInfo::PROTOCOL_370,
        ProtocolInfo::PROTOCOL_360,
        ProtocolInfo::PROTOCOL_354,
        ProtocolInfo::PROTOCOL_340,
        ProtocolInfo::PROTOCOL_330,
        ProtocolInfo::PROTOCOL_310,
        ProtocolInfo::PROTOCOL_280,
        ProtocolInfo::PROTOCOL_274,
        ProtocolInfo::PROTOCOL_270,
        ProtocolInfo::PROTOCOL_260,
        ProtocolInfo::PROTOCOL_240,
        ProtocolInfo::PROTOCOL_221,
        ProtocolInfo::PROTOCOL_220
    ];

	/** @var BlockPaletteInterface[] */
    protected static array $pool = [];

    public static function initPalette(int $protocol){
        $paletteDirectory = RESOURCE_PATH . '/vanilla/palette/block/';
        foreach(self::BLOCK_PALETTE_PROTOCOLS as $paletteProtocol){
            if($protocol >= $paletteProtocol){
                $directory = $paletteDirectory . $paletteProtocol;

                $paletteClass = match(true){
                    $paletteProtocol >= ProtocolInfo::PROTOCOL_419 => new BlockPalette(),
                    $paletteProtocol >= ProtocolInfo::PROTOCOL_408 => new BlockPalettePre419(),
                    $paletteProtocol >= ProtocolInfo::PROTOCOL_407 => new BlockPalettePre408(),
                    $paletteProtocol >= ProtocolInfo::PROTOCOL_389 => new BlockPalettePre407(),
                    $paletteProtocol >= ProtocolInfo::PROTOCOL_370 => new BlockPalettePre389(),
                    $paletteProtocol >= ProtocolInfo::PROTOCOL_360 => new BlockPalettePre370(),
                    $paletteProtocol >= ProtocolInfo::PROTOCOL_354 => new BlockPalettePre360(),
                    $paletteProtocol >= ProtocolInfo::PROTOCOL_340 => new BlockPalettePre354(),
                    $paletteProtocol >= ProtocolInfo::PROTOCOL_330 => new BlockPalettePre340(),
                    $paletteProtocol >= ProtocolInfo::PROTOCOL_280 => new BlockPalettePre330(),
                    $paletteProtocol >= ProtocolInfo::PROTOCOL_220 => new BlockPalettePre280(),
                    default => throw new RuntimeException("Invalid palette protocol: " . $paletteProtocol)
                };
                $paletteClass->setProtocol($paletteProtocol);
                $paletteClass->init($directory);

                self::register($paletteClass);

                break;
            }
        }
    }

	/**
	 * @param BlockPaletteInterface $palette
	 */
	public static function register(BlockPaletteInterface $palette) : void{
		self::$pool[$palette->getProtocol()] = $palette;
        krsort(self::$pool);
	}

    /**
     * @param int $protocol
     * 
     * @return BlockPaletteInterface
     */
    public static function getPalette(int $protocol) : BlockPaletteInterface{
        foreach(self::$pool as $paletteProtocol => $palette){
            if($protocol >= $paletteProtocol){
                return $palette;
            }
        }

        throw new RuntimeException("Not founded block palette for protocol $protocol, MCBE library need to update");
    }

    /**
     * @return BlockPaletteInterface[]
     */
    public static function getPalettes() : array{
        return self::$pool;
    }
}
