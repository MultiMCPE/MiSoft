<?php

/*
 * This file is part of BedrockProtocol.
 * Copyright (C) 2014-2022 PocketMine Team <https://github.com/pmmp/BedrockProtocol>
 *
 * BedrockProtocol is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use pocketmine\network\mcpe\NetworkSession;
use function count;

class UnlockedRecipesPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UNLOCKED_RECIPES_PACKET;

	private bool $newRecipes;
	/** @var string[] */
	private array $recipes;

	/**
	 * @generate-create-func
	 * @param string[] $recipes
	 */
	public static function create(bool $newRecipes, array $recipes) : self{
		$result = new self;
		$result->newRecipes = $newRecipes;
		$result->recipes = $recipes;
		return $result;
	}

	public function isNewRecipes() : bool{ return $this->newRecipes; }

	/**
	 * @return string[]
	 */
	public function getRecipes() : array{ return $this->recipes; }

	protected function decodePayload() : void{
		$this->newRecipes = $this->getBool();
		$this->recipes = [];
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; $i++){
			$this->recipes[] = $this->getString();
		}
	}

	protected function encodePayload() : void{
		$this->putBool($this->newRecipes);
		$this->putUnsignedVarInt(count($this->recipes));
		foreach($this->recipes as $recipe){
			$this->putString($recipe);
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleUnlockedRecipes($this);
	}
}
