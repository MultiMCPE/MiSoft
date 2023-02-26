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

namespace pocketmine\crafting;

use pocketmine\network\mcpe\multiversion\inventory\ItemPalette;
use pocketmine\network\mcpe\multiversion\block\BlockPalette;
use pocketmine\network\mcpe\multiversion\block\palettes\Palette;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\item\ItemFactory;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use function assert;
use function strlen;

class CraftingDataBuildTask extends AsyncTask{
    /** @var array */
    protected $shapelessRecipes;
    /** @var array */
    protected $shapedRecipes;
    /** @var array */
    protected $furnaceRecipes;
    /** @var int */
    protected $protocol;
    /** @var ?Palette */
    protected $palette;
    /** @var int */
    protected $compressionLevel;

	public function __construct(array $shapelessRecipes, array $shapedRecipes, array $furnaceRecipes, int $protocol, ?Palette $palette, int $networkCompressionLevel){
	    $this->shapelessRecipes = $shapelessRecipes;
	    $this->shapedRecipes = $shapedRecipes;
	    $this->furnaceRecipes = $furnaceRecipes;
	    $this->protocol = $protocol;
	    $this->palette = $palette;
		$this->compressionLevel = $networkCompressionLevel;
	}

	public function onRun(){
	    ItemPalette::init();
	    if($this->protocol >= ProtocolInfo::PROTOCOL_429){
	        BlockPalette::addPalette($this->palette, $this->protocol);
	    }
	    PacketPool::init();
	    ItemFactory::init();

	    $pk = new CraftingDataPacket();
	    $pk->cleanRecipes = true;
	    foreach($this->shapelessRecipes as $list){
		    foreach($list as $recipe){
			    $pk->addShapelessRecipe($recipe);
		    }
	    }
	    foreach($this->shapedRecipes as $list){
		    foreach($list as $recipe){
			    $pk->addShapedRecipe($recipe);
		    }
	    }
	    foreach($this->furnaceRecipes as $recipe){
		    $pk->addFurnaceRecipe($recipe);
	    }
        $pk->setProtocol($this->protocol);
	    $pk->encode();

	    $batch = new BatchPacket();
	    $batch->addPacket($pk);
	    $batch->setCompressionLevel($this->compressionLevel);
	    $batch->setProtocol($this->protocol);
	    $batch->encode();

		$this->setResult($batch->getBuffer());
	}

	public function onCompletion(Server $server){
	    $result = $this->getResult();

		$batch = new BatchPacket($result);
		assert(strlen($batch->buffer) > 0);
		$batch->setProtocol($this->protocol);
		$batch->isEncoded = true;

		$server->getCraftingManager()->buildCraftingDataCache($batch, $this->protocol);
	}
}
