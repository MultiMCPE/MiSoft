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
use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds130;
use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds310;
use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds331;

class PacketPool {
    /** @var array */
	protected static $packetPoolFrom = null;
	protected static $packetPoolTo = null;
	/** @var \SplFixedArray<DataPacket> */
    protected static $pool = null;

    protected const PROTOCOLS = [ProtocolInfo::PROTOCOL_331, ProtocolInfo::PROTOCOL_310, ProtocolInfo::PROTOCOL_130, ProtocolInfo::PROTOCOL_110];

	public static function init() {
	    static::$packetPoolFrom = [];
	    static::$packetPoolTo = [];
	    static::$pool = new \SplFixedArray(256);

        $magicIds = (new \ReflectionClass('pocketmine\network\mcpe\protocol\ProtocolInfo'))->getConstants();
        foreach (self::PROTOCOLS as $protocol) {
            $packetsIds = (new \ReflectionClass('pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds' . $protocol))->getConstants();
            foreach ($magicIds as $name => $id) {
                if (isset($packetsIds[$name])) {
                    static::$packetPoolTo[$protocol][$id] = $packetsIds[$name];
                    static::$packetPoolFrom[$protocol][$packetsIds[$name]] = $id;
                }
            }
        }

		static::registerPacket(new LoginPacket());
		static::registerPacket(new PlayStatusPacket());
		static::registerPacket(new ServerToClientHandshakePacket());
		static::registerPacket(new ClientToServerHandshakePacket());
		static::registerPacket(new DisconnectPacket());
		static::registerPacket(new ResourcePacksInfoPacket());
		static::registerPacket(new ResourcePackStackPacket());
		static::registerPacket(new ResourcePackClientResponsePacket());
		static::registerPacket(new TextPacket());
		static::registerPacket(new SetTimePacket());
		static::registerPacket(new StartGamePacket());
		static::registerPacket(new AddPlayerPacket());
		static::registerPacket(new AddActorPacket());
		static::registerPacket(new RemoveActorPacket());
		static::registerPacket(new AddItemActorPacket());
		static::registerPacket(new TakeItemActorPacket());
		static::registerPacket(new MoveActorAbsolutePacket());
		static::registerPacket(new MovePlayerPacket());
		static::registerPacket(new UpdateBlockPacket());
		static::registerPacket(new AddPaintingPacket());
		static::registerPacket(new ExplodePacket());
		static::registerPacket(new LevelEventPacket());
		static::registerPacket(new BlockEventPacket());
		static::registerPacket(new ActorEventPacket());
		static::registerPacket(new MobEffectPacket());
		static::registerPacket(new UpdateAttributesPacket());
		static::registerPacket(new InventoryTransactionPacket());
		static::registerPacket(new MobEquipmentPacket());
		static::registerPacket(new MobArmorEquipmentPacket());
		static::registerPacket(new InteractPacket());
		static::registerPacket(new BlockPickRequestPacket());
		static::registerPacket(new PlayerActionPacket());
		static::registerPacket(new SetActorDataPacket());
		static::registerPacket(new SetActorMotionPacket());
		static::registerPacket(new SetActorLinkPacket());
		static::registerPacket(new SetSpawnPositionPacket());
		static::registerPacket(new AnimatePacket());
		static::registerPacket(new RespawnPacket());
		static::registerPacket(new ContainerOpenPacket());
		static::registerPacket(new ContainerClosePacket());
		static::registerPacket(new PlayerHotbarPacket());
		static::registerPacket(new InventoryContentPacket());
		static::registerPacket(new InventorySlotPacket());
		static::registerPacket(new ContainerSetDataPacket());
		static::registerPacket(new CraftingDataPacket());
		static::registerPacket(new CraftingEventPacket());
		static::registerPacket(new AdventureSettingsPacket());
		static::registerPacket(new BlockActorDataPacket());
		static::registerPacket(new LevelChunkPacket());
		static::registerPacket(new SetCommandsEnabledPacket());
		static::registerPacket(new SetDifficultyPacket());
		static::registerPacket(new ChangeDimensionPacket());
		static::registerPacket(new SetPlayerGameTypePacket());
		static::registerPacket(new PlayerListPacket());
		static::registerPacket(new SpawnExperienceOrbPacket());
		static::registerPacket(new ClientboundMapItemDataPacket());
		static::registerPacket(new MapInfoRequestPacket());
		static::registerPacket(new RequestChunkRadiusPacket());
		static::registerPacket(new ChunkRadiusUpdatedPacket());
		static::registerPacket(new ItemFrameDropItemPacket());
		static::registerPacket(new GameRulesChangedPacket());
		static::registerPacket(new BossEventPacket());
		static::registerPacket(new AvailableCommandsPacket());
		static::registerPacket(new CommandRequestPacket());
		static::registerPacket(new ResourcePackDataInfoPacket());
		static::registerPacket(new ResourcePackChunkDataPacket());
		static::registerPacket(new ResourcePackChunkRequestPacket());
		static::registerPacket(new TransferPacket());
		static::registerPacket(new PlaySoundPacket());
		static::registerPacket(new SetTitlePacket());
		static::registerPacket(new ShowStoreOfferPacket());
		static::registerPacket(new PurchaseReceiptPacket());
		static::registerPacket(new PlayerSkinPacket());
		static::registerPacket(new SubClientLoginPacket());
		static::registerPacket(new BookEditPacket());
		static::registerPacket(new ModalFormRequestPacket());
		static::registerPacket(new ModalFormResponsePacket());
		static::registerPacket(new ServerSettingsRequestPacket());
		static::registerPacket(new ServerSettingsResponsePacket());
		static::registerPacket(new SetDefaultGameTypePacket());
		static::registerPacket(new RemoveObjectivePacket());
		static::registerPacket(new SetDisplayObjectivePacket());
		static::registerPacket(new SetScorePacket());
		static::registerPacket(new SetScoreboardIdentityPacket());
		static::registerPacket(new SetLocalPlayerAsInitializedPacket());
		static::registerPacket(new SpawnParticleEffectPacket());
		static::registerPacket(new AvailableActorIdentifiersPacket());
		static::registerPacket(new NetworkChunkPublisherUpdatePacket());
		static::registerPacket(new BiomeDefinitionListPacket());
		static::registerPacket(new LevelSoundEventPacket());
		// 1.1
		static::registerPacket(new RemoveBlockPacket());
		static::registerPacket(new UseItemPacket());
		static::registerPacket(new DropItemPacket());
		static::registerPacket(new ContainerSetContentPacket());
		static::registerPacket(new ContainerSetSlotPacket());
		static::registerPacket(new CommandStepPacket());
	}

	/**
	 * @param DataPacket $packet
	 */
	public static function registerPacket(DataPacket $packet){
		static::$pool[$packet->pid()] = clone $packet;
	}

	/**
	 * @param int $pid
	 * @param int $playerProtocol
	 *
	 * @return ?DataPacket
	 */
	public static function getPacketById(int $pid, int $playerProtocol) : ?DataPacket{
		$magicId = static::getPacketMagicById($pid, $playerProtocol);
		/** @var DataPacket $class */
		$class = isset(static::$pool[$magicId]) ? clone static::$pool[$magicId] : null;

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

    /**
     * @param int $magic
     * @param int $playerProtocol
     * 
     * @return ?int
     */
    public static function getPacketIdByMagic(int $magic, int $playerProtocol) : ?int{
        $selectedProtocol = null;
        foreach (self::PROTOCOLS as $protocol) {
            if ($playerProtocol >= $protocol) {
                $selectedProtocol = $protocol;
                break;
            }
        }

        if ($selectedProtocol === null) {
            $selectedProtocol = self::PROTOCOLS[array_key_last(self::PROTOCOLS)];
        }

        return isset(static::$packetPoolTo[$selectedProtocol][$magic]) ? static::$packetPoolTo[$selectedProtocol][$magic] : null;
    }

    /**
     * @param int $id
     * @param int $playerProtocol
     * 
     * @return ?int
     */
    public static function getPacketMagicById(int $id, int $playerProtocol) : ?int{
        $selectedProtocol = null;
        foreach (self::PROTOCOLS as $protocol) {
            if ($playerProtocol >= $protocol) {
                $selectedProtocol = $protocol;
                break;
            }
        }

        if ($selectedProtocol === null) {
            $selectedProtocol = self::PROTOCOLS[array_key_last(self::PROTOCOLS)];
        }

        return isset(static::$packetPoolFrom[$selectedProtocol][$id]) ? static::$packetPoolFrom[$selectedProtocol][$id] : null;
    }

}
