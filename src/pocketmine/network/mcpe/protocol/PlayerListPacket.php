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

use pocketmine\entity\Skin;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use function chr;
use function count;
use function ord;
use function pack;
use function unpack;

class PlayerListPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_LIST_PACKET;

	public const TYPE_ADD = 0;
	public const TYPE_REMOVE = 1;

	/** @var PlayerListEntry[] */
	public $entries = [];
	/** @var int */
	public $type;

    protected const PE_SKIN_PACKS = ["Standard", "BattleAndBeasts", "Skyrim", "GreekMythology", "MagicTheGathering", "Fallout", "ChineseMythology", "PowerRangers", "BiomeSettlers03", "FestiveMashup26", "CampfireTales", "Villains", "Biome2", "StoryMode", "Redstone", "JTTW", "Festive", "PvPWarriors", "Halloween", "CityFolk", "TownFolk"];

    public static function getPESkinId(Skin $skin) : string{
		$skinId = $skin->getSkinId();
		if($skinId !== "Custom"){
			$id = explode(" ", $skinId);
			if(count($id) < 2){
				$id = explode("_", $skinId);
				if(count($id) < 2){
					goto end;
				}
			}

			$packId = array_shift($id);
			if(in_array($packId, self::PE_SKIN_PACKS, true)){
			    return $skinId;
			}
		}

        end:

        $type = "Custom";
		switch ($skin->getGeometryName()){
			case "geometry.humanoid.customSlim":
			    $type = "CustomSlim";
			    break;
			case "geometry.humanoid.custom":
			default:
			    $type = "Custom";
			    break;
		}

		return "Standard_" . $type;
	}

	public function clean(){
		$this->entries = [];
		return parent::clean();
	}

	protected function decodePayload(){
		$this->type = (ord($this->get(1)));
		$count = $this->getUnsignedVarInt();
		for($i = 0; $i < $count; ++$i){
			$entry = new PlayerListEntry();

			if($this->type === self::TYPE_ADD){
				$entry->uuid = $this->getUUID();
				$entry->entityUniqueId = $this->getEntityUniqueId();
				$entry->username = $this->getString();
				if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
			    	if($this->getProtocol() !== ProtocolInfo::PROTOCOL_201 && $this->getProtocol() >= ProtocolInfo::PROTOCOL_200 && $this->getProtocol() < ProtocolInfo::PROTOCOL_290){
		            	$entry->thirdPartyName = $this->getString();
		            	$entry->platform = $this->getVarInt();
			    	}

                    if($this->getProtocol() < ProtocolInfo::PROTOCOL_370){
			        	$skinId = $this->getString();
			        	$skinData = $this->getString();
			        	$capeData = "";
			        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_131){
			            	$capeData = $this->getString();
			        	}
			        	$geometryName = $this->getString();
			        	$geometryData = $this->getString();

			        	$entry->skin = new Skin(
				        	$skinId,
				        	$skinData,
				        	$capeData,
				        	$geometryName,
				        	$geometryData
			        	);
                    }
			    	$entry->xboxUserId = $this->getString();
			    	if($this->getProtocol() !== ProtocolInfo::PROTOCOL_201 && $this->getProtocol() >= ProtocolInfo::PROTOCOL_200){
			        	$entry->platformChatId = $this->getString();
			        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_370){
			        	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_385){
			                	$entry->buildPlatform = ((unpack("V", $this->get(4))[1] << 32 >> 32));
			        	    }
			            	$entry->skin = $this->getSkin($this->getProtocol());
			            	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_385){
			                	$entry->isTeacher = (($this->get(1) !== "\x00"));
			                	$entry->isHost = (($this->get(1) !== "\x00"));
								if($this->getProtocol() >= ProtocolInfo::PROTOCOL_649){
							    	$entry->isSubClient = $this->getBool();
								}
			            	}
			        	}
			    	}
				} else {
			    	$skinId = $this->getString();
			    	$skinData = $this->getString();

			    	$entry->skin = new Skin(
				    	$skinId,
				    	$skinData
			    	);
				}
			}else{
				$entry->uuid = $this->getUUID();
			}

			$this->entries[$i] = $entry;
		}
		if($this->getProtocol() === ProtocolInfo::PROTOCOL_390 || $this->getProtocol() >= ProtocolInfo::PROTOCOL_406){
	    	if($this->type === self::TYPE_ADD){
		    	for($i = 0; $i < $count; ++$i){
			    	$this->entries[$i]->skin->getSerializedSkin()->setIsTrustedSkin((($this->get(1) !== "\x00")));
		    	}
			}
		}
	}

	protected function encodePayload(){
		($this->buffer .= chr($this->type));
		$this->putUnsignedVarInt(count($this->entries));
		foreach($this->entries as $entry){
			if($this->type === self::TYPE_ADD){
				$this->putUUID($entry->uuid);
				$this->putEntityUniqueId($entry->entityUniqueId);
				$this->putString($entry->username);
				if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
			    	if($this->getProtocol() !== ProtocolInfo::PROTOCOL_201 && $this->getProtocol() >= ProtocolInfo::PROTOCOL_200 && $this->getProtocol() < ProtocolInfo::PROTOCOL_290){
		            	$this->putString($entry->thirdPartyName);
		            	$this->putVarInt($entry->platform);
			    	}
			    	if($this->getProtocol() < ProtocolInfo::PROTOCOL_370){
			        	$this->putString($entry->skin->getSkinId());

			        	if($this->getProtocol() !== ProtocolInfo::PROTOCOL_201 && $this->getProtocol() >= ProtocolInfo::PROTOCOL_200 && $this->getProtocol() < ProtocolInfo::PROTOCOL_220){
				            $this->putLInt(1); // num skins, always 1
			        	}

			        	$emptySkin = str_repeat("\x00", 8192);
			            $skinData = !empty($entry->skin->getSkinData()) ? $entry->skin->getSkinData() : $emptySkin;
			        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_360){
			            	if(empty($entry->skin->getGeometryData()) && strlen($skinData) === 8192){
				            	$skinData = $this->duplicateArmAndLeg($skinData);
			            	}
			        	}
			        	$this->putString($skinData);

			        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_131){
			            	$capeData = $entry->skin->getCapeData();
			            	if($this->getProtocol() !== ProtocolInfo::PROTOCOL_201 && $this->getProtocol() >= ProtocolInfo::PROTOCOL_200 && $this->getProtocol() < ProtocolInfo::PROTOCOL_220){
				            	if(!empty($capeData)){
					            	$this->putLInt(1); // isNotEmpty
					            	$this->putString($capeData);
				            	} else {
					            	$this->putLInt(0); // isEmpty
				            	}
			            	} else {
				            	$this->putString($capeData);
			            	}
			        	}

			        	$skinGeometryName = $entry->skin->getGeometryName();
			        	$skinGeometryData = $entry->skin->getGeometryData();
			        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_310){
				            $skinGeometryName = strtolower($skinGeometryName);
			            	$tempData = json_decode($skinGeometryData, true);
			            	if(is_array($tempData)){
				            	foreach($tempData as $key => $value){
					            	unset($tempData[$key]);
					            	$tempData[strtolower($key)] = $value;
				            	}

				            	$skinGeometryData = json_encode($tempData);
			            	}
			        	}
			        	$this->putString($skinGeometryName);
			        	$this->putString($this->prepareGeometryDataForOld($skinGeometryData));
			    	}

			    	$this->putString($entry->xboxUserId);
			    	if($this->getProtocol() !== ProtocolInfo::PROTOCOL_201 && $this->getProtocol() >= ProtocolInfo::PROTOCOL_200){
			        	$this->putString($entry->platformChatId);
			        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_370){
			        	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_385){
			                	($this->buffer .= (pack("V", $entry->buildPlatform)));
			        	    }
			            	$this->putSkin($entry->skin, $this->getProtocol());
			            	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_385){
			                	($this->buffer .= ($entry->isTeacher ? "\x01" : "\x00"));
			                	($this->buffer .= ($entry->isHost ? "\x01" : "\x00"));
								if($this->getProtocol() >= ProtocolInfo::PROTOCOL_649){
									$this->putBool($entry->isSubClient);
								}
			            	}
			        	}
			    	}
				} else {
				    $this->putString(self::getPESkinId($entry->skin));
				    $this->putString($entry->skin->getSkinData());
				}
			}else{
				$this->putUUID($entry->uuid);
			}
		}
		if($this->getProtocol() === ProtocolInfo::PROTOCOL_390 || $this->getProtocol() >= ProtocolInfo::PROTOCOL_406){
	    	if($this->type === self::TYPE_ADD){
		    	foreach($this->entries as $entry){
			    	($this->buffer .= ($entry->skin->getSerializedSkin()->isTrustedSkin() ? "\x01" : "\x00"));
		    	}
			}
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handlePlayerList($this);
	}
}
