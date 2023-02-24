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
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;

/**
 * Unclear purpose, not used in vanilla Bedrock. Seems to be related to a new Minecraft "editor" edition or mode.
 */
class EditorNetworkPacket extends DataPacket/* implements ClientboundPacket, ServerboundPacket*/{
	public const NETWORK_ID = ProtocolInfo::EDITOR_NETWORK_PACKET;

	/** @phpstan-var CompoundTag */
	private CompoundTag $payload;

	/**
	 * @generate-create-func
	 * @phpstan-param CompoundTag $payload
	 */
	public static function create(CompoundTag $payload) : self{
		$result = new self;
		$result->payload = $payload;
		return $result;
	}

	/** @phpstan-return CompoundTag */
	public function getPayload() : CompoundTag{ return $this->payload; }

	protected function decodePayload(){
		$this->payload = $this->getNbtCompoundRoot();
	}

	protected function encodePayload(){
		$this->put((new NetworkLittleEndianNBTStream())->write($this->payload));
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleEditorNetwork($this);
	}
}
