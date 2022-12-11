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

namespace pocketmine\network\mcpe\multiversion;

use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\ProtocolInfo;

class MetadataConvertor {

    public const FLAGS = [
        ProtocolInfo::PROTOCOL_110 => ["DATA_FLAG_CAN_CLIMB" => 0x13, "DATA_FLAG_SWIMMER" => 0x14, "DATA_FLAG_CAN_FLY" => 0x15, "DATA_FLAG_RESTING" => 0x16, "DATA_FLAG_SITTING" => 0x17, "DATA_FLAG_ANGRY" => 0x18, "DATA_FLAG_INTERESTED" => 0x19, "DATA_FLAG_CHARGED" => 0x1a, "DATA_FLAG_TAMED" => 0x1b, "DATA_FLAG_LEASHED" => 0x1c, "DATA_FLAG_SHEARED" => 0x1d, "DATA_FLAG_GLIDING" => 0x1e, "DATA_FLAG_ELDER" => 0x1f, "DATA_FLAG_MOVING" => 0x20, "DATA_FLAG_BREATHING" => 0x21, "DATA_FLAG_CHESTED" => 0x22, "DATA_FLAG_STACKABLE" => 0x23, "DATA_FLAG_SHOWBASE" => 0x24, "DATA_FLAG_REARING" => 0x25, "DATA_FLAG_VIBRATING" => 0x26, "DATA_FLAG_IDLING" => 0x27, "DATA_FLAG_EVOKER_SPELL" => 0x28, "DATA_FLAG_CHARGE_ATTACK" => 0x29, "DATA_FLAG_WASD_CONTROLLED" => 0x2a, "DATA_FLAG_CAN_POWER_JUMP" => 0x2b, "DATA_FLAG_LINGER" => 0x2d],
        ProtocolInfo::PROTOCOL_130 => ["DATA_FLAG_LINGER" => 0x2c, "DATA_FLAG_HAS_COLLISION" => 0x2d, "DATA_FLAG_AFFECTED_BY_GRAVITY" => 0x2e, "DATA_FLAG_FIRE_IMMUNE" => 0x2f, "DATA_FLAG_DANCING" => 0x30],
        ProtocolInfo::PROTOCOL_221 => ["DATA_FLAG_WALKER" => 0x16, "DATA_FLAG_RESTING" => 0x17, "DATA_FLAG_SITTING" => 0x18, "DATA_FLAG_ANGRY" => 0x19, "DATA_FLAG_INTERESTED" => 0x1a, "DATA_FLAG_CHARGED" => 0x1b, "DATA_FLAG_TAMED" => 0x1c, "DATA_FLAG_LEASHED" => 0x1d, "DATA_FLAG_SHEARED" => 0x1e, "DATA_FLAG_GLIDING" => 0x1f, "DATA_FLAG_ELDER" => 0x20, "DATA_FLAG_MOVING" => 0x21, "DATA_FLAG_BREATHING" => 0x22, "DATA_FLAG_CHESTED" => 0x23, "DATA_FLAG_STACKABLE" => 0x24, "DATA_FLAG_SHOWBASE" => 0x25, "DATA_FLAG_REARING" => 0x26, "DATA_FLAG_VIBRATING" => 0x27, "DATA_FLAG_IDLING" => 0x28, "DATA_FLAG_EVOKER_SPELL" => 0x29, "DATA_FLAG_CHARGE_ATTACK" => 0x2a, "DATA_FLAG_WASD_CONTROLLED" => 0x2b, "DATA_FLAG_CAN_POWER_JUMP" => 0x2c, "DATA_FLAG_LINGER" => 0x2d, "DATA_FLAG_HAS_COLLISION" => 0x2e, "DATA_FLAG_AFFECTED_BY_GRAVITY" => 0x2f, "DATA_FLAG_FIRE_IMMUNE" => 0x30, "DATA_FLAG_DANCING" => 0x31, "DATA_FLAG_ENCHANTED" => 0x32, "DATA_FLAG_SHOW_TRIDENT_ROPE" => 0x33, "DATA_FLAG_CONTAINER_PRIVATE" => 0x34, "DATA_FLAG_TRANSFORMING" => 0x35, "DATA_FLAG_SPIN_ATTACK" => 0x36, "DATA_FLAG_SWIMMING" => 0x37, "DATA_FLAG_BRIBED" => 0x38],
        ProtocolInfo::PROTOCOL_290 => ["DATA_FLAG_ORPHANED" => 0x1d, "DATA_FLAG_LEASHED" => 0x1e, "DATA_FLAG_SHEARED" => 0x1f, "DATA_FLAG_GLIDING" => 0x20, "DATA_FLAG_ELDER" => 0x21, "DATA_FLAG_MOVING" => 0x22, "DATA_FLAG_BREATHING" => 0x23, "DATA_FLAG_CHESTED" => 0x24, "DATA_FLAG_STACKABLE" => 0x25, "DATA_FLAG_SHOWBASE" => 0x26, "DATA_FLAG_REARING" => 0x27, "DATA_FLAG_VIBRATING" => 0x28, "DATA_FLAG_IDLING" => 0x29, "DATA_FLAG_EVOKER_SPELL" => 0x2a, "DATA_FLAG_CHARGE_ATTACK" => 0x2b, "DATA_FLAG_WASD_CONTROLLED" => 0x2c, "DATA_FLAG_CAN_POWER_JUMP" => 0x2d, "DATA_FLAG_LINGER" => 0x2e, "DATA_FLAG_HAS_COLLISION" => 0x2f, "DATA_FLAG_AFFECTED_BY_GRAVITY" => 0x30, "DATA_FLAG_FIRE_IMMUNE" => 0x31, "DATA_FLAG_DANCING" => 0x32, "DATA_FLAG_ENCHANTED" => 0x33, "DATA_FLAG_SHOW_TRIDENT_ROPE" => 0x34, "DATA_FLAG_CONTAINER_PRIVATE" => 0x35, "DATA_FLAG_TRANSFORMING" => 0x36, "DATA_FLAG_SPIN_ATTACK" => 0x37, "DATA_FLAG_SWIMMING" => 0x38, "DATA_FLAG_BRIBED" => 0x39, "DATA_FLAG_PREGNANT" => 0x3a, "DATA_FLAG_LAYING_EGG" => 0x3b, "DATA_FLAG_RIDER_CAN_PICK" => 0x3c, "DATA_FLAG_TRANSITION_SITTING" => 0x3d, "DATA_FLAG_EATING" => 0x3e, "DATA_FLAG_LAYING_DOWN" => 0x3f, "DATA_FLAG_SNEEZING" => 0x40, "DATA_FLAG_TRUSTING" => 0x41, "DATA_FLAG_ROLLING" => 0x42, "DATA_FLAG_SCARED" => 0x43, "DATA_FLAG_IN_SCAFFOLDING" => 0x44, "DATA_FLAG_OVER_SCAFFOLDING" => 0x45, "DATA_FLAG_FALL_THROUGH_SCAFFOLDING" => 0x46, "DATA_FLAG_BLOCKING" => 0x47, "DATA_FLAG_DISABLE_BLOCKING" => 0x48, "DATA_FLAG_SLEEPING" => 0x4a, "DATA_FLAG_TRADE_INTEREST" => 0x4c, "DATA_FLAG_DOOR_BREAKER" => 0x4d, "DATA_FLAG_BREAKING_OBSTRUCTION" => 0x4e, "DATA_FLAG_DOOR_OPENER" => 0x4f],
        ProtocolInfo::PROTOCOL_354 => ["DATA_FLAG_SLEEPING" => 0x4b, "DATA_FLAG_TRADE_INTEREST" => 0x4d, "DATA_FLAG_DOOR_BREAKER" => 0x4e, "DATA_FLAG_BREAKING_OBSTRUCTION" => 0x4f, "DATA_FLAG_DOOR_OPENER" => 0x50, "DATA_FLAG_ILLAGER_CAPTAIN" => 0x51, "DATA_FLAG_STUNNED" => 0x52, "DATA_FLAG_ROARING" => 0x53, "DATA_FLAG_DELAYED_ATTACKING" => 0x54, "DATA_FLAG_AVOIDING_MOBS" => 0x55, "DATA_FLAG_RANGED_ATTACK" => 0x56, "DATA_FLAG_STALK_AND_POUNCE_ON_TARGET" => 0x59, "DATA_FLAG_EMOTING_STATUS" => 0x5a, "DATA_FLAG_RAIDER_CELEBRATION" => 0x5b]
    ];
    
    public const METADATA = [
        ProtocolInfo::PROTOCOL_110 => ["DATA_NPC_SKIN_INDEX" => 0x29, "DATA_NPC_ACTIONS" => 0x2a, "DATA_MAX_AIR" => 0x2b, "DATA_MARK_VARIANT" => 0x2c, "DATA_BLOCK_TARGET" => 0x30, "DATA_WITHER_INVULNERABLE_TICKS" => 0x31, "DATA_WITHER_TARGET_1" => 0x32, "DATA_WITHER_TARGET_2" => 0x33, "DATA_WITHER_TARGET_3" => 0x34, "DATA_BOUNDING_BOX_WIDTH" => 0x36, "DATA_BOUNDING_BOX_HEIGHT" => 0x37, "DATA_FUSE_LENGTH" => 0x38, "DATA_RIDER_SEAT_POSITION" => 0x39, "DATA_RIDER_ROTATION_LOCKED" => 0x3a, "DATA_RIDER_MAX_ROTATION" => 0x3b, "DATA_RIDER_MIN_ROTATION" => 0x3c, "DATA_AREA_EFFECT_CLOUD_RADIUS" => 0x3d, "DATA_AREA_EFFECT_CLOUD_WAITING" => 0x3e, "DATA_AREA_EFFECT_CLOUD_PARTICLE_ID" => 0x3f, "DATA_SHULKER_ATTACH_FACE" => 0x41, "DATA_SHULKER_ATTACH_POS" => 0x43, "DATA_TRADING_PLAYER_EID" => 0x44, "DATA_COMMAND_BLOCK_COMMAND" => 0x47, "DATA_COMMAND_BLOCK_LAST_OUTPUT" => 0x48, "DATA_COMMAND_BLOCK_TRACK_OUTPUT" => 0x49, "DATA_CONTROLLING_RIDER_SEAT_NUMBER" => 0x4a, "DATA_STRENGTH" => 0x4b, "DATA_MAX_STRENGTH" => 0x4c],
        ProtocolInfo::PROTOCOL_220 => ["DATA_ENTITY_AGE" => 0x18, "DATA_PLAYER_FLAGS" => 0x1a, "DATA_PLAYER_INDEX" => 0x1b, "DATA_PLAYER_BED_POSITION" => 0x1c, "DATA_FIREBALL_POWER_X" => 0x1d, "DATA_FIREBALL_POWER_Y" => 0x1e, "DATA_FIREBALL_POWER_Z" => 0x1f, "DATA_POTION_AUX_VALUE" => 0x24, "DATA_LEAD_HOLDER_EID" => 0x25, "DATA_SCALE" => 0x26, "DATA_INTERACTIVE_TAG" => 0x27, "DATA_NPC_SKIN_INDEX" => 0x28, "DATA_NPC_ACTIONS" => 0x29, "DATA_MAX_AIR" => 0x2a, "DATA_MARK_VARIANT" => 0x2b, "DATA_CONTAINER_TYPE" => 0x2c, "DATA_CONTAINER_BASE_SIZE" => 0x2d, "DATA_CONTAINER_EXTRA_SLOTS_PER_STRENGTH" => 0x2e, "DATA_BLOCK_TARGET" => 0x2f, "DATA_WITHER_INVULNERABLE_TICKS" => 0x30, "DATA_WITHER_TARGET_1" => 0x31, "DATA_WITHER_TARGET_2" => 0x32, "DATA_WITHER_TARGET_3" => 0x33, "DATA_BOUNDING_BOX_WIDTH" => 0x35, "DATA_BOUNDING_BOX_HEIGHT" => 0x36, "DATA_FUSE_LENGTH" => 0x37, "DATA_RIDER_SEAT_POSITION" => 0x38, "DATA_RIDER_ROTATION_LOCKED" => 0x39, "DATA_RIDER_MAX_ROTATION" => 0x3a, "DATA_RIDER_MIN_ROTATION" => 0x3b, "DATA_AREA_EFFECT_CLOUD_RADIUS" => 0x3c, "DATA_AREA_EFFECT_CLOUD_WAITING" => 0x3d, "DATA_AREA_EFFECT_CLOUD_PARTICLE_ID" => 0x3e, "DATA_SHULKER_ATTACH_FACE" => 0x40, "DATA_SHULKER_ATTACH_POS" => 0x42, "DATA_TRADING_PLAYER_EID" => 0x43, "DATA_COMMAND_BLOCK_COMMAND" => 0x46, "DATA_COMMAND_BLOCK_LAST_OUTPUT" => 0x47, "DATA_COMMAND_BLOCK_TRACK_OUTPUT" => 0x48, "DATA_CONTROLLING_RIDER_SEAT_NUMBER" => 0x49, "DATA_STRENGTH" => 0x4a, "DATA_MAX_STRENGTH" => 0x4b, "DATA_LIMITED_LIFE" => 0x4d, "DATA_ARMOR_STAND_POSE_INDEX" => 0x4e, "DATA_ENDER_CRYSTAL_TIME_OFFSET" => 0x4f, "DATA_ALWAYS_SHOW_NAMETAG" => 0x50, "DATA_COLOR_2" => 0x51, "DATA_SCORE_TAG" => 0x53, "DATA_BALLOON_ATTACHED_ENTITY" => 0x54, "DATA_PUFFERFISH_SIZE" => 0x55, "DATA_BOAT_BUBBLE_TIME" => 0x56, "DATA_PLAYER_AGENT_EID" => 0x57, "DATA_EAT_COUNTER" => 0x5a, "DATA_FLAGS2" => 0x5b, "DATA_AREA_EFFECT_CLOUD_DURATION" => 0x5e, "DATA_AREA_EFFECT_CLOUD_SPAWN_TIME" => 0x5f, "DATA_AREA_EFFECT_CLOUD_RADIUS_PER_TICK" => 0x60, "DATA_AREA_EFFECT_CLOUD_RADIUS_CHANGE_ON_PICKUP" => 0x61, "DATA_AREA_EFFECT_CLOUD_PICKUP_COUNT" => 0x62],
        ProtocolInfo::PROTOCOL_340 => ["DATA_HAS_NPC_COMPONENT" => 0x27, "DATA_INTERACTIVE_TAG" => 0x63, "DATA_TRADE_TIER" => 0x64, "DATA_MAX_TRADE_TIER" => 0x65, "DATA_TRADE_XP" => 0x66],
        ProtocolInfo::PROTOCOL_354 => ["DATA_SKIN_ID" => 0x28, "DATA_NPC_SKIN_INDEX" => 0x29, "DATA_NPC_ACTIONS" => 0x2a, "DATA_MAX_AIR" => 0x2b, "DATA_MARK_VARIANT" => 0x2c, "DATA_CONTAINER_TYPE" => 0x2d, "DATA_CONTAINER_BASE_SIZE" => 0x2e, "DATA_CONTAINER_EXTRA_SLOTS_PER_STRENGTH" => 0x2f, "DATA_BLOCK_TARGET" => 0x30, "DATA_WITHER_INVULNERABLE_TICKS" => 0x31, "DATA_WITHER_TARGET_1" => 0x32, "DATA_WITHER_TARGET_2" => 0x33, "DATA_WITHER_TARGET_3" => 0x34, "DATA_BOUNDING_BOX_WIDTH" => 0x36, "DATA_BOUNDING_BOX_HEIGHT" => 0x37, "DATA_FUSE_LENGTH" => 0x38, "DATA_RIDER_SEAT_POSITION" => 0x39, "DATA_RIDER_ROTATION_LOCKED" => 0x3a, "DATA_RIDER_MAX_ROTATION" => 0x3b, "DATA_RIDER_MIN_ROTATION" => 0x3c, "DATA_AREA_EFFECT_CLOUD_RADIUS" => 0x3d, "DATA_AREA_EFFECT_CLOUD_WAITING" => 0x3e, "DATA_AREA_EFFECT_CLOUD_PARTICLE_ID" => 0x3f, "DATA_SHULKER_ATTACH_FACE" => 0x41, "DATA_SHULKER_ATTACH_POS" => 0x43, "DATA_TRADING_PLAYER_EID" => 0x44, "DATA_COMMAND_BLOCK_COMMAND" => 0x47, "DATA_COMMAND_BLOCK_LAST_OUTPUT" => 0x48, "DATA_COMMAND_BLOCK_TRACK_OUTPUT" => 0x49, "DATA_CONTROLLING_RIDER_SEAT_NUMBER" => 0x4a, "DATA_STRENGTH" => 0x4b, "DATA_MAX_STRENGTH" => 0x4c, "DATA_LIMITED_LIFE" => 0x4e, "DATA_ARMOR_STAND_POSE_INDEX" => 0x4f, "DATA_ENDER_CRYSTAL_TIME_OFFSET" => 0x50, "DATA_ALWAYS_SHOW_NAMETAG" => 0x51, "DATA_COLOR_2" => 0x52, "DATA_SCORE_TAG" => 0x54, "DATA_BALLOON_ATTACHED_ENTITY" => 0x55, "DATA_PUFFERFISH_SIZE" => 0x56, "DATA_BOAT_BUBBLE_TIME" => 0x57, "DATA_PLAYER_AGENT_EID" => 0x58, "DATA_EAT_COUNTER" => 0x5b, "DATA_FLAGS2" => 0x5c, "DATA_AREA_EFFECT_CLOUD_DURATION" => 0x5f, "DATA_AREA_EFFECT_CLOUD_SPAWN_TIME" => 0x60, "DATA_AREA_EFFECT_CLOUD_RADIUS_PER_TICK" => 0x61, "DATA_AREA_EFFECT_CLOUD_RADIUS_CHANGE_ON_PICKUP" => 0x62, "DATA_AREA_EFFECT_CLOUD_PICKUP_COUNT" => 0x63, "DATA_INTERACTIVE_TAG" => 0x64, "DATA_TRADE_TIER" => 0x65, "DATA_MAX_TRADE_TIER" => 0x66, "DATA_TRADE_XP" => 0x67],
        ProtocolInfo::PROTOCOL_360 => ["DATA_NPC_SKIN_INDEX" => 0x28, "DATA_NPC_ACTIONS" => 0x29, "DATA_MAX_AIR" => 0x2a, "DATA_MARK_VARIANT" => 0x2b, "DATA_CONTAINER_TYPE" => 0x2c, "DATA_CONTAINER_BASE_SIZE" => 0x2d, "DATA_CONTAINER_EXTRA_SLOTS_PER_STRENGTH" => 0x2e, "DATA_BLOCK_TARGET" => 0x2f, "DATA_WITHER_INVULNERABLE_TICKS" => 0x30, "DATA_WITHER_TARGET_1" => 0x31, "DATA_WITHER_TARGET_2" => 0x32, "DATA_WITHER_TARGET_3" => 0x33, "DATA_BOUNDING_BOX_WIDTH" => 0x35, "DATA_BOUNDING_BOX_HEIGHT" => 0x36, "DATA_FUSE_LENGTH" => 0x37, "DATA_RIDER_SEAT_POSITION" => 0x38, "DATA_RIDER_ROTATION_LOCKED" => 0x39, "DATA_RIDER_MAX_ROTATION" => 0x3a, "DATA_RIDER_MIN_ROTATION" => 0x3b, "DATA_AREA_EFFECT_CLOUD_RADIUS" => 0x3c, "DATA_AREA_EFFECT_CLOUD_WAITING" => 0x3d, "DATA_AREA_EFFECT_CLOUD_PARTICLE_ID" => 0x3e, "DATA_SHULKER_ATTACH_FACE" => 0x40, "DATA_SHULKER_ATTACH_POS" => 0x42, "DATA_TRADING_PLAYER_EID" => 0x43, "DATA_COMMAND_BLOCK_COMMAND" => 0x46, "DATA_COMMAND_BLOCK_LAST_OUTPUT" => 0x47, "DATA_COMMAND_BLOCK_TRACK_OUTPUT" => 0x48, "DATA_CONTROLLING_RIDER_SEAT_NUMBER" => 0x49, "DATA_STRENGTH" => 0x4a, "DATA_MAX_STRENGTH" => 0x4b, "DATA_LIMITED_LIFE" => 0x4d, "DATA_ARMOR_STAND_POSE_INDEX" => 0x4e, "DATA_ENDER_CRYSTAL_TIME_OFFSET" => 0x4f, "DATA_ALWAYS_SHOW_NAMETAG" => 0x50, "DATA_COLOR_2" => 0x51, "DATA_SCORE_TAG" => 0x53, "DATA_BALLOON_ATTACHED_ENTITY" => 0x54, "DATA_PUFFERFISH_SIZE" => 0x55, "DATA_BOAT_BUBBLE_TIME" => 0x56, "DATA_PLAYER_AGENT_EID" => 0x57, "DATA_EAT_COUNTER" => 0x5a, "DATA_FLAGS2" => 0x5b, "DATA_AREA_EFFECT_CLOUD_DURATION" => 0x5e, "DATA_AREA_EFFECT_CLOUD_SPAWN_TIME" => 0x5f, "DATA_AREA_EFFECT_CLOUD_RADIUS_PER_TICK" => 0x60, "DATA_AREA_EFFECT_CLOUD_RADIUS_CHANGE_ON_PICKUP" => 0x61, "DATA_AREA_EFFECT_CLOUD_PICKUP_COUNT" => 0x62, "DATA_INTERACTIVE_TAG" => 0x63, "DATA_TRADE_TIER" => 0x64, "DATA_MAX_TRADE_TIER" => 0x65, "DATA_TRADE_XP" => 0x66, "DATA_SKIN_ID" => 0x67, "DATA_COMMAND_BLOCK_TICK_DELAY" => 0x69, "DATA_COMMAND_BLOCK_EXECUTE_ON_FIRST_TICK" => 0x6a, "DATA_AMBIENT_SOUND_INTERVAL_MIN" => 0x6b, "DATA_AMBIENT_SOUND_INTERVAL_RANGE" => 0x6c, "DATA_AMBIENT_SOUND_EVENT" => 0x6d]
    ];

    protected static $protocolFlags = [];
    protected static $protocolMetadata = [];

	public static function init() : void{
		$oClass = new \ReflectionClass('pocketmine\entity\Entity');
		$initialMetadata = $oClass->getConstants();

        $flags = self::FLAGS;
        $metadata = self::METADATA;

		foreach ($flags[ProtocolInfo::PROTOCOL_110] as $key => $value) {
			if (isset($initialMetadata[$key])) {
				self::$protocolFlags[ProtocolInfo::PROTOCOL_110][$initialMetadata[$key]] = $value;
			}
		}

		foreach ($flags[ProtocolInfo::PROTOCOL_130] as $key => $value) {
			if (isset($initialMetadata[$key])) {
				self::$protocolFlags[ProtocolInfo::PROTOCOL_130][$initialMetadata[$key]] = $value;
			}
		}

		foreach ($flags[ProtocolInfo::PROTOCOL_221] as $key => $value) {
			if (isset($initialMetadata[$key])) {
				self::$protocolFlags[ProtocolInfo::PROTOCOL_221][$initialMetadata[$key]] = $value;
			}
		}

		foreach ($flags[ProtocolInfo::PROTOCOL_290] as $key => $value) {
			if (isset($initialMetadata[$key])) {
				self::$protocolFlags[ProtocolInfo::PROTOCOL_290][$initialMetadata[$key]] = $value;
			}
		}

		foreach ($flags[ProtocolInfo::PROTOCOL_354] as $key => $value) {
			if (isset($initialMetadata[$key])) {
				self::$protocolFlags[ProtocolInfo::PROTOCOL_354][$initialMetadata[$key]] = $value;
			}
		}

		foreach ($metadata[ProtocolInfo::PROTOCOL_110] as $key => $value) {
			if (isset($initialMetadata[$key])) {
				self::$protocolMetadata[ProtocolInfo::PROTOCOL_110][$initialMetadata[$key]] = $value;
			}
		}

		foreach ($metadata[ProtocolInfo::PROTOCOL_220] as $key => $value) {
			if (isset($initialMetadata[$key])) {
				self::$protocolMetadata[ProtocolInfo::PROTOCOL_220][$initialMetadata[$key]] = $value;
			}
		}

		foreach ($metadata[ProtocolInfo::PROTOCOL_340] as $key => $value) {
			if (isset($initialMetadata[$key])) {
				self::$protocolMetadata[ProtocolInfo::PROTOCOL_340][$initialMetadata[$key]] = $value;
			}
		}

		foreach ($metadata[ProtocolInfo::PROTOCOL_354] as $key => $value) {
			if (isset($initialMetadata[$key])) {
				self::$protocolMetadata[ProtocolInfo::PROTOCOL_354][$initialMetadata[$key]] = $value;
			}
		}

		foreach ($metadata[ProtocolInfo::PROTOCOL_360] as $key => $value) {
			if (isset($initialMetadata[$key])) {
				self::$protocolMetadata[ProtocolInfo::PROTOCOL_360][$initialMetadata[$key]] = $value;
			}
		}
	}

    /**
     * @param array $metadata
     * @param int $playerProtocol
     * 
     * @return array
     */
	public static function updateMetadata(array $metadata, int $playerProtocol) : array{
		$metadata = self::updateEntityFlags($metadata, $playerProtocol);
		$metadata = self::updateMetadataIds($metadata, $playerProtocol);

		return $metadata;
	}

    /**
     * @param array $metadata
     * @param int $playerProtocol
     * 
     * @return array
     */
	private static function updateMetadataIds(array $metadata, int $playerProtocol) : array{
		switch ($playerProtocol) {
			case ProtocolInfo::PROTOCOL_361:
			case ProtocolInfo::PROTOCOL_360:
			    $protocolMetadata = self::$protocolMetadata[ProtocolInfo::PROTOCOL_360];
			    break;
			case ProtocolInfo::PROTOCOL_354:
			    $protocolMetadata = self::$protocolMetadata[ProtocolInfo::PROTOCOL_354];
			    break;
			case ProtocolInfo::PROTOCOL_351:
			case ProtocolInfo::PROTOCOL_350:
			case ProtocolInfo::PROTOCOL_342:
			case ProtocolInfo::PROTOCOL_340:
			    $protocolMetadata = self::$protocolMetadata[ProtocolInfo::PROTOCOL_340];
			    break;
			case ProtocolInfo::PROTOCOL_332:
			case ProtocolInfo::PROTOCOL_331:
			case ProtocolInfo::PROTOCOL_330:
			case ProtocolInfo::PROTOCOL_311:
			case ProtocolInfo::PROTOCOL_310:
			case ProtocolInfo::PROTOCOL_290:
			case ProtocolInfo::PROTOCOL_282:
			case ProtocolInfo::PROTOCOL_280:
			case ProtocolInfo::PROTOCOL_274:
			case ProtocolInfo::PROTOCOL_273:
			case ProtocolInfo::PROTOCOL_271:
			case ProtocolInfo::PROTOCOL_260:
			case ProtocolInfo::PROTOCOL_240:
			case ProtocolInfo::PROTOCOL_221:
			case ProtocolInfo::PROTOCOL_220:
				$protocolMetadata = self::$protocolMetadata[ProtocolInfo::PROTOCOL_220];
				break;
			case ProtocolInfo::PROTOCOL_200:
			case ProtocolInfo::PROTOCOL_140:
			case ProtocolInfo::PROTOCOL_135:
			case ProtocolInfo::PROTOCOL_134:
			case ProtocolInfo::PROTOCOL_133:
			case ProtocolInfo::PROTOCOL_132:
			case ProtocolInfo::PROTOCOL_131:
			case ProtocolInfo::PROTOCOL_130:
			case ProtocolInfo::PROTOCOL_110:
			    $protocolMetadata = self::$protocolMetadata[ProtocolInfo::PROTOCOL_110];
			    break;
			default:
			    $protocolMetadata = self::$protocolMetadata[ProtocolInfo::PROTOCOL_360];
			    break;
		}

		$newMetadata = [];
		foreach ($metadata as $key => $value) {
			if (isset($protocolMetadata[$key])) {
				$newMetadata[$protocolMetadata[$key]] = $value;
			} else {
				$newMetadata[$key] = $value;
			}
		}

		return $newMetadata;
	}

    /**
     * @param array $metadata
     * @param int $playerProtocol
     * 
     * @return array
     */
	private static function updateEntityFlags(array $metadata, int $playerProtocol) : array{
		if (!isset($metadata[Entity::DATA_FLAGS])) {
			return $metadata;
		}

		switch ($playerProtocol) {
			case ProtocolInfo::PROTOCOL_361:
			case ProtocolInfo::PROTOCOL_360:
			case ProtocolInfo::PROTOCOL_354:
			    $protocolFlags = self::$protocolFlags[ProtocolInfo::PROTOCOL_354];
			    break;
			case ProtocolInfo::PROTOCOL_351:
			case ProtocolInfo::PROTOCOL_350:
			case ProtocolInfo::PROTOCOL_342:
			case ProtocolInfo::PROTOCOL_340:
			case ProtocolInfo::PROTOCOL_332:
			case ProtocolInfo::PROTOCOL_331:
			case ProtocolInfo::PROTOCOL_330:
			case ProtocolInfo::PROTOCOL_311:
			case ProtocolInfo::PROTOCOL_310:
			case ProtocolInfo::PROTOCOL_290:
			    $protocolFlags = self::$protocolFlags[ProtocolInfo::PROTOCOL_290];
				break;
			case ProtocolInfo::PROTOCOL_282:
			case ProtocolInfo::PROTOCOL_280:
			case ProtocolInfo::PROTOCOL_274:
			case ProtocolInfo::PROTOCOL_273:
			case ProtocolInfo::PROTOCOL_271:
			case ProtocolInfo::PROTOCOL_260:
			case ProtocolInfo::PROTOCOL_240:
			case ProtocolInfo::PROTOCOL_221:
			    $protocolFlags = self::$protocolFlags[ProtocolInfo::PROTOCOL_221];
			    break;
			case ProtocolInfo::PROTOCOL_220:
			case ProtocolInfo::PROTOCOL_200:
			case ProtocolInfo::PROTOCOL_140:
			case ProtocolInfo::PROTOCOL_135:
			case ProtocolInfo::PROTOCOL_134:
			case ProtocolInfo::PROTOCOL_133:
			case ProtocolInfo::PROTOCOL_132:
			case ProtocolInfo::PROTOCOL_131:
			case ProtocolInfo::PROTOCOL_130:
			    $protocolFlags = self::$protocolFlags[ProtocolInfo::PROTOCOL_130];
			    break;
			case ProtocolInfo::PROTOCOL_110:
			    $protocolFlags = self::$protocolFlags[ProtocolInfo::PROTOCOL_110];
			    break;
			default:
			    $protocolFlags = self::$protocolFlags[ProtocolInfo::PROTOCOL_354];
			    break;
		}

        $newFlags = 0;

		$flags = strrev(decbin($metadata[Entity::DATA_FLAGS][1]));
		$flagsLength = strlen($flags);
		for ($i = 0; $i < $flagsLength; $i++) {
			if ($flags[$i] === '1') {
				$newFlags |= 1 << (isset($protocolFlags[$i]) ? $protocolFlags[$i] : $i);
			}
		}

		$metadata[Entity::DATA_FLAGS][1] = $newFlags;

		return $metadata;
	}

}
