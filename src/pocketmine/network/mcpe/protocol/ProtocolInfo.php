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

interface ProtocolInfo {

	/**
	 * Actual Minecraft: PE protocol version
	 */

	public const CURRENT_PROTOCOL = 110;
	public const ACCEPTED_PROTOCOLS = [ProtocolInfo::PROTOCOL_290, ProtocolInfo::PROTOCOL_291, ProtocolInfo::PROTOCOL_310, ProtocolInfo::PROTOCOL_311, ProtocolInfo::PROTOCOL_312, ProtocolInfo::PROTOCOL_313, ProtocolInfo::PROTOCOL_330, ProtocolInfo::PROTOCOL_331, ProtocolInfo::PROTOCOL_332, ProtocolInfo::PROTOCOL_340, ProtocolInfo::PROTOCOL_342, ProtocolInfo::PROTOCOL_350, ProtocolInfo::PROTOCOL_351, ProtocolInfo::PROTOCOL_352, ProtocolInfo::PROTOCOL_353, ProtocolInfo::PROTOCOL_354, ProtocolInfo::PROTOCOL_360, ProtocolInfo::PROTOCOL_361];
	public const MINECRAFT_VERSION = 'v1.x';
	public const MINECRAFT_VERSION_NETWORK = '1.x';

	/** 1.0.x - 1.1.x OLD */
	public const PROTOCOL_101 = 101; // 1.0.3.0, 1.0.4, 1.0.4.0
	public const PROTOCOL_102 = 102; // 1.0.4.1, 1.0.4
	public const PROTOCOL_105 = 105; // 1.0.5.0, 1.0.5.3, 1.0.5.11, 1.0.5, 1.0.6.0
	public const PROTOCOL_106 = 106; // 1.0.6
	public const PROTOCOL_107 = 107; // 1.0.7, 1.0.8, 1.0.9
	public const PROTOCOL_110 = 110; // 1.1.0.0, 1.1.0.1, 1.1.0.2, 1.1.0.3, 1.1.0.4, 1.1.0.5
	public const PROTOCOL_111 = 111; // 1.1.0.8
	public const PROTOCOL_112 = 112; // 1.1.0.9
	public const PROTOCOL_113 = 113; // 1.1.0, 1.1.1.0, 1.1.1.1, 1.1.1, 1.1.2, 1.1.3.0, 1.1.3.1, 1.1.3, 1.1.4, 1.1.5, 1.1.7
	/** OUTDATED (supporting will be removed with next release, may didn't work properly)*/
	public const PROTOCOL_120 = 120; // 1.2.0.xx (beta)
	public const PROTOCOL_121 = 121; // 1.2.0.xx (beta)
	public const PROTOCOL_130 = 130; // 1.2.0.xx (beta)
	public const PROTOCOL_131 = 131; // 1.2.0.xx (beta)
	public const PROTOCOL_132 = 132; // 1.2.0.15 (beta)
	public const PROTOCOL_133 = 133; // 1.2.0.18 (beta)
	/** 1.2 BETAS */
	public const PROTOCOL_280 = 280; // 1.6.0.1
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
	 * Minecraft: PE packets
	 */
	public const LOGIN_PACKET = 0x01;
	public const PLAY_STATUS_PACKET = 0x02;
	public const CLIENT_TO_SERVER_HANDSHAKE_PACKET = 0x04;
	public const DISCONNECT_PACKET = 0x05;
	public const BATCH_PACKET = 0xfe;
	public const RESOURCE_PACKS_INFO_PACKET = 0x06;
	public const RESOURCE_PACK_STACK_PACKET = 0x07;
	public const RESOURCE_PACK_CLIENT_RESPONSE_PACKET = 0x08;
	public const TEXT_PACKET = 0x0a;
	public const SET_TIME_PACKET = 0x0b;
	public const START_GAME_PACKET = 0x0c;
	public const ADD_PLAYER_PACKET = 0x0d;
	public const ADD_ACTOR_PACKET = 0x0e;
	public const REMOVE_ACTOR_PACKET = 0x0f;
	public const ADD_ITEM_ACTOR_PACKET = 0x10;
//	public const ADD_HANGING_ACTOR_PACKET = 0x11;
	public const TAKE_ITEM_ACTOR_PACKET = 0x12;
	public const MOVE_ACTOR_ABSOLUTE_PACKET = 0x13;
	public const MOVE_PLAYER_PACKET = 0x14;
//	public const RIDER_JUMP_PACKET = 0x15;
	public const REMOVE_BLOCK_PACKET = 0x16;
	public const UPDATE_BLOCK_PACKET = 0x17;
	public const ADD_PAINTING_PACKET = 0x18;
	public const EXPLODE_PACKET = 0x19;
	public const LEVEL_SOUND_EVENT_PACKET = 0x1a;
	public const LEVEL_EVENT_PACKET = 0x1b;
	public const BLOCK_EVENT_PACKET = 0x1c;
	public const ACTOR_EVENT_PACKET = 0x1d;
	public const MOB_EFFECT_PACKET = 0x1e;
	public const UPDATE_ATTRIBUTES_PACKET = 0x1f;
	public const MOB_EQUIPMENT_PACKET = 0x20;
	public const MOB_ARMOR_EQUIPMENT_PACKET = 0x21;
	public const INTERACT_PACKET = 0x22;
	public const USE_ITEM_PACKET = 0x23;
	public const PLAYER_ACTION_PACKET = 0x24;
//	public const PLAYER_FALL = 0x25;
	public const HURT_ARMOR_PACKET = 0x26;
	public const SET_ACTOR_DATA_PACKET = 0x27;
	public const SET_ACTOR_MOTION_PACKET = 0x28;
	public const SET_ACTOR_LINK_PACKET = 0x29;
	public const SET_HEALTH_PACKET = 0x2a;
	public const SET_SPAWN_POSITION_PACKET = 0x2b;
	public const ANIMATE_PACKET = 0x2c;
	public const RESPAWN_PACKET = 0x2d;
	public const DROP_ITEM_PACKET = 0x2e;
//	public const INVENTORY_ACTION_PACKET = 0x2f;
	public const CONTAINER_OPEN_PACKET = 0x30;
	public const CONTAINER_CLOSE_PACKET = 0x31;
	public const CONTAINER_SET_SLOT_PACKET = 0x32;
	public const CONTAINER_SET_DATA_PACKET = 0x33;
	public const CONTAINER_SET_CONTENT_PACKET = 0x34;
	public const CRAFTING_DATA_PACKET = 0x35;
	public const CRAFTING_EVENT_PACKET = 0x36;
	public const ADVENTURE_SETTINGS_PACKET = 0x37;
	public const BLOCK_ACTOR_DATA_PACKET = 0x38;
	public const PLAYER_INPUT_PACKET = 0x39;
	public const LEVEL_CHUNK_PACKET = 0x3a;
	public const SET_COMMANDS_ENABLED_PACKET = 0x3b;
	public const SET_DIFFICULTY_PACKET = 0x3c;
	public const CHANGE_DIMENSION_PACKET = 0x3d;
	public const SET_PLAYER_GAMETYPE_PACKET = 0x3e;
	public const PLAYER_LIST_PACKET = 0x3f;
//	public const TELEMETRY_EVENT_PACKET = 0x40; // ??? EVENT_PACKET in 0.15.90.8
	public const SPAWN_EXPERIENCE_ORB_PACKET = 0x42;
	public const CLIENTBOUND_MAP_ITEM_DATA_PACKET = 0x43;
	public const MAP_INFO_REQUEST_PACKET = 0x44;
	public const REQUEST_CHUNK_RADIUS_PACKET = 0x45;
	public const CHUNK_RADIUS_UPDATED_PACKET = 0x46;
	public const ITEM_FRAME_DROP_ITEM_PACKET = 0x47;
//	public const REPLACE_SELECTED_ITEM_PACKET = 0x47;
	public const GAME_RULES_CHANGED_PACKET = 0x48;
//	public const CAMERA_PACKET = 0x49;
//	public const ADD_ITEM_PACKET = 0x4a;
	public const BOSS_EVENT_PACKET = 0x4b;
//	public const SHOW_CREDITS_PACKET = 0x4c;
	public const AVAILABLE_COMMANDS_PACKET = 0x4d;
	public const COMMAND_STEP_PACKET = 0x4e;
	public const RESOURCE_PACK_DATA_INFO_PACKET = 0x52;
	public const RESOURCE_PACK_CHUNK_DATA_PACKET = 0x53;
	public const RESOURCE_PACK_CHUNK_REQUEST_PACKET = 0x54;
	public const TRANSFER_PACKET = 0x56;
	public const SET_LOCAL_PLAYER_AS_INITIALIZED_PACKET = 0x70;    // NEW 274
}