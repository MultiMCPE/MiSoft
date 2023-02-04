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

interface EntityMetadataFlags{
    public const DATA_FLAG_ONFIRE = 0x0;
    public const DATA_FLAG_SNEAKING = 0x1;
    public const DATA_FLAG_RIDING = 0x2;
    public const DATA_FLAG_SPRINTING = 0x3;
    public const DATA_FLAG_ACTION = 0x4;
    public const DATA_FLAG_INVISIBLE = 0x5;
    public const DATA_FLAG_TEMPTED = 0x6;
    public const DATA_FLAG_INLOVE = 0x7;
    public const DATA_FLAG_SADDLED = 0x8;
    public const DATA_FLAG_POWERED = 0x9;
    public const DATA_FLAG_IGNITED = 0xa;
    public const DATA_FLAG_BABY = 0xb;
    public const DATA_FLAG_CONVERTING = 0xc;
    public const DATA_FLAG_CRITICAL = 0xd;
    public const DATA_FLAG_CAN_SHOW_NAMETAG = 0xe;
    public const DATA_FLAG_ALWAYS_SHOW_NAMETAG = 0xf;
    public const DATA_FLAG_IMMOBILE = 0x10, DATA_FLAG_NO_AI = 0x10;
    public const DATA_FLAG_SILENT = 0x11;
    public const DATA_FLAG_WALLCLIMBING = 0x12;
    public const DATA_FLAG_CAN_CLIMB = 0x13;
    public const DATA_FLAG_SWIMMER = 0x14;
    public const DATA_FLAG_CAN_FLY = 0x15;
    public const DATA_FLAG_WALKER = 0x16;
    public const DATA_FLAG_RESTING = 0x17;
    public const DATA_FLAG_SITTING = 0x18;
    public const DATA_FLAG_ANGRY = 0x19;
    public const DATA_FLAG_INTERESTED = 0x1a;
    public const DATA_FLAG_CHARGED = 0x1b;
    public const DATA_FLAG_TAMED = 0x1c;
    public const DATA_FLAG_ORPHANED = 0x1d;
    public const DATA_FLAG_LEASHED = 0x1e;
    public const DATA_FLAG_SHEARED = 0x1f;
    public const DATA_FLAG_GLIDING = 0x20;
    public const DATA_FLAG_ELDER = 0x21;
    public const DATA_FLAG_MOVING = 0x22;
    public const DATA_FLAG_BREATHING = 0x23;
    public const DATA_FLAG_CHESTED = 0x24;
    public const DATA_FLAG_STACKABLE = 0x25;
    public const DATA_FLAG_SHOWBASE = 0x26;
    public const DATA_FLAG_REARING = 0x27;
    public const DATA_FLAG_VIBRATING = 0x28;
    public const DATA_FLAG_IDLING = 0x29;
    public const DATA_FLAG_EVOKER_SPELL = 0x2a;
    public const DATA_FLAG_CHARGE_ATTACK = 0x2b;
    public const DATA_FLAG_WASD_CONTROLLED = 0x2c;
    public const DATA_FLAG_CAN_POWER_JUMP = 0x2d;
    public const DATA_FLAG_LINGER = 0x2e;
    public const DATA_FLAG_HAS_COLLISION = 0x2f;
    public const DATA_FLAG_AFFECTED_BY_GRAVITY = 0x30;
    public const DATA_FLAG_FIRE_IMMUNE = 0x31;
    public const DATA_FLAG_DANCING = 0x32;
    public const DATA_FLAG_ENCHANTED = 0x33;
    public const DATA_FLAG_SHOW_TRIDENT_ROPE = 0x34;
    public const DATA_FLAG_CONTAINER_PRIVATE = 0x35;
    public const DATA_FLAG_TRANSFORMING = 0x36;
    public const DATA_FLAG_SPIN_ATTACK = 0x37;
    public const DATA_FLAG_SWIMMING = 0x38;
    public const DATA_FLAG_BRIBED = 0x39;
    public const DATA_FLAG_PREGNANT = 0x3a;
    public const DATA_FLAG_LAYING_EGG = 0x3b;
    public const DATA_FLAG_RIDER_CAN_PICK = 0x3c;
    public const DATA_FLAG_TRANSITION_SITTING = 0x3d;
    public const DATA_FLAG_EATING = 0x3e;
    public const DATA_FLAG_LAYING_DOWN = 0x3f;
    public const DATA_FLAG_SNEEZING = 0x40;
    public const DATA_FLAG_TRUSTING = 0x41;
    public const DATA_FLAG_ROLLING = 0x42;
    public const DATA_FLAG_SCARED = 0x43;
    public const DATA_FLAG_IN_SCAFFOLDING = 0x44;
    public const DATA_FLAG_OVER_SCAFFOLDING = 0x45;
    public const DATA_FLAG_FALL_THROUGH_SCAFFOLDING = 0x46;
    public const DATA_FLAG_BLOCKING = 0x47;
    public const DATA_FLAG_DISABLE_BLOCKING = 0x48;
    public const DATA_FLAG_BLOCKED_USING_SHIELD = 0x49;
    public const DATA_FLAG_BLOCKED_USING_DAMAGED_SHIELD = 0x4a;
    public const DATA_FLAG_SLEEPING = 0x4b;
    public const DATA_FLAG_TRADE_INTEREST = 0x4d;
    public const DATA_FLAG_DOOR_BREAKER = 0x4e;
    public const DATA_FLAG_BREAKING_OBSTRUCTION = 0x4f;
    public const DATA_FLAG_DOOR_OPENER = 0x50;
    public const DATA_FLAG_ILLAGER_CAPTAIN = 0x51;
    public const DATA_FLAG_STUNNED = 0x52;
    public const DATA_FLAG_ROARING = 0x53;
    public const DATA_FLAG_DELAYED_ATTACKING = 0x54;
    public const DATA_FLAG_AVOIDING_MOBS = 0x55;
    public const DATA_FLAG_STALK_AND_POUNCE_ON_TARGET = 0x59;
    public const DATA_FLAG_EMOTING_STATUS = 0x5a;
    public const DATA_FLAG_RAIDER_CELEBRATION = 0x5b;
}