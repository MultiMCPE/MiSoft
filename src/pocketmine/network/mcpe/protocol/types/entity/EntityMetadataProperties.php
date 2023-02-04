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

namespace pocketmine\network\mcpe\protocol\types\entity;

interface EntityMetadataProperties{
	/*
	 * Readers beware: this isn't a nice list. Some of the properties have different types for different entities, and
	 * are used for entirely different things.
	 */
    public const DATA_FLAGS = 0x0;
    public const DATA_HEALTH = 0x1;
    public const DATA_VARIANT = 0x2;
    public const DATA_COLOR = 0x3, DATA_COLOUR = 0x3;
    public const DATA_NAMETAG = 0x4;
    public const DATA_OWNER_EID = 0x5;
    public const DATA_TARGET_EID = 0x6;
    public const DATA_AIR = 0x7;
    public const DATA_POTION_COLOR = 0x8;
    public const DATA_POTION_AMBIENT = 0x9;
    public const DATA_JUMP_DURATION = 0xa;
    public const DATA_HURT_TIME = 0xb;
    public const DATA_HURT_DIRECTION = 0xc;
    public const DATA_PADDLE_TIME_LEFT = 0xd;
    public const DATA_PADDLE_TIME_RIGHT = 0xe;
    public const DATA_EXPERIENCE_VALUE = 0xf;
    public const DATA_MINECART_DISPLAY_BLOCK = 0x10;
    public const DATA_HORSE_FLAGS = 0x10;
    public const DATA_MINECART_DISPLAY_OFFSET = 0x11;
    public const DATA_SHOOTER_ID = 0x11;
    public const DATA_MINECART_HAS_DISPLAY = 0x12;
    public const DATA_HORSE_TYPE = 0x13;
    public const DATA_CHARGE_AMOUNT = 0x16;
    public const DATA_ENDERMAN_HELD_ITEM_ID = 0x17;
    public const DATA_ENTITY_AGE = 0x18;
    public const DATA_PLAYER_FLAGS = 0x1a;
    public const DATA_PLAYER_INDEX = 0x1b;
    public const DATA_PLAYER_BED_POSITION = 0x1c;
    public const DATA_FIREBALL_POWER_X = 0x1d;
    public const DATA_FIREBALL_POWER_Y = 0x1e;
    public const DATA_FIREBALL_POWER_Z = 0x1f;
    public const DATA_FISH_X = 0x21;
    public const DATA_FISH_Z = 0x22;
    public const DATA_FISH_ANGLE = 0x23;
    public const DATA_POTION_AUX_VALUE = 0x24;
    public const DATA_LEAD_HOLDER_EID = 0x25;
    public const DATA_SCALE = 0x26;
    public const DATA_HAS_NPC_COMPONENT = 0x27;
    public const DATA_NPC_SKIN_INDEX = 0x28;
    public const DATA_NPC_ACTIONS = 0x29;
    public const DATA_MAX_AIR = 0x2a;
    public const DATA_MARK_VARIANT = 0x2b;
    public const DATA_CONTAINER_TYPE = 0x2c;
    public const DATA_CONTAINER_BASE_SIZE = 0x2d;
    public const DATA_CONTAINER_EXTRA_SLOTS_PER_STRENGTH = 0x2e;
    public const DATA_BLOCK_TARGET = 0x2f;
    public const DATA_WITHER_INVULNERABLE_TICKS = 0x30;
    public const DATA_WITHER_TARGET_1 = 0x31;
    public const DATA_WITHER_TARGET_2 = 0x32;
    public const DATA_WITHER_TARGET_3 = 0x33;
    public const DATA_AERIAL_ATTACK = 0x34;
    public const DATA_BOUNDING_BOX_WIDTH = 0x35;
    public const DATA_BOUNDING_BOX_HEIGHT = 0x36;
    public const DATA_FUSE_LENGTH = 0x37;
    public const DATA_RIDER_SEAT_POSITION = 0x38;
    public const DATA_RIDER_ROTATION_LOCKED = 0x39;
    public const DATA_RIDER_MAX_ROTATION = 0x3a;
    public const DATA_RIDER_MIN_ROTATION = 0x3b;
    public const DATA_AREA_EFFECT_CLOUD_RADIUS = 0x3c;
    public const DATA_AREA_EFFECT_CLOUD_WAITING = 0x3d;
    public const DATA_AREA_EFFECT_CLOUD_PARTICLE_ID = 0x3e;
    public const DATA_SHULKER_ATTACH_FACE = 0x40;
    public const DATA_SHULKER_ATTACH_POS = 0x42;
    public const DATA_TRADING_PLAYER_EID = 0x43;
    public const DATA_HAS_COMMAND_BLOCK = 0x45;
    public const DATA_COMMAND_BLOCK_COMMAND = 0x46;
    public const DATA_COMMAND_BLOCK_LAST_OUTPUT = 0x47;
    public const DATA_COMMAND_BLOCK_TRACK_OUTPUT = 0x48;
    public const DATA_CONTROLLING_RIDER_SEAT_NUMBER = 0x49;
    public const DATA_STRENGTH = 0x4a;
    public const DATA_MAX_STRENGTH = 0x4b;
    public const DATA_SPELL_CASTING_COLOR = 0x4c;
    public const DATA_LIMITED_LIFE = 0x4d;
    public const DATA_ARMOR_STAND_POSE_INDEX = 0x4e;
    public const DATA_ENDER_CRYSTAL_TIME_OFFSET = 0x4f;
    public const DATA_ALWAYS_SHOW_NAMETAG = 0x50;
    public const DATA_COLOR_2 = 0x51;
    public const DATA_SCORE_TAG = 0x53;
    public const DATA_BALLOON_ATTACHED_ENTITY = 0x54;
    public const DATA_PUFFERFISH_SIZE = 0x55;
    public const DATA_BOAT_BUBBLE_TIME = 0x56;
    public const DATA_PLAYER_AGENT_EID = 0x57;
    public const DATA_EAT_COUNTER = 0x5a;
    public const DATA_FLAGS2 = 0x5b;
    public const DATA_AREA_EFFECT_CLOUD_DURATION = 0x5e;
    public const DATA_AREA_EFFECT_CLOUD_SPAWN_TIME = 0x5f;
    public const DATA_AREA_EFFECT_CLOUD_RADIUS_PER_TICK = 0x60;
    public const DATA_AREA_EFFECT_CLOUD_RADIUS_CHANGE_ON_PICKUP = 0x61;
    public const DATA_AREA_EFFECT_CLOUD_PICKUP_COUNT = 0x62;
    public const DATA_INTERACTIVE_TAG = 0x63;
    public const DATA_TRADE_TIER = 0x64;
    public const DATA_MAX_TRADE_TIER = 0x65;
    public const DATA_TRADE_XP = 0x66;
    public const DATA_SKIN_ID = 0x67;
    public const DATA_SPAWNING_FRAMES = 0x68;
    public const DATA_COMMAND_BLOCK_TICK_DELAY = 0x69;
    public const DATA_COMMAND_BLOCK_EXECUTE_ON_FIRST_TICK = 0x6a;
    public const DATA_AMBIENT_SOUND_INTERVAL_MIN = 0x6b;
    public const DATA_AMBIENT_SOUND_INTERVAL_RANGE = 0x6c;
    public const DATA_AMBIENT_SOUND_EVENT = 0x6d;
    public const DATA_FALL_DAMAGE_MULTIPLIER = 0x6e;
    public const DATA_NAME_RAW_TEXT = 0x6f;
    public const DATA_CAN_RIDE_TARGET = 0x70;
}