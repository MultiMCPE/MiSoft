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


use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkSession;

class MoveActorAbsolutePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOVE_ACTOR_ABSOLUTE_PACKET;

	public const FLAG_GROUND = 0x01;
	public const FLAG_TELEPORT = 0x02;

	/** @var int */
	public $entityRuntimeId;
	/** @var int */
	public $flags = 0;
	/** @var Vector3 */
	public $position;
	/** @var float */
	public $xRot;
	/** @var float */
	public $yRot;
	/** @var float */
	public $zRot;

	protected function decodePayload(){
		if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_273) {
	    	$this->entityRuntimeId = $this->getEntityRuntimeId();
	    	if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_274) {
	        	$this->flags = (\ord($this->get(1)));
	    	} else {
		        $this->flags = (\unpack("v", $this->get(2))[1]);
	    	}
	    	$this->position = $this->getVector3();
	    	$this->xRot = $this->getByteRotation();
	    	$this->yRot = $this->getByteRotation();
	    	$this->zRot = $this->getByteRotation();
		} else {
	    	$this->entityRuntimeId = $this->getEntityRuntimeId();
	    	$this->position = $this->getVector3();
	    	$this->xRot = $this->getByteRotation();
	    	$this->yRot = $this->getByteRotation();
	    	$this->zRot = $this->getByteRotation();

	    	$this->flags = 0;
	    	$onGround = $this->get(1) !== "\x00";
	    	$isTeleported = $this->get(1) !== "\x00";
	    	if($onGround){
	    	    $this->flags |= self::FLAG_GROUND;
	    	}
	    	if($isTeleported){
	    	    $this->flags |= self::FLAG_TELEPORT;
	    	}
		}
	}

	protected function encodePayload(){
		if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_273) {
	    	$this->putEntityRuntimeId($this->entityRuntimeId);
	    	if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_274) {
	        	($this->buffer .= \chr($this->flags));
	    	} else {
		        ($this->buffer .= \pack("v", $this->flags));
	    	}
	    	$this->putVector3($this->position);
	    	$this->putByteRotation($this->xRot);
	    	$this->putByteRotation($this->yRot);
	    	$this->putByteRotation($this->zRot);
		} else {
	    	$this->putEntityRuntimeId($this->entityRuntimeId);
	    	$this->putVector3($this->position);
	    	$this->putByteRotation($this->xRot);
	    	$this->putByteRotation($this->yRot);
	    	$this->putByteRotation($this->zRot);

	    	$onGround = ($this->flags & self::FLAG_GROUND) !== 0;
	    	$isTeleported = ($this->flags & self::FLAG_TELEPORT) !== 0;
	    	($this->buffer .= ($onGround ? "\x01" : "\x00"));
	    	($this->buffer .= ($isTeleported ? "\x01" : "\x00"));
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleMoveActorAbsolute($this);
	}
}
