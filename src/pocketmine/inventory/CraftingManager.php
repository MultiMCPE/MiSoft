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

namespace pocketmine\inventory;

use Closure;
use Generator;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\crafting\CraftingDataBuildTask;
use pocketmine\timings\Timings;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\multiversion\block\BlockPalette;
use function array_map;
use function file_get_contents;
use function json_decode;
use function json_encode;
use function usort;
use const DIRECTORY_SEPARATOR;
use const pocketmine\RESOURCE_PATH;

class CraftingManager{
	/** @var ShapedRecipe[][] */
	protected $shapedRecipes = [];
	/** @var ShapelessRecipe[][] */
	protected $shapelessRecipes = [];
	/** @var FurnaceRecipe[] */
	protected $furnaceRecipes = [];

    /** @var BatchPacket[]|null[] */
    protected $craftingDataCache = [];
    /** @var CraftingDataBuildTask[]|null[] */
    protected $craftingDataBuildTask = [];
    /** @var Player[] */
    protected $waitingPlayers = [];

	public function __construct(){
		$this->init();
	}

	public function init() : void{
		$recipes = json_decode(file_get_contents(RESOURCE_PATH . "vanilla" . DIRECTORY_SEPARATOR . "recipes.json"), true);

		$itemDeserializerFunc = Closure::fromCallable([Item::class, 'jsonDeserialize']);
		foreach($recipes as $recipe){
			switch($recipe["type"]){
				case "shapeless":
					if($recipe["block"] !== "crafting_table"){ //TODO: filter others out for now to avoid breaking economics
						break;
					}
					$this->registerRecipe(new ShapelessRecipe(
						array_map($itemDeserializerFunc, $recipe["input"]),
						array_map($itemDeserializerFunc, $recipe["output"])
					));
					break;
				case "shaped":
					if($recipe["block"] !== "crafting_table"){ //TODO: filter others out for now to avoid breaking economics
						break;
					}
					$this->registerRecipe(new ShapedRecipe(
						$recipe["shape"],
						array_map($itemDeserializerFunc, $recipe["input"]),
						array_map($itemDeserializerFunc, $recipe["output"])
					));
					break;
				case "smelting":
					if($recipe["block"] !== "furnace"){ //TODO: filter others out for now to avoid breaking economics
						break;
					}
					$this->registerRecipe(new FurnaceRecipe(
						Item::jsonDeserialize($recipe["output"]),
						Item::jsonDeserialize($recipe["input"]))
					);
					break;
				default:
					break;
			}
		}
	}

    /**
     * @param BatchPacket
     * @param int $protocol
     * 
     * @return void
     */
    public function buildCraftingDataCache(BatchPacket $payload, int $protocol) : void{
        unset($this->craftingDataBuildTask[$protocol]);
        $this->craftingDataCache[$protocol] = $payload;

        foreach($this->waitingPlayers[$protocol] as $player){
            if($player instanceof Player && $player->isConnected()){
                $this->requestCraftingDataPacket($player);
            }
        }

        unset($this->waitingPlayers[$protocol]);
    }

    /**
     * @param Player $player
     * 
     * @return void
     */
    public function requestCraftingDataPacket(Player $player) : void{
        if(!isset($this->craftingDataCache[$player->getCraftingProtocol()])){
            if(!isset($this->craftingDataBuildTask[$player->getCraftingProtocol()])){
                Timings::$craftingDataCacheRebuildTimer->startTiming();

                $player->getServer()->getAsyncPool()->submitTask($this->craftingDataBuildTask[$player->getCraftingProtocol()] = new CraftingDataBuildTask(
                    $this->shapelessRecipes,
                    $this->shapedRecipes,
                    $this->furnaceRecipes,
                    $player->getCraftingProtocol(),
                    ($player->getCraftingProtocol() >= ProtocolInfo::PROTOCOL_429 ? BlockPalette::getPalette($player->getCraftingProtocol()) : null),
                    Server::getInstance()->networkCompressionLevel
                ));

                Timings::$craftingDataCacheRebuildTimer->stopTiming();
            }
            $this->waitingPlayers[$player->getCraftingProtocol()][] = $player;
            return;
        }

        $player->dataPacket($this->craftingDataCache[$player->getCraftingProtocol()]);
    }

	/**
	 * Function used to arrange Shapeless Recipe ingredient lists into a consistent order.
	 *
	 * @param Item $i1
	 * @param Item $i2
	 *
	 * @return int
	 */
	public static function sort(Item $i1, Item $i2){
		//Use spaceship operator to compare each property, then try the next one if they are equivalent.
		($retval = $i1->getId() <=> $i2->getId()) === 0 && ($retval = $i1->getDamage() <=> $i2->getDamage()) === 0 && ($retval = $i1->getCount() <=> $i2->getCount());

		return $retval;
	}

	/**
	 * @param Item[] $items
	 *
	 * @return Item[]
	 */
	private static function pack(array $items) : array{
		/** @var Item[] $result */
		$result = [];

		foreach($items as $i => $item){
			foreach($result as $otherItem){
				if($item->equals($otherItem)){
					$otherItem->setCount($otherItem->getCount() + $item->getCount());
					continue 2;
				}
			}

			//No matching item found
			$result[] = clone $item;
		}

		return $result;
	}

	private static function hashOutputs(array $outputs) : string{
		$outputs = self::pack($outputs);
		usort($outputs, [self::class, "sort"]);
		foreach($outputs as $o){
			//this reduces accuracy of hash, but it's necessary to deal with recipe book shift-clicking stupidity
			$o->setCount(1);
		}

		return json_encode($outputs);
	}

	/**
	 * @return ShapelessRecipe[][]
	 */
	public function getShapelessRecipes() : array{
		return $this->shapelessRecipes;
	}

	/**
	 * @return ShapedRecipe[][]
	 */
	public function getShapedRecipes() : array{
		return $this->shapedRecipes;
	}

	/**
	 * @return FurnaceRecipe[]
	 */
	public function getFurnaceRecipes() : array{
		return $this->furnaceRecipes;
	}

	/**
	 * @param ShapedRecipe $recipe
	 */
	public function registerShapedRecipe(ShapedRecipe $recipe) : void{
		$this->shapedRecipes[self::hashOutputs($recipe->getResults())][] = $recipe;

		$this->craftingDataCache = [];
	}

	/**
	 * @param ShapelessRecipe $recipe
	 */
	public function registerShapelessRecipe(ShapelessRecipe $recipe) : void{
		$this->shapelessRecipes[self::hashOutputs($recipe->getResults())][] = $recipe;

		$this->craftingDataCache = [];
	}

	/**
	 * @param FurnaceRecipe $recipe
	 */
	public function registerFurnaceRecipe(FurnaceRecipe $recipe) : void{
		$input = $recipe->getInput();
		$this->furnaceRecipes[$input->getId() . ":" . ($input->hasAnyDamageValue() ? "?" : $input->getDamage())] = $recipe;
		$this->craftingDataCache = [];
	}

	/**
	 * @param CraftingGrid $grid
	 * @param Item[]       $outputs
	 *
	 * @return CraftingRecipe|null
	 */
	public function matchRecipe(CraftingGrid $grid, array $outputs) : ?CraftingRecipe{
		//TODO: try to match special recipes before anything else (first they need to be implemented!)

		$outputHash = self::hashOutputs($outputs);

		if(isset($this->shapedRecipes[$outputHash])){
			foreach($this->shapedRecipes[$outputHash] as $recipe){
				if($recipe->matchesCraftingGrid($grid)){
					return $recipe;
				}
			}
		}

		if(isset($this->shapelessRecipes[$outputHash])){
			foreach($this->shapelessRecipes[$outputHash] as $recipe){
				if($recipe->matchesCraftingGrid($grid)){
					return $recipe;
				}
			}
		}

		return null;
	}

	/**
	 * @param Item[] $outputs
	 *
	 * @return CraftingRecipe[]|Generator
	 */
	public function matchRecipeByOutputs(array $outputs) : Generator{
		//TODO: try to match special recipes before anything else (first they need to be implemented!)

		$outputHash = self::hashOutputs($outputs);

		if(isset($this->shapedRecipes[$outputHash])){
			foreach($this->shapedRecipes[$outputHash] as $recipe){
				yield $recipe;
			}
		}

		if(isset($this->shapelessRecipes[$outputHash])){
			foreach($this->shapelessRecipes[$outputHash] as $recipe){
				yield $recipe;
			}
		}
	}

	/**
	 * @param Item $input
	 *
	 * @return FurnaceRecipe|null
	 */
	public function matchFurnaceRecipe(Item $input) : ?FurnaceRecipe{
		return $this->furnaceRecipes[$input->getId() . ":" . $input->getDamage()] ?? $this->furnaceRecipes[$input->getId() . ":?"] ?? null;
	}

	/**
	 * @param Recipe $recipe
	 */
	public function registerRecipe(Recipe $recipe) : void{
		$recipe->registerToCraftingManager($this);
	}
}
