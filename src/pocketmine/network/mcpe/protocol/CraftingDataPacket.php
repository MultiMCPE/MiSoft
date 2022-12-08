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

use pocketmine\utils\Binary;


use pocketmine\inventory\FurnaceRecipe;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\mcpe\NetworkSession;
use function count;
use function str_repeat;

class CraftingDataPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CRAFTING_DATA_PACKET;

	public const ENTRY_SHAPELESS = 0;
	public const ENTRY_SHAPED = 1;
	public const ENTRY_FURNACE = 2;
	public const ENTRY_FURNACE_DATA = 3;
	public const ENTRY_MULTI = 4; //TODO
	public const ENTRY_SHULKER_BOX = 5; //TODO
	public const ENTRY_SHAPELESS_CHEMISTRY = 6; //TODO
	public const ENTRY_SHAPED_CHEMISTRY = 7; //TODO

	/** @var object[] */
	public $entries = [];
	/** @var bool */
	public $cleanRecipes = false;

	public $decodedEntries = [];

	public function clean(){
		$this->entries = [];
		$this->decodedEntries = [];
		return parent::clean();
	}

	protected function decodePayload(){
		$this->decodedEntries = [];
		$recipeCount = $this->getUnsignedVarInt();
		for($i = 0; $i < $recipeCount; ++$i){
			$entry = [];
			$entry["type"] = $recipeType = $this->getVarInt();

			switch($recipeType){
				case self::ENTRY_SHAPELESS:
				case self::ENTRY_SHULKER_BOX:
				case self::ENTRY_SHAPELESS_CHEMISTRY:
				    if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_360) {
				    	$entry["recipe_id"] = $this->getString();
				    }
					$ingredientCount = $this->getUnsignedVarInt();
					/** @var Item */
					$entry["input"] = [];
					for($j = 0; $j < $ingredientCount; ++$j){
					   	if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_360) {
					    	$entry["input"][] = $in = $this->getRecipeIngredient();
					    	$in->setCount(1); //TODO HACK: they send a useless count field which breaks the PM crafting system because it isn't always 1
					   	} else {
					   	    $entry["input"][] = $this->getSlot($this->getProtocol());
					   	}
					}
					$resultCount = $this->getUnsignedVarInt();
					$entry["output"] = [];
					for($k = 0; $k < $resultCount; ++$k){
						$entry["output"][] = $this->getSlot($this->getProtocol());
					}
					$entry["uuid"] = $this->getUUID()->toString();
					if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_350) {
				    	$entry["block"] = $this->getString();
					}
					if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_360) {
				    	$entry["priority"] = $this->getVarInt();
					}

					break;
				case self::ENTRY_SHAPED:
				case self::ENTRY_SHAPED_CHEMISTRY:
				    if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_360) {
					    $entry["recipe_id"] = $this->getString();
				    }
					$entry["width"] = $this->getVarInt();
					$entry["height"] = $this->getVarInt();
					$count = $entry["width"] * $entry["height"];
					$entry["input"] = [];
					for($j = 0; $j < $count; ++$j){
					    if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_360) {
					    	$entry["input"][] = $in = $this->getRecipeIngredient();
					    	$in->setCount(1); //TODO HACK: they send a useless count field which breaks the PM crafting system
					    } else {
					        $entry["input"][] = $this->getSlot($this->getProtocol());
					    }
					}
					$resultCount = $this->getUnsignedVarInt();
					$entry["output"] = [];
					for($k = 0; $k < $resultCount; ++$k){
						$entry["output"][] = $this->getSlot($this->getProtocol());
					}
					$entry["uuid"] = $this->getUUID()->toString();
					if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_350) {
				    	$entry["block"] = $this->getString();
					}
					if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_360) {
				    	$entry["priority"] = $this->getVarInt();
					}

					break;
				case self::ENTRY_FURNACE:
				case self::ENTRY_FURNACE_DATA:
					$inputId = $this->getVarInt();
					$inputData = -1;
					if($recipeType === self::ENTRY_FURNACE_DATA){
						$inputData = $this->getVarInt();
						if($inputData === 0x7fff){
							$inputData = -1;
						}
					}
					$entry["input"] = ItemFactory::get($inputId, $inputData);
					if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_360) {
				    	$entry["output"] = $out = $this->getSlot($this->getProtocol());
				    	if($out->getDamage() === 0x7fff){
					    	$out->setDamage(0); //TODO HACK: some 1.12 furnace recipe outputs have wildcard damage values
				    	}
					} else {
					    $entry["output"] = $this->getSlot($this->getProtocol());
					}
					if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_350) {
				    	$entry["block"] = $this->getString();
					}

					break;
				case self::ENTRY_MULTI:
					$entry["uuid"] = $this->getUUID()->toString();
					break;
				default:
					throw new \UnexpectedValueException("Unhandled recipe type $recipeType!"); //do not continue attempting to decode
			}
			$this->decodedEntries[] = $entry;
		}
		$this->cleanRecipes = (($this->get(1) !== "\x00"));
	}

	private static function writeEntry($entry, NetworkBinaryStream $stream, int $pos, int $playerProtocol){
		if($entry instanceof ShapelessRecipe){
			return self::writeShapelessRecipe($entry, $stream, $pos, $playerProtocol);
		}elseif($entry instanceof ShapedRecipe){
			return self::writeShapedRecipe($entry, $stream, $pos, $playerProtocol);
		}elseif($entry instanceof FurnaceRecipe){
			return self::writeFurnaceRecipe($entry, $stream, $playerProtocol);
		}
		//TODO: add MultiRecipe

		return -1;
	}

	private static function writeShapelessRecipe(ShapelessRecipe $recipe, NetworkBinaryStream $stream, int $pos, int $playerProtocol){
	    if ($playerProtocol >= ProtocolInfo::PROTOCOL_360) {
	    	$stream->putString((\pack("N", $pos))); //some kind of recipe ID, doesn't matter what it is as long as it's unique
	    }
		$stream->putUnsignedVarInt($recipe->getIngredientCount());
		foreach($recipe->getIngredientList() as $item){
		    if ($playerProtocol >= ProtocolInfo::PROTOCOL_360) {
		    	$stream->putRecipeIngredient($item);
		    } else {
		        $stream->putSlot($item, $playerProtocol);
		    }
		}

		$results = $recipe->getResults();
		$stream->putUnsignedVarInt(count($results));
		foreach($results as $item){
			$stream->putSlot($item, $playerProtocol);
		}

		$stream->put(str_repeat("\x00", 16)); //Null UUID
		if ($playerProtocol >= ProtocolInfo::PROTOCOL_350) {
	    	$stream->putString("crafting_table"); //TODO: blocktype (no prefix) (this might require internal API breaks)
		}
		if ($playerProtocol >= ProtocolInfo::PROTOCOL_361) {
	    	$stream->putVarInt(50); //TODO: priority
		}

		return CraftingDataPacket::ENTRY_SHAPELESS;
	}

	private static function writeShapedRecipe(ShapedRecipe $recipe, NetworkBinaryStream $stream, int $pos, int $playerProtocol){
	    if ($playerProtocol >= ProtocolInfo::PROTOCOL_360) {
	    	$stream->putString((\pack("N", $pos))); //some kind of recipe ID, doesn't matter what it is as long as it's unique
	    }
		$stream->putVarInt($recipe->getWidth());
		$stream->putVarInt($recipe->getHeight());

		for($z = 0; $z < $recipe->getHeight(); ++$z){
			for($x = 0; $x < $recipe->getWidth(); ++$x){
			    $ingredient = $recipe->getIngredient($x, $z);
			    if ($playerProtocol >= ProtocolInfo::PROTOCOL_360) {
			    	$stream->putRecipeIngredient($ingredient);
			    } else {
			        $stream->putSlot($ingredient, $playerProtocol);
			    }
			}
		}

		$results = $recipe->getResults();
		$stream->putUnsignedVarInt(count($results));
		foreach($results as $item){
			$stream->putSlot($item, $playerProtocol);
		}

		$stream->put(str_repeat("\x00", 16)); //Null UUID
		if ($playerProtocol >= ProtocolInfo::PROTOCOL_350) {
	    	$stream->putString("crafting_table"); //TODO: blocktype (no prefix) (this might require internal API breaks)
		}
		if ($playerProtocol >= ProtocolInfo::PROTOCOL_361) {
	    	$stream->putVarInt(50); //TODO: priority
		}

		return CraftingDataPacket::ENTRY_SHAPED;
	}

	private static function writeFurnaceRecipe(FurnaceRecipe $recipe, NetworkBinaryStream $stream, int $playerProtocol){
		$stream->putVarInt($recipe->getInput()->getId());
		$result = CraftingDataPacket::ENTRY_FURNACE;
		if(!$recipe->getInput()->hasAnyDamageValue()){ //Data recipe
			$stream->putVarInt($recipe->getInput()->getDamage());
			$result = CraftingDataPacket::ENTRY_FURNACE_DATA;
		}
		$stream->putSlot($recipe->getResult(), $playerProtocol);
		if ($playerProtocol >= ProtocolInfo::PROTOCOL_350) {
	    	$stream->putString("furnace"); //TODO: blocktype (no prefix) (this might require internal API breaks)
		}
		return $result;
	}

	public function addShapelessRecipe(ShapelessRecipe $recipe){
		$this->entries[] = $recipe;
	}

	public function addShapedRecipe(ShapedRecipe $recipe){
		$this->entries[] = $recipe;
	}

	public function addFurnaceRecipe(FurnaceRecipe $recipe){
		$this->entries[] = $recipe;
	}

	protected function encodePayload(){
		$this->putUnsignedVarInt(count($this->entries));

		$writer = new NetworkBinaryStream();
		$counter = 0;
		foreach($this->entries as $d){
			$entryType = self::writeEntry($d, $writer, $counter++, $this->getProtocol());
			if($entryType >= 0){
				$this->putVarInt($entryType);
				($this->buffer .= $writer->getBuffer());
			}else{
				$this->putVarInt(-1);
			}

			$writer->reset();
		}

		($this->buffer .= ($this->cleanRecipes ? "\x01" : "\x00"));
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleCraftingData($this);
	}
}
