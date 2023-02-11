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


use pocketmine\network\mcpe\NetworkSession;
use function chr;
use function ord;

class BlockPickRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::BLOCK_PICK_REQUEST_PACKET;

	/** @var int */
	public $blockX;
	/** @var int */
	public $blockY;
	/** @var int */
	public $blockZ;
	/** @var bool */
	public $addUserData = false;
	/** @var int */
	public $hotbarSlot;

	protected function decodePayload(){
		$this->getSignedBlockPosition($this->blockX, $this->blockY, $this->blockZ);
		if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_130) {
	        $this->addUserData = (($this->get(1) !== "\x00"));
		}
		$this->hotbarSlot = (ord($this->get(1)));
	}

	protected function encodePayload(){
		$this->putSignedBlockPosition($this->blockX, $this->blockY, $this->blockZ);
		if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_130) {
	        ($this->buffer .= ($this->addUserData ? "\x01" : "\x00"));
		}
		($this->buffer .= chr($this->hotbarSlot));
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleBlockPickRequest($this);
	}
}
