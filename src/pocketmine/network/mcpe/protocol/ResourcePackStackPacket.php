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
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\resourcepacks\ResourcePack;
use function count;

class ResourcePackStackPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_STACK_PACKET;

	/** @var bool */
	public $mustAccept = false;

	/** @var ResourcePack[] */
	public $behaviorPackStack = [];
	/** @var ResourcePack[] */
	public $resourcePackStack = [];

	/** @var bool */
	public $isExperimental = false;
	/** @var string */
	public $baseGameVersion = ProtocolInfo::MINECRAFT_VERSION_NETWORK;
	/** @var Experiments */
	public $experiments;
	/** @var bool */
	public $useVanillaEditorPacks;

	protected function decodePayload(){
		$this->mustAccept = (($this->get(1) !== "\x00"));
		$behaviorPackCount = $this->getUnsignedVarInt();
		while($behaviorPackCount-- > 0){
			$this->getString();
			$this->getString();
			if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
		    	$this->getString();
			}
		}

		$resourcePackCount = $this->getUnsignedVarInt();
		while($resourcePackCount-- > 0){
			$this->getString();
			$this->getString();
			if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
		    	$this->getString();
			}
		}

		if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_290) {
		    if($this->getProtocol() < ProtocolInfo::PROTOCOL_419){
	        	$this->isExperimental = (($this->get(1) !== "\x00"));
		    }
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_385){
	        	$this->baseGameVersion = $this->getString();
	        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_419){
	        	    $this->experiments = Experiments::read($this);
					if($this->getProtocol() >= ProtocolInfo::PROTOCOL_671){
				    	$this->useVanillaEditorPacks = (($this->get(1) !== "\x00"));
					}
	        	}
	    	}
		}
	}

	protected function encodePayload(){
		($this->buffer .= ($this->mustAccept ? "\x01" : "\x00"));

		$this->putUnsignedVarInt(count($this->behaviorPackStack));
		foreach($this->behaviorPackStack as $entry){
			$this->putString($entry->getPackId());
			$this->putString($entry->getPackVersion());
			if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
		    	$this->putString(""); //TODO: subpack name
			}
		}

		$this->putUnsignedVarInt(count($this->resourcePackStack));
		foreach($this->resourcePackStack as $entry){
			$this->putString($entry->getPackId());
			$this->putString($entry->getPackVersion());
			if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
		    	$this->putString(""); //TODO: subpack name
			}
		}

		if ($this->getProtocol() >= ProtocolInfo::PROTOCOL_290) {
		    if($this->getProtocol() < ProtocolInfo::PROTOCOL_419){
	        	($this->buffer .= ($this->isExperimental ? "\x01" : "\x00"));
		    }
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_385){
	        	$this->putString($this->baseGameVersion);
	        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_419){
	        	    $this->experiments->write($this);
					if($this->getProtocol() >= ProtocolInfo::PROTOCOL_671){
						($this->buffer .= ($this->useVanillaEditorPacks ? "\x01" : "\x00"));
					}
	        	}
	    	}
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleResourcePackStack($this);
	}
}
