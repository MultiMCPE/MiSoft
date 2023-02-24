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

class CodeBuilderSourcePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CODE_BUILDER_SOURCE_PACKET;

	private int $operation;
	private int $category;
	private string $value;

	/**
	 * @generate-create-func
	 */
	public static function create(int $operation, int $category, string $value) : self{
		$result = new self;
		$result->operation = $operation;
		$result->category = $category;
		$result->value = $value;
		return $result;
	}

	public function getOperation() : int{ return $this->operation; }

	public function getCategory() : int{ return $this->category; }

	public function getValue() : string{ return $this->value; }

	protected function decodePayload(){
		$this->operation = $this->getByte();
		$this->category = $this->getByte();
		$this->value = $this->getString();
	}

	protected function encodePayload(){
		$this->putByte($this->operation);
		$this->putByte($this->category);
		$this->putString($this->value);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleCodeBuilderSource($this);
	}
}
