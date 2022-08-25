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
	protected static $packetPool120 = null;
	protected static $packetPool310 = null;
	protected static $packetPool331 = null;

	public static function init() {
	    static::registerPackets110();
	    static::registerPackets120();
	    static::registerPackets310();
	    static::registerPackets331();
	}

	/**
	 * @param int        $id 0-255
	 * @param DataPacket $class
	 */
	public static function registerPacket110(int $id, DataPacket $class) {
		static::$packetPool110[$id] = $class;
	}

	/**
	 * @param int        $id 0-255
	 * @param DataPacket $class
	 */
	public static function registerPacket120(int $id, DataPacket $class) {
		static::$packetPool120[$id] = $class;
	}

	/**
	 * @param int        $id 0-255
	 * @param DataPacket $class
	 */
	public static function registerPacket310(int $id, DataPacket $class) {
		static::$packetPool310[$id] = $class;
	}

	/**
	 * @param int        $id 0-255
	 * @param DataPacket $class
	 */
	public static function registerPacket331(int $id, DataPacket $class) {
		static::$packetPool331[$id] = $class;
	}

    /**
     * @return void
     */
	private static function registerPackets110() : void{
		static::$packetPool110 = new \SplFixedArray(256);
		static::registerPacket110(ProtocolInfo110::CHANGE_DIMENSION_PACKET, new ChangeDimensionPacket());
		static::registerPacket110(ProtocolInfo110::CLIENTBOUND_MAP_ITEM_DATA_PACKET, new ClientboundMapItemDataPacket());
		static::registerPacket110(ProtocolInfo110::LOGIN_PACKET, new LoginPacket());
		static::registerPacket110(ProtocolInfo110::PLAY_STATUS_PACKET, new PlayStatusPacket());
		static::registerPacket110(ProtocolInfo110::DISCONNECT_PACKET, new DisconnectPacket());
		static::registerPacket110(ProtocolInfo110::TEXT_PACKET, new TextPacket());
		static::registerPacket110(ProtocolInfo110::SET_TIME_PACKET, new SetTimePacket());
		static::registerPacket110(ProtocolInfo110::START_GAME_PACKET, new StartGamePacket());
		static::registerPacket110(ProtocolInfo110::ADD_PLAYER_PACKET, new AddPlayerPacket());
		static::registerPacket110(ProtocolInfo110::ADD_ACTOR_PACKET, new AddActorPacket());
		static::registerPacket110(ProtocolInfo110::REMOVE_ACTOR_PACKET, new RemoveActorPacket());
		static::registerPacket110(ProtocolInfo110::ADD_ITEM_ACTOR_PACKET, new AddItemActorPacket());
		static::registerPacket110(ProtocolInfo110::TAKE_ITEM_ACTOR_PACKET, new TakeItemActorPacket());
		static::registerPacket110(ProtocolInfo110::MOVE_ACTOR_ABSOLUTE_PACKET, new MoveActorAbsolutePacket());
		static::registerPacket110(ProtocolInfo110::MOVE_PLAYER_PACKET, new MovePlayerPacket());
		static::registerPacket110(ProtocolInfo110::UPDATE_BLOCK_PACKET, new UpdateBlockPacket());
		static::registerPacket110(ProtocolInfo110::ADD_PAINTING_PACKET, new AddPaintingPacket());
		static::registerPacket110(ProtocolInfo110::EXPLODE_PACKET, new ExplodePacket());
		static::registerPacket110(ProtocolInfo110::LEVEL_EVENT_PACKET, new LevelEventPacket());
		static::registerPacket110(ProtocolInfo110::LEVEL_SOUND_EVENT_PACKET, new LevelSoundEventPacket());
		static::registerPacket110(ProtocolInfo110::BLOCK_EVENT_PACKET, new BlockEventPacket());
		static::registerPacket110(ProtocolInfo110::ACTOR_EVENT_PACKET, new ActorEventPacket());
		static::registerPacket110(ProtocolInfo110::MOB_EQUIPMENT_PACKET, new MobEquipmentPacket());
		static::registerPacket110(ProtocolInfo110::MOB_ARMOR_EQUIPMENT_PACKET, new MobArmorEquipmentPacket());
		static::registerPacket110(ProtocolInfo110::INTERACT_PACKET, new InteractPacket());
		static::registerPacket110(ProtocolInfo110::PLAYER_ACTION_PACKET, new PlayerActionPacket());
		static::registerPacket110(ProtocolInfo110::SET_ACTOR_DATA_PACKET, new SetActorDataPacket());
		static::registerPacket110(ProtocolInfo110::SET_ACTOR_MOTION_PACKET, new SetActorMotionPacket());
		static::registerPacket110(ProtocolInfo110::SET_ACTOR_LINK_PACKET, new SetActorLinkPacket());
		static::registerPacket110(ProtocolInfo110::SET_SPAWN_POSITION_PACKET, new SetSpawnPositionPacket());
		static::registerPacket110(ProtocolInfo110::ANIMATE_PACKET, new AnimatePacket());
		static::registerPacket110(ProtocolInfo110::RESPAWN_PACKET, new RespawnPacket());
		static::registerPacket110(ProtocolInfo110::CONTAINER_OPEN_PACKET, new ContainerOpenPacket());
		static::registerPacket110(ProtocolInfo110::CONTAINER_CLOSE_PACKET, new ContainerClosePacket());
		static::registerPacket110(ProtocolInfo110::CONTAINER_SET_DATA_PACKET, new ContainerSetDataPacket());
		static::registerPacket110(ProtocolInfo110::CRAFTING_DATA_PACKET, new CraftingDataPacket());
		static::registerPacket110(ProtocolInfo110::CRAFTING_EVENT_PACKET, new CraftingEventPacket());
		static::registerPacket110(ProtocolInfo110::ADVENTURE_SETTINGS_PACKET, new AdventureSettingsPacket());
		static::registerPacket110(ProtocolInfo110::BLOCK_ACTOR_DATA_PACKET, new BlockActorDataPacket());
		static::registerPacket110(ProtocolInfo110::LEVEL_CHUNK_PACKET, new LevelChunkPacket());
		static::registerPacket110(ProtocolInfo110::SET_COMMANDS_ENABLED_PACKET, new SetCommandsEnabledPacket());
		static::registerPacket110(ProtocolInfo110::SET_DIFFICULTY_PACKET, new SetDifficultyPacket());
		static::registerPacket110(ProtocolInfo110::PLAYER_LIST_PACKET, new PlayerListPacket());
		static::registerPacket110(ProtocolInfo110::REQUEST_CHUNK_RADIUS_PACKET, new RequestChunkRadiusPacket());
		static::registerPacket110(ProtocolInfo110::CHUNK_RADIUS_UPDATED_PACKET, new ChunkRadiusUpdatedPacket());
		static::registerPacket110(ProtocolInfo110::AVAILABLE_COMMANDS_PACKET, new AvailableCommandsPacket());
		static::registerPacket110(ProtocolInfo110::TRANSFER_PACKET, new TransferPacket());
		static::registerPacket110(ProtocolInfo110::CLIENT_TO_SERVER_HANDSHAKE_PACKET, new ClientToServerHandshakePacket());
		static::registerPacket110(ProtocolInfo110::RESOURCE_PACK_DATA_INFO_PACKET, new ResourcePackDataInfoPacket());
		static::registerPacket110(ProtocolInfo110::RESOURCE_PACKS_INFO_PACKET, new ResourcePacksInfoPacket());
		static::registerPacket110(ProtocolInfo110::RESOURCE_PACK_CLIENT_RESPONSE_PACKET, new ResourcePackClientResponsePacket());
		static::registerPacket110(ProtocolInfo110::RESOURCE_PACK_CHUNK_REQUEST_PACKET, new ResourcePackChunkRequestPacket());
		static::registerPacket110(ProtocolInfo110::PLAYER_INPUT_PACKET, new PlayerInputPacket());
    	static::registerPacket110(ProtocolInfo110::PLAY_SOUND_PACKET, new PlaySoundPacket());
    	static::registerPacket110(ProtocolInfo110::ITEM_FRAME_DROP_ITEM_PACKET, new ItemFrameDropItemPacket());
	    static::registerPacket110(ProtocolInfo110::BOSS_EVENT_PACKET, new BossEventPacket());
		static::registerPacket110(ProtocolInfo110::MAP_INFO_REQUEST_PACKET, new MapInfoRequestPacket());
		static::registerPacket110(ProtocolInfo110::SPAWN_EXPERIENCE_ORB_PACKET, new SpawnExperienceOrbPacket());
		static::registerPacket110(0xfe, new BatchPacket());
	}

    /**
     * @return void
     */
	private static function registerPackets310() : void{
		static::$packetPool120 = new \SplFixedArray(256);
		static::registerPacket120(ProtocolInfo120::SPAWN_EXPERIENCE_ORB_PACKET, new SpawnExperienceOrbPacket());
		static::registerPacket120(ProtocolInfo120::CHANGE_DIMENSION_PACKET, new ChangeDimensionPacket());
		static::registerPacket120(ProtocolInfo120::CLIENTBOUND_MAP_ITEM_DATA_PACKET, new ClientboundMapItemDataPacket());
		static::registerPacket120(ProtocolInfo120::PLAY_STATUS_PACKET, new PlayStatusPacket());
		static::registerPacket120(ProtocolInfo120::DISCONNECT_PACKET, new DisconnectPacket());
		static::registerPacket120(ProtocolInfo120::TEXT_PACKET, new TextPacket());
		static::registerPacket120(ProtocolInfo120::SET_TIME_PACKET, new SetTimePacket());
		static::registerPacket120(ProtocolInfo120::START_GAME_PACKET, new StartGamePacket());
		static::registerPacket120(ProtocolInfo120::ADD_PLAYER_PACKET, new AddPlayerPacket());
		static::registerPacket120(ProtocolInfo120::ADD_ACTOR_PACKET, new AddActorPacket());
		static::registerPacket120(ProtocolInfo120::REMOVE_ACTOR_PACKET, new RemoveActorPacket());
		static::registerPacket120(ProtocolInfo120::ADD_ITEM_ACTOR_PACKET, new AddItemActorPacket());
		static::registerPacket120(ProtocolInfo120::TAKE_ITEM_ACTOR_PACKET, new TakeItemActorPacket());
		static::registerPacket120(ProtocolInfo120::MOVE_ACTOR_ABSOLUTE_PACKET, new MoveActorAbsolutePacket());
		static::registerPacket120(ProtocolInfo120::MOVE_PLAYER_PACKET, new MovePlayerPacket());
		static::registerPacket120(ProtocolInfo120::UPDATE_BLOCK_PACKET, new UpdateBlockPacket());
		static::registerPacket120(ProtocolInfo120::ADD_PAINTING_PACKET, new AddPaintingPacket());
		static::registerPacket120(ProtocolInfo120::EXPLODE_PACKET, new ExplodePacket());
		static::registerPacket120(ProtocolInfo120::LEVEL_EVENT_PACKET, new LevelEventPacket());
		static::registerPacket120(ProtocolInfo120::BLOCK_EVENT_PACKET, new BlockEventPacket());
		static::registerPacket120(ProtocolInfo120::ACTOR_EVENT_PACKET, new ActorEventPacket());
		static::registerPacket120(ProtocolInfo120::MOB_EQUIPMENT_PACKET, new MobEquipmentPacket());
		static::registerPacket120(ProtocolInfo120::MOB_ARMOR_EQUIPMENT_PACKET, new MobArmorEquipmentPacket());
		static::registerPacket120(ProtocolInfo120::INTERACT_PACKET, new InteractPacket());
		static::registerPacket120(ProtocolInfo120::PLAYER_ACTION_PACKET, new PlayerActionPacket());
		static::registerPacket120(ProtocolInfo120::SET_ACTOR_DATA_PACKET, new SetActorDataPacket());
		static::registerPacket120(ProtocolInfo120::SET_ACTOR_MOTION_PACKET, new SetActorMotionPacket());
		static::registerPacket120(ProtocolInfo120::SET_ACTOR_LINK_PACKET, new SetActorLinkPacket());
		static::registerPacket120(ProtocolInfo120::SET_SPAWN_POSITION_PACKET, new SetSpawnPositionPacket());
		static::registerPacket120(ProtocolInfo120::ANIMATE_PACKET, new AnimatePacket());
		static::registerPacket120(ProtocolInfo120::RESPAWN_PACKET, new RespawnPacket());
		static::registerPacket120(ProtocolInfo120::CONTAINER_OPEN_PACKET, new ContainerOpenPacket());
		static::registerPacket120(ProtocolInfo120::CONTAINER_CLOSE_PACKET, new ContainerClosePacket());
		static::registerPacket120(ProtocolInfo120::CONTAINER_SET_DATA_PACKET, new ContainerSetDataPacket());
		static::registerPacket120(ProtocolInfo120::CRAFTING_DATA_PACKET, new CraftingDataPacket());
		static::registerPacket120(ProtocolInfo120::CRAFTING_EVENT_PACKET, new CraftingEventPacket());
		static::registerPacket120(ProtocolInfo120::ADVENTURE_SETTINGS_PACKET, new AdventureSettingsPacket());
		static::registerPacket120(ProtocolInfo120::BLOCK_ACTOR_DATA_PACKET, new BlockActorDataPacket());
		static::registerPacket120(ProtocolInfo120::LEVEL_CHUNK_PACKET, new LevelChunkPacket());
		static::registerPacket120(ProtocolInfo120::SET_COMMANDS_ENABLED_PACKET, new SetCommandsEnabledPacket());
		static::registerPacket120(ProtocolInfo120::SET_DIFFICULTY_PACKET, new SetDifficultyPacket());
		static::registerPacket120(ProtocolInfo120::PLAYER_LIST_PACKET, new PlayerListPacket());
		static::registerPacket120(ProtocolInfo120::REQUEST_CHUNK_RADIUS_PACKET, new RequestChunkRadiusPacket());
		static::registerPacket120(ProtocolInfo120::CHUNK_RADIUS_UPDATED_PACKET, new ChunkRadiusUpdatedPacket());
		static::registerPacket120(ProtocolInfo120::AVAILABLE_COMMANDS_PACKET, new AvailableCommandsPacket());
		static::registerPacket120(ProtocolInfo120::TRANSFER_PACKET, new TransferPacket());
		static::registerPacket120(ProtocolInfo120::CLIENT_TO_SERVER_HANDSHAKE_PACKET, new ClientToServerHandshakePacket());
		static::registerPacket120(ProtocolInfo120::RESOURCE_PACK_DATA_INFO_PACKET, new ResourcePackDataInfoPacket());
		static::registerPacket120(ProtocolInfo120::RESOURCE_PACKS_INFO_PACKET, new ResourcePackDataInfoPacket());
		static::registerPacket120(ProtocolInfo120::RESOURCE_PACK_CLIENT_RESPONSE_PACKET, new ResourcePackClientResponsePacket());
		static::registerPacket120(ProtocolInfo120::RESOURCE_PACK_CHUNK_REQUEST_PACKET, new ResourcePackChunkRequestPacket());
		static::registerPacket120(ProtocolInfo120::PLAYER_INPUT_PACKET, new PlayerInputPacket());
		static::registerPacket120(ProtocolInfo120::MAP_INFO_REQUEST_PACKET, new MapInfoRequestPacket());
		static::registerPacket120(ProtocolInfo120::INVENTORY_TRANSACTION_PACKET, new InventoryTransactionPacket());
		static::registerPacket120(ProtocolInfo120::INVENTORY_CONTENT_PACKET, new InventoryContentPacket());
		static::registerPacket120(ProtocolInfo120::INVENTORY_SLOT_PACKET, new InventorySlotPacket());
		static::registerPacket120(ProtocolInfo120::PLAYER_HOTBAR_PACKET, new PlayerHotbarPacket());
		static::registerPacket120(ProtocolInfo120::COMMAND_REQUEST_PACKET, new CommandRequestPacket());
		static::registerPacket120(ProtocolInfo120::PLAYER_HOTBAR_PACKET, new PlayerHotbarPacket());
		static::registerPacket120(ProtocolInfo120::PLAYER_SKIN_PACKET, new PlayerSkinPacket());
		static::registerPacket120(ProtocolInfo120::MODAL_FORM_RESPONSE_PACKET, new ModalFormResponsePacket());
		static::registerPacket120(ProtocolInfo120::SERVER_SETTINGS_REQUEST_PACKET, new ServerSettingsRequestPacket());
		static::registerPacket120(ProtocolInfo120::PURCHASE_RECEIPT_PACKET, new PurchaseReceiptPacket());
		static::registerPacket120(ProtocolInfo120::SUB_CLIENT_LOGIN_PACKET, new SubClientLoginPacket());
		static::registerPacket120(ProtocolInfo120::AVAILABLE_ACTOR_IDENTIFIERS_PACKET, new AvailableActorIdentifiersPacket());
		static::registerPacket120(ProtocolInfo120::LEVEL_SOUND_EVENT_PACKET, new LevelSoundEventPacket());
		static::registerPacket120(ProtocolInfo120::SPAWN_PARTICLE_EFFECT_PACKET, new SpawnParticleEffectPacket());
    	static::registerPacket120(ProtocolInfo120::PLAY_SOUND_PACKET, new PlaySoundPacket());
    	static::registerPacket120(ProtocolInfo120::ITEM_FRAME_DROP_ITEM_PACKET, new ItemFrameDropItemPacket());
    	static::registerPacket120(ProtocolInfo120::BOSS_EVENT_PACKET, new BossEventPacket());
    	static::registerPacket120(ProtocolInfo120::BOOK_EDIT_PACKET, new BookEditPacket());
		static::registerPacket120(ProtocolInfo120::SET_LOCAL_PLAYER_AS_INITIALIZED_PACKET, new SetLocalPlayerAsInitializedPacket());
		static::registerPacket120(0xfe, new BatchPacket());
	}

    /**
     * @return void
     */
	private static function registerPackets310() : void{
		static::$packetPool310 = new \SplFixedArray(256);
		static::registerPacket310(ProtocolInfo310::SPAWN_EXPERIENCE_ORB_PACKET, new SpawnExperienceOrbPacket());
		static::registerPacket310(ProtocolInfo310::CHANGE_DIMENSION_PACKET, new ChangeDimensionPacket());
		static::registerPacket310(ProtocolInfo310::CLIENTBOUND_MAP_ITEM_DATA_PACKET, new ClientboundMapItemDataPacket());
		static::registerPacket310(ProtocolInfo310::PLAY_STATUS_PACKET, new PlayStatusPacket());
		static::registerPacket310(ProtocolInfo310::DISCONNECT_PACKET, new DisconnectPacket());
		static::registerPacket310(ProtocolInfo310::TEXT_PACKET, new TextPacket());
		static::registerPacket310(ProtocolInfo310::SET_TIME_PACKET, new SetTimePacket());
		static::registerPacket310(ProtocolInfo310::START_GAME_PACKET, new StartGamePacket());
		static::registerPacket310(ProtocolInfo310::ADD_PLAYER_PACKET, new AddPlayerPacket());
		static::registerPacket310(ProtocolInfo310::ADD_ACTOR_PACKET, new AddActorPacket());
		static::registerPacket310(ProtocolInfo310::REMOVE_ACTOR_PACKET, new RemoveActorPacket());
		static::registerPacket310(ProtocolInfo310::ADD_ITEM_ACTOR_PACKET, new AddItemActorPacket());
		static::registerPacket310(ProtocolInfo310::TAKE_ITEM_ACTOR_PACKET, new TakeItemActorPacket());
		static::registerPacket310(ProtocolInfo310::MOVE_ACTOR_ABSOLUTE_PACKET, new MoveActorAbsolutePacket());
		static::registerPacket310(ProtocolInfo310::MOVE_PLAYER_PACKET, new MovePlayerPacket());
		static::registerPacket310(ProtocolInfo310::UPDATE_BLOCK_PACKET, new UpdateBlockPacket());
		static::registerPacket310(ProtocolInfo310::ADD_PAINTING_PACKET, new AddPaintingPacket());
		static::registerPacket310(ProtocolInfo310::EXPLODE_PACKET, new ExplodePacket());
		static::registerPacket310(ProtocolInfo310::LEVEL_EVENT_PACKET, new LevelEventPacket());
		static::registerPacket310(ProtocolInfo310::BLOCK_EVENT_PACKET, new BlockEventPacket());
		static::registerPacket310(ProtocolInfo310::ACTOR_EVENT_PACKET, new ActorEventPacket());
		static::registerPacket310(ProtocolInfo310::MOB_EQUIPMENT_PACKET, new MobEquipmentPacket());
		static::registerPacket310(ProtocolInfo310::MOB_ARMOR_EQUIPMENT_PACKET, new MobArmorEquipmentPacket());
		static::registerPacket310(ProtocolInfo310::INTERACT_PACKET, new InteractPacket());
		static::registerPacket310(ProtocolInfo310::PLAYER_ACTION_PACKET, new PlayerActionPacket());
		static::registerPacket310(ProtocolInfo310::SET_ACTOR_DATA_PACKET, new SetActorDataPacket());
		static::registerPacket310(ProtocolInfo310::SET_ACTOR_MOTION_PACKET, new SetActorMotionPacket());
		static::registerPacket310(ProtocolInfo310::SET_ACTOR_LINK_PACKET, new SetActorLinkPacket());
		static::registerPacket310(ProtocolInfo310::SET_SPAWN_POSITION_PACKET, new SetSpawnPositionPacket());
		static::registerPacket310(ProtocolInfo310::ANIMATE_PACKET, new AnimatePacket());
		static::registerPacket310(ProtocolInfo310::RESPAWN_PACKET, new RespawnPacket());
		static::registerPacket310(ProtocolInfo310::CONTAINER_OPEN_PACKET, new ContainerOpenPacket());
		static::registerPacket310(ProtocolInfo310::CONTAINER_CLOSE_PACKET, new ContainerClosePacket());
		static::registerPacket310(ProtocolInfo310::CONTAINER_SET_DATA_PACKET, new ContainerSetDataPacket());
		static::registerPacket310(ProtocolInfo310::CRAFTING_DATA_PACKET, new CraftingDataPacket());
		static::registerPacket310(ProtocolInfo310::CRAFTING_EVENT_PACKET, new CraftingEventPacket());
		static::registerPacket310(ProtocolInfo310::ADVENTURE_SETTINGS_PACKET, new AdventureSettingsPacket());
		static::registerPacket310(ProtocolInfo310::BLOCK_ACTOR_DATA_PACKET, new BlockActorDataPacket());
		static::registerPacket310(ProtocolInfo310::LEVEL_CHUNK_PACKET, new LevelChunkPacket());
		static::registerPacket310(ProtocolInfo310::SET_COMMANDS_ENABLED_PACKET, new SetCommandsEnabledPacket());
		static::registerPacket310(ProtocolInfo310::SET_DIFFICULTY_PACKET, new SetDifficultyPacket());
		static::registerPacket310(ProtocolInfo310::PLAYER_LIST_PACKET, new PlayerListPacket());
		static::registerPacket310(ProtocolInfo310::REQUEST_CHUNK_RADIUS_PACKET, new RequestChunkRadiusPacket());
		static::registerPacket310(ProtocolInfo310::CHUNK_RADIUS_UPDATED_PACKET, new ChunkRadiusUpdatedPacket());
		static::registerPacket310(ProtocolInfo310::AVAILABLE_COMMANDS_PACKET, new AvailableCommandsPacket());
		static::registerPacket310(ProtocolInfo310::TRANSFER_PACKET, new TransferPacket());
		static::registerPacket310(ProtocolInfo310::CLIENT_TO_SERVER_HANDSHAKE_PACKET, new ClientToServerHandshakePacket());
		static::registerPacket310(ProtocolInfo310::RESOURCE_PACK_DATA_INFO_PACKET, new ResourcePackDataInfoPacket());
		static::registerPacket310(ProtocolInfo310::RESOURCE_PACKS_INFO_PACKET, new ResourcePackDataInfoPacket());
		static::registerPacket310(ProtocolInfo310::RESOURCE_PACK_CLIENT_RESPONSE_PACKET, new ResourcePackClientResponsePacket());
		static::registerPacket310(ProtocolInfo310::RESOURCE_PACK_CHUNK_REQUEST_PACKET, new ResourcePackChunkRequestPacket());
		static::registerPacket310(ProtocolInfo310::PLAYER_INPUT_PACKET, new PlayerInputPacket());
		static::registerPacket310(ProtocolInfo310::MAP_INFO_REQUEST_PACKET, new MapInfoRequestPacket());
		static::registerPacket310(ProtocolInfo310::INVENTORY_TRANSACTION_PACKET, new InventoryTransactionPacket());
		static::registerPacket310(ProtocolInfo310::INVENTORY_CONTENT_PACKET, new InventoryContentPacket());
		static::registerPacket310(ProtocolInfo310::INVENTORY_SLOT_PACKET, new InventorySlotPacket());
		static::registerPacket310(ProtocolInfo310::PLAYER_HOTBAR_PACKET, new PlayerHotbarPacket());
		static::registerPacket310(ProtocolInfo310::COMMAND_REQUEST_PACKET, new CommandRequestPacket());
		static::registerPacket310(ProtocolInfo310::PLAYER_HOTBAR_PACKET, new PlayerHotbarPacket());
		static::registerPacket310(ProtocolInfo310::PLAYER_SKIN_PACKET, new PlayerSkinPacket());
		static::registerPacket310(ProtocolInfo310::MODAL_FORM_RESPONSE_PACKET, new ModalFormResponsePacket());
		static::registerPacket310(ProtocolInfo310::SERVER_SETTINGS_REQUEST_PACKET, new ServerSettingsRequestPacket());
		static::registerPacket310(ProtocolInfo310::PURCHASE_RECEIPT_PACKET, new PurchaseReceiptPacket());
		static::registerPacket310(ProtocolInfo310::SUB_CLIENT_LOGIN_PACKET, new SubClientLoginPacket());
		static::registerPacket310(ProtocolInfo310::AVAILABLE_ACTOR_IDENTIFIERS_PACKET, new AvailableActorIdentifiersPacket());
		static::registerPacket310(ProtocolInfo310::LEVEL_SOUND_EVENT_PACKET, new LevelSoundEventPacket());
		static::registerPacket310(ProtocolInfo310::NETWORK_CHUNK_PUBLISHER_UPDATE_PACKET, new NetworkChunkPublisherUpdatePacket());
		static::registerPacket310(ProtocolInfo310::SPAWN_PARTICLE_EFFECT_PACKET, new SpawnParticleEffectPacket());
    	static::registerPacket310(ProtocolInfo310::PLAY_SOUND_PACKET, new PlaySoundPacket());
    	static::registerPacket310(ProtocolInfo310::ITEM_FRAME_DROP_ITEM_PACKET, new ItemFrameDropItemPacket());
    	static::registerPacket310(ProtocolInfo310::BOSS_EVENT_PACKET, new BossEventPacket());
    	static::registerPacket310(ProtocolInfo310::BOOK_EDIT_PACKET, new BookEditPacket());
		static::registerPacket310(ProtocolInfo310::SET_LOCAL_PLAYER_AS_INITIALIZED_PACKET, new SetLocalPlayerAsInitializedPacket());
		static::registerPacket310(0xfe, new BatchPacket());
	}

    /**
     * @return void
     */
	private static function registerPackets331() : void{
		static::$packetPool331 = new \SplFixedArray(256);
		static::registerPacket331(ProtocolInfo331::SPAWN_EXPERIENCE_ORB_PACKET, new SpawnExperienceOrbPacket());
		static::registerPacket331(ProtocolInfo331::CHANGE_DIMENSION_PACKET, new ChangeDimensionPacket());
		static::registerPacket331(ProtocolInfo331::CLIENTBOUND_MAP_ITEM_DATA_PACKET, new ClientboundMapItemDataPacket());
		static::registerPacket331(ProtocolInfo331::PLAY_STATUS_PACKET, new PlayStatusPacket());
		static::registerPacket331(ProtocolInfo331::DISCONNECT_PACKET, new DisconnectPacket());
		static::registerPacket331(ProtocolInfo331::TEXT_PACKET, new TextPacket());
		static::registerPacket331(ProtocolInfo331::SET_TIME_PACKET, new SetTimePacket());
		static::registerPacket331(ProtocolInfo331::START_GAME_PACKET, new StartGamePacket());
		static::registerPacket331(ProtocolInfo331::ADD_PLAYER_PACKET, new AddPlayerPacket());
		static::registerPacket331(ProtocolInfo331::ADD_ACTOR_PACKET, new AddActorPacket());
		static::registerPacket331(ProtocolInfo331::REMOVE_ACTOR_PACKET, new RemoveActorPacket());
		static::registerPacket331(ProtocolInfo331::ADD_ITEM_ACTOR_PACKET, new AddItemActorPacket());
		static::registerPacket331(ProtocolInfo331::TAKE_ITEM_ACTOR_PACKET, new TakeItemActorPacket());
		static::registerPacket331(ProtocolInfo331::MOVE_ACTOR_ABSOLUTE_PACKET, new MoveActorAbsolutePacket());
		static::registerPacket331(ProtocolInfo331::MOVE_PLAYER_PACKET, new MovePlayerPacket());
		static::registerPacket331(ProtocolInfo331::UPDATE_BLOCK_PACKET, new UpdateBlockPacket());
		static::registerPacket331(ProtocolInfo331::ADD_PAINTING_PACKET, new AddPaintingPacket());
		static::registerPacket331(ProtocolInfo331::EXPLODE_PACKET, new ExplodePacket());
		static::registerPacket331(ProtocolInfo331::LEVEL_EVENT_PACKET, new LevelEventPacket());
		static::registerPacket331(ProtocolInfo331::BLOCK_EVENT_PACKET, new BlockEventPacket());
		static::registerPacket331(ProtocolInfo331::ACTOR_EVENT_PACKET, new ActorEventPacket());
		static::registerPacket331(ProtocolInfo331::MOB_EQUIPMENT_PACKET, new MobEquipmentPacket());
		static::registerPacket331(ProtocolInfo331::MOB_ARMOR_EQUIPMENT_PACKET, new MobArmorEquipmentPacket());
		static::registerPacket331(ProtocolInfo331::INTERACT_PACKET, new InteractPacket());
		static::registerPacket331(ProtocolInfo331::PLAYER_ACTION_PACKET, new PlayerActionPacket());
		static::registerPacket331(ProtocolInfo331::SET_ACTOR_DATA_PACKET, new SetActorDataPacket());
		static::registerPacket331(ProtocolInfo331::SET_ACTOR_MOTION_PACKET, new SetActorMotionPacket());
		static::registerPacket331(ProtocolInfo331::SET_ACTOR_LINK_PACKET, new SetActorLinkPacket());
		static::registerPacket331(ProtocolInfo331::SET_SPAWN_POSITION_PACKET, new SetSpawnPositionPacket());
		static::registerPacket331(ProtocolInfo331::ANIMATE_PACKET, new AnimatePacket());
		static::registerPacket331(ProtocolInfo331::RESPAWN_PACKET, new RespawnPacket());
		static::registerPacket331(ProtocolInfo331::CONTAINER_OPEN_PACKET, new ContainerOpenPacket());
		static::registerPacket331(ProtocolInfo331::CONTAINER_CLOSE_PACKET, new ContainerClosePacket());
		static::registerPacket331(ProtocolInfo331::CONTAINER_SET_DATA_PACKET, new ContainerSetDataPacket());
		static::registerPacket331(ProtocolInfo331::CRAFTING_DATA_PACKET, new CraftingDataPacket());
		static::registerPacket331(ProtocolInfo331::CRAFTING_EVENT_PACKET, new CraftingEventPacket());
		static::registerPacket331(ProtocolInfo331::ADVENTURE_SETTINGS_PACKET, new AdventureSettingsPacket());
		static::registerPacket331(ProtocolInfo331::BLOCK_ACTOR_DATA_PACKET, new BlockActorDataPacket());
		static::registerPacket331(ProtocolInfo331::LEVEL_CHUNK_PACKET, new LevelChunkPacket());
		static::registerPacket331(ProtocolInfo331::SET_COMMANDS_ENABLED_PACKET, new SetCommandsEnabledPacket());
		static::registerPacket331(ProtocolInfo331::SET_DIFFICULTY_PACKET, new SetDifficultyPacket());
		static::registerPacket331(ProtocolInfo331::PLAYER_LIST_PACKET, new PlayerListPacket());
		static::registerPacket331(ProtocolInfo331::REQUEST_CHUNK_RADIUS_PACKET, new RequestChunkRadiusPacket());
		static::registerPacket331(ProtocolInfo331::CHUNK_RADIUS_UPDATED_PACKET, new ChunkRadiusUpdatedPacket());
		static::registerPacket331(ProtocolInfo331::AVAILABLE_COMMANDS_PACKET, new AvailableCommandsPacket());
		static::registerPacket331(ProtocolInfo331::TRANSFER_PACKET, new TransferPacket());
		static::registerPacket331(ProtocolInfo331::CLIENT_TO_SERVER_HANDSHAKE_PACKET, new ClientToServerHandshakePacket());
		static::registerPacket331(ProtocolInfo331::RESOURCE_PACK_DATA_INFO_PACKET, new ResourcePackDataInfoPacket());
		static::registerPacket331(ProtocolInfo331::RESOURCE_PACKS_INFO_PACKET, new ResourcePackDataInfoPacket());
		static::registerPacket331(ProtocolInfo331::RESOURCE_PACK_CLIENT_RESPONSE_PACKET, new ResourcePackClientResponsePacket());
		static::registerPacket331(ProtocolInfo331::RESOURCE_PACK_CHUNK_REQUEST_PACKET, new ResourcePackChunkRequestPacket());
		static::registerPacket331(ProtocolInfo331::PLAYER_INPUT_PACKET, new PlayerInputPacket());
		static::registerPacket331(ProtocolInfo331::MAP_INFO_REQUEST_PACKET, new MapInfoRequestPacket());
		static::registerPacket331(ProtocolInfo331::INVENTORY_TRANSACTION_PACKET, new InventoryTransactionPacket());
		static::registerPacket331(ProtocolInfo331::INVENTORY_CONTENT_PACKET, new InventoryContentPacket());
		static::registerPacket331(ProtocolInfo331::INVENTORY_SLOT_PACKET, new InventorySlotPacket());
		static::registerPacket331(ProtocolInfo331::PLAYER_HOTBAR_PACKET, new PlayerHotbarPacket());
		static::registerPacket331(ProtocolInfo331::COMMAND_REQUEST_PACKET, new CommandRequestPacket());
		static::registerPacket331(ProtocolInfo331::PLAYER_HOTBAR_PACKET, new PlayerHotbarPacket());
		static::registerPacket331(ProtocolInfo331::PLAYER_SKIN_PACKET, new PlayerSkinPacket());
		static::registerPacket331(ProtocolInfo331::MODAL_FORM_RESPONSE_PACKET, new ModalFormResponsePacket());
		static::registerPacket331(ProtocolInfo331::SERVER_SETTINGS_REQUEST_PACKET, new ServerSettingsRequestPacket());
		static::registerPacket331(ProtocolInfo331::PURCHASE_RECEIPT_PACKET, new PurchaseReceiptPacket());
		static::registerPacket331(ProtocolInfo331::SUB_CLIENT_LOGIN_PACKET, new SubClientLoginPacket());
		static::registerPacket331(ProtocolInfo331::AVAILABLE_ACTOR_IDENTIFIERS_PACKET, new AvailableActorIdentifiersPacket());
		static::registerPacket331(ProtocolInfo331::LEVEL_SOUND_EVENT_PACKET, new LevelSoundEventPacket());
		static::registerPacket331(ProtocolInfo331::NETWORK_CHUNK_PUBLISHER_UPDATE_PACKET, new NetworkChunkPublisherUpdatePacket());
		static::registerPacket331(ProtocolInfo331::SPAWN_PARTICLE_EFFECT_PACKET, new SpawnParticleEffectPacket());
    	static::registerPacket331(ProtocolInfo331::PLAY_SOUND_PACKET, new PlaySoundPacket());
    	static::registerPacket331(ProtocolInfo331::ITEM_FRAME_DROP_ITEM_PACKET, new ItemFrameDropItemPacket());
    	static::registerPacket331(ProtocolInfo331::BOSS_EVENT_PACKET, new BossEventPacket());
    	static::registerPacket331(ProtocolInfo331::BOOK_EDIT_PACKET, new BookEditPacket());
		static::registerPacket331(ProtocolInfo331::SET_LOCAL_PLAYER_AS_INITIALIZED_PACKET, new SetLocalPlayerAsInitializedPacket());
		static::registerPacket331(0xfe, new BatchPacket());
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
			case ProtocolInfo::PROTOCOL_354:
			case ProtocolInfo::PROTOCOL_351:
			case ProtocolInfo::PROTOCOL_350:
			case ProtocolInfo::PROTOCOL_342:
			case ProtocolInfo::PROTOCOL_340:
			case ProtocolInfo::PROTOCOL_332:
			case ProtocolInfo::PROTOCOL_331:
				$class = isset(static::$packetPool331[$pid]) ? clone static::$packetPool331[$pid] : null;
				break;
			case ProtocolInfo::PROTOCOL_330:
			case ProtocolInfo::PROTOCOL_311:
			case ProtocolInfo::PROTOCOL_310:
				$class = isset(static::$packetPool310[$pid]) ? clone static::$packetPool310[$pid] : null;
				break;
			case ProtocolInfo::PROTOCOL_290:
				$class = isset(static::$packetPool120[$pid]) ? clone static::$packetPool120[$pid] : null;
				break;
			default:
				$class = isset(static::$packetPool110[$pid]) ? clone static::$packetPool110[$pid] : null;
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
