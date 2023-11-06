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

use pocketmine\utils\UUID;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\multiversion\inventory\ItemPalette;
use pocketmine\network\mcpe\multiversion\block\BlockPalette;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\EducationEditionOffer;
use pocketmine\network\mcpe\protocol\types\EducationUriResource;
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\network\mcpe\protocol\types\GameRuleType;
use pocketmine\network\mcpe\protocol\types\GeneratorType;
use pocketmine\network\mcpe\protocol\types\MultiplayerGameVisibility;
use pocketmine\network\mcpe\protocol\types\PlayerMovementSettings;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\network\mcpe\protocol\types\SpawnSettings;
use pocketmine\network\mcpe\protocol\types\ChatRestrictionLevel;
use UnexpectedValueException;
use function ord;
use function chr;
use function count;
use function file_get_contents;
use function json_decode;
use function pack;
use function unpack;
use const pocketmine\RESOURCE_PATH;

class StartGamePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::START_GAME_PACKET;

	/** @var string|null */
	private static $blockTableCache = null;
	/** @var string|null */
	private static $itemTableCache = null;

	/** @var int */
	public $entityUniqueId;
	/** @var int */
	public $entityRuntimeId;
	/** @var int */
	public $playerGamemode;

	/** @var Vector3 */
	public $playerPosition;

	/** @var float */
	public $pitch;
	/** @var float */
	public $yaw;

	/** @var int */
	public $seed;
	/** @var SpawnSettings */
	public $spawnSettings;
	/** @var int */
	public $generator = GeneratorType::OVERWORLD;
	/** @var int */
	public $worldGamemode;
	/** @var int */
	public $difficulty;
	/** @var int */
	public $spawnX;
	/** @var int */
	public $spawnY;
	/** @var int */
	public $spawnZ;
	/** @var bool */
	public $hasAchievementsDisabled = true;
	/** @var bool */
	public $isEditorMode = false;
	/** @var bool */
	public $createdInEditorMode = false;
	/** @var bool */
	public $exportedFromEditorMode = false;
	/** @var int */
	public $time = -1;
	/** @var int */
	public $eduEditionOffer = EducationEditionOffer::NONE;
	/** @var bool */
	public $eduMode = false;
	/** @var bool */
	public $hasEduFeaturesEnabled = false;
	/** @var string */
	public $eduProductUUID = "";
	/** @var float */
	public $rainLevel;
	/** @var float */
	public $lightningLevel;
	/** @var bool */
	public $hasConfirmedPlatformLockedContent = false;
	/** @var bool */
	public $isMultiplayerGame = true;
	/** @var bool */
	public $hasLANBroadcast = true;
	/** @var bool */
	public $hasXboxLiveBroadcast = false;
	/** @var int */
	public $xboxLiveBroadcastMode = MultiplayerGameVisibility::PUBLIC;
	/** @var int */
	public $platformBroadcastMode = MultiplayerGameVisibility::PUBLIC;
	/** @var bool */
	public $commandsEnabled;
	/** @var bool */
	public $isTexturePacksRequired = true;
	/** @var array */
	public $gameRules = [ //TODO: implement this
		"naturalregeneration" => [GameRuleType::BOOL, false, false] //Hack for client side regeneration
	];
	/** @var Experiments */
	public $experiments;
	/** @var bool */
	public $hasBonusChestEnabled = false;
	/** @var bool */
	public $hasStartWithMapEnabled = false;
	/** @var bool */
	public $hasTrustPlayersEnabled = false;
	/** @var int */
	public $defaultPlayerPermission = PlayerPermissions::MEMBER; //TODO

	/** @var int */
	public $serverChunkTickRadius = 4; //TODO (leave as default for now)

	/** @var bool */
	public $hasPlatformBroadcast = false;
	/** @var bool */
	public $xboxLiveBroadcastIntent = false;
	/** @var bool */
	public $hasLockedBehaviorPack = false;
	/** @var bool */
	public $hasLockedResourcePack = false;
	/** @var bool */
	public $isFromLockedWorldTemplate = false;
	/** @var bool */
	public $useMsaGamertagsOnly = false;
	/** @var bool */
	public $isFromWorldTemplate = false;
	/** @var bool */
	public $isWorldTemplateOptionLocked = false;
	/** @var bool */
	public $onlySpawnV1Villagers = false;
	/** @var bool */
	public $disablePersona = false;
	/** @var bool */
	public $disableCustomSkins = false;
	/** @var bool */
	public $muteEmoteAnnouncements = false;
	/** @var string */
	public $vanillaVersion = ProtocolInfo::MINECRAFT_VERSION_NETWORK;
	/** @var int */
	public $limitedWorldWidth = 0;
	/** @var int */
	public $limitedWorldLength = 0;
	/** @var bool */
	public $isNewNether = true;
	/** @var EducationUriResource|null */
	public $eduSharedUriResource = null;
	/** @var bool|null */
	public $experimentalGameplayOverride = null;
	/** @var int */
	public $chatRestrictionLevel = ChatRestrictionLevel::NONE;
	/** @var bool */
	public $disablePlayerInteractions = false;

	/** @var string */
	public $levelId = ""; //base64 string, usually the same as world folder name in vanilla
	/** @var string */
	public $worldName;
	/** @var string */
	public $premiumWorldTemplateId = "";
	/** @var bool */
	public $isTrial = false;
	/** @var PlayerMovementSettings */
	public $playerMovementSettings;
	/** @var bool */
	public $isMovementServerAuthoritative = false;
	/** @var int */
	public $currentTick = 0; //only used if isTrial is true
	/** @var int */
	public $enchantmentSeed = 0;
	/** @var string */
	public $multiplayerCorrelationId = ""; //TODO: this should be filled with a UUID of some sort
	/** @var bool */
	public $enableNewInventorySystem = false; //TODO
	/** @var string */
	public $serverSoftwareVersion;
	/** @var CompoundTag */
	public $playerActorProperties;
	/** @var int */
	public $blockPaletteChecksum;
    /** @var UUID */
    public $worldTemplateId;
    /** @var bool */
    public $enableClientSideChunkGeneration = false;
	/** @var bool */
	public $blockNetworkIdsAreHashes = false; //new in 1.19.80, possibly useful for multi version

	/** @var ListTag|array|null ["name" (string), "data" (int16), "legacy_id" (int16)] */
	public $blockTable = null;
	/** @var BlockPaletteEntry[] */
	public $blockPalette = [];
	/** @var array|null string (name) => int16 (legacyID) */
	public $itemTable = null;

	protected function decodePayload(){
		$this->entityUniqueId = $this->getEntityUniqueId();
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->playerGamemode = $this->getVarInt();

		$this->playerPosition = $this->getVector3();

		$this->pitch = ((unpack("g", $this->get(4))[1]));
		$this->yaw = ((unpack("g", $this->get(4))[1]));

		//Level settings
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_503){
		    $this->seed = $this->getLLong();
		}else{
	    	$this->seed = $this->getVarInt();
		}
		if($this->getProtocol() === ProtocolInfo::PROTOCOL_400){
		    $this->get(1);
		    $this->get(1);
		    $this->getString();
		}
		$this->spawnSettings = SpawnSettings::read($this);
		$this->generator = $this->getVarInt();
		$this->worldGamemode = $this->getVarInt();
		$this->difficulty = $this->getVarInt();
		$this->getBlockPosition($this->spawnX, $this->spawnY, $this->spawnZ);
		$this->hasAchievementsDisabled = (($this->get(1) !== "\x00"));
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_534){
		    $this->isEditorMode = (($this->get(1) !== "\x00"));
			if($this->getProtocol() >= ProtocolInfo::PROTOCOL_582){
				$this->createdInEditorMode = (($this->get(1) !== "\x00"));
				$this->exportedFromEditorMode = (($this->get(1) !== "\x00"));
			}
		}
		$this->time = $this->getVarInt();
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_370){
		    if($this->getProtocol() === ProtocolInfo::PROTOCOL_400){
		        $this->get(1);
		    }
		    $this->eduEditionOffer = $this->getVarInt();
		}else{
	    	$this->eduMode = (($this->get(1) !== "\x00"));
		}
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_260 && $this->vanillaVersion != '1.2.20.1'){
	        $this->hasEduFeaturesEnabled = (($this->get(1) !== "\x00"));
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_406){
	        	$this->eduProductUUID = $this->getString();
	    	}
		}
		$this->rainLevel = ((unpack("g", $this->get(4))[1]));
		$this->lightningLevel = ((unpack("g", $this->get(4))[1]));
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_332){
	    	$this->hasConfirmedPlatformLockedContent = (($this->get(1) !== "\x00"));
		}
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
	    	$this->isMultiplayerGame = (($this->get(1) !== "\x00"));
	    	$this->hasLANBroadcast = (($this->get(1) !== "\x00"));
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_330){
	        	$this->xboxLiveBroadcastMode = $this->getVarInt();
	            $this->platformBroadcastMode = $this->getVarInt();
	        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_392 && $this->getProtocol() < ProtocolInfo::PROTOCOL_400){
	        	    $this->get(1);
	        	}
	    	} else {
	    	    $this->hasXboxLiveBroadcast = (($this->get(1) !== "\x00"));
	    	}
		}
		$this->commandsEnabled = (($this->get(1) !== "\x00"));
		$this->isTexturePacksRequired = (($this->get(1) !== "\x00"));
		$this->gameRules = $this->getGameRules($this->getProtocol());
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_419){
                $this->experiments = Experiments::read($this);
            }
	    	$this->hasBonusChestEnabled = (($this->get(1) !== "\x00"));
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_132){
	        	$this->hasStartWithMapEnabled = (($this->get(1) !== "\x00"));
	    	}
	    	if($this->getProtocol() < ProtocolInfo::PROTOCOL_330){
		        $this->hasTrustPlayersEnabled = (($this->get(1) !== "\x00"));
	    	}
	    	$this->defaultPlayerPermission = $this->getVarInt();
	    	if($this->getProtocol() < ProtocolInfo::PROTOCOL_330){
		        $this->xboxLiveBroadcastMode = $this->getVarInt();
	    	}
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_200){
	        	$this->serverChunkTickRadius = ((unpack("V", $this->get(4))[1] << 32 >> 32));
	    	}
	    	if($this->getProtocol() < ProtocolInfo::PROTOCOL_330){
		        $this->hasPlatformBroadcast = (($this->get(1) !== "\x00"));
		        $this->platformBroadcastMode = $this->getVarInt();
		        $this->xboxLiveBroadcastIntent = (($this->get(1) !== "\x00"));
	    	}
            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_260 && $this->vanillaVersion != '1.2.20.1'){
	        	$this->hasLockedBehaviorPack = (($this->get(1) !== "\x00"));
	        	$this->hasLockedResourcePack = (($this->get(1) !== "\x00"));
	        	$this->isFromLockedWorldTemplate = (($this->get(1) !== "\x00"));
	        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_290){
	    	        $this->useMsaGamertagsOnly = (($this->get(1) !== "\x00"));
		            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_311){
	                	$this->isFromWorldTemplate = (($this->get(1) !== "\x00"));
	                	$this->isWorldTemplateOptionLocked = (($this->get(1) !== "\x00"));
	                	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_361){
	                    	$this->onlySpawnV1Villagers = (($this->get(1) !== "\x00"));
	                    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_544){
	                    	    $this->disablePersona = (($this->get(1) !== "\x00"));
	                    	    $this->disableCustomSkins = (($this->get(1) !== "\x00"));
	                    	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_567){
	                    	        $this->muteEmoteAnnouncements = (($this->get(1) !== "\x00"));
	                    	    }
	                    	}
	                    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_370){
	                        	$this->vanillaVersion = $this->getString();
	                        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_392){
	                            	$this->limitedWorldWidth = ((unpack("V", $this->get(4))[1] << 32 >> 32));
	                            	$this->limitedWorldLength = ((unpack("V", $this->get(4))[1] << 32 >> 32));
	                            	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_400){
	                                	$this->isNewNether = (($this->get(1) !== "\x00"));
	                                	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_407){
	                                	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_465){
	                                	        $this->eduSharedUriResource = EducationUriResource::read($this);
	                                	    }
	                                	    if((($this->get(1) !== "\x00"))){
	                                	    	$this->experimentalGameplayOverride = (($this->get(1) !== "\x00"));
	                                	    }else{
	                                	    	$this->experimentalGameplayOverride = null;
	                                	    }
	                                	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_544){
	                                	        $this->chatRestrictionLevel = ord($this->get(1));
	                                	        $this->disablePlayerInteractions = (($this->get(1) !== "\x00"));
	                                	    }
	                                	}
	                            	}
	                        	}else{
	                            	if($this->getProtocol() === ProtocolInfo::PROTOCOL_386 || $this->getProtocol() === ProtocolInfo::PROTOCOL_387){
	        	                        $this->get(1);
	        	                        $this->get(1);
	        	                        $this->getLFloat();
	                            	}
	                        	}
	                    	}
	                	}
		            }
	        	}
            }
		}

	    $this->levelId = $this->getString();
	    $this->worldName = $this->getString();
	    $this->premiumWorldTemplateId = $this->getString();
	   	$this->isTrial = (($this->get(1) !== "\x00"));
	   	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_388){
	   	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_419){
	   	        $this->playerMovementSettings = PlayerMovementSettings::read($this);
	   	    }else{
	         	$this->isMovementServerAuthoritative = (($this->get(1) !== "\x00"));
	   	    }
	   	}
	    $this->currentTick = (Binary::readLLong($this->get(8)));

        if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
	        $this->enchantmentSeed = $this->getVarInt();

            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_280){
                if($this->getProtocol() >= ProtocolInfo::PROTOCOL_419){
	            	$this->blockPalette = [];
	            	for($i = 0, $len = $this->getUnsignedVarInt(); $i < $len; ++$i){
		             	$blockName = $this->getString();
		            	$state = $this->getNbtCompoundRoot();
		            	$this->blockPalette[] = new BlockPaletteEntry($blockName, $state);
	            	}
                }elseif($this->getProtocol() >= ProtocolInfo::PROTOCOL_370){
	            	$blockTable = (new NetworkLittleEndianNBTStream())->read($this->buffer, false, $this->offset, 512);
	            	if(!($blockTable instanceof ListTag)){
		            	throw new UnexpectedValueException("Wrong block table root NBT tag type");
	            	}
	            	$this->blockTable = $blockTable;
                }else{
	            	$this->blockTable = [];
	            	for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
		            	$id = $this->getString();
		            	$data = ((unpack("v", $this->get(2))[1] << 48 >> 48));
		            	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_360){
		                	$unknown = ((unpack("v", $this->get(2))[1] << 48 >> 48));
			            }

		            	$this->blockTable[$i] = ["name" => $id, "data" => $data, "legacy_id" => $unknown ?? 0];
	            	}
	        	}
	        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_360){
		            $this->itemTable = [];
	            	for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
		            	$id = $this->getString();
		            	$legacyId = ((unpack("v", $this->get(2))[1] << 48 >> 48));
		            	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_419){
		                	$this->get(1);
		            	}

		            	$this->itemTable[$id] = $legacyId;
	            	}
	        	}
                if($this->getProtocol() >= ProtocolInfo::PROTOCOL_282){
	            	$this->multiplayerCorrelationId = $this->getString();
	            	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_392){
	        	        $this->enableNewInventorySystem = (($this->get(1) !== "\x00"));
	        	        if($this->getProtocol() >= ProtocolInfo::PROTOCOL_440){
	        	            $this->serverSoftwareVersion = $this->getString();
	        	            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_526){
	        	                $this->playerActorProperties = $this->getNbtCompoundRoot();
	        	            }
							if($this->getProtocol() >= ProtocolInfo::PROTOCOL_475){
								$this->blockPaletteChecksum = $this->getLLong();
								if($this->getProtocol() >= ProtocolInfo::PROTOCOL_526){
								    $this->worldTemplateId = $this->getUUID();
								    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_544){
								        $this->enableClientSideChunkGeneration = (($this->get(1) !== "\x00"));
										if($this->getProtocol() >= ProtocolInfo::PROTOCOL_582){
											$this->blockNetworkIdsAreHashes = (($this->get(1) !== "\x00"));
										}
								    }
								}
							}
	        	        }
	            	}
	        	}
            }
        }
	}

	protected function encodePayload(){
		$this->putEntityUniqueId($this->entityUniqueId);
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putVarInt($this->playerGamemode);

		$this->putVector3($this->playerPosition);

		($this->buffer .= (pack("g", $this->pitch)));
		($this->buffer .= (pack("g", $this->yaw)));

		//Level settings
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_503){
		    $this->putLLong($this->seed);
		}else{
	    	$this->putVarInt($this->seed);
		}
		if($this->getProtocol() === ProtocolInfo::PROTOCOL_400){
		    ($this->buffer .= (chr(0)));
		    ($this->buffer .= (chr(0)));
            $this->putString("");
		}
		$this->spawnSettings->write($this);
		$this->putVarInt($this->generator);
		$this->putVarInt($this->worldGamemode);
		$this->putVarInt($this->difficulty);
		$this->putBlockPosition($this->spawnX, $this->spawnY, $this->spawnZ);
		($this->buffer .= ($this->hasAchievementsDisabled ? "\x01" : "\x00"));
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_534){
		    ($this->buffer .= ($this->isEditorMode ? "\x01" : "\x00"));
			if($this->getProtocol() >= ProtocolInfo::PROTOCOL_582){
				($this->buffer .= ($this->createdInEditorMode ? "\x01" : "\x00"));
				($this->buffer .= ($this->exportedFromEditorMode ? "\x01" : "\x00"));
			}
		}
		$this->putVarInt($this->time);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_370){
		    if($this->getProtocol() === ProtocolInfo::PROTOCOL_400){
		        ($this->buffer .= (chr(0)));
		    }
		    $this->putVarInt($this->eduEditionOffer);
		}else{
		    ($this->buffer .= ($this->eduMode ? "\x01" : "\x00"));
		}
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_260 && $this->vanillaVersion != '1.2.20.1'){
	    	($this->buffer .= ($this->hasEduFeaturesEnabled ? "\x01" : "\x00"));
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_406){
	    	    $this->putString($this->eduProductUUID);
	    	}
		}
		($this->buffer .= (pack("g", $this->rainLevel)));
		($this->buffer .= (pack("g", $this->lightningLevel)));
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_332){
	    	($this->buffer .= ($this->hasConfirmedPlatformLockedContent ? "\x01" : "\x00"));
		}
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
	    	($this->buffer .= ($this->isMultiplayerGame ? "\x01" : "\x00"));
	    	($this->buffer .= ($this->hasLANBroadcast ? "\x01" : "\x00"));
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_330){
	        	$this->putVarInt($this->xboxLiveBroadcastMode);
		        $this->putVarInt($this->platformBroadcastMode);
	        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_392 && $this->getProtocol() < ProtocolInfo::PROTOCOL_400){
		            ($this->buffer .= (chr(0)));
	        	}
	    	} else {
		        ($this->buffer .= ($this->hasXboxLiveBroadcast ? "\x01" : "\x00"));
	    	}
		}
		($this->buffer .= ($this->commandsEnabled ? "\x01" : "\x00"));
		($this->buffer .= ($this->isTexturePacksRequired ? "\x01" : "\x00"));
		$this->putGameRules($this->gameRules, $this->getProtocol());
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
		    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_419){
		        $this->experiments->write($this);
		    }
	    	($this->buffer .= ($this->hasBonusChestEnabled ? "\x01" : "\x00"));
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_132){
	        	($this->buffer .= ($this->hasStartWithMapEnabled ? "\x01" : "\x00"));
	    	}
	    	if($this->getProtocol() < ProtocolInfo::PROTOCOL_330){
		        ($this->buffer .= ($this->hasTrustPlayersEnabled ? "\x01" : "\x00"));
	    	}
	    	$this->putVarInt($this->defaultPlayerPermission);
	    	if($this->getProtocol() < ProtocolInfo::PROTOCOL_330){
		        $this->putVarInt($this->xboxLiveBroadcastMode);
	    	}
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_200){
	        	($this->buffer .= (pack("V", $this->serverChunkTickRadius)));
	    	}
	    	if($this->getProtocol() < ProtocolInfo::PROTOCOL_330){
	        	($this->buffer .= ($this->hasPlatformBroadcast ? "\x01" : "\x00"));
	        	$this->putVarInt($this->platformBroadcastMode);
	        	($this->buffer .= ($this->xboxLiveBroadcastIntent ? "\x01" : "\x00"));
	    	}
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_260 && $this->vanillaVersion != '1.2.20.1'){
	        	($this->buffer .= ($this->hasLockedBehaviorPack ? "\x01" : "\x00"));
	        	($this->buffer .= ($this->hasLockedResourcePack ? "\x01" : "\x00"));
	        	($this->buffer .= ($this->isFromLockedWorldTemplate ? "\x01" : "\x00"));
	        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_290){
	            	($this->buffer .= ($this->useMsaGamertagsOnly ? "\x01" : "\x00"));
	            	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_311){
	                	($this->buffer .= ($this->isFromWorldTemplate ? "\x01" : "\x00"));
	                	($this->buffer .= ($this->isWorldTemplateOptionLocked ? "\x01" : "\x00"));
	                	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_361){
	                    	($this->buffer .= ($this->onlySpawnV1Villagers ? "\x01" : "\x00"));
	                    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_544){
	                    	    ($this->buffer .= ($this->disablePersona ? "\x01" : "\x00"));
	                    	    ($this->buffer .= ($this->disableCustomSkins ? "\x01" : "\x00"));
	                    	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_567){
	                    	        ($this->buffer .= ($this->muteEmoteAnnouncements ? "\x01" : "\x00"));
	                    	    }
	                    	}
	                    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_370){
	        	                $this->putString($this->vanillaVersion);
                                if($this->getProtocol() >= ProtocolInfo::PROTOCOL_392){
	                            	($this->buffer .= (pack("V", $this->limitedWorldWidth)));
	                            	($this->buffer .= (pack("V", $this->limitedWorldLength)));
	                            	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_400){
	                                	($this->buffer .= ($this->isNewNether ? "\x01" : "\x00"));
	                                	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_407){
	                                	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_465){
	                                	        ($this->eduSharedUriResource ?? new EducationUriResource("", ""))->write($this);
	                                	    }
	                                	    ($this->buffer .= ($this->experimentalGameplayOverride !== null ? "\x01" : "\x00"));
	                                	    if($this->experimentalGameplayOverride !== null){
	                                	        ($this->buffer .= ($this->experimentalGameplayOverride ? "\x01" : "\x00"));
	                                	    }
	                                	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_544){
	                                	        ($this->buffer .= (chr($this->chatRestrictionLevel)));
	                                	        ($this->buffer .= ($this->disablePlayerInteractions ? "\x01" : "\x00"));
	                                	    }
	                                	}
                                    }
                                }else{
	                            	if($this->getProtocol() === ProtocolInfo::PROTOCOL_386 || $this->getProtocol() === ProtocolInfo::PROTOCOL_387){
			                        	($this->buffer .= (chr(0)));
			                        	($this->buffer .= (chr(1)));
			                        	$this->putLFloat(0);
	                            	}
                                }
	                    	}
	                	}
	            	}
	        	}
	    	}
        }

		$this->putString($this->levelId);
		$this->putString($this->worldName);
		$this->putString($this->premiumWorldTemplateId);
		($this->buffer .= ($this->isTrial ? "\x01" : "\x00"));
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_388){
		    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_419){
		        $this->playerMovementSettings->write($this);
		    }else{
	        	($this->buffer .= ($this->isMovementServerAuthoritative ? "\x01" : "\x00"));
		    }
		}
		($this->buffer .= (pack("VV", $this->currentTick & 0xFFFFFFFF, $this->currentTick >> 32)));

        if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
	    	$this->putVarInt($this->enchantmentSeed);

            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_280){
                if($this->getProtocol() < ProtocolInfo::PROTOCOL_419){
                    $palette = BlockPalette::getPalette($this->getProtocol());
                    $this->put($palette::getEncodedPalette());
                }else{
	            	$this->putUnsignedVarInt(count($this->blockPalette));
	            	$nbtWriter = new NetworkLittleEndianNBTStream();
	            	foreach($this->blockPalette as $entry){
		            	$this->putString($entry->getName());
		            	$this->put($nbtWriter->write($entry->getStates()));
	            	}
                }
	        	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_360){
	        	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_419){
	        	        $palette = ItemPalette::getPalette($this->getProtocol());
	        	        $this->put($palette::getEncodedPalette());
	        	    }else{
	            	    if($this->itemTable === null){
		                	if(self::$itemTableCache === null){
			                	self::$itemTableCache = self::serializeItemTable(json_decode(file_get_contents(RESOURCE_PATH . '/vanilla/item_id_map.json'), true));
		                  	}
		                	($this->buffer .= self::$itemTableCache);
	                	}else{
		                	($this->buffer .= self::serializeItemTable($this->itemTable));
	                	}
	        	    }
		        }
                if($this->getProtocol() >= ProtocolInfo::PROTOCOL_282){
	    	        $this->putString($this->multiplayerCorrelationId);
	    	        if($this->getProtocol() >= ProtocolInfo::PROTOCOL_392){
	    	            ($this->buffer .= ($this->enableNewInventorySystem ? "\x01" : "\x00"));
	    	            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_440){
	    	                $this->putString($this->serverSoftwareVersion);
	    	                if($this->getProtocol() >= ProtocolInfo::PROTOCOL_526){
	    	                    $this->put((new NetworkLittleEndianNBTStream())->write($this->playerActorProperties));
	    	                }
							if($this->getProtocol() >= ProtocolInfo::PROTOCOL_475){
								$this->putLLong($this->blockPaletteChecksum);
								if($this->getProtocol() >= ProtocolInfo::PROTOCOL_526){
								    $this->putUUID($this->worldTemplateId);
								    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_544){
								        ($this->buffer .= ($this->enableClientSideChunkGeneration ? "\x01" : "\x00"));
										if($this->getProtocol() >= ProtocolInfo::PROTOCOL_582){
											($this->buffer .= ($this->blockNetworkIdsAreHashes ? "\x01" : "\x00"));
										}
								    }
								}
							}
	    	            }
	    	        }
	    	    }
            }
        }
	}

	private static function serializeItemTable(array $table) : string{
		$stream = new NetworkBinaryStream();
		$stream->putUnsignedVarInt(count($table));
		foreach($table as $name => $legacyId){
			$stream->putString($name);
			$stream->putLShort($legacyId);
		}
		return $stream->getBuffer();
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleStartGame($this);
	}
}
