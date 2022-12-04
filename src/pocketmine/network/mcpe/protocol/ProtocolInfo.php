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

/**
 * Version numbers and packet IDs for the current Minecraft PE protocol
 */
interface ProtocolInfo{

	/**
	 * NOTE TO DEVELOPERS
	 * Do not waste your time or ours submitting pull requests changing game and/or protocol version numbers.
	 * Pull requests changing game and/or protocol version numbers will be closed.
	 *
	 * This file is generated automatically, do not edit it manually.
	 */

	/**
	 * Actual Minecraft: PE protocol version
	 */
	public const CURRENT_PROTOCOL = 110;
	public const ACCEPTED_PROTOCOLS = [ProtocolInfo::PROTOCOL_130, ProtocolInfo::PROTOCOL_131, ProtocolInfo::PROTOCOL_132, ProtocolInfo::PROTOCOL_133, ProtocolInfo::PROTOCOL_134, ProtocolInfo::PROTOCOL_135, ProtocolInfo::PROTOCOL_136, ProtocolInfo::PROTOCOL_137, ProtocolInfo::PROTOCOL_140, ProtocolInfo::PROTOCOL_141, ProtocolInfo::PROTOCOL_150, ProtocolInfo::PROTOCOL_160, ProtocolInfo::PROTOCOL_200, ProtocolInfo::PROTOCOL_201, ProtocolInfo::PROTOCOL_220, ProtocolInfo::PROTOCOL_221, ProtocolInfo::PROTOCOL_222, ProtocolInfo::PROTOCOL_223, ProtocolInfo::PROTOCOL_224, ProtocolInfo::PROTOCOL_240, ProtocolInfo::PROTOCOL_250, ProtocolInfo::PROTOCOL_260, ProtocolInfo::PROTOCOL_261, ProtocolInfo::PROTOCOL_270, ProtocolInfo::PROTOCOL_271, ProtocolInfo::PROTOCOL_273, ProtocolInfo::PROTOCOL_274, ProtocolInfo::PROTOCOL_280, ProtocolInfo::PROTOCOL_281, ProtocolInfo::PROTOCOL_282, ProtocolInfo::PROTOCOL_290, ProtocolInfo::PROTOCOL_291, ProtocolInfo::PROTOCOL_310, ProtocolInfo::PROTOCOL_311, ProtocolInfo::PROTOCOL_312, ProtocolInfo::PROTOCOL_313, ProtocolInfo::PROTOCOL_330, ProtocolInfo::PROTOCOL_331, ProtocolInfo::PROTOCOL_332, ProtocolInfo::PROTOCOL_340, ProtocolInfo::PROTOCOL_342, ProtocolInfo::PROTOCOL_350, ProtocolInfo::PROTOCOL_351, ProtocolInfo::PROTOCOL_352, ProtocolInfo::PROTOCOL_353, ProtocolInfo::PROTOCOL_354, ProtocolInfo::PROTOCOL_360, ProtocolInfo::PROTOCOL_361];
	public const PROTOCOL_130 = 130; // 1.2.0.2
	public const PROTOCOL_131 = 131; // 1.2.0.7
	public const PROTOCOL_132 = 132; // 1.2.0.15
	public const PROTOCOL_133 = 133; // 1.2.0.18
	public const PROTOCOL_134 = 134; // 1.2.0.20, 1.2.0.22
	public const PROTOCOL_135 = 135; // 1.2.0.24, 1.2.0.25
	public const PROTOCOL_136 = 136; // 1.2.0.31
	public const PROTOCOL_137 = 137; // 1.2.0, 1.2.1, 1.2.2, 1.2.3.3, 1.2.3, 1.2.5.0
	public const PROTOCOL_140 = 140; // 1.2.5.11
	public const PROTOCOL_141 = 141; // 1.2.5, 1.2.5.15
	public const PROTOCOL_150 = 150; // 1.2.6, 1.2.6.1
	public const PROTOCOL_160 = 160; // 1.2.7, 1.2.8, 1.2.9
	public const PROTOCOL_200 = 200; // 1.2.10.1
	public const PROTOCOL_201 = 201; // 1.2.10, 1.2.11
	public const PROTOCOL_220 = 220; // 1.2.13.5, 1.2.13.6
	public const PROTOCOL_221 = 221; // 1.2.13.8
	public const PROTOCOL_222 = 222; // 1.2.13.10
	public const PROTOCOL_223 = 223; // 1.2.3, 1.2.4, 1.2.5, 1.2.13.60
	public const PROTOCOL_224 = 224; // 1.2.13.11
	public const PROTOCOL_240 = 240; // 1.2.14.2, 1.2.14.3
	public const PROTOCOL_250 = 250; // 1.2.15.1
	public const PROTOCOL_260 = 260; // 1.2.20.1, 1.2.20.2
	public const PROTOCOL_261 = 261; // 1.4.0, 1.4.1, 1.4.2, 1.4.3, 1.4.4
	public const PROTOCOL_270 = 270; // 1.5.0.0
	public const PROTOCOL_271 = 271; // 1.5.0.1, 1.5.0.4
	public const PROTOCOL_273 = 273; // 1.5.0.7
	public const PROTOCOL_274 = 274; // 1.5.0.10, 1.5.0, 1.5.1, 1.5.2, 1.5.3
	public const PROTOCOL_280 = 280; // 1.6.0.1
	public const PROTOCOL_281 = 281; // 1.6.0.5, 1.6.0.6
	public const PROTOCOL_282 = 282; // 1.6.0.8, 1.6.0.30, 1.6.0, 1.6.1, 1.6.2
	public const PROTOCOL_290 = 290; // 1.7.0.2, 1.7.0.3
	public const PROTOCOL_291 = 291; // 1.7.0.5, 1.7.0.7, 1.7.0.9, 1.7.0, 1.7.1
	public const PROTOCOL_310 = 310; // 1.8.0.4, 1.8.0.8
	public const PROTOCOL_311 = 311; // 1.8.0.9, 1.8.0.10
	public const PROTOCOL_312 = 312; // 1.8.0.11, 1.8.0.13, 1.8.0.14
	public const PROTOCOL_313 = 313; // 1.8.0, 1.8.1
	public const PROTOCOL_330 = 330; // 1.9.0.0
	public const PROTOCOL_331 = 331; // 1.9.0.2
	public const PROTOCOL_332 = 332; // 1.9.0.3, 1.9.0.5, 1.9.0
	public const PROTOCOL_340 = 340; // 1.10.0.3, 1.10.0.4, 1.10.0, 1.10.1
	public const PROTOCOL_342 = 342; // 1.10.0
	public const PROTOCOL_350 = 350; // 1.11.0.1
	public const PROTOCOL_351 = 351; // 1.11.0.3
	public const PROTOCOL_352 = 352; // 1.11.0.4
	public const PROTOCOL_353 = 353; // 1.11.0.5
	public const PROTOCOL_354 = 354; // 1.11.0.7, 1.11.0.8, 1.11.0.9, 1.11.0.10, 1.11.0, 1.11.1, 1.11.2, 1.11.3, 1.11.4
	public const PROTOCOL_360 = 360; // 1.12.0.2
	public const PROTOCOL_361 = 361; // 1.12.0.3, 1.12.0.4, 1.12.0.6, 1.12.0.9, 1.12.0.10, 1.12.0.11, 1.12.0.12, 1.12.0.13, 1.12.0.14
	/**
	 * Current Minecraft PE version reported by the server. This is usually the earliest currently supported version.
	 */
	public const MINECRAFT_VERSION = 'v1.x';
	/**
	 * Version number sent to clients in ping responses.
	 */
	public const MINECRAFT_VERSION_NETWORK = '1.12.1';

	public const LOGIN_PACKET = 0x01;
	public const PLAY_STATUS_PACKET = 0x02;
	public const SERVER_TO_CLIENT_HANDSHAKE_PACKET = 0x03;
	public const CLIENT_TO_SERVER_HANDSHAKE_PACKET = 0x04;
	public const DISCONNECT_PACKET = 0x05;
	public const RESOURCE_PACKS_INFO_PACKET = 0x06;
	public const RESOURCE_PACK_STACK_PACKET = 0x07;
	public const RESOURCE_PACK_CLIENT_RESPONSE_PACKET = 0x08;
	public const TEXT_PACKET = 0x09;
	public const SET_TIME_PACKET = 0x0a;
	public const START_GAME_PACKET = 0x0b;
	public const ADD_PLAYER_PACKET = 0x0c;
	public const ADD_ACTOR_PACKET = 0x0d;
	public const REMOVE_ACTOR_PACKET = 0x0e;
	public const ADD_ITEM_ACTOR_PACKET = 0x0f;
	public const TAKE_ITEM_ACTOR_PACKET = 0x11;
	public const MOVE_ACTOR_ABSOLUTE_PACKET = 0x12;
	public const MOVE_PLAYER_PACKET = 0x13;
	public const RIDER_JUMP_PACKET = 0x14;
	public const UPDATE_BLOCK_PACKET = 0x15;
	public const ADD_PAINTING_PACKET = 0x16;
	public const EXPLODE_PACKET = 0x17;
	public const LEVEL_SOUND_EVENT_PACKET_V1 = 0x18;
	public const LEVEL_EVENT_PACKET = 0x19;
	public const BLOCK_EVENT_PACKET = 0x1a;
	public const ACTOR_EVENT_PACKET = 0x1b;
	public const MOB_EFFECT_PACKET = 0x1c;
	public const UPDATE_ATTRIBUTES_PACKET = 0x1d;
	public const INVENTORY_TRANSACTION_PACKET = 0x1e;
	public const MOB_EQUIPMENT_PACKET = 0x1f;
	public const MOB_ARMOR_EQUIPMENT_PACKET = 0x20;
	public const INTERACT_PACKET = 0x21;
	public const BLOCK_PICK_REQUEST_PACKET = 0x22;
	public const ACTOR_PICK_REQUEST_PACKET = 0x23;
	public const PLAYER_ACTION_PACKET = 0x24;
	public const ACTOR_FALL_PACKET = 0x25;
	public const HURT_ARMOR_PACKET = 0x26;
	public const SET_ACTOR_DATA_PACKET = 0x27;
	public const SET_ACTOR_MOTION_PACKET = 0x28;
	public const SET_ACTOR_LINK_PACKET = 0x29;
	public const SET_HEALTH_PACKET = 0x2a;
	public const SET_SPAWN_POSITION_PACKET = 0x2b;
	public const ANIMATE_PACKET = 0x2c;
	public const RESPAWN_PACKET = 0x2d;
	public const CONTAINER_OPEN_PACKET = 0x2e;
	public const CONTAINER_CLOSE_PACKET = 0x2f;
	public const PLAYER_HOTBAR_PACKET = 0x30;
	public const INVENTORY_CONTENT_PACKET = 0x31;
	public const INVENTORY_SLOT_PACKET = 0x32;
	public const CONTAINER_SET_DATA_PACKET = 0x33;
	public const CRAFTING_DATA_PACKET = 0x34;
	public const CRAFTING_EVENT_PACKET = 0x35;
	public const GUI_DATA_PICK_ITEM_PACKET = 0x36;
	public const ADVENTURE_SETTINGS_PACKET = 0x37;
	public const BLOCK_ACTOR_DATA_PACKET = 0x38;
	public const PLAYER_INPUT_PACKET = 0x39;
	public const LEVEL_CHUNK_PACKET = 0x3a;
	public const SET_COMMANDS_ENABLED_PACKET = 0x3b;
	public const SET_DIFFICULTY_PACKET = 0x3c;
	public const CHANGE_DIMENSION_PACKET = 0x3d;
	public const SET_PLAYER_GAME_TYPE_PACKET = 0x3e;
	public const PLAYER_LIST_PACKET = 0x3f;
	public const SIMPLE_EVENT_PACKET = 0x40;
	public const EVENT_PACKET = 0x41;
	public const SPAWN_EXPERIENCE_ORB_PACKET = 0x42;
	public const CLIENTBOUND_MAP_ITEM_DATA_PACKET = 0x43;
	public const MAP_INFO_REQUEST_PACKET = 0x44;
	public const REQUEST_CHUNK_RADIUS_PACKET = 0x45;
	public const CHUNK_RADIUS_UPDATED_PACKET = 0x46;
	public const ITEM_FRAME_DROP_ITEM_PACKET = 0x47;
	public const GAME_RULES_CHANGED_PACKET = 0x48;
	public const CAMERA_PACKET = 0x49;
	public const BOSS_EVENT_PACKET = 0x4a;
	public const SHOW_CREDITS_PACKET = 0x4b;
	public const AVAILABLE_COMMANDS_PACKET = 0x4c;
	public const COMMAND_REQUEST_PACKET = 0x4d;
	public const COMMAND_BLOCK_UPDATE_PACKET = 0x4e;
	public const COMMAND_OUTPUT_PACKET = 0x4f;
	public const UPDATE_TRADE_PACKET = 0x50;
	public const UPDATE_EQUIP_PACKET = 0x51;
	public const RESOURCE_PACK_DATA_INFO_PACKET = 0x52;
	public const RESOURCE_PACK_CHUNK_DATA_PACKET = 0x53;
	public const RESOURCE_PACK_CHUNK_REQUEST_PACKET = 0x54;
	public const TRANSFER_PACKET = 0x55;
	public const PLAY_SOUND_PACKET = 0x56;
	public const STOP_SOUND_PACKET = 0x57;
	public const SET_TITLE_PACKET = 0x58;
	public const ADD_BEHAVIOR_TREE_PACKET = 0x59;
	public const STRUCTURE_BLOCK_UPDATE_PACKET = 0x5a;
	public const SHOW_STORE_OFFER_PACKET = 0x5b;
	public const PURCHASE_RECEIPT_PACKET = 0x5c;
	public const PLAYER_SKIN_PACKET = 0x5d;
	public const SUB_CLIENT_LOGIN_PACKET = 0x5e;
	public const AUTOMATION_CLIENT_CONNECT_PACKET = 0x5f;
	public const SET_LAST_HURT_BY_PACKET = 0x60;
	public const BOOK_EDIT_PACKET = 0x61;
	public const NPC_REQUEST_PACKET = 0x62;
	public const PHOTO_TRANSFER_PACKET = 0x63;
	public const MODAL_FORM_REQUEST_PACKET = 0x64;
	public const MODAL_FORM_RESPONSE_PACKET = 0x65;
	public const SERVER_SETTINGS_REQUEST_PACKET = 0x66;
	public const SERVER_SETTINGS_RESPONSE_PACKET = 0x67;
	public const SHOW_PROFILE_PACKET = 0x68;
	public const SET_DEFAULT_GAME_TYPE_PACKET = 0x69;
	public const REMOVE_OBJECTIVE_PACKET = 0x6a;
	public const SET_DISPLAY_OBJECTIVE_PACKET = 0x6b;
	public const SET_SCORE_PACKET = 0x6c;
	public const LAB_TABLE_PACKET = 0x6d;
	public const UPDATE_BLOCK_SYNCED_PACKET = 0x6e;
	public const MOVE_ACTOR_DELTA_PACKET = 0x6f;
	public const SET_SCOREBOARD_IDENTITY_PACKET = 0x70;
	public const SET_LOCAL_PLAYER_AS_INITIALIZED_PACKET = 0x71;
	public const UPDATE_SOFT_ENUM_PACKET = 0x72;
	public const NETWORK_STACK_LATENCY_PACKET = 0x73;
	public const SCRIPT_CUSTOM_EVENT_PACKET = 0x75;
	public const SPAWN_PARTICLE_EFFECT_PACKET = 0x76;
	public const AVAILABLE_ACTOR_IDENTIFIERS_PACKET = 0x77;
	public const LEVEL_SOUND_EVENT_PACKET_V2 = 0x78;
	public const NETWORK_CHUNK_PUBLISHER_UPDATE_PACKET = 0x79;
	public const BIOME_DEFINITION_LIST_PACKET = 0x7a;
	public const LEVEL_SOUND_EVENT_PACKET = 0x7b;
	public const LEVEL_EVENT_GENERIC_PACKET = 0x7c;
	public const LECTERN_UPDATE_PACKET = 0x7d;
	public const VIDEO_STREAM_CONNECT_PACKET = 0x7e;
	public const ADD_ENTITY_PACKET = 0x7f;
	public const REMOVE_ENTITY_PACKET = 0x80;
	public const CLIENT_CACHE_STATUS_PACKET = 0x81;
	public const ON_SCREEN_TEXTURE_ANIMATION_PACKET = 0x82;
	public const MAP_CREATE_LOCKED_COPY_PACKET = 0x83;
	public const STRUCTURE_TEMPLATE_DATA_EXPORT_REQUEST_PACKET = 0x84;
	public const STRUCTURE_TEMPLATE_DATA_EXPORT_RESPONSE_PACKET = 0x85;
	public const UPDATE_BLOCK_PROPERTIES_PACKET = 0x86;
	public const CLIENT_CACHE_BLOB_STATUS_PACKET = 0x87;
	public const CLIENT_CACHE_MISS_RESPONSE_PACKET = 0x88;

}