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


use pocketmine\network\mcpe\NetworkSession;

class BlockEventPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::BLOCK_EVENT_PACKET;

    public const TYPE_CHEST = 1;

    public const DATA_CHEST_CLOSED = 0;
    public const DATA_CHEST_OPEN = 1;

	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var int */
	public $eventType;
	/** @var int */
	public $eventData;

	protected function decodePayload(){
		$this->getBlockPosition($this->x, $this->y, $this->z);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->eventType = $this->getVarInt();
	    	$this->eventData = $this->getVarInt();
		}else{
	    	$this->eventType = $this->getInt();
	    	$this->eventData = $this->getInt();
		}
	}

	protected function encodePayload(){
		$this->putBlockPosition($this->x, $this->y, $this->z);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putVarInt($this->eventType);
	    	$this->putVarInt($this->eventData);
		}else{
	    	$this->putInt($this->eventType);
	    	$this->putInt($this->eventData);
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleBlockEvent($this);
	}
}
