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

use pocketmine\network\mcpe\multiversion\utils\TableConverter;
use pocketmine\network\mcpe\multiversion\constants\LevelSoundIds;
use pocketmine\network\mcpe\multiversion\constants\CommandArgumentTypeIds;
use pocketmine\network\mcpe\multiversion\constants\PlayerActionIds;
use pocketmine\network\mcpe\multiversion\constants\ResourcePackTypeIds;
use pocketmine\network\mcpe\multiversion\constants\TextPacketTypeIds;
use pocketmine\network\mcpe\multiversion\constants\ParticleIds;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\network\mcpe\protocol\types\ResourcePackType;
use pocketmine\level\particle\Particle;
use function end;
use function is_int;
use function substr;
use function array_filter;
use const ARRAY_FILTER_USE_BOTH;
use SplFixedArray;
use ReflectionClass;
use Closure;
use RuntimeException;

abstract class MultiversionEnums{
    public const FROM = 0;
    public const TO = 1;

    /** @var SplFixedArray<int>[] */
    public static $soundList = [];
    /** @var SplFixedArray<int>[] */
    public static $commandArgTypes = [];
    /** @var SplFixedArray<int>[] */
    public static $playerActions = [];
    /** @var SplFixedArray<int>[] */
    public static $resourcePackTypes = [];
    /** @var SplFixedArray<int>[] */
    public static $textPacketTypes = [];
    /** @var SplFixedArray<int>[] */
    public static $particleIds = [];

    public static function init() : void{
        static::initList(LevelSoundEventPacket::class, LevelSoundIds::LEVEL_SOUND_IDS, static::$soundList, function(array $soundTypes) : int{
            return end($soundTypes);
        });
        static::initList(AvailableCommandsPacket::class, CommandArgumentTypeIds::COMMAND_ARGUMENT_TYPE_IDS, static::$commandArgTypes, null, function($value, string $name) : bool{
            return is_int($value) && substr($name, 0, 8) === "ARG_TYPE";
        });
        static::initList(PlayerActionPacket::class, PlayerActionIds::PLAYER_ACTION_IDS, static::$playerActions);
        static::initList(ResourcePackType::class, ResourcePackTypeIds::RESOURCE_PACK_TYPE_IDS, static::$resourcePackTypes);
        static::initList(TextPacket::class, TextPacketTypeIds::TEXT_PACKET_TYPE_IDS, static::$textPacketTypes);
        static::initList(Particle::class, ParticleIds::PARTICLE_IDS, static::$particleIds);
    }

    /**
     * @param string           $class
     * @param array            $protocolValues
     * @param array            &$listToFill
     * @param Closure|null     $onDefault
     * @param Closure|null     $filter
     */
    public static function initList(string $class, array $protocolValues, array &$listToFill, ?Closure $onDefault = null, ?Closure $filter = null) : void{
        $classConstants = static::getClassConstants($class);
        if($filter !== null){
            $classConstants = array_filter($classConstants, $filter, ARRAY_FILTER_USE_BOTH);
        }

        foreach($protocolValues as $protocol => $value){
            $defaultValue = ($onDefault !== null) ? $onDefault($value) : 0;
            TableConverter::convert($classConstants, $value, $fromTo, $toFrom, $defaultValue);

            $protocolValues = new SplFixedArray(2);
            $protocolValues[self::FROM] = $fromTo;
            $protocolValues[self::TO] = $toFrom;

            $listToFill[$protocol] = $protocolValues;
        }
    }

    /**
     * @param string $class
     * 
     * @return array
     */
    public static function getClassConstants(string $class) : array{
        return (new ReflectionClass($class))->getConstants();
    }

    /**
     * @param int $playerProtocol
     * @param int $soundId
     * 
     * @return int
     */
    public static function getLevelSoundEventName(int $playerProtocol, int $soundId) : int{
        foreach(static::$soundList as $protocol => $soundTypes){
            if($playerProtocol >= $protocol){
                return $soundTypes[self::TO][$soundId];
            }
        }
        throw new RuntimeException("Not founded sound list for protocol $playerProtocol, MCPE library need to update");
    }

    /**
     * @param int $playerProtocol
     * @param int $soundName
     * 
     * @return int
     */
    public static function getLevelSoundEventId(int $playerProtocol, int $soundName) : int{
        foreach(static::$soundList as $protocol => $soundTypes){
            if($playerProtocol >= $protocol){
                return $soundTypes[self::FROM][$soundName];
            }
        }
        throw new RuntimeException("Not founded sound list for protocol $playerProtocol, MCPE library need to update");
    }

    /**
     * @param int $playerProtocol
     * @param int $messageTypeId
     * 
     * @return int
     */
    public static function getMessageType(int $playerProtocol, int $messageTypeId) : int{
        foreach(self::$textPacketTypes as $protocol => $messageTypes){
            if($playerProtocol >= $protocol){
                return $messageTypes[self::TO][$messageTypeId];
            }
        }
        throw new RuntimeException("Not founded message types list for protocol $playerProtocol, MCPE library need to update");
    }

    /**
     * @param int $playerProtocol
     * @param int $messageType
     * 
     * @return int
     */
    public static function getMessageTypeId(int $playerProtocol, int $messageType) : int{
        foreach(self::$textPacketTypes as $protocol => $messageTypes){
            if($playerProtocol >= $protocol){
                return $messageTypes[self::FROM][$messageType];
            }
        }
        throw new RuntimeException("Not founded message types list for protocol $playerProtocol, MCPE library need to update");
    }

    /**
     * @param int $playerProtocol
     * @param int $actionId
     * 
     * @return int
     */
    public static function getPlayerActionName(int $playerProtocol, int $actionId) : int{
        return self::getPlayerActionListByProtocol($playerProtocol)[self::TO][$actionId];
    }

    /**
     * @param int $playerProtocol
     * @param int $actionName
     * 
     * @return int
     */
    public static function getPlayerActionId(int $playerProtocol, int $actionName) : int{
        return self::getPlayerActionListByProtocol($playerProtocol)[self::FROM][$actionName];
    }

    /**
     * @param int $playerProtocol
     * 
     * @return SplFixedArray
     */
    public static function getPlayerActionListByProtocol(int $playerProtocol) : SplFixedArray{
        foreach(static::$playerActions as $protocol => $actionTypes){
            if($playerProtocol >= $protocol){
                return $actionTypes;
            }
        }
        throw new RuntimeException("Not founded player actions list for protocol $playerProtocol, MCPE library need to update");
    }

    /**
     * @param int $commandType
     * @param int $playerProtocol
     * 
     * @return int
     */
    public static function getCommandArgType(int $commandType, int $playerProtocol) : int{
        foreach(static::$commandArgTypes as $protocol => $commandTypes){
            if($playerProtocol >= $protocol){
                return $commandTypes[self::FROM][$commandType];
            }
        }
        throw new RuntimeException("Not founded command argument types list for protocol $playerProtocol, MCBE library need to update");
    }

    /**
     * @param int $playerProtocol
     * @param int $packTypeId
     * 
     * @return int
     */
    public static function getPackType(int $playerProtocol, int $packTypeId) : int{
        foreach(self::$resourcePackTypes as $protocol => $packTypes){
            if($playerProtocol >= $protocol){
                return $packTypes[self::TO][$packTypeId];
            }
        }
        return ResourcePackType::INVALID;
    }

    /**
     * @param int $playerProtocol
     * @param int $packType
     * 
     * @return int
     */
    public static function getPackTypeId(int $playerProtocol, int $packType) : int{
        foreach(static::$resourcePackTypes as $protocol => $packTypes){
            if($playerProtocol >= $protocol){
                if($packType >= $packTypes[self::FROM]->getSize()){
                    return ResourcePackType::INVALID;
                }
                return $packTypes[self::FROM][$packType];
            }
        }
        return ResourcePackType::INVALID;
    }

    /**
     * @param int $playerProtocol
     * @param int $particleType
     * 
     * @return int
     */
    public static function getParticleId(int $playerProtocol, int $particleType) : int{
        foreach(self::$particleIds as $protocol => $particleTypes){
            if($playerProtocol >= $protocol){
                if($particleType >= $particleTypes[self::FROM]->getSize()){
                    return 0;
                }
                return $particleTypes[self::FROM][$particleType];
            }
        }
        throw new RuntimeException("Not founded particle ids list for protocol $playerProtocol, MCPE library need to update");
    }
}
