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

namespace pocketmine\network\mcpe\multiversion\palette\item;

use RuntimeException;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\multiversion\palette\item\palettes\ItemPalette;
use pocketmine\network\mcpe\multiversion\palette\item\palettes\ItemPalettePre776;
use pocketmine\network\mcpe\multiversion\palette\item\palettes\interface\ItemPaletteInterface;
use function array_filter;
use function file_exists;
use function krsort;
use function scandir;
use function is_numeric;
use const pocketmine\RESOURCE_PATH;

class ItemPalettePool{
	/** @var ItemPaletteInterface[] */
    protected static array $pool = [];

	public static function init() : void{
        self::$pool = [];

        $paletteDirectory = RESOURCE_PATH . '/vanilla/palette/item/';
        foreach(array_filter(
            scandir($paletteDirectory),
            function(string $protocol) : bool{
                return is_numeric($protocol);
            }
        ) as $protocol){
            $directory = $paletteDirectory . $protocol;
            $paletteProtocol = (int) $protocol;

            $paletteClass = match(true){
                $paletteProtocol >= ProtocolInfo::PROTOCOL_776 => new ItemPalette(),
                $paletteProtocol >= ProtocolInfo::PROTOCOL_419 => new ItemPalettePre776(),
                default => throw new RuntimeException("Invalid palette protocol: " . $paletteProtocol)
            };
            $paletteClass->setProtocol($paletteProtocol);

            $requiredItemListFile = $directory . "/required_item_list.json";
            if(!isset($requiredItemList) && !file_exists($requiredItemListFile)){
                throw new RuntimeException("Not found required item list for protocol " . $paletteProtocol);
            }

            $r16ToCurrentItemMapFile = $directory . "/r16_to_current_item_map.json";
            if(!isset($r16ToCurrentItemMap) && !file_exists($r16ToCurrentItemMapFile)){
                throw new RuntimeException("Not found r16 to current item map file for protocol " . $paletteProtocol);
            }

            $paletteClass->init(
                $requiredItemList = $requiredItemListFile,
                $r16ToCurrentItemMap = $r16ToCurrentItemMapFile
            );

            self::register($paletteClass);
        }
	}

	/**
	 * @param ItemPaletteInterface $palette
	 */
	public static function register(ItemPaletteInterface $palette) : void{
		self::$pool[$palette->getProtocol()] = $palette;
        krsort(self::$pool);
	}

    /**
     * @param int $protocol
     * 
     * @return ItemPaletteInterface
     */
    public static function getPalette(int $protocol) : ItemPaletteInterface{
        foreach(self::$pool as $paletteProtocol => $palette){
            if($protocol >= $paletteProtocol){
                return $palette;
            }
        }

        throw new RuntimeException("Not founded item palette for protocol $protocol, MCBE library need to update");
    }

    /**
     * @return ItemPaletteInterface[]
     */
    public static function getPalettes() : array{
        return self::$pool;
    }
}
