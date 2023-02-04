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
use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds110;
use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds130;
use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds280;
use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds310;
use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds331;
use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds360;
use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds385;
use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds387;
use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds406;
use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds419;
use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds422;
use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds428;
use pocketmine\network\mcpe\protocol\PacketsIds\PacketMagicNumbers;

class PacketPool {
    private const PROTOCOL_POOL = [
        ProtocolInfo::PROTOCOL_428 => PacketsIds428::class,
        ProtocolInfo::PROTOCOL_422 => PacketsIds422::class,
        ProtocolInfo::PROTOCOL_419 => PacketsIds419::class,
        ProtocolInfo::PROTOCOL_406 => PacketsIds406::class,
        ProtocolInfo::PROTOCOL_387 => PacketsIds387::class,
        ProtocolInfo::PROTOCOL_385 => PacketsIds385::class,
        ProtocolInfo::PROTOCOL_360 => PacketsIds360::class,
        ProtocolInfo::PROTOCOL_331 => PacketsIds331::class,
        ProtocolInfo::PROTOCOL_310 => PacketsIds310::class,
        ProtocolInfo::PROTOCOL_280 => PacketsIds280::class,
        ProtocolInfo::PROTOCOL_130 => PacketsIds130::class,
        ProtocolInfo::PROTOCOL_110 => PacketsIds110::class
    ];

	/** @var \SplFixedArray<int>[] */
	protected static $magicPoolTo = null;
	/** @var \SplFixedArray<int>[] */
	protected static $magicPoolFrom = null;
	/** @var \SplFixedArray<DataPacket> */
    protected static $pool = null;
	/** @var int[] */
	protected static $protocolList = null;

	public static function init() {
		$magicNumbers = (new \ReflectionClass(PacketMagicNumbers::class))->getConstants();
		self::$magicPoolTo = [];
		self::$magicPoolFrom = [];
	    self::$pool = new \SplFixedArray(356);
		self::$protocolList = array_keys(self::PROTOCOL_POOL);
		foreach(self::$protocolList as $protocol){
			$protocolInfoClass = self::PROTOCOL_POOL[$protocol];
			$protocolMagicNumbers = (new \ReflectionClass($protocolInfoClass))->getConstants();
			self::$magicPoolTo[$protocol] = new \SplFixedArray(356);
			self::$magicPoolFrom[$protocol] = new \SplFixedArray(356);
			foreach($protocolMagicNumbers as $name => $magic){
				if(isset($magicNumbers[$name])){
					self::$magicPoolTo[$protocol][$magicNumbers[$name]] = $magic;
					self::$magicPoolFrom[$protocol][$magic] = $magicNumbers[$name];
				}else{
					throw new \InvalidArgumentException("Packet $name not founded in magic numbers class.($protocolInfoClass $magic)");
				}
			}
        }

		self::registerPacket(new LoginPacket());
		self::registerPacket(new PlayStatusPacket());
		self::registerPacket(new ServerToClientHandshakePacket());
		self::registerPacket(new ClientToServerHandshakePacket());
		self::registerPacket(new DisconnectPacket());
		self::registerPacket(new ResourcePacksInfoPacket());
		self::registerPacket(new ResourcePackStackPacket());
		self::registerPacket(new ResourcePackClientResponsePacket());
		self::registerPacket(new TextPacket());
		self::registerPacket(new SetTimePacket());
		self::registerPacket(new StartGamePacket());
		self::registerPacket(new AddPlayerPacket());
		self::registerPacket(new AddActorPacket());
		self::registerPacket(new RemoveActorPacket());
		self::registerPacket(new AddItemActorPacket());
		self::registerPacket(new TakeItemActorPacket());
		self::registerPacket(new MoveActorAbsolutePacket());
		self::registerPacket(new MovePlayerPacket());
		self::registerPacket(new RiderJumpPacket());
		self::registerPacket(new UpdateBlockPacket());
		self::registerPacket(new AddPaintingPacket());
		self::registerPacket(new TickSyncPacket());
		self::registerPacket(new LevelSoundEventPacketV1());
		self::registerPacket(new LevelEventPacket());
		self::registerPacket(new BlockEventPacket());
		self::registerPacket(new ActorEventPacket());
		self::registerPacket(new MobEffectPacket());
		self::registerPacket(new UpdateAttributesPacket());
		self::registerPacket(new InventoryTransactionPacket());
		self::registerPacket(new MobEquipmentPacket());
		self::registerPacket(new MobArmorEquipmentPacket());
		self::registerPacket(new InteractPacket());
		self::registerPacket(new BlockPickRequestPacket());
		self::registerPacket(new ActorPickRequestPacket());
		self::registerPacket(new PlayerActionPacket());
		self::registerPacket(new ActorFallPacket());
		self::registerPacket(new HurtArmorPacket());
		self::registerPacket(new SetActorDataPacket());
		self::registerPacket(new SetActorMotionPacket());
		self::registerPacket(new SetActorLinkPacket());
		self::registerPacket(new SetHealthPacket());
		self::registerPacket(new SetSpawnPositionPacket());
		self::registerPacket(new AnimatePacket());
		self::registerPacket(new RespawnPacket());
		self::registerPacket(new ContainerOpenPacket());
		self::registerPacket(new ContainerClosePacket());
		self::registerPacket(new PlayerHotbarPacket());
		self::registerPacket(new InventoryContentPacket());
		self::registerPacket(new InventorySlotPacket());
		self::registerPacket(new ContainerSetDataPacket());
		self::registerPacket(new CraftingDataPacket());
		self::registerPacket(new CraftingEventPacket());
		self::registerPacket(new GuiDataPickItemPacket());
		self::registerPacket(new AdventureSettingsPacket());
		self::registerPacket(new BlockActorDataPacket());
		self::registerPacket(new PlayerInputPacket());
		self::registerPacket(new LevelChunkPacket());
		self::registerPacket(new SetCommandsEnabledPacket());
		self::registerPacket(new SetDifficultyPacket());
		self::registerPacket(new ChangeDimensionPacket());
		self::registerPacket(new SetPlayerGameTypePacket());
		self::registerPacket(new PlayerListPacket());
		self::registerPacket(new SimpleEventPacket());
		self::registerPacket(new EventPacket());
		self::registerPacket(new SpawnExperienceOrbPacket());
		self::registerPacket(new ClientboundMapItemDataPacket());
		self::registerPacket(new MapInfoRequestPacket());
		self::registerPacket(new RequestChunkRadiusPacket());
		self::registerPacket(new ChunkRadiusUpdatedPacket());
		self::registerPacket(new ItemFrameDropItemPacket());
		self::registerPacket(new GameRulesChangedPacket());
		self::registerPacket(new CameraPacket());
		self::registerPacket(new BossEventPacket());
		self::registerPacket(new ShowCreditsPacket());
		self::registerPacket(new AvailableCommandsPacket());
		self::registerPacket(new CommandRequestPacket());
		self::registerPacket(new CommandBlockUpdatePacket());
		self::registerPacket(new CommandOutputPacket());
		self::registerPacket(new UpdateTradePacket());
		self::registerPacket(new UpdateEquipPacket());
		self::registerPacket(new ResourcePackDataInfoPacket());
		self::registerPacket(new ResourcePackChunkDataPacket());
		self::registerPacket(new ResourcePackChunkRequestPacket());
		self::registerPacket(new TransferPacket());
		self::registerPacket(new PlaySoundPacket());
		self::registerPacket(new StopSoundPacket());
		self::registerPacket(new SetTitlePacket());
		self::registerPacket(new AddBehaviorTreePacket());
		self::registerPacket(new StructureBlockUpdatePacket());
		self::registerPacket(new ShowStoreOfferPacket());
		self::registerPacket(new PurchaseReceiptPacket());
		self::registerPacket(new PlayerSkinPacket());
		self::registerPacket(new SubClientLoginPacket());
		self::registerPacket(new AutomationClientConnectPacket());
		self::registerPacket(new SetLastHurtByPacket());
		self::registerPacket(new BookEditPacket());
		self::registerPacket(new NpcRequestPacket());
		self::registerPacket(new PhotoTransferPacket());
		self::registerPacket(new ModalFormRequestPacket());
		self::registerPacket(new ModalFormResponsePacket());
		self::registerPacket(new ServerSettingsRequestPacket());
		self::registerPacket(new ServerSettingsResponsePacket());
		self::registerPacket(new ShowProfilePacket());
		self::registerPacket(new SetDefaultGameTypePacket());
		self::registerPacket(new RemoveObjectivePacket());
		self::registerPacket(new SetDisplayObjectivePacket());
		self::registerPacket(new SetScorePacket());
		self::registerPacket(new LabTablePacket());
		self::registerPacket(new UpdateBlockSyncedPacket());
		self::registerPacket(new MoveActorDeltaPacket());
		self::registerPacket(new SetScoreboardIdentityPacket());
		self::registerPacket(new SetLocalPlayerAsInitializedPacket());
		self::registerPacket(new UpdateSoftEnumPacket());
		self::registerPacket(new NetworkStackLatencyPacket());
		self::registerPacket(new ScriptCustomEventPacket());
		self::registerPacket(new SpawnParticleEffectPacket());
		self::registerPacket(new AvailableActorIdentifiersPacket());
		self::registerPacket(new LevelSoundEventPacketV2());
		self::registerPacket(new NetworkChunkPublisherUpdatePacket());
		self::registerPacket(new BiomeDefinitionListPacket());
		self::registerPacket(new LevelSoundEventPacket());
		self::registerPacket(new LevelEventGenericPacket());
		self::registerPacket(new LecternUpdatePacket());
		self::registerPacket(new VideoStreamConnectPacket());
		self::registerPacket(new ClientCacheStatusPacket());
		self::registerPacket(new OnScreenTextureAnimationPacket());
		self::registerPacket(new MapCreateLockedCopyPacket());
		self::registerPacket(new StructureTemplateDataRequestPacket());
		self::registerPacket(new StructureTemplateDataResponsePacket());
		self::registerPacket(new UpdateBlockPropertiesPacket());
		self::registerPacket(new ClientCacheBlobStatusPacket());
		self::registerPacket(new ClientCacheMissResponsePacket());
		self::registerPacket(new EducationSettingsPacket());
		self::registerPacket(new EmotePacket());
		self::registerPacket(new MultiplayerSettingsPacket());
		self::registerPacket(new SettingsCommandPacket());
		self::registerPacket(new AnvilDamagePacket());
		self::registerPacket(new CompletedUsingItemPacket());
		self::registerPacket(new NetworkSettingsPacket());
		self::registerPacket(new PlayerAuthInputPacket());
		self::registerPacket(new CreativeContentPacket());
		self::registerPacket(new PlayerEnchantOptionsPacket());
		self::registerPacket(new ItemStackRequestPacket());
		self::registerPacket(new ItemStackResponsePacket());
		self::registerPacket(new PlayerArmorDamagePacket());
		self::registerPacket(new CodeBuilderPacket());
		self::registerPacket(new UpdatePlayerGameTypePacket());
		self::registerPacket(new EmoteListPacket());
		self::registerPacket(new PositionTrackingDBServerBroadcastPacket());
		self::registerPacket(new PositionTrackingDBClientRequestPacket());
		self::registerPacket(new DebugInfoPacket());
		self::registerPacket(new PacketViolationWarningPacket());
		self::registerPacket(new MotionPredictionHintsPacket());
		self::registerPacket(new AnimateEntityPacket());
		self::registerPacket(new CameraShakePacket());
		self::registerPacket(new PlayerFogPacket());
		self::registerPacket(new CorrectPlayerMovePredictionPacket());
		self::registerPacket(new ItemComponentPacket());
		self::registerPacket(new FilterTextPacket());
		self::registerPacket(new ClientboundDebugRendererPacket());
		// ExplodePacket - TickSyncPacket
		self::registerPacket(new ExplodePacket());
		// 1.1
		self::registerPacket(new RemoveBlockPacket());
		self::registerPacket(new UseItemPacket());
		self::registerPacket(new DropItemPacket());
		self::registerPacket(new ContainerSetContentPacket());
		self::registerPacket(new ContainerSetSlotPacket());
		self::registerPacket(new CommandStepPacket());
	}

	/**
	 * @param DataPacket $packet
	 */
	public static function registerPacket(DataPacket $packet){
		self::$pool[$packet->pid()] = clone $packet;
	}

	/**
	 * @param int $pid
	 * @param int $playerProtocol
	 *
	 * @return DataPacket
	 */
	public static function getPacketById(int $pid, int $playerProtocol) : DataPacket{
		$magicId = self::getPacketMagicById($pid, $playerProtocol);
		return isset(self::$pool[$magicId]) ? clone self::$pool[$magicId] : new UnknownPacket();
	}

	/**
	 * @param string $buffer
	 * @param int $playerProtocol
	 * 
	 * @return DataPacket
	 */
	public static function getPacket(string $buffer, int $playerProtocol) : DataPacket{
		$offset = 0;
		$pk = self::getPacketById(Binary::readUnsignedVarInt($buffer, $offset), $playerProtocol);
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
        foreach(self::$protocolList as $protocol){
            if($playerProtocol >= $protocol){
                return isset(self::$magicPoolTo[$protocol][$magic]) ? self::$magicPoolTo[$protocol][$magic] : null;
            }
        }

        return null;
    }

    /**
     * @param int $id
     * @param int $playerProtocol
     * 
     * @return ?int
     */
    public static function getPacketMagicById(int $id, int $playerProtocol) : ?int{
        foreach(self::$protocolList as $protocol){
            if($playerProtocol >= $protocol){
                return isset(self::$magicPoolFrom[$protocol][$id]) ? self::$magicPoolFrom[$protocol][$id] : null;
            }
        }

        return null;
    }

}
