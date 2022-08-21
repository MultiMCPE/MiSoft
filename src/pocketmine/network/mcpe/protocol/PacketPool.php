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

class PacketPool {
	/** @var \SplFixedArray<DataPacket> */
	protected static $packetPool110 = null;
	protected static $packetPool331 = null;

	public static function init(){
	    static::registerPackets110();
	    static::registerPackets331();
	}

	/**
	 * @param int        $id 0-255
	 * @param DataPacket $class
	 */
	public static function registerPacket110($id, $class){
		static::$packetPool110[$id] = new $class;
	}

	/**
	 * @param int        $id 0-255
	 * @param DataPacket $class
	 */
	public static function registerPacket331($id, $class){
		static::$packetPool331[$id] = new $class;
	}

    /**
     * @return void
     */
	private static function registerPackets110() {
		static::$packetPool110 = new \SplFixedArray(256);
		static::registerPacket110(ProtocolInfo110::CHANGE_DIMENSION_PACKET, ChangeDimensionPacket::class);
		static::registerPacket110(ProtocolInfo110::CLIENTBOUND_MAP_ITEM_DATA_PACKET, ClientboundMapItemDataPacket::class);
		static::registerPacket110(ProtocolInfo110::LOGIN_PACKET, LoginPacket::class);
		static::registerPacket110(ProtocolInfo110::PLAY_STATUS_PACKET, PlayStatusPacket::class);
		static::registerPacket110(ProtocolInfo110::DISCONNECT_PACKET, DisconnectPacket::class);
		static::registerPacket110(ProtocolInfo110::TEXT_PACKET, TextPacket::class);
		static::registerPacket110(ProtocolInfo110::SET_TIME_PACKET, SetTimePacket::class);
		static::registerPacket110(ProtocolInfo110::START_GAME_PACKET, StartGamePacket::class);
		static::registerPacket110(ProtocolInfo110::ADD_PLAYER_PACKET, AddPlayerPacket::class);
		static::registerPacket110(ProtocolInfo110::ADD_ACTOR_PACKET, AddActorPacket::class);
		static::registerPacket110(ProtocolInfo110::REMOVE_ACTOR_PACKET, RemoveActorPacket::class);
		static::registerPacket110(ProtocolInfo110::ADD_ITEM_ACTOR_PACKET, AddItemActorPacket::class);
		static::registerPacket110(ProtocolInfo110::TAKE_ITEM_ACTOR_PACKET, TakeItemActorPacket::class);
		static::registerPacket110(ProtocolInfo110::MOVE_ACTOR_ABSOLUTE_PACKET, MoveActorAbsolutePacket::class);
		static::registerPacket110(ProtocolInfo110::MOVE_PLAYER_PACKET, MovePlayerPacket::class);
		static::registerPacket110(ProtocolInfo110::UPDATE_BLOCK_PACKET, UpdateBlockPacket::class);
		static::registerPacket110(ProtocolInfo110::ADD_PAINTING_PACKET, AddPaintingPacket::class);
		static::registerPacket110(ProtocolInfo110::EXPLODE_PACKET, ExplodePacket::class);
		static::registerPacket110(ProtocolInfo110::LEVEL_EVENT_PACKET, LevelEventPacket::class);
		static::registerPacket110(ProtocolInfo110::LEVEL_SOUND_EVENT_PACKET, LevelSoundEventPacket::class);
		static::registerPacket110(ProtocolInfo110::BLOCK_EVENT_PACKET, BlockEventPacket::class);
		static::registerPacket110(ProtocolInfo110::ACTOR_EVENT_PACKET, ActorEventPacket::class);
		static::registerPacket110(ProtocolInfo110::MOB_EQUIPMENT_PACKET, MobEquipmentPacket::class);
		static::registerPacket110(ProtocolInfo110::MOB_ARMOR_EQUIPMENT_PACKET, MobArmorEquipmentPacket::class);
		static::registerPacket110(ProtocolInfo110::INTERACT_PACKET, InteractPacket::class);
		static::registerPacket110(ProtocolInfo110::PLAYER_ACTION_PACKET, PlayerActionPacket::class);
		static::registerPacket110(ProtocolInfo110::SET_ACTOR_DATA_PACKET, SetActorDataPacket::class);
		static::registerPacket110(ProtocolInfo110::SET_ACTOR_MOTION_PACKET, SetActorMotionPacket::class);
		static::registerPacket110(ProtocolInfo110::SET_ACTOR_LINK_PACKET, SetActorLinkPacket::class);
		static::registerPacket110(ProtocolInfo110::SET_SPAWN_POSITION_PACKET, SetSpawnPositionPacket::class);
		static::registerPacket110(ProtocolInfo110::ANIMATE_PACKET, AnimatePacket::class);
		static::registerPacket110(ProtocolInfo110::RESPAWN_PACKET, RespawnPacket::class);
		static::registerPacket110(ProtocolInfo110::CONTAINER_OPEN_PACKET, ContainerOpenPacket::class);
		static::registerPacket110(ProtocolInfo110::CONTAINER_CLOSE_PACKET, ContainerClosePacket::class);
		static::registerPacket110(ProtocolInfo110::CONTAINER_SET_DATA_PACKET, ContainerSetDataPacket::class);
		static::registerPacket110(ProtocolInfo110::CRAFTING_DATA_PACKET, CraftingDataPacket::class);
		static::registerPacket110(ProtocolInfo110::CRAFTING_EVENT_PACKET, CraftingEventPacket::class);
		static::registerPacket110(ProtocolInfo110::ADVENTURE_SETTINGS_PACKET, AdventureSettingsPacket::class);
		static::registerPacket110(ProtocolInfo110::BLOCK_ACTOR_DATA_PACKET, BlockActorDataPacket::class);
		static::registerPacket110(ProtocolInfo110::LEVEL_CHUNK_PACKET, LevelChunkPacket::class);
		static::registerPacket110(ProtocolInfo110::SET_COMMANDS_ENABLED_PACKET, SetCommandsEnabledPacket::class);
		static::registerPacket110(ProtocolInfo110::SET_DIFFICULTY_PACKET, SetDifficultyPacket::class);
		static::registerPacket110(ProtocolInfo110::PLAYER_LIST_PACKET, PlayerListPacket::class);
		static::registerPacket110(ProtocolInfo110::REQUEST_CHUNK_RADIUS_PACKET, RequestChunkRadiusPacket::class);
		static::registerPacket110(ProtocolInfo110::CHUNK_RADIUS_UPDATED_PACKET, ChunkRadiusUpdatedPacket::class);
		static::registerPacket110(ProtocolInfo110::AVAILABLE_COMMANDS_PACKET, AvailableCommandsPacket::class);
		static::registerPacket110(ProtocolInfo110::TRANSFER_PACKET, TransferPacket::class);
		static::registerPacket110(ProtocolInfo110::CLIENT_TO_SERVER_HANDSHAKE_PACKET, ClientToServerHandshakePacket::class);
		static::registerPacket110(ProtocolInfo110::RESOURCE_PACK_DATA_INFO_PACKET, ResourcePackDataInfoPacket::class);
		static::registerPacket110(ProtocolInfo110::RESOURCE_PACKS_INFO_PACKET, ResourcePacksInfoPacket::class);
		static::registerPacket110(ProtocolInfo110::RESOURCE_PACK_CLIENT_RESPONSE_PACKET, ResourcePackClientResponsePacket::class);
		static::registerPacket110(ProtocolInfo110::RESOURCE_PACK_CHUNK_REQUEST_PACKET, ResourcePackChunkRequestPacket::class);
		static::registerPacket110(ProtocolInfo110::PLAYER_INPUT_PACKET, PlayerInputPacket::class);
    	static::registerPacket110(ProtocolInfo110::PLAY_SOUND_PACKET, PlaySoundPacket::class);
    	static::registerPacket110(ProtocolInfo110::ITEM_FRAME_DROP_ITEM_PACKET, ItemFrameDropItemPacket::class);
	    static::registerPacket110(ProtocolInfo110::BOSS_EVENT_PACKET, BossEventPacket::class);
		static::registerPacket110(ProtocolInfo110::MAP_INFO_REQUEST_PACKET, MapInfoRequestPacket::class);
		static::registerPacket110(ProtocolInfo110::SPAWN_EXPERIENCE_ORB_PACKET, SpawnExperienceOrbPacket::class);
		static::registerPacket110(0xfe, BatchPacket::class);
	}

    /**
     * @return void
     */
	private static function registerPackets331() {
		static::$packetPool331 = new \SplFixedArray(256);
		static::registerPacket331(ProtocolInfo331::SPAWN_EXPERIENCE_ORB_PACKET, SpawnExperienceOrbPacket::class);
		static::registerPacket331(ProtocolInfo331::CHANGE_DIMENSION_PACKET, ChangeDimensionPacket::class);
		static::registerPacket331(ProtocolInfo331::CLIENTBOUND_MAP_ITEM_DATA_PACKET, ClientboundMapItemDataPacket::class);
		static::registerPacket331(ProtocolInfo331::PLAY_STATUS_PACKET, PlayStatusPacket::class);
		static::registerPacket331(ProtocolInfo331::DISCONNECT_PACKET, DisconnectPacket::class);
		static::registerPacket331(ProtocolInfo331::TEXT_PACKET, TextPacket::class);
		static::registerPacket331(ProtocolInfo331::SET_TIME_PACKET, SetTimePacket::class);
		static::registerPacket331(ProtocolInfo331::START_GAME_PACKET, StartGamePacket::class);
		static::registerPacket331(ProtocolInfo331::ADD_PLAYER_PACKET, AddPlayerPacket::class);
		static::registerPacket331(ProtocolInfo331::ADD_ACTOR_PACKET, AddActorPacket::class);
		static::registerPacket331(ProtocolInfo331::REMOVE_ACTOR_PACKET, RemoveActorPacket::class);
		static::registerPacket331(ProtocolInfo331::ADD_ITEM_ACTOR_PACKET, AddItemActorPacket::class);
		static::registerPacket331(ProtocolInfo331::TAKE_ITEM_ACTOR_PACKET, TakeItemActorPacket::class);
		static::registerPacket331(ProtocolInfo331::MOVE_ACTOR_ABSOLUTE_PACKET, MoveActorAbsolutePacket::class);
		static::registerPacket331(ProtocolInfo331::MOVE_PLAYER_PACKET, MovePlayerPacket::class);
		static::registerPacket331(ProtocolInfo331::UPDATE_BLOCK_PACKET, UpdateBlockPacket::class);
		static::registerPacket331(ProtocolInfo331::ADD_PAINTING_PACKET, AddPaintingPacket::class);
		static::registerPacket331(ProtocolInfo331::EXPLODE_PACKET, ExplodePacket::class);
		static::registerPacket331(ProtocolInfo331::LEVEL_EVENT_PACKET, LevelEventPacket::class);
		static::registerPacket331(ProtocolInfo331::BLOCK_EVENT_PACKET, BlockEventPacket::class);
		static::registerPacket331(ProtocolInfo331::ACTOR_EVENT_PACKET, ActorEventPacket::class);
		static::registerPacket331(ProtocolInfo331::MOB_EQUIPMENT_PACKET, MobEquipmentPacket::class);
		static::registerPacket331(ProtocolInfo331::MOB_ARMOR_EQUIPMENT_PACKET, MobArmorEquipmentPacket::class);
		static::registerPacket331(ProtocolInfo331::INTERACT_PACKET, InteractPacket::class);
		static::registerPacket331(ProtocolInfo331::PLAYER_ACTION_PACKET, PlayerActionPacket::class);
		static::registerPacket331(ProtocolInfo331::SET_ACTOR_DATA_PACKET, SetActorDataPacket::class);
		static::registerPacket331(ProtocolInfo331::SET_ACTOR_MOTION_PACKET, SetActorMotionPacket::class);
		static::registerPacket331(ProtocolInfo331::SET_ACTOR_LINK_PACKET, SetActorLinkPacket::class);
		static::registerPacket331(ProtocolInfo331::SET_SPAWN_POSITION_PACKET, SetSpawnPositionPacket::class);
		static::registerPacket331(ProtocolInfo331::ANIMATE_PACKET, AnimatePacket::class);
		static::registerPacket331(ProtocolInfo331::RESPAWN_PACKET, RespawnPacket::class);
		static::registerPacket331(ProtocolInfo331::CONTAINER_OPEN_PACKET, ContainerOpenPacket::class);
		static::registerPacket331(ProtocolInfo331::CONTAINER_CLOSE_PACKET, ContainerClosePacket::class);
		static::registerPacket331(ProtocolInfo331::CONTAINER_SET_DATA_PACKET, ContainerSetDataPacket::class);
		static::registerPacket331(ProtocolInfo331::CRAFTING_DATA_PACKET, CraftingDataPacket::class);
		static::registerPacket331(ProtocolInfo331::CRAFTING_EVENT_PACKET, CraftingEventPacket::class);
		static::registerPacket331(ProtocolInfo331::ADVENTURE_SETTINGS_PACKET, AdventureSettingsPacket::class);
		static::registerPacket331(ProtocolInfo331::BLOCK_ACTOR_DATA_PACKET, BlockActorDataPacket::class);
		static::registerPacket331(ProtocolInfo331::LEVEL_CHUNK_PACKET, LevelChunkPacket::class);
		static::registerPacket331(ProtocolInfo331::SET_COMMANDS_ENABLED_PACKET, SetCommandsEnabledPacket::class);
		static::registerPacket331(ProtocolInfo331::SET_DIFFICULTY_PACKET, SetDifficultyPacket::class);
		static::registerPacket331(ProtocolInfo331::PLAYER_LIST_PACKET, PlayerListPacket::class);
		static::registerPacket331(ProtocolInfo331::REQUEST_CHUNK_RADIUS_PACKET, RequestChunkRadiusPacket::class);
		static::registerPacket331(ProtocolInfo331::CHUNK_RADIUS_UPDATED_PACKET, ChunkRadiusUpdatedPacket::class);
		static::registerPacket331(ProtocolInfo331::AVAILABLE_COMMANDS_PACKET, AvailableCommandsPacket::class);
		static::registerPacket331(ProtocolInfo331::TRANSFER_PACKET, TransferPacket::class);
		static::registerPacket331(ProtocolInfo331::CLIENT_TO_SERVER_HANDSHAKE_PACKET, ClientToServerHandshakePacket::class);
		static::registerPacket331(ProtocolInfo331::RESOURCE_PACK_DATA_INFO_PACKET, ResourcePackDataInfoPacket::class);
		static::registerPacket331(ProtocolInfo331::RESOURCE_PACKS_INFO_PACKET, ResourcePackDataInfoPacket::class);
		static::registerPacket331(ProtocolInfo331::RESOURCE_PACK_CLIENT_RESPONSE_PACKET, ResourcePackClientResponsePacket::class);
		static::registerPacket331(ProtocolInfo331::RESOURCE_PACK_CHUNK_REQUEST_PACKET, ResourcePackChunkRequestPacket::class);
		static::registerPacket331(ProtocolInfo331::PLAYER_INPUT_PACKET, PlayerInputPacket::class);
		static::registerPacket331(ProtocolInfo331::MAP_INFO_REQUEST_PACKET, MapInfoRequestPacket::class);
		static::registerPacket331(ProtocolInfo331::INVENTORY_TRANSACTION_PACKET, InventoryTransactionPacket::class);
		static::registerPacket331(ProtocolInfo331::INVENTORY_CONTENT_PACKET, InventoryContentPacket::class);
		static::registerPacket331(ProtocolInfo331::INVENTORY_SLOT_PACKET, InventorySlotPacket::class);
		static::registerPacket331(ProtocolInfo331::PLAYER_HOTBAR_PACKET, PlayerHotbarPacket::class);
		static::registerPacket331(ProtocolInfo331::COMMAND_REQUEST_PACKET, CommandRequestPacket::class);
		static::registerPacket331(ProtocolInfo331::PLAYER_HOTBAR_PACKET, PlayerHotbarPacket::class);
		static::registerPacket331(ProtocolInfo331::PLAYER_SKIN_PACKET, PlayerSkinPacket::class);
		static::registerPacket331(ProtocolInfo331::MODAL_FORM_RESPONSE_PACKET, ModalFormResponsePacket::class);
		static::registerPacket331(ProtocolInfo331::SERVER_SETTINGS_REQUEST_PACKET, ServerSettingsRequestPacket::class);
		static::registerPacket331(ProtocolInfo331::PURCHASE_RECEIPT_PACKET, PurchaseReceiptPacket::class);
		static::registerPacket331(ProtocolInfo331::SUB_CLIENT_LOGIN_PACKET, SubClientLoginPacket::class);
		static::registerPacket331(ProtocolInfo331::AVAILABLE_ACTOR_IDENTIFIERS_PACKET, AvailableActorIdentifiersPacket::class);
		static::registerPacket331(ProtocolInfo331::LEVEL_SOUND_EVENT_PACKET, LevelSoundEventPacket::class);
		static::registerPacket331(ProtocolInfo331::NETWORK_CHUNK_PUBLISHER_UPDATE_PACKET, NetworkChunkPublisherUpdatePacket::class);
		static::registerPacket331(ProtocolInfo331::SPAWN_PARTICLE_EFFECT_PACKET, SpawnParticleEffectPacket::class);
    	static::registerPacket331(ProtocolInfo331::PLAY_SOUND_PACKET, PlaySoundPacket::class);
    	static::registerPacket331(ProtocolInfo331::ITEM_FRAME_DROP_ITEM_PACKET, ItemFrameDropItemPacket::class);
    	static::registerPacket331(ProtocolInfo331::BOSS_EVENT_PACKET, BossEventPacket::class);
		static::registerPacket331(ProtocolInfo331::SET_LOCAL_PLAYER_AS_INITIALIZED_PACKET, SetLocalPlayerAsInitializedPacket::class);
		static::registerPacket331(0xfe, BatchPacket::class);
	}

	/**
	 * @param int $pid
	 * @param int $playerProtocol
	 *
	 * @return ?DataPacket
	 */
	public static function getPacketById(int $pid, int $playerProtocol) : ?DataPacket{
		/** @var DataPacket $class */
		$class = null;
		switch ($playerProtocol) {
			case ProtocolInfo::PROTOCOL_361:
			case ProtocolInfo::PROTOCOL_360:
				$class = static::$packetPool331[$pid];
				break;
			default:
				$class = static::$packetPool110[$pid];
				break;
		}

		return $class;
	}

	/**
	 * @param string $buffer
	 * @param int $playerProtocol
	 * 
	 * @return ?DataPacket
	 */
	public static function getPacket(string $buffer, int $playerProtocol) : ?DataPacket{
		$offset = 0;

		$pk = static::getPacketById(Binary::readUnsignedVarInt($buffer, $offset), $playerProtocol);
		if($pk === null){
		    return null;
		}

		$pk->setBuffer($buffer, $offset);

		return $pk;
	}

}
