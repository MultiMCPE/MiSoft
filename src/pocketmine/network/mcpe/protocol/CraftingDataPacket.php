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


use pocketmine\inventory\FurnaceRecipe;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\multiversion\inventory\ItemPalette;
use pocketmine\network\mcpe\protocol\types\PotionContainerChangeRecipe;
use pocketmine\network\mcpe\protocol\types\PotionTypeRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\MaterialReducerRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\MaterialReducerRecipeOutput;
use UnexpectedValueException;
use function count;
use function pack;
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
	/** @var PotionTypeRecipe[] */
	public $potionTypeRecipes = [];
	/** @var PotionContainerChangeRecipe[] */
	public $potionContainerRecipes = [];
	/** @var MaterialReducerRecipe[] */
	public $materialReducerRecipes = [];
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
				    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_360){
				    	$entry["recipe_id"] = $this->getString();
				    }
					$ingredientCount = $this->getUnsignedVarInt();
					/** @var Item */
					$entry["input"] = [];
					for($j = 0; $j < $ingredientCount; ++$j){
					   	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_360){
					    	$entry["input"][] = $in = $this->getRecipeIngredient($this->getProtocol());
					    	$in->setCount(1); //TODO HACK: they send a useless count field which breaks the PM crafting system because it isn't always 1
					   	}else{
					   	    $entry["input"][] = $this->getSlot($this->getProtocol(), false);
					   	}
					}
					$resultCount = $this->getUnsignedVarInt();
					$entry["output"] = [];
					for($k = 0; $k < $resultCount; ++$k){
						$entry["output"][] = $this->getSlot($this->getProtocol(), false);
					}
					$entry["uuid"] = $this->getUUID()->toString();
					if($this->getProtocol() >= ProtocolInfo::PROTOCOL_350){
				    	$entry["block"] = $this->getString();
				    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_360){
				        	$entry["priority"] = $this->getVarInt();
				        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_392){
				        	    $entry["net_id"] = $this->readGenericTypeNetworkId();
				        	}
				    	}
					}

					break;
				case self::ENTRY_SHAPED:
				case self::ENTRY_SHAPED_CHEMISTRY:
				    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_360){
					    $entry["recipe_id"] = $this->getString();
				    }
					$entry["width"] = $this->getVarInt();
					$entry["height"] = $this->getVarInt();
					$count = $entry["width"] * $entry["height"];
					$entry["input"] = [];
					for($j = 0; $j < $count; ++$j){
					    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_360){
					    	$entry["input"][] = $in = $this->getRecipeIngredient($this->getProtocol());
					    	$in->setCount(1); //TODO HACK: they send a useless count field which breaks the PM crafting system
					    }else{
					        $entry["input"][] = $this->getSlot($this->getProtocol(), false);
					    }
					}
					$resultCount = $this->getUnsignedVarInt();
					$entry["output"] = [];
					for($k = 0; $k < $resultCount; ++$k){
						$entry["output"][] = $this->getSlot($this->getProtocol(), false);
					}
					$entry["uuid"] = $this->getUUID()->toString();
					if($this->getProtocol() >= ProtocolInfo::PROTOCOL_350){
				    	$entry["block"] = $this->getString();
				    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_360){
				        	$entry["priority"] = $this->getVarInt();
				        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_392){
				        	    $entry["net_id"] = $this->readGenericTypeNetworkId();
				        	}
				    	}
					}

					break;
				case self::ENTRY_FURNACE:
				case self::ENTRY_FURNACE_DATA:
					$inputId = $this->getVarInt();
					if($this->getProtocol() >= ProtocolInfo::PROTOCOL_419){
					    $palette = ItemPalette::getPalette($this->getProtocol());
				    	if($recipeType === self::ENTRY_FURNACE){
					    	[$inputId, $inputData] = $palette::getLegacyFromRuntimeIdWildcard($inputId, 0x7fff);
				    	}else{
					    	$inputData = $this->getVarInt();
					    	[$inputId, $inputData] = $palette::getLegacyFromRuntimeIdWildcard($inputId, $inputData);
				    	}
					}else{
				    	$inputData = -1;
				    	if($recipeType === self::ENTRY_FURNACE_DATA){
					    	$inputData = $this->getVarInt();
					    	if($inputData === 0x7fff){
						    	$inputData = -1;
					    	}
				    	}
					}
					$entry["input"] = ItemFactory::get($inputId, $inputData);
					if($this->getProtocol() >= ProtocolInfo::PROTOCOL_360){
				    	$entry["output"] = $out = $this->getSlot($this->getProtocol(), false);
				    	if($out->getDamage() === 0x7fff){
					    	$out->setDamage(0); //TODO HACK: some 1.12 furnace recipe outputs have wildcard damage values
				    	}
					}else{
					    $entry["output"] = $this->getSlot($this->getProtocol(), false);
					}
					if($this->getProtocol() >= ProtocolInfo::PROTOCOL_350){
				    	$entry["block"] = $this->getString();
					}

					break;
				case self::ENTRY_MULTI:
					$entry["uuid"] = $this->getUUID()->toString();
					if($this->getProtocol() >= ProtocolInfo::PROTOCOL_392){
					    $entry["net_id"] = $this->readGenericTypeNetworkId();
					}
					break;
				default:
					throw new UnexpectedValueException("Unhandled recipe type $recipeType!"); //do not continue attempting to decode
			}
			$this->decodedEntries[] = $entry;
		}
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_385){
	    	for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
		    	$input = $this->getVarInt();
		    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_406){
		        	$inputMeta = $this->getVarInt();
		    	}
		    	$ingredient = $this->getVarInt();
		    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_406){
		        	$ingredientMeta = $this->getVarInt();
		    	}
		    	$output = $this->getVarInt();
		    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_406){
		        	$outputMeta = $this->getVarInt();
		    	}
		    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_419){
		    	    $palette = ItemPalette::getPalette($this->getProtocol());
		    	    [$input, $inputMeta] = $palette::getLegacyFromRuntimeId($input, $inputMeta);
		    	    [$ingredient, $ingredientMeta] = $palette::getLegacyFromRuntimeId($ingredient, $ingredientMeta);
		    	    [$output, $outputMeta] = $palette::getLegacyFromRuntimeId($output, $outputMeta);
		    	}
		    	$this->potionTypeRecipes[] = new PotionTypeRecipe($input, $inputMeta ?? 0, $ingredient, $ingredientMeta ?? 0, $output, $outputMeta ?? 0);
	    	}
	    	for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
		    	$input = $this->getVarInt();
		    	$ingredient = $this->getVarInt();
		    	$output = $this->getVarInt();
		    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_419){
		    	    $palette = ItemPalette::getPalette($this->getProtocol());
		    	    [$input, ] = $palette::getLegacyFromRuntimeId($input, 0);
		    	    [$ingredient, ] = $palette::getLegacyFromRuntimeId($ingredient, 0);
		    	    [$output, ] = $palette::getLegacyFromRuntimeId($output, 0);
		    	}
		    	$this->potionContainerRecipes[] = new PotionContainerChangeRecipe($input, $ingredient, $output);
	    	}
			if($this->getProtocol() >= ProtocolInfo::PROTOCOL_465){
				for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
					$inputIdAndData = $this->getVarInt();
					[$inputId, $inputMeta] = [$inputIdAndData >> 16, $inputIdAndData & 0x7fff];
					$outputs = [];
					for($j = 0, $outputCount = $this->getUnsignedVarInt(); $j < $outputCount; ++$j){
						$outputItemId = $this->getVarInt();
						$outputItemCount = $this->getVarInt();
						$outputs[] = new MaterialReducerRecipeOutput($outputItemId, $outputItemCount);
					}
					$this->materialReducerRecipes[] = new MaterialReducerRecipe($inputId, $inputMeta, $outputs);
				}
			}
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
	    if($playerProtocol >= ProtocolInfo::PROTOCOL_360){
	    	$stream->putString((pack("N", $pos))); //some kind of recipe ID, doesn't matter what it is as long as it's unique
	    }
		$stream->putUnsignedVarInt($recipe->getIngredientCount());
		foreach($recipe->getIngredientList() as $item){
		    if($playerProtocol >= ProtocolInfo::PROTOCOL_360){
		    	$stream->putRecipeIngredient($item, $playerProtocol);
		    }else{
		        $stream->putSlot($item, $playerProtocol, false);
		    }
		}

		$results = $recipe->getResults();
		$stream->putUnsignedVarInt(count($results));
		foreach($results as $item){
			$stream->putSlot($item, $playerProtocol, false);
		}

		$stream->put(str_repeat("\x00", 16)); //Null UUID
		if($playerProtocol >= ProtocolInfo::PROTOCOL_350){
	    	$stream->putString("crafting_table"); //TODO: blocktype (no prefix) (this might require internal API breaks)
	    	if($playerProtocol >= ProtocolInfo::PROTOCOL_361){
				$stream->putVarInt(50); //TODO: priority
	        	if($playerProtocol >= ProtocolInfo::PROTOCOL_392){
	        	    $stream->writeGenericTypeNetworkId($pos); //TODO: ANOTHER recipe ID, only used on the network
	        	}
	    	}
		}

		return CraftingDataPacket::ENTRY_SHAPELESS;
	}

	private static function writeShapedRecipe(ShapedRecipe $recipe, NetworkBinaryStream $stream, int $pos, int $playerProtocol){
	    if($playerProtocol >= ProtocolInfo::PROTOCOL_360){
	    	$stream->putString((pack("N", $pos))); //some kind of recipe ID, doesn't matter what it is as long as it's unique
	    }
		$stream->putVarInt($recipe->getWidth());
		$stream->putVarInt($recipe->getHeight());

		for($z = 0; $z < $recipe->getHeight(); ++$z){
			for($x = 0; $x < $recipe->getWidth(); ++$x){
			    $ingredient = $recipe->getIngredient($x, $z);
			    if($playerProtocol >= ProtocolInfo::PROTOCOL_360){
			    	$stream->putRecipeIngredient($ingredient, $playerProtocol);
			    }else{
			        $stream->putSlot($ingredient, $playerProtocol, false);
			    }
			}
		}

		$results = $recipe->getResults();
		$stream->putUnsignedVarInt(count($results));
		foreach($results as $item){
			$stream->putSlot($item, $playerProtocol, false);
		}

		$stream->put(str_repeat("\x00", 16)); //Null UUID
		if($playerProtocol >= ProtocolInfo::PROTOCOL_350){
	    	$stream->putString("crafting_table"); //TODO: blocktype (no prefix) (this might require internal API breaks)
	    	if($playerProtocol >= ProtocolInfo::PROTOCOL_361){
				$stream->putVarInt(50); //TODO: priority
	        	if($playerProtocol >= ProtocolInfo::PROTOCOL_392){
	        	    $stream->writeGenericTypeNetworkId($pos); //TODO: ANOTHER recipe ID, only used on the network
	        	}
	    	}
		}

		return CraftingDataPacket::ENTRY_SHAPED;
	}

	private static function writeFurnaceRecipe(FurnaceRecipe $recipe, NetworkBinaryStream $stream, int $playerProtocol){
	    $id = $recipe->getInput()->getId();
	    $damage = $recipe->getInput()->getDamage();
	    if($playerProtocol >= ProtocolInfo::PROTOCOL_419){
	        $palette = ItemPalette::getPalette($playerProtocol);
	    	if($recipe->getInput()->hasAnyDamageValue()){
		    	[$id, ] = $palette::getRuntimeFromLegacyId($id, 0);
		    	$damage = 0x7fff;
	    	}else{
		    	[$id, $damage] = $palette::getRuntimeFromLegacyId($id, $damage);
	    	}
		}
		$stream->putVarInt($id);
		if($playerProtocol >= ProtocolInfo::PROTOCOL_419){
		    $stream->putVarInt($damage);
		    $result = CraftingDataPacket::ENTRY_FURNACE_DATA;
		}else{
	    	$result = CraftingDataPacket::ENTRY_FURNACE;
	    	if(!$recipe->getInput()->hasAnyDamageValue()){ //Data recipe
		     	$stream->putVarInt($damage);
		    	$result = CraftingDataPacket::ENTRY_FURNACE_DATA;
	    	}
		}
		$stream->putSlot($recipe->getResult(), $playerProtocol, false);
		if($playerProtocol >= ProtocolInfo::PROTOCOL_350){
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
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_385){
	    	$this->putUnsignedVarInt(count($this->potionTypeRecipes));
	    	foreach($this->potionTypeRecipes as $recipe){
	    	    $input = $recipe->getInputItemId();
	    	    $inputMeta = $recipe->getInputItemMeta();
	    	    $ingredient = $recipe->getIngredientItemId();
	    	    $ingredientMeta = $recipe->getIngredientItemMeta();
	    	    $output = $recipe->getOutputItemId();
	    	    $outputMeta = $recipe->getOutputItemMeta();
	    	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_419){
	    	        $palette = ItemPalette::getPalette($this->getProtocol());
	    	        [$input, $inputMeta] = $palette::getRuntimeFromLegacyId($input, $inputMeta);
	    	        [$ingredient, $ingredientMeta] = $palette::getRuntimeFromLegacyId($ingredient, $ingredientMeta);
	    	        [$output, $outputMeta] = $palette::getRuntimeFromLegacyId($output, $outputMeta);
	    	    }
		    	$this->putVarInt($input);
		    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_406){
		        	$this->putVarInt($inputMeta);
		    	}
		    	$this->putVarInt($ingredient);
		    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_406){
		        	$this->putVarInt($ingredientMeta);
		    	}
		    	$this->putVarInt($output);
		    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_406){
		        	$this->putVarInt($outputMeta);
		    	}
	    	}
	    	$this->putUnsignedVarInt(count($this->potionContainerRecipes));
	    	foreach($this->potionContainerRecipes as $recipe){
	    	    $input = $recipe->getInputItemId();
	    	    $ingredient = $recipe->getIngredientItemId();
	    	    $output = $recipe->getOutputItemId();
	    	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_419){
	    	        $palette = ItemPalette::getPalette($this->getProtocol());
	    	        [$input, ] = $palette::getRuntimeFromLegacyId($input, 0);
	    	        [$ingredient, ] = $palette::getRuntimeFromLegacyId($ingredient, 0);
	    	        [$output, ] = $palette::getRuntimeFromLegacyId($output, 0);
	    	    }
		    	$this->putVarInt($input);
		    	$this->putVarInt($ingredient);
		    	$this->putVarInt($output);
		    }
			if($this->getProtocol() >= ProtocolInfo::PROTOCOL_465){
				$this->putUnsignedVarInt(count($this->materialReducerRecipes));
				foreach($this->materialReducerRecipes as $recipe){
					$this->putVarInt(($recipe->getInputItemId() << 16) | $recipe->getInputItemMeta());
					$this->putUnsignedVarInt(count($recipe->getOutputs()));
					foreach($recipe->getOutputs() as $output){
						$this->putVarInt($output->getItemId());
						$this->putVarInt($output->getCount());
					}
				}
			}
		}

		($this->buffer .= ($this->cleanRecipes ? "\x01" : "\x00"));
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleCraftingData($this);
	}
}
