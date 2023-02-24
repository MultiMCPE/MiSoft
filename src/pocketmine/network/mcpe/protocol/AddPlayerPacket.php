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

use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\network\mcpe\protocol\types\GameMode;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\network\mcpe\protocol\types\UpdateAbilitiesPacketLayer;
use pocketmine\utils\UUID;
use function count;
use function pack;
use function unpack;

class AddPlayerPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_PLAYER_PACKET;

	/** @var UUID */
	public $uuid;
	/** @var string */
	public $username;
	/** @var string */
	public $thirdPartyName = "";
	/** @var int */
	public $platform = 0;
	/** @var int|null */
	public $entityUniqueId = null; //TODO
	/** @var int */
	public $entityRuntimeId;
	/** @var string */
	public $platformChatId = "";
	/** @var Vector3 */
	public $position;
	/** @var Vector3|null */
	public $motion;
	/** @var float */
	public $pitch = 0.0;
	/** @var float */
	public $yaw = 0.0;
	/** @var float|null */
	public $headYaw = null; //TODO
	/** @var Item */
	public $item;
	/** @var int */
	public $gameMode = GameMode::SURVIVAL;
	/** @var array */
	public $metadata = [];

    //TODO: update abilities stuff
	private $commandPermission = AdventureSettingsPacket::PERMISSION_NORMAL;
	private $playerPermission = PlayerPermissions::MEMBER;
	private $targetActorUniqueId; //This is a little-endian long, NOT a var-long. (WTF Mojang)
	private $abilityLayers = [];

	//TODO: adventure settings stuff
	public $uvarint1 = 0;
	public $uvarint2 = 0;
	public $uvarint3 = 0;
	public $uvarint4 = 0;
	public $uvarint5 = 0;

	public $long1 = 0;

	/** @var EntityLink[] */
	public $links = [];

	/** @var string */
	public $deviceId = ""; //TODO: fill player's device ID (???)
	/** @var int */
	public $buildPlatform = DeviceOS::UNKNOWN;

	protected function decodePayload(){
		$this->uuid = $this->getUUID();
		$this->username = $this->getString();
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_200 && $this->getProtocol() < ProtocolInfo::PROTOCOL_290){
			$this->thirdPartyName = $this->getString();
			$this->platform = $this->getVarInt();
		}
		if($this->getProtocol() < ProtocolInfo::PROTOCOL_534){
	    	$this->entityUniqueId = $this->getEntityUniqueId();
		}
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_200){
	    	$this->platformChatId = $this->getString();
		}
		$this->position = $this->getVector3();
		$this->motion = $this->getVector3();
		$this->pitch = ((unpack("g", $this->get(4))[1]));
		$this->yaw = ((unpack("g", $this->get(4))[1]));
		$this->headYaw = ((unpack("g", $this->get(4))[1]));
		$this->item = $this->getSlot($this->getProtocol());
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_503){
		    $this->gameMode = $this->getVarInt();
		}
		$this->metadata = $this->getEntityMetadata($this->getProtocol());

        if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_534){
	        	$this->targetActorUniqueId = $this->getLLong(); //WHY IS THIS NON-STANDARD?
	        	$this->playerPermission = $this->getByte();
	        	$this->commandPermission = $this->getByte();

	        	$this->abilityLayers = [];
	        	for($i = 0, $len = $this->getByte(); $i < $len; $i++){
		        	$this->abilityLayers[] = UpdateAbilitiesPacketLayer::decode($this);
	        	}
            }else{
	        	$this->uvarint1 = $this->getUnsignedVarInt();
	        	$this->uvarint2 = $this->getUnsignedVarInt();
	        	$this->uvarint3 = $this->getUnsignedVarInt();
	        	$this->uvarint4 = $this->getUnsignedVarInt();
	        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_133){
	            	$this->uvarint5 = $this->getUnsignedVarInt();
	        	}

	        	$this->long1 = (Binary::readLLong($this->get(8)));
            }

            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_132){
	        	$linkCount = $this->getUnsignedVarInt();
	        	for($i = 0; $i < $linkCount; ++$i){
		        	$this->links[$i] = $this->getEntityLink($this->getProtocol());
	        	}

                if($this->getProtocol() >= ProtocolInfo::PROTOCOL_282){
	            	$this->deviceId = $this->getString();
	            	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_385){
	            	    $this->buildPlatform = ((unpack("V", $this->get(4))[1] << 32 >> 32));
	            	}
                }
            }
        }
	}

	protected function encodePayload(){
		$this->putUUID($this->uuid);
		$this->putString($this->username);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_200 && $this->getProtocol() < ProtocolInfo::PROTOCOL_290){
			$this->putString($this->thirdPartyName);
			$this->putVarInt($this->platform);
		}
		if($this->getProtocol() < ProtocolInfo::PROTOCOL_534){
	    	$this->putEntityUniqueId($this->entityUniqueId ?? $this->entityRuntimeId);
		}
		$this->putEntityRuntimeId($this->entityRuntimeId);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_200){
	    	$this->putString($this->platformChatId);
		}
		$this->putVector3($this->position);
		$this->putVector3Nullable($this->motion);
		($this->buffer .= (pack("g", $this->pitch)));
		($this->buffer .= (pack("g", $this->yaw)));
		($this->buffer .= (pack("g", $this->headYaw ?? $this->yaw)));
		$this->putSlot($this->item, $this->getProtocol());
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_503){
		    $this->putVarInt($this->gameMode);
		}
		$this->putEntityMetadata($this->metadata, $this->getProtocol());

        if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_534){
	        	$this->putLLong($this->targetActorUniqueId);
	        	$this->putByte($this->playerPermission);
	        	$this->putByte($this->commandPermission);

	        	$this->putByte(count($this->abilityLayers));
	        	foreach($this->abilityLayers as $layer){
		        	$layer->encode($this);
	        	}
            }else{
	        	$this->putUnsignedVarInt($this->uvarint1);
	        	$this->putUnsignedVarInt($this->uvarint2);
	        	$this->putUnsignedVarInt($this->uvarint3);
	        	$this->putUnsignedVarInt($this->uvarint4);
	        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_133){
	             	$this->putUnsignedVarInt($this->uvarint5);
	        	}

	        	($this->buffer .= (pack("VV", $this->long1 & 0xFFFFFFFF, $this->long1 >> 32)));
            }

            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_132){
	        	$this->putUnsignedVarInt(count($this->links));
	        	foreach($this->links as $link){
		        	$this->putEntityLink($link, $this->getProtocol());
	        	}

                if($this->getProtocol() >= ProtocolInfo::PROTOCOL_282){
	            	$this->putString($this->deviceId);
	            	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_385){
	            	    ($this->buffer .= (pack("V", $this->buildPlatform)));
	            	}
                }
            }
        }
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAddPlayer($this);
	}
}
