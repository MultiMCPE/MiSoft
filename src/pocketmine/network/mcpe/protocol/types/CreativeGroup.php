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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\utils\EnumTrait;

class CreativeGroup{
    public const CREATIVE_GROUP_NONE = 0xffffffff;
    use EnumTrait{
        __construct as Enum___construct;
        register as Enum_register;
        fromString as Enum_fromString;
    }

    /**
     * @return void
     */
    protected static function setup() : void{
        self::registerAll(
            new self("planks",  "itemGroup.name.planks",  0x5),
            new self("walls", "itemGroup.name.walls", 0x8b),
            new self("fence", "itemGroup.name.fence", 0x55),
            new self("fence_gate", "itemGroup.name.fenceGate", 0x6b),
            new self("stairs", "itemGroup.name.stairs", 0x43),
            new self("door", "itemGroup.name.door", 0x144),
            new self("trapdoor", "itemGroup.name.trapdoor", 0x60),
            new self("glass", "itemGroup.name.glass", 0x14),
            new self("glass_pane", "itemGroup.name.glassPane", 0x66),
            new self("slab", "itemGroup.name.slab", 0x2c),
            new self("stone_brick", "itemGroup.name.stoneBrick", 0x62),
            new self("sandstone", "itemGroup.name.sandstone", 0x18),
            new self("wool", "itemGroup.name.wool", 0x23),
            new self("wool_carpet", "itemGroup.name.woolCarpet", 0xab),
            new self("stained_clay", "itemGroup.name.stainedClay", 0x52),
            new self("ore", "itemGroup.name.ore", 0xf),
            new self("stone", "itemGroup.name.stone", 0x1),
            new self("log", "itemGroup.name.log", 0x11),
            new self("wood", "itemGroup.name.wood", 0x12),
            new self("sapling", "itemGroup.name.sapling", 0x6),
            new self("seed", "itemGroup.name.seed", 0x127),
            new self("crop", "itemGroup.name.crop", 0x128),
            new self("grass", "itemGroup.name.grass", 0x1f),
            new self("flower", "itemGroup.name.flower", 0x25),
            new self("dye", "itemGroup.name.dye", 0x15f),
            new self("mushroom", "itemGroup.name.mushroom", 0x27),
            new self("mob_egg", "itemGroup.name.mobEgg", 0x17f),
            new self("helmet", "itemGroup.name.helmet", 0x12a),
            new self("chestplate", "itemGroup.name.chestplate", 0x12b),
            new self("leggings", "itemGroup.name.leggings", 0x12c),
            new self("boots", "itemGroup.name.boots", 0x12d),
            new self("sword", "itemGroup.name.sword", 0x10c),
            new self("axe", "itemGroup.name.axe", 0x10f),
            new self("pickaxe", "itemGroup.name.pickaxe", 0x10e),
            new self("shovel", "itemGroup.name.shovel", 0x10d),
            new self("hoe", "itemGroup.name.hoe", 0x122),
            new self("arrow", "itemGroup.name.arrow", 0x106),
            new self("cooked_food", "itemGroup.name.cookedFood", 0x16e),
            new self("misc_food", "itemGroup.name.miscFood", 0x129),
            new self("bed", "itemGroup.name.bed", 0x163),
            new self("anvil", "itemGroup.name.anvil", 0x91),
            new self("chest", "itemGroup.name.chest", 0x36),
            new self("sign", "itemGroup.name.sign", 0x143),
            new self("rail", "itemGroup.name.rail", 0x42),
            new self("minecart", "itemGroup.name.minecart", 0x148)
        );
    }

    /** @var string[] */
    protected static $aliasMap = [];

    /**
     * @param self $group
     * 
     * @return void
     */
    protected static function register(self $group) : void{
        self::Enum_register($group);
        self::$aliasMap[$group->getGroupName()] = $group;
    }

    /**
     * @param string $groupName
     * 
     * @return self
     */
    public static function fromString(string $groupName) : self{
        self::checkInit();

        return self::$aliasMap[$groupName] ?? self::Enum_fromString($groupName);
    }

    /** @var int */
    private static $nextGroupId = 0;
    /** @var int */
    private $magicNumber;
    /** @var string */
    private $groupName;
    /** @var int */
    private $itemLegacyId;

    private function __construct(string $group, string $groupName, int $itemLegacyId){
        $this->Enum___construct($group);
        $this->magicNumber = self::$nextGroupId++;
        $this->groupName = $groupName;
        $this->itemLegacyId = $itemLegacyId;
    }

    /**
     * @return int
     */
    public function getMagicNumber() : int{
        return $this->magicNumber;
    }

    /**
     * @return string
     */
    public function getGroupName() : string{
        return $this->groupName;
    }

    /**
     * @return int
     */
    public function getItemLegacyId() : int{
        return $this->itemLegacyId;
    }

}