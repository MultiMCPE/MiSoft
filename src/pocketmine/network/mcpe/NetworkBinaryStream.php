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

namespace pocketmine\network\mcpe;

use pocketmine\entity\Attribute;
use pocketmine\entity\Entity;
use pocketmine\entity\Skin;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\block\BlockIds;
use pocketmine\block\BlockFactory;
use pocketmine\math\Vector3;
use pocketmine\nbt\LittleEndianNBTStream;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\network\mcpe\multiversion\inventory\ItemPalette;
use pocketmine\network\mcpe\multiversion\block\BlockPalette;
use pocketmine\network\mcpe\multiversion\MetadataConvertor;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\types\entity\AttributeModifier;
use pocketmine\network\mcpe\protocol\types\CommandOriginData;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\network\mcpe\protocol\types\GameRuleType;
use pocketmine\network\mcpe\protocol\types\PersonaPieceTintColor;
use pocketmine\network\mcpe\protocol\types\PersonaSkinPiece;
use pocketmine\network\mcpe\protocol\types\SerializedSkin;
use pocketmine\network\mcpe\protocol\types\SkinImage;
use pocketmine\network\mcpe\protocol\types\SkinAnimation;
use pocketmine\network\mcpe\protocol\types\StructureEditorData;
use pocketmine\network\mcpe\protocol\types\StructureSettings;
use pocketmine\network\mcpe\protocol\types\recipe\IntIdMetaItemDescriptor;
use pocketmine\network\mcpe\protocol\types\recipe\ItemDescriptorType;
use pocketmine\network\mcpe\protocol\types\recipe\MolangItemDescriptor;
use pocketmine\network\mcpe\protocol\types\recipe\StringIdMetaItemDescriptor;
use pocketmine\network\mcpe\protocol\types\recipe\TagItemDescriptor;
use pocketmine\utils\BinaryStream;
use pocketmine\utils\UUID;
use pocketmine\utils\Color;
use SplFixedArray;
use UnexpectedValueException;
use function chr;
use function count;
use function ord;
use function pack;
use function round;
use function strlen;
use function unpack;

class NetworkBinaryStream extends BinaryStream{

	private const DAMAGE_TAG = "Damage"; //TAG_Int
	private const DAMAGE_TAG_CONFLICT_RESOLUTION = "___Damage_ProtocolCollisionResolution___";
	private const PM_ID_TAG = "___Id___";
	private const PM_META_TAG = "___Meta___";

	public function getString() : string{
		return $this->get($this->getUnsignedVarInt());
	}

	public function putString(string $v) : void{
		$this->putUnsignedVarInt(strlen($v));
		($this->buffer .= $v);
	}

	public function getUUID() : UUID{
		//This is actually two little-endian longs: UUID Most followed by UUID Least
		$part1 = ((unpack("V", $this->get(4))[1] << 32 >> 32));
		$part0 = ((unpack("V", $this->get(4))[1] << 32 >> 32));
		$part3 = ((unpack("V", $this->get(4))[1] << 32 >> 32));
		$part2 = ((unpack("V", $this->get(4))[1] << 32 >> 32));

		return new UUID($part0, $part1, $part2, $part3);
	}

	public function putUUID(UUID $uuid) : void{
		($this->buffer .= (pack("V", $uuid->getPart(1))));
		($this->buffer .= (pack("V", $uuid->getPart(0))));
		($this->buffer .= (pack("V", $uuid->getPart(3))));
		($this->buffer .= (pack("V", $uuid->getPart(2))));
	}

	public function getSkin(int $playerProtocol) : Skin{
		$skinId = $this->getString();
		if($playerProtocol >= ProtocolInfo::PROTOCOL_425){
	    	$skinPlayFabId = $this->getString();
		}
		$skinResourcePatch = $this->getString();
		$skinData = $this->getSkinImage();
		$animationCount = ((unpack("V", $this->get(4))[1] << 32 >> 32));
		if($animationCount > 128){
			throw new UnexpectedValueException("Too many skin animations: $animationCount");
		}
		$animations = [];
		for($i = 0; $i < $animationCount; ++$i){
			$animations[] = new SkinAnimation(
				$skinImage = $this->getSkinImage(),
				$animationType = ((unpack("V", $this->get(4))[1] << 32 >> 32)),
				$animationFrames = ((unpack("g", $this->get(4))[1])),
				$expressionType = ($this->getProtocol() >= ProtocolInfo::PROTOCOL_419 ? ((unpack("V", $this->get(4))[1] << 32 >> 32)) : 0)
			);
		}
		$capeData = $this->getSkinImage();
		$geometryData = $this->getString();
        if($playerProtocol >= ProtocolInfo::PROTOCOL_465){
            $geometryDataVersion = $this->getString();
        }
		$animationData = $this->getString();
		if($playerProtocol < ProtocolInfo::PROTOCOL_465){
	    	$premium = (($this->get(1) !== "\x00"));
	    	$persona = (($this->get(1) !== "\x00"));
	    	$capeOnClassic = (($this->get(1) !== "\x00"));
		}
		$capeId = $this->getString();
		$fullSkinId = $this->getString();

		if($playerProtocol === ProtocolInfo::PROTOCOL_390 || $playerProtocol >= ProtocolInfo::PROTOCOL_406){
	    	$armSize = $this->getString();
	    	$skinColor = Color::fromHexString($this->getString());
	    	$personaPieceCount = ((unpack("V", $this->get(4))[1] << 32 >> 32));
			if($personaPieceCount > 128){
				throw new UnexpectedValueException("Too many persona pieces: $personaPieceCount");
			}
    		$personaPieces = [];
	    	for($i = 0; $i < $personaPieceCount; ++$i){
		    	$personaPieces[] = new PersonaSkinPiece(
			    	$pieceId = $this->getString(),
			    	$pieceType = $this->getString(),
			    	$packId = $this->getString(),
			    	$isDefaultPiece = (($this->get(1) !== "\x00")),
			    	$productId = $this->getString()
		    	);
	    	}
	    	$pieceTintColorCount = ((unpack("V", $this->get(4))[1] << 32 >> 32));
			if($pieceTintColorCount > 128){
				throw new UnexpectedValueException("Too many piece tint colors: $pieceTintColorCount");
			}
	    	$pieceTintColors = [];
	    	for($i = 0; $i < $pieceTintColorCount; ++$i){
		    	$pieceType = $this->getString();
		    	$colorCount = ((unpack("V", $this->get(4))[1] << 32 >> 32));
		    	$colors = [];
		    	for($j = 0; $j < $colorCount; ++$j){
			    	$colors[] = $this->getString();
		    	}
		    	$pieceTintColors[] = new PersonaPieceTintColor(
			    	$pieceType,
			    	$colors
		    	);
	    	}
	    	$personaPieces = SplFixedArray::fromArray($personaPieces);
	    	$pieceTintColors = SplFixedArray::fromArray($pieceTintColors);
	    	if($playerProtocol >= ProtocolInfo::PROTOCOL_465){
	        	$premium = (($this->get(1) !== "\x00"));
	        	$persona = (($this->get(1) !== "\x00"));
	        	$capeOnClassic = (($this->get(1) !== "\x00"));
	        	$isPrimaryUser = (($this->get(1) !== "\x00"));
	        	if($playerProtocol >= ProtocolInfo::PROTOCOL_568){
	        	    $override = (($this->get(1) !== "\x00"));
	        	}
	    	}
		}else{
            $armSize = "wide";
            $skinColor = new Color(0, 0, 0);
            $personaPieces = SplFixedArray::fromArray([]);
            $pieceTintColors = SplFixedArray::fromArray([]);
		}

        return (new SerializedSkin($skinId, $skinPlayFabId ?? "", $skinData, $capeId, $capeData, $skinResourcePatch, $geometryData, $geometryDataVersion ?? "", $animationData, $animations, $premium, $persona, $capeOnClassic, $fullSkinId, $armSize, $skinColor, $personaPieces, $pieceTintColors, $isPrimaryUser ?? true, $override ?? true))->toSkin();
	}

	public function putSkin(Skin $skin, int $playerProtocol){
	    $skin = $skin->getSerializedSkin();
		$this->putString($skin->getSkinId());
		if($playerProtocol >= ProtocolInfo::PROTOCOL_425){
		    $this->putString($skin->getPlayFabId());
		}
		$this->putString($skin->getResourcePatch());
		$this->putSkinImage($skin->getSkinImage());
		($this->buffer .= (pack("V", count($skin->getAnimationFrames()))));
		foreach($skin->getAnimationFrames() as $animation){
			$this->putSkinImage($animation->getImage());
			($this->buffer .= (pack("V", $animation->getType())));
			($this->buffer .= (pack("g", $animation->getFrames())));
			if($this->getProtocol() >= ProtocolInfo::PROTOCOL_419){
			    ($this->buffer .= (pack("V", $animation->getExpressionType())));
			}
		}
		$this->putSkinImage($skin->getCapeImage());
		$this->putString($skin->getGeometryData());
        if($playerProtocol >= ProtocolInfo::PROTOCOL_465){
            $this->putString($skin->getGeometryDataEngineVersion());
        }
		$this->putString($skin->getAnimationData());
		if($playerProtocol < ProtocolInfo::PROTOCOL_465){
    		($this->buffer .= ($skin->isPremiumSkin() ? "\x01" : "\x00"));
	    	($this->buffer .= ($skin->isPersonaSkin() ? "\x01" : "\x00"));
	    	($this->buffer .= ($skin->isCapeOnClassicSkin() ? "\x01" : "\x00"));
		}
		$this->putString($skin->getCapeId());
		$this->putString($skin->getFullSkinId());
		if($playerProtocol === ProtocolInfo::PROTOCOL_390 || $playerProtocol >= ProtocolInfo::PROTOCOL_406){
	    	$this->putString($skin->getArmSize());
	    	$this->putString($skin->getSkinColor()->toHexString());
	    	($this->buffer .= (pack("V", count($skin->getPersonaPieces()))));
	    	foreach($skin->getPersonaPieces() as $piece){
		    	$this->putString($piece->getPieceId());
		    	$this->putString($piece->getPieceType());
		    	$this->putString($piece->getPackId());
		    	($this->buffer .= ($piece->isDefaultPiece() ? "\x01" : "\x00"));
		    	$this->putString($piece->getProductId());
	    	}
	    	($this->buffer .= (pack("V", count($skin->getPieceTintColors()))));
	    	foreach($skin->getPieceTintColors() as $tint){
		    	$this->putString($tint->getPieceType());
		    	($this->buffer .= (pack("V", count($tint->getColors()))));
		    	foreach($tint->getColors() as $color){
			    	$this->putString($color);
		    	}
	    	}
	    	if($playerProtocol >= ProtocolInfo::PROTOCOL_465){
        		($this->buffer .= ($skin->isPremiumSkin() ? "\x01" : "\x00"));
	        	($this->buffer .= ($skin->isPersonaSkin() ? "\x01" : "\x00"));
	        	($this->buffer .= ($skin->isCapeOnClassicSkin() ? "\x01" : "\x00"));
	        	($this->buffer .= ($skin->isPrimaryUser() ? "\x01" : "\x00"));
	        	if($playerProtocol >= ProtocolInfo::PROTOCOL_568){
	        	    ($this->buffer .= ($skin->isOverride() ? "\x01" : "\x00"));
	        	}
	    	}
		}
	}

	private function getSkinImage() : SkinImage{
		$width = ((unpack("V", $this->get(4))[1] << 32 >> 32));
		$height = ((unpack("V", $this->get(4))[1] << 32 >> 32));
		$data = $this->getString();
		return new SkinImage($height, $width, $data);
	}

	private function putSkinImage(SkinImage $image) : void{
		($this->buffer .= (pack("V", $image->getWidth())));
		($this->buffer .= (pack("V", $image->getHeight())));
		$this->putString($image->getData());
	}

	public function getSlot(int $playerProtocol, bool $withStackId = true) : Item{
	    if($playerProtocol >= ProtocolInfo::PROTOCOL_429){
	        return $this->getItemStack($playerProtocol, $withStackId);
	    }

		$id = $this->getVarInt();
		if($id === 0){
			return ItemFactory::get(0, 0, 0);
		}

		$auxValue = $this->getVarInt();
		$data = $auxValue >> 8;
		$cnt = $auxValue & 0xff;

        if($playerProtocol >= ProtocolInfo::PROTOCOL_419){
            $palette = ItemPalette::getPalette($playerProtocol);
            [$id, $data] = $palette::getLegacyFromRuntimeId($id, $data);
        }

		$nbtLen = ((unpack("v", $this->get(2))[1]));

		/** @var CompoundTag|null $nbt */
		$nbt = null;
		if($playerProtocol >= ProtocolInfo::PROTOCOL_330){
	    	if($nbtLen === 0xffff){
		    	$c = (ord($this->get(1)));
		    	if($c !== 1){
			    	throw new UnexpectedValueException("Unexpected NBT count $c");
		    	}
		    	$decodedNBT = (new NetworkLittleEndianNBTStream())->read($this->buffer, false, $this->offset, 512);
		    	if(!($decodedNBT instanceof CompoundTag)){
			    	throw new UnexpectedValueException("Unexpected root tag type for itemstack");
		    	}
		    	$nbt = $decodedNBT;
		    }elseif($nbtLen !== 0){
		    	throw new UnexpectedValueException("Unexpected fake NBT length $nbtLen");
		    }
		}
		elseif($nbtLen > 0){
		    $decodedNBT = (new LittleEndianNBTStream())->read($this->get($nbtLen));
		    if(!($decodedNBT instanceof CompoundTag)){
			    throw new UnexpectedValueException("Unexpected root tag type for itemstack");
		    }
            if($playerProtocol < ProtocolInfo::PROTOCOL_130 && $id === ItemIds::FILLED_MAP && $decodedNBT->hasTag("map_uuid", StringTag::class)){
                $decodedNBT->setLong("map_uuid", (int) $decodedNBT->getString("map_uuid"), true);
            }
		    $nbt = $decodedNBT;
		}

		if($playerProtocol >= ProtocolInfo::PROTOCOL_110){
	    	//TODO
	    	$canPlaceOn = $this->getVarInt();
	     	if($canPlaceOn > 128){
	    		throw new UnexpectedValueException("Too many canPlaceOn: $canPlaceOn");
	    	}
	    	for($i = 0; $i < $canPlaceOn; ++$i){
		    	$this->getString();
	    	}

	    	//TODO
		    $canDestroy = $this->getVarInt();
	    	if($canDestroy > 128){
		    	throw new UnexpectedValueException("Too many canDestroy: $canDestroy");
	    	}
		    for($i = 0; $i < $canDestroy; ++$i){
		    	$this->getString();
	    	}

	    	if($id === ItemIds::SHIELD){
		    	$this->getVarLong(); //"blocking tick" (ffs mojang)
	    	}
		}

		if($nbt !== null){
			if($nbt->hasTag(self::PM_ID_TAG, IntTag::class)){
				$id = $nbt->getInt(self::PM_ID_TAG);
				$nbt->removeTag(self::PM_ID_TAG);
				if($nbt->count() === 0){
					$nbt = null;
				}
			}
			if($nbt->hasTag(self::DAMAGE_TAG, IntTag::class)){
				$data = $nbt->getInt(self::DAMAGE_TAG);
				$nbt->removeTag(self::DAMAGE_TAG);
				if($nbt->count() === 0){
					$nbt = null;
					goto end;
				}
			}
			if(($conflicted = $nbt->getTag(self::DAMAGE_TAG_CONFLICT_RESOLUTION)) !== null){
				$nbt->removeTag(self::DAMAGE_TAG_CONFLICT_RESOLUTION);
				$conflicted->setName(self::DAMAGE_TAG);
				$nbt->setTag($conflicted);
			}
		    if(($metaTag = $nbt->getTag(self::PM_META_TAG)) instanceof IntTag){
		    	$data = $metaTag->getValue();
		    	$nbt->removeTag(self::PM_META_TAG);
		    	if($nbt->count() === 0){
			     	$nbt = null;
		    	}
	    	}
		}
		end:
		return ItemFactory::get($id, $data, $cnt, $nbt);
	}

	public function putSlot(Item $item, int $playerProtocol, bool $withStackId = true) : void{
	    if($playerProtocol >= ProtocolInfo::PROTOCOL_429){
	        $this->putItemStack($item, $playerProtocol, $withStackId);

	        return;
	    }

		if($item->getId() === 0){
			$this->putVarInt(0);

			return;
		}

        $id = $item->getId();
        $damage = $item->getDamage();

		$nbt = null;
		if($item->hasCompoundTag()){
			$nbt = clone $item->getNamedTag();
		}

		if($id === ItemIds::PUMPKIN && $playerProtocol >= ProtocolInfo::PROTOCOL_260){
		    if($nbt === null){
		    	$nbt = new CompoundTag();
	    	}
	    	$nbt->setInt(self::PM_ID_TAG, $item->getId());
	    	$nbt->setInt(self::PM_META_TAG, $item->getDamage());

			$id = ItemIds::CARVED_PUMPKIN;
		}elseif($id === ItemIds::BANNER && $playerProtocol < ProtocolInfo::PROTOCOL_130){
			if($nbt === null){
		    	$nbt = new CompoundTag();
	    	}
	    	$nbt->setInt(self::PM_ID_TAG, $item->getId());
	    	$nbt->setInt(self::PM_META_TAG, $item->getDamage());

			$id = ItemIds::INFO_UPDATE;
			$damage = 0;
		}

        if($playerProtocol >= ProtocolInfo::PROTOCOL_419){
            $palette = ItemPalette::getPalette($playerProtocol);
            $idMeta = $palette::getRuntimeFromLegacyIdQuiet($id, $damage);

			if($idMeta === null){
				//Display unmapped items as INFO_UPDATE, but stick something in their NBT to make sure they don't stack with
				//other unmapped items.
				[$id, $damage] = $palette::getRuntimeFromLegacyId(ItemIds::INFO_UPDATE, 0);
				if($nbt === null){
					$nbt = new CompoundTag();
				}
				$nbt->setInt(self::PM_ID_TAG, $item->getId());
				$nbt->setInt(self::PM_META_TAG, $item->getDamage());
			}else{
				[$id, $damage] = $idMeta;
				if($item instanceof Durable and $item->getDamage() > 0){
					if($nbt !== null){
						if(($existing = $nbt->getTag(self::DAMAGE_TAG)) !== null){
							$nbt->removeTag(self::DAMAGE_TAG);
							$existing->setName(self::DAMAGE_TAG_CONFLICT_RESOLUTION);
							$nbt->setTag($existing);
						}
					}else{
						$nbt = new CompoundTag();
					}
					$nbt->setInt(self::DAMAGE_TAG, $item->getDamage());
				}
			}
        }else{
			if($item instanceof Durable and $item->getDamage() > 0){
				if($nbt !== null){
					if(($existing = $nbt->getTag(self::DAMAGE_TAG)) !== null){
						$nbt->removeTag(self::DAMAGE_TAG);
						$existing->setName(self::DAMAGE_TAG_CONFLICT_RESOLUTION);
						$nbt->setTag($existing);
					}
				}else{
					$nbt = new CompoundTag();
				}
				$nbt->setInt(self::DAMAGE_TAG, $item->getDamage());
			}
		}

		$this->putVarInt($id);
		$auxValue = (($damage & 0x7fff) << 8) | $item->getCount();
		$this->putVarInt($auxValue);

		if($nbt !== null){
		    if($playerProtocol >= ProtocolInfo::PROTOCOL_330){
		    	($this->buffer .= (pack("v", 0xffff)));
		    	($this->buffer .= chr(1)); //TODO: some kind of count field? always 1 as of 1.9.0
		    	($this->buffer .= (new NetworkLittleEndianNBTStream())->write($nbt));
		    }else{
                if($playerProtocol < ProtocolInfo::PROTOCOL_130 && $item->getId() === ItemIds::FILLED_MAP && $item->getNamedTag()->hasTag("map_uuid", LongTag::class)){
                    $tag = $item->getNamedTag();
                    $uuid = $tag->getLong("map_uuid");
                    $nbt->setString("map_uuid", (string) $uuid, true);
                }
                $nbt = (new LittleEndianNBTStream())->write($nbt);
		    	($this->buffer .= (pack("v", strlen($nbt))));
		    	($this->buffer .= $nbt);
		    }
		}else{
			($this->buffer .= (pack("v", 0)));
		}

		if($playerProtocol >= ProtocolInfo::PROTOCOL_110){
	    	$this->putVarInt(0); //CanPlaceOn entry count (TODO)
	    	$this->putVarInt(0); //CanDestroy entry count (TODO)

	    	if($id === ItemIds::SHIELD){
		    	$this->putVarLong(0); //"blocking tick" (ffs mojang)
			}
		}
	}

	public function getItemStack(int $playerProtocol, bool $withStackId = true) : Item{
		$netId = $this->getVarInt();
		if($netId === 0){
			return ItemFactory::get(0, 0, 0);
		}

		$cnt = ((unpack("v", $this->get(2))[1]));
		$netData = $this->getUnsignedVarInt();

        $palette = ItemPalette::getPalette($playerProtocol);
		[$id, $meta] = $palette::getLegacyFromRuntimeId($netId, $netData);

		if($withStackId === true){
			$includeNetId = $this->getBool();
			if($includeNetId === true){
				$this->getVarInt();
			}
		}

		$this->getVarInt();

		$extraData = new NetworkBinaryStream($this->getString());
		return (static function() use ($extraData, $netId, $id, $meta, $cnt) : Item{
			$nbtLen = $extraData->getLShort();

			/** @var CompoundTag|null $nbt */
			$nbt = null;
			if($nbtLen === 0xffff){
				$nbtDataVersion = $extraData->getByte();
				if($nbtDataVersion !== 1){
					throw new UnexpectedValueException("Unexpected NBT data version $nbtDataVersion");
				}
				$decodedNBT = (new LittleEndianNBTStream())->read($extraData->buffer, false, $extraData->offset, 512);
				if(!($decodedNBT instanceof CompoundTag)){
					throw new UnexpectedValueException("Unexpected root tag type for itemstack");
				}
				$nbt = $decodedNBT;
			}elseif($nbtLen !== 0){
				throw new UnexpectedValueException("Unexpected fake NBT length $nbtLen");
			}

			//TODO
			$canPlaceOn = $extraData->getLInt();
			if($canPlaceOn > 128){
				throw new UnexpectedValueException("Too many canPlaceOn: $canPlaceOn");
			}
			for($i = 0; $i < $canPlaceOn; ++$i){
				$extraData->get($extraData->getLShort());
			}

			//TODO
			$canDestroy = $extraData->getLInt();
			if($canDestroy > 128){
				throw new UnexpectedValueException("Too many canDestroy: $canDestroy");
			}
			for($i = 0; $i < $canDestroy; ++$i){
				$extraData->get($extraData->getLShort());
			}

			if($id === ItemIds::SHIELD){
				$extraData->getLLong(); //"blocking tick" (ffs mojang)
			}

			if(!$extraData->feof()){
				throw new UnexpectedValueException("Unexpected trailing extradata for network item $netId");
			}

			if($nbt !== null){
				if($nbt->hasTag(self::PM_ID_TAG, IntTag::class)){
					$id = $nbt->getInt(self::PM_ID_TAG);
					$nbt->removeTag(self::PM_ID_TAG);
					if($nbt->count() === 0){
						$nbt = null;
					}
				}
				if($nbt->hasTag(self::DAMAGE_TAG, IntTag::class)){
					$meta = $nbt->getInt(self::DAMAGE_TAG);
					$nbt->removeTag(self::DAMAGE_TAG);
					if(($conflicted = $nbt->getTag(self::DAMAGE_TAG_CONFLICT_RESOLUTION)) !== null){
						$nbt->removeTag(self::DAMAGE_TAG_CONFLICT_RESOLUTION);
						$conflicted->setName(self::DAMAGE_TAG);
						$nbt->setTag($conflicted);
					}elseif($nbt->count() === 0){
						$nbt = null;
					}
				}elseif(($metaTag = $nbt->getTag(self::PM_META_TAG)) instanceof IntTag){
					//TODO HACK: This foul-smelling code ensures that we can correctly deserialize an item when the
					//client sends it back to us, because as of 1.16.220, blockitems quietly discard their metadata
					//client-side. Aside from being very annoying, this also breaks various server-side behaviours.
					$meta = $metaTag->getValue();
					$nbt->removeTag(self::PM_META_TAG);
					if($nbt->count() === 0){
						$nbt = null;
					}
				}
			}
			return ItemFactory::get($id, $meta, $cnt, $nbt);
		})();
	}

	public function putItemStack(Item $item, int $playerProtocol, bool $withStackId = true) : void{
		if($item->getId() === 0){
			$this->putVarInt(0);

			return;
		}

		$id = $item->getId();
		$coreData = $item->getDamage();

		$isBlockItem = $item->getId() < 256;

		$nbt = null;
		if($item->hasCompoundTag()){
			$nbt = clone $item->getNamedTag();
		}

		if($id === ItemIds::PUMPKIN){
		    if($nbt === null){
		    	$nbt = new CompoundTag();
	    	}
	    	$nbt->setInt(self::PM_ID_TAG, $item->getId());
	    	$nbt->setInt(self::PM_META_TAG, $item->getDamage());

			$id = ItemIds::CARVED_PUMPKIN;
		}

        $palette = ItemPalette::getPalette($playerProtocol);
		$idMeta = $palette::getRuntimeFromLegacyIdQuiet($id, $coreData);
		if($idMeta === null){
			//Display unmapped items as INFO_UPDATE, but stick something in their NBT to make sure they don't stack with
			//other unmapped items.
			[$netId, $netData] = $palette::getRuntimeFromLegacyId(ItemIds::INFO_UPDATE, 0);
			if($nbt === null){
				$nbt = new CompoundTag();
			}
			$nbt->setInt(self::PM_ID_TAG, $id);
			$nbt->setInt(self::PM_META_TAG, $coreData);
		}else{
	    	[$netId, $netData] = $idMeta;

			if($item instanceof Durable and $coreData > 0){
				if($nbt !== null){
					if(($existing = $nbt->getTag(self::DAMAGE_TAG)) !== null){
						$nbt->removeTag(self::DAMAGE_TAG);
						$existing->setName(self::DAMAGE_TAG_CONFLICT_RESOLUTION);
						$nbt->setTag($existing);
					}
				}else{
					$nbt = new CompoundTag();
				}
				$nbt->setInt(self::DAMAGE_TAG, $coreData);
			}elseif($isBlockItem && $coreData !== 0){
				//TODO HACK: This foul-smelling code ensures that we can correctly deserialize an item when the
				//client sends it back to us, because as of 1.16.220, blockitems quietly discard their metadata
				//client-side. Aside from being very annoying, this also breaks various server-side behaviours.
				if($nbt === null){
					$nbt = new CompoundTag();
				}
				$nbt->setInt(self::PM_META_TAG, $coreData);
			}
		}

		$this->putVarInt($netId);
		($this->buffer .= (pack("v", $item->getCount())));
		$this->putUnsignedVarInt($netData);

		if($withStackId === true){
			if($item->getId() === 0){
				$this->putBool(false);
			}else{
				$this->putBool(true);
				$this->putVarInt(1);
			}
		}

		$blockRuntimeId = 0;
		if($isBlockItem){
			if($id === ItemIds::CARVED_PUMPKIN){
				$block = BlockFactory::get(ItemIds::PUMPKIN);
			}else{
		    	$block = $item->getBlock();
			}

			if($block->getId() !== BlockIds::AIR){
			    $palette = BlockPalette::getPalette($playerProtocol);
			    $blockRuntimeId = $palette::toStaticRuntimeId($block->getId(), $block->getDamage());
			}
		}
		$this->putVarInt($blockRuntimeId);

		$this->putString(
		(static function() use ($nbt, $netId) : string{
			$extraData = new NetworkBinaryStream();

			if($nbt !== null){
				$extraData->putLShort(0xffff);
				$extraData->putByte(1); //TODO: NBT data version (?)
				$extraData->put((new LittleEndianNBTStream())->write($nbt));
			}else{
				$extraData->putLShort(0);
			}

			$extraData->putLInt(0); //CanPlaceOn entry count (TODO)
			$extraData->putLInt(0); //CanDestroy entry count (TODO)

			if($netId === ItemIds::SHIELD){
				$extraData->putLLong(0); //"blocking tick" (ffs mojang)
			}
			return $extraData->getBuffer();
		})());
	}

	public function getRecipeIngredient(int $playerProtocol) : Item{
		$id = $this->getVarInt();
		if($id === 0){
			return ItemFactory::get(ItemIds::AIR, 0, 0);
		}
		$meta = $this->getVarInt();
		if($playerProtocol >= ProtocolInfo::PROTOCOL_419){
            $palette = ItemPalette::getPalette($playerProtocol);
		    [$id, $meta] = $palette::getLegacyFromRuntimeId($id, $meta);
		}elseif($meta === 0x7fff){
		    $meta = -1;
		}
		$count = $this->getVarInt();
		return ItemFactory::get($id, $meta, $count);
	}

	public function putRecipeIngredient(Item $item, int $playerProtocol) : void{
	    if($playerProtocol < ProtocolInfo::PROTOCOL_553){
	    	if($item->isNull()){
		    	$this->putVarInt(0);
	    	}else{
		        $id = $item->getId();
		        $damage = $item->getDamage();
		        if($playerProtocol >= ProtocolInfo::PROTOCOL_419){
		            $palette = ItemPalette::getPalette($playerProtocol);
		            if($item->hasAnyDamageValue()){
		                [$id, ] = $palette::getRuntimeFromLegacyId($id, 0);
		                $damage = 0x7fff;
		            }else{
		                [$id, $damage] = $palette::getRuntimeFromLegacyId($id, $damage);
		            }
		        }else{
		            $damage = $damage & 0x7fff;
		        }
		    	$this->putVarInt($id);
		    	$this->putVarInt($damage);
		    	$this->putVarInt($item->getCount());
	    	}
		}else{
			if($item->isNull()){
				$this->putByte(0); // internal item descriptor type
			    $this->putVarInt(0);
			}else{
				$this->putByte(1); // internal item descriptor type

		        $palette = ItemPalette::getPalette($playerProtocol);
		        if($item->hasAnyDamageValue()){
		            [$netId, ] = $palette::getRuntimeFromLegacyId($item->getId(), 0);
		            $netData = 0x7fff;
		        }else{
		            [$netId, $netData] = $palette::getRuntimeFromLegacyId($item->getId(), $item->getDamage());
		        }

			    $this->putLShort($netId);
				$this->putLShort($netData);
				$this->putVarInt($item->getCount());
			}
		}
	}

	/**
	 * Decodes entity metadata from the stream.
	 *
	 * @param bool $types Whether to include metadata types along with values in the returned array
	 *
	 * @return array
	 */
	public function getEntityMetadata(int $playerProtocol, bool $types = true) : array{
		$count = $this->getUnsignedVarInt();
		if($count > 128){
			throw new UnexpectedValueException("Too many actor metadata: $count");
		}
		$data = [];
		for($i = 0; $i < $count; ++$i){
			$key = $this->getUnsignedVarInt();
			$type = $this->getUnsignedVarInt();
			$value = null;
			switch($type){
				case Entity::DATA_TYPE_BYTE:
					$value = (ord($this->get(1)));
					break;
				case Entity::DATA_TYPE_SHORT:
					$value = ((unpack("v", $this->get(2))[1] << 48 >> 48));
					break;
				case Entity::DATA_TYPE_INT:
					$value = $this->getVarInt();
					break;
				case Entity::DATA_TYPE_FLOAT:
					$value = ((unpack("g", $this->get(4))[1]));
					break;
				case Entity::DATA_TYPE_STRING:
					$value = $this->getString();
					break;
				case Entity::DATA_TYPE_SLOT:
				    if($playerProtocol >= ProtocolInfo::PROTOCOL_361){
				        $value = (new NetworkLittleEndianNBTStream())->read($this->buffer, false, $this->offset, 512);
				    }else{
				        $value = $this->getSlot($playerProtocol);
				    }
					break;
				case Entity::DATA_TYPE_POS:
					$value = new Vector3();
					$this->getSignedBlockPosition($value->x, $value->y, $value->z);
					break;
				case Entity::DATA_TYPE_LONG:
					$value = $this->getVarLong();
					break;
				case Entity::DATA_TYPE_VECTOR3F:
					$value = $this->getVector3();
					break;
				default:
					throw new UnexpectedValueException("Invalid data type " . $type);
			}
			if($types){
				$data[$key] = [$type, $value];
			}else{
				$data[$key] = $value;
			}
		}

		return MetadataConvertor::rollbackMeta($data, $playerProtocol);
	}

	/**
	 * Writes entity metadata to the packet buffer.
	 *
	 * @param array $metadata
	 */
	public function putEntityMetadata(array $metadata, int $playerProtocol) : void{
	    $metadata = MetadataConvertor::updateMeta($metadata, $playerProtocol);

		$this->putUnsignedVarInt(count($metadata));
		foreach($metadata as $key => $d){
			$this->putUnsignedVarInt($key); //data key
			$this->putUnsignedVarInt($d[0]); //data type
			switch($d[0]){
				case Entity::DATA_TYPE_BYTE:
					($this->buffer .= chr($d[1]));
					break;
				case Entity::DATA_TYPE_SHORT:
					($this->buffer .= (pack("v", $d[1]))); //SIGNED short!
					break;
				case Entity::DATA_TYPE_INT:
					$this->putVarInt($d[1]);
					break;
				case Entity::DATA_TYPE_FLOAT:
					($this->buffer .= (pack("g", $d[1])));
					break;
				case Entity::DATA_TYPE_STRING:
					$this->putString($d[1]);
					break;
				case Entity::DATA_TYPE_SLOT:
				    if($playerProtocol >= ProtocolInfo::PROTOCOL_361){
				        ($this->buffer .= (new NetworkLittleEndianNBTStream())->write($d[1]->getNamedTag()));
				    }else{
				        $this->putSlot($d[1], $playerProtocol);
				    }
					break;
				case Entity::DATA_TYPE_POS:
					$v = $d[1];
					if($v !== null){
						$this->putSignedBlockPosition($v->x, $v->y, $v->z);
					}else{
						$this->putSignedBlockPosition(0, 0, 0);
					}
					break;
				case Entity::DATA_TYPE_LONG:
					$this->putVarLong($d[1]);
					break;
				case Entity::DATA_TYPE_VECTOR3F:
					$this->putVector3Nullable($d[1]);
					break;
				default:
					throw new UnexpectedValueException("Invalid data type " . $d[0]);
			}
		}
	}

	/**
	 * Reads a list of Attributes from the stream.
	 * @return Attribute[]
	 *
	 * @throws UnexpectedValueException if reading an attribute with an unrecognized name
	 */
	public function getAttributeList(int $playerProtocol) : array{
		$list = [];
		$count = $this->getUnsignedVarInt();

		for($i = 0; $i < $count; ++$i){
			$min = ((unpack("g", $this->get(4))[1]));
			$max = ((unpack("g", $this->get(4))[1]));
			$current = ((unpack("g", $this->get(4))[1]));
			$default = ((unpack("g", $this->get(4))[1]));
			$name = $this->getString();
			$modifiers = [];
			if($playerProtocol >= ProtocolInfo::PROTOCOL_544){
		    	for($j = 0, $modifierCount = $this->getUnsignedVarInt(); $j < $modifierCount; $j++){
			    	$modifiers[] = AttributeModifier::read($this);
		    	}
			}

			$attr = Attribute::getAttributeByName($name);
			if($attr !== null){
				$attr->setMinValue($min);
				$attr->setMaxValue($max);
				$attr->setValue($current);
				$attr->setDefaultValue($default);
				$attr->setModifiers($modifiers);

				$list[] = $attr;
			}else{
				throw new UnexpectedValueException("Unknown attribute type \"$name\"");
			}
		}

		return $list;
	}

	/**
	 * Writes a list of Attributes to the packet buffer using the standard format.
	 *
	 * @param Attribute ...$attributes
	 */
	public function putAttributeList(int $playerProtocol, Attribute ...$attributes) : void{
		$this->putUnsignedVarInt(count($attributes));
		foreach($attributes as $attribute){
			($this->buffer .= (pack("g", $attribute->getMinValue())));
			($this->buffer .= (pack("g", $attribute->getMaxValue())));
			($this->buffer .= (pack("g", $attribute->getValue())));
			($this->buffer .= (pack("g", $attribute->getDefaultValue())));
			$this->putString($attribute->getName());
			if($playerProtocol >= ProtocolInfo::PROTOCOL_544){
		    	$this->putUnsignedVarInt(count($attribute->getModifiers()));
		    	foreach($attribute->getModifiers() as $modifier){
		    	    $modifier->write($this);
		    	}
			}
		}
	}

	/**
	 * Reads and returns an EntityUniqueID
	 * @return int
	 */
	final public function getEntityUniqueId() : int{
		return $this->getVarLong();
	}

	/**
	 * Writes an EntityUniqueID
	 *
	 * @param int $eid
	 */
	public function putEntityUniqueId(int $eid) : void{
		$this->putVarLong($eid);
	}

	/**
	 * Reads and returns an EntityRuntimeID
	 * @return int
	 */
	final public function getEntityRuntimeId() : int{
		return $this->getUnsignedVarLong();
	}

	/**
	 * Writes an EntityRuntimeID
	 *
	 * @param int $eid
	 */
	public function putEntityRuntimeId(int $eid) : void{
		$this->putUnsignedVarLong($eid);
	}

	/**
	 * Reads an block position with unsigned Y coordinate.
	 *
	 * @param int &$x
	 * @param int &$y
	 * @param int &$z
	 */
	public function getBlockPosition(&$x, &$y, &$z) : void{
		$x = $this->getVarInt();
		$y = $this->getUnsignedVarInt();
		$z = $this->getVarInt();
	}

	/**
	 * Writes a block position with unsigned Y coordinate.
	 *
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 */
	public function putBlockPosition(int $x, int $y, int $z) : void{
		$this->putVarInt($x);
		$this->putUnsignedVarInt($y);
		$this->putVarInt($z);
	}

	/**
	 * Reads a block position with a signed Y coordinate.
	 *
	 * @param int &$x
	 * @param int &$y
	 * @param int &$z
	 */
	public function getSignedBlockPosition(&$x, &$y, &$z) : void{
		$x = $this->getVarInt();
		$y = $this->getVarInt();
		$z = $this->getVarInt();
	}

	/**
	 * Writes a block position with a signed Y coordinate.
	 *
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 */
	public function putSignedBlockPosition(int $x, int $y, int $z) : void{
		$this->putVarInt($x);
		$this->putVarInt($y);
		$this->putVarInt($z);
	}

	/**
	 * Reads a floating-point Vector3 object with coordinates rounded to 4 decimal places.
	 *
	 * @return Vector3
	 */
	public function getVector3() : Vector3{
		return new Vector3(
			(((unpack("g", $this->get(4))[1]))),
			(((unpack("g", $this->get(4))[1]))),
			(((unpack("g", $this->get(4))[1])))
		);
	}

	/**
	 * Writes a floating-point Vector3 object, or 3x zero if null is given.
	 *
	 * Note: ONLY use this where it is reasonable to allow not specifying the vector.
	 * For all other purposes, use the non-nullable version.
	 *
	 * @see NetworkBinaryStream::putVector3()
	 *
	 * @param Vector3|null $vector
	 */
	public function putVector3Nullable(?Vector3 $vector) : void{
		if($vector){
			$this->putVector3($vector);
		}else{
			($this->buffer .= (pack("g", 0.0)));
			($this->buffer .= (pack("g", 0.0)));
			($this->buffer .= (pack("g", 0.0)));
		}
	}

	/**
	 * Writes a floating-point Vector3 object
	 *
	 * @param Vector3 $vector
	 */
	public function putVector3(Vector3 $vector) : void{
		($this->buffer .= (pack("g", $vector->x)));
		($this->buffer .= (pack("g", $vector->y)));
		($this->buffer .= (pack("g", $vector->z)));
	}

	public function getByteRotation() : float{
		return (float) ((ord($this->get(1))) * (360 / 256));
	}

	public function putByteRotation(float $rotation) : void{
		($this->buffer .= chr((int) ($rotation / (360 / 256))));
	}

	/**
	 * Reads gamerules
	 * TODO: implement this properly
	 *
	 * @return array, members are in the structure [name => [type, value, isPlayerModifiable]]
	 */
	public function getGameRules(int $playerProtocol) : array{
		$count = $this->getUnsignedVarInt();
		$rules = [];
		for($i = 0; $i < $count; ++$i){
			$name = $this->getString();
			if($playerProtocol >= ProtocolInfo::PROTOCOL_440){
		    	$isPlayerModifiable = $this->getBool();
			}
			$type = $this->getUnsignedVarInt();
			$value = null;
			switch($type){
				case GameRuleType::BOOL:
					$value = (($this->get(1) !== "\x00"));
					break;
				case GameRuleType::INT:
					$value = $this->getUnsignedVarInt();
					break;
				case GameRuleType::FLOAT:
					$value = ((unpack("g", $this->get(4))[1]));
					break;
			}

			$rules[$name] = [$type, $value, $isPlayerModifiable ?? false];
		}

		return $rules;
	}

	/**
	 * Writes a gamerule array, members should be in the structure [name => [type, value, isPlayerModifiable]]
	 * TODO: implement this properly
	 *
	 * @param array $rules
	 */
	public function putGameRules(array $rules, int $playerProtocol) : void{
		$this->putUnsignedVarInt(count($rules));
		foreach($rules as $name => $rule){
			$this->putString($name);
			if($playerProtocol >= ProtocolInfo::PROTOCOL_440){
			    $this->putBool($rule[2]);
			}
	    	$this->putUnsignedVarInt($rule[0]);
			switch($rule[0]){
				case GameRuleType::BOOL:
					($this->buffer .= ($rule[1] ? "\x01" : "\x00"));
					break;
				case GameRuleType::INT:
					$this->putUnsignedVarInt($rule[1]);
					break;
				case GameRuleType::FLOAT:
					($this->buffer .= (pack("g", $rule[1])));
					break;
			}
		}
	}

	/**
	 * @param int $playerProtocol
	 * 
	 * @return EntityLink
	 */
	protected function getEntityLink(int $playerProtocol) : EntityLink{
		$link = new EntityLink();

		$link->fromEntityUniqueId = $this->getEntityUniqueId();
		$link->toEntityUniqueId = $this->getEntityUniqueId();
		$link->type = (ord($this->get(1)));
		if($playerProtocol >= ProtocolInfo::PROTOCOL_130){
	    	$link->immediate = (($this->get(1) !== "\x00"));
	    	if($playerProtocol >= ProtocolInfo::PROTOCOL_406){
		        $link->causedByRider = (($this->get(1) !== "\x00"));
	    	}
		}

		return $link;
	}

	/**
	 * @param EntityLink $link
	 * @param int $playerProtocol
	 */
	protected function putEntityLink(EntityLink $link, int $playerProtocol) : void{
		$this->putEntityUniqueId($link->fromEntityUniqueId);
		$this->putEntityUniqueId($link->toEntityUniqueId);
		($this->buffer .= chr($link->type));
		if($playerProtocol >= ProtocolInfo::PROTOCOL_130){
	    	($this->buffer .= ($link->immediate ? "\x01" : "\x00"));
	    	if($playerProtocol >= ProtocolInfo::PROTOCOL_406){
		        ($this->buffer .= ($link->causedByRider ? "\x01" : "\x00"));
	    	}
		}
	}

	protected function getCommandOriginData() : CommandOriginData{
		$result = new CommandOriginData();

		$result->type = $this->getUnsignedVarInt();
		$result->uuid = $this->getUUID();
		$result->requestId = $this->getString();

		if($result->type === CommandOriginData::ORIGIN_DEV_CONSOLE or $result->type === CommandOriginData::ORIGIN_TEST){
			$result->varlong1 = $this->getVarLong();
		}

		return $result;
	}

	protected function putCommandOriginData(CommandOriginData $data) : void{
		$this->putUnsignedVarInt($data->type);
		$this->putUUID($data->uuid);
		$this->putString($data->requestId);

		if($data->type === CommandOriginData::ORIGIN_DEV_CONSOLE or $data->type === CommandOriginData::ORIGIN_TEST){
			$this->putVarLong($data->varlong1);
		}
	}

	protected function getStructureSettings() : StructureSettings{
		$result = new StructureSettings();

		$result->paletteName = $this->getString();

		$result->ignoreEntities = (($this->get(1) !== "\x00"));
		$result->ignoreBlocks = (($this->get(1) !== "\x00"));

		$this->getBlockPosition($result->structureSizeX, $result->structureSizeY, $result->structureSizeZ);
		$this->getBlockPosition($result->structureOffsetX, $result->structureOffsetY, $result->structureOffsetZ);

		$result->lastTouchedByPlayerID = $this->getEntityUniqueId();
		$result->rotation = (ord($this->get(1)));
		$result->mirror = (ord($this->get(1)));
		$result->integrityValue = ((unpack("G", $this->get(4))[1]));
		$result->integritySeed = ((unpack("N", $this->get(4))[1] << 32 >> 32));

		return $result;
	}

	protected function putStructureSettings(StructureSettings $structureSettings) : void{
		$this->putString($structureSettings->paletteName);

		($this->buffer .= ($structureSettings->ignoreEntities ? "\x01" : "\x00"));
		($this->buffer .= ($structureSettings->ignoreBlocks ? "\x01" : "\x00"));

		$this->putBlockPosition($structureSettings->structureSizeX, $structureSettings->structureSizeY, $structureSettings->structureSizeZ);
		$this->putBlockPosition($structureSettings->structureOffsetX, $structureSettings->structureOffsetY, $structureSettings->structureOffsetZ);

		$this->putEntityUniqueId($structureSettings->lastTouchedByPlayerID);
		($this->buffer .= chr($structureSettings->rotation));
		($this->buffer .= chr($structureSettings->mirror));
		($this->buffer .= (pack("G", $structureSettings->integrityValue)));
		($this->buffer .= (pack("N", $structureSettings->integritySeed)));
	}

	protected function getStructureEditorData() : StructureEditorData{
		$result = new StructureEditorData();

		$result->structureName = $this->getString();
		$result->structureDataField = $this->getString();

		$result->includePlayers = (($this->get(1) !== "\x00"));
		$result->showBoundingBox = (($this->get(1) !== "\x00"));

		$result->structureBlockType = $this->getVarInt();
		$result->structureSettings = $this->getStructureSettings();
		$result->structureRedstoneSaveMove = $this->getVarInt();

		return $result;
	}

	protected function putStructureEditorData(StructureEditorData $structureEditorData) : void{
		$this->putString($structureEditorData->structureName);
		$this->putString($structureEditorData->structureDataField);

		($this->buffer .= ($structureEditorData->includePlayers ? "\x01" : "\x00"));
		($this->buffer .= ($structureEditorData->showBoundingBox ? "\x01" : "\x00"));

		$this->putVarInt($structureEditorData->structureBlockType);
		$this->putStructureSettings($structureEditorData->structureSettings);
		$this->putVarInt($structureEditorData->structureRedstoneSaveMove);
	}

	public function getNbtRoot() : NamedTag{
		$offset = $this->getOffset();
		try{
			$result = (new NetworkLittleEndianNBTStream())->read($this->getBuffer(), false, $offset, 512);
			assert($result instanceof NamedTag, "doMultiple is false so we should definitely have a NamedTag here");
			return $result;
		}finally{
			$this->setOffset($offset);
		}
	}

	public function getNbtCompoundRoot() : CompoundTag{
		$root = $this->getNbtRoot();
		if(!($root instanceof CompoundTag)){
			throw new UnexpectedValueException("Expected TAG_Compound root");
		}
		return $root;
	}

	public function readGenericTypeNetworkId() : int{
		return $this->getVarInt();
	}

	public function writeGenericTypeNetworkId(int $id) : void{
		$this->putVarInt($id);
	}

	protected function prepareGeometryDataForOld(?string $skinGeometryData) : ?string{
		if(!empty($skinGeometryData)){
			if(($tempData = @json_decode($skinGeometryData, true))){
				unset($tempData["format_version"]);
				return json_encode($tempData);
			}
		}

		return $skinGeometryData;
	}
}
