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

use pocketmine\network\mcpe\CachedEncapsulatedPacket;
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\multiversion\BlockPallet;
use pocketmine\utils\BinaryDataException;
use pocketmine\utils\Utils;
use function bin2hex;
use function get_class;
use function is_object;
use function is_string;
use function method_exists;

abstract class DataPacket extends NetworkBinaryStream{

	public const NETWORK_ID = 0x0;
	public const PACKET_NAME = "DATA_PACKET"; // xD

    public const PID_MASK = 0x3ff;

    private const SUBCLIENT_ID_MASK = 0x3;
    private const SENDER_SUBCLIENT_ID_SHIFT = 0xa;
    private const RECIPIENT_SUBCLIENT_ID_SHIFT = 0xc;

	/** @var bool */
	public $isEncoded = false;
	/** @var CachedEncapsulatedPacket */
	public $__encapsulatedPacket = null;

	/** @var int */
	public $senderSubId = 0x0;
	/** @var int */
	public $recipientSubId = 0x0;
	/** @var int|null */
	private $packetProtocol;
    /** @var array */
	protected static $packetsIds = [];
	/** @var BlockPallet[] */
	protected static $blockPalletes = [];

	public function pid(){
		return $this::NETWORK_ID;
	}

	public function getName() : string{
		return (new \ReflectionClass($this))->getShortName();
	}

	public function setProtocol(int $protocol) : void{
	    $this->packetProtocol = $protocol;
	}
	
	public function getProtocol() : int{
	    return $this->packetProtocol;
	}

    public function checkProtocol() : void{
        if ($this->packetProtocol === null) {
            throw new \InvalidArgumentException('Protocol has not passed. Please use $packet->setProtocol(int $protocol)->... for fix it.');
        }
    }

	public function canBeBatched() : bool{
		return true;
	}

	public function canBeSentBeforeLogin() : bool{
		return false;
	}

	/**
	 * Returns whether the packet may legally have unread bytes left in the buffer.
	 * @return bool
	 */
	public function mayHaveUnreadBytes() : bool{
		return false;
	}

	/**
	 * @throws \OutOfBoundsException
	 * @throws \UnexpectedValueException
	 */
	public function decode(){
		$this->rewind();
		$this->checkProtocol();
		$this->decodeHeader();
		$this->decodePayload();
	}

	/**
	 * @throws \OutOfBoundsException
	 * @throws \UnexpectedValueException
	 */
	protected function decodeHeader(){
        if ($this->packetProtocol < ProtocolInfo::PROTOCOL_280) {
            $this->getByte();
            if ($this->packetProtocol >= ProtocolInfo::PROTOCOL_120) {
                $this->senderSubId = $this->getByte();
                $this->recipientSubId = $this->getByte();
                if ($this->senderSubId > 0x4 || $this->recipientSubId > 0x4) {
                    throw new BinaryDataException(($this) . ": Packet decode headers error");
                }
            }
        } else {
            $pid = $this->getUnsignedVarInt();
            $this->senderSubId = ($pid >> self::SENDER_SUBCLIENT_ID_SHIFT) & self::SUBCLIENT_ID_MASK;
            $this->recipientSubId = ($pid >> self::RECIPIENT_SUBCLIENT_ID_SHIFT) & self::SUBCLIENT_ID_MASK;
        }
	}

	/**
	 * Note for plugin developers: If you're adding your own packets, you should perform decoding in here.
	 *
	 * @throws \OutOfBoundsException
	 * @throws \UnexpectedValueException
	 */
	protected function decodePayload(){

	}

	public function encode(){
		$this->reset();
		$this->checkProtocol();
		$this->encodeHeader();
		$this->encodePayload();
		$this->isEncoded = true;
	}

	protected function encodeHeader(){
		if (empty(self::$packetsIds)) {
		    self::initPackets();
		}

	    $pid = self::$packetsIds[$this->packetProtocol][$this::PACKET_NAME];
        if ($this->packetProtocol < ProtocolInfo::PROTOCOL_280) {
            $this->putByte($pid);
            if ($this->packetProtocol >= ProtocolInfo::PROTOCOL_120) {
                $this->putByte($this->senderSubId);
                $this->putByte($this->recipientSubId);
            }
        } else {
            $this->putUnsignedVarInt($pid | ($this->senderSubId << self::SENDER_SUBCLIENT_ID_SHIFT) | ($this->recipientSubId << self::RECIPIENT_SUBCLIENT_ID_SHIFT));
        }
	}

	/**
	 * Note for plugin developers: If you're adding your own packets, you should perform encoding in here.
	 */
	protected function encodePayload(){

	}

	/**
	 * Performs handling for this packet. Usually you'll want an appropriately named method in the NetworkSession for this.
	 *
	 * This method returns a bool to indicate whether the packet was handled or not. If the packet was unhandled, a debug message will be logged with a hexdump of the packet.
	 * Typically this method returns the return value of the handler in the supplied NetworkSession. See other packets for examples how to implement this.
	 *
	 * @param NetworkSession $session
	 *
	 * @return bool true if the packet was handled successfully, false if not.
	 */
	abstract public function handle(NetworkSession $session) : bool;

	public function clean(){
		$this->buffer = "";
		$this->isEncoded = false;
		$this->offset = 0;
		return $this;
	}

	public function __debugInfo(){
		$data = [];
		foreach((array) $this as $k => $v){
			if($k === "buffer" and is_string($v)){
				$data[$k] = bin2hex($v);
			}elseif(is_string($v) or (is_object($v) and method_exists($v, "__toString"))){
				$data[$k] = Utils::printable((string) $v);
			}else{
				$data[$k] = $v;
			}
		}

		return $data;
	}

	public function __get($name){
		throw new \Error("Undefined property: " . get_class($this) . "::\$" . $name);
	}

	public function __set($name, $value){
		throw new \Error("Undefined property: " . get_class($this) . "::\$" . $name);
	}

	public static function initPackets() {
		$oClass = new \ReflectionClass ('pocketmine\network\mcpe\protocol\ProtocolInfo110');
		self::$packetsIds[ProtocolInfo::PROTOCOL_110] = $oClass->getConstants();
		$oClass = new \ReflectionClass ('pocketmine\network\mcpe\protocol\ProtocolInfo331');
		self::$packetsIds[ProtocolInfo::PROTOCOL_361] = $oClass->getConstants();
		self::$packetsIds[ProtocolInfo::PROTOCOL_360] = $oClass->getConstants();
		self::$packetsIds[ProtocolInfo::PROTOCOL_354] = $oClass->getConstants();
		self::$packetsIds[ProtocolInfo::PROTOCOL_351] = $oClass->getConstants();
		self::$packetsIds[ProtocolInfo::PROTOCOL_350] = $oClass->getConstants();
		self::$packetsIds[ProtocolInfo::PROTOCOL_342] = $oClass->getConstants();
		self::$packetsIds[ProtocolInfo::PROTOCOL_340] = $oClass->getConstants();
	}

    /**
     * @return void
     */
	public static function initPallet() : void{
		self::$blockPalletes = BlockPallet::initAll();
	}

    /**
     * @param int $runtimeId
     * @param int $playerProtocol
     * 
     * @return array
     */
	public static function getBlockIDByRuntime(int $runtimeId, int $playerProtocol) : array{
		$pallet = self::getPallet($playerProtocol);

		return is_null($pallet) ? [0, 0, ""] : $pallet->getBlockDataByRuntimeID($runtimeId);
	}

    /**
     * @param int $id
     * @param int $meta
     * @param int $playerProtocol
     * 
     * @return int
     */
	public static function getBlockRuntimeID(int $id, int $meta, int $playerProtocol) : int{
		$pallet = self::getPallet($playerProtocol);

		return is_null($pallet) ? 0 : $pallet->getBlockRuntimeIDByData($id, $meta);
	}

    /**
     * @param int $playerProtocol
     * 
     * @return string
     */
	public static function getBlockPalletData(int $playerProtocol) : string{
		$pallet = self::getPallet($playerProtocol);

		return is_null($pallet) ? "" : $pallet->getDataForPackets();
	}

	/**
	 * @param int $playerProtocol
	 * 
	 * @return BlockPallet
	 */
	public static function getPallet(int $playerProtocol) : ?BlockPallet{
		foreach (self::$blockPalletes as $protocol => $pallet) {
			if ($playerProtocol >= $protocol) {
				return $pallet;
			}
		}

		return null;
	}

}
