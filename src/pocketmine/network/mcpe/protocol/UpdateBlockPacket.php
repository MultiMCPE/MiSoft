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

use pocketmine\network\mcpe\multiversion\block\BlockPalette;
use pocketmine\network\mcpe\NetworkSession;

class UpdateBlockPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_BLOCK_PACKET;

	public const FLAG_NONE      = 0b0000;
	public const FLAG_NEIGHBORS = 0b0001;
	public const FLAG_NETWORK   = 0b0010;
	public const FLAG_NOGRAPHIC = 0b0100;
	public const FLAG_PRIORITY  = 0b1000;

	public const FLAG_ALL = self::FLAG_NEIGHBORS | self::FLAG_NETWORK;
	public const FLAG_ALL_PRIORITY = self::FLAG_ALL | self::FLAG_PRIORITY;

	public const DATA_LAYER_NORMAL = 0;
	public const DATA_LAYER_LIQUID = 1;

	/** @var int */
	public $x;
	/** @var int */
	public $z;
	/** @var int */
	public $y;
	/** @var int */
	public $blockId;
	/** @var int */
	public $blockMeta;
	/** @var int */
	public $flags;
	/** @var int */
	public $dataLayerId = self::DATA_LAYER_NORMAL;

	protected function decodePayload(){
		$this->getBlockPosition($this->x, $this->y, $this->z);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_220){
	    	$runtimeId = $this->getUnsignedVarInt();
	    	$palette = BlockPalette::getPalette($this->getProtocol());
	    	$blockData = $palette::fromStaticRuntimeId($runtimeId);
		    $this->blockId = $blockData[0];
		    $this->blockMeta = $blockData[1];
	    	$this->flags = $this->getUnsignedVarInt();
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_240){
	        	$this->dataLayerId = $this->getUnsignedVarInt();
	    	}
		}else{
	    	$this->blockId = $this->getUnsignedVarInt();
	    	$aux = $this->getUnsignedVarInt();
	    	$this->blockMeta = $aux & 0x0f;
	    	$this->flags = $aux >> 4;
		}
	}

	protected function encodePayload(){
		$this->putBlockPosition($this->x, $this->y, $this->z);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_220){
		    $palette = BlockPalette::getPalette($this->getProtocol());
	    	$runtimeId = $palette::toStaticRuntimeId($this->blockId, $this->blockMeta);
	    	$this->putUnsignedVarInt($runtimeId);
	    	$this->putUnsignedVarInt($this->flags);
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_240){
	        	$this->putUnsignedVarInt($this->dataLayerId);
	    	}
		}else{
	    	$this->putUnsignedVarInt($this->blockId);
	    	$this->putUnsignedVarInt(($this->flags << 4) | $this->blockMeta);
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleUpdateBlock($this);
	}
}
