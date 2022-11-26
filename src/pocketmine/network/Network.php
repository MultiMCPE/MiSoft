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

/**
 * Network-related classes
 */
namespace pocketmine\network;

use pocketmine\event\server\NetworkInterfaceRegisterEvent;
use pocketmine\event\server\NetworkInterfaceUnregisterEvent;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\Server;
use function spl_object_hash;

class Network{

	public static $BATCH_THRESHOLD = 512;

	/** @var Server */
	private $server;

	/** @var SourceInterface[] */
	private $interfaces = [];

	/** @var AdvancedSourceInterface[] */
	private $advancedInterfaces = [];

	private $upload = 0;
	private $download = 0;

	/** @var string */
	private $name;

	public function __construct(Server $server){
		PacketPool::init();

		$this->server = $server;

	}

	public function addStatistics($upload, $download){
		$this->upload += $upload;
		$this->download += $download;
	}

	public function getUpload(){
		return $this->upload;
	}

	public function getDownload(){
		return $this->download;
	}

	public function resetStatistics(){
		$this->upload = 0;
		$this->download = 0;
	}

	/**
	 * @return SourceInterface[]
	 */
	public function getInterfaces() : array{
		return $this->interfaces;
	}

	public function processInterfaces(){
		foreach($this->interfaces as $interface){
			$interface->process();
		}
	}

	/**
	 * @deprecated
	 * @param SourceInterface $interface
	 */
	public function processInterface(SourceInterface $interface) : void{
		$interface->process();
	}

	/**
	 * @param SourceInterface $interface
	 */
	public function registerInterface(SourceInterface $interface){
		$ev = new NetworkInterfaceRegisterEvent($interface);
		$ev->call();
		if(!$ev->isCancelled()){
			$interface->start();
			$this->interfaces[$hash = spl_object_hash($interface)] = $interface;
			if($interface instanceof AdvancedSourceInterface){
				$this->advancedInterfaces[$hash] = $interface;
				$interface->setNetwork($this);
			}
			$interface->setName($this->name);
		}
	}

	/**
	 * @param SourceInterface $interface
	 */
	public function unregisterInterface(SourceInterface $interface){
		(new NetworkInterfaceUnregisterEvent($interface))->call();
		unset($this->interfaces[$hash = spl_object_hash($interface)], $this->advancedInterfaces[$hash]);
	}

	/**
	 * Sets the server name shown on each interface Query
	 *
	 * @param string $name
	 */
	public function setName(string $name){
		$this->name = $name;
		foreach($this->interfaces as $interface){
			$interface->setName($this->name);
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}

	public function updateName(){
		foreach($this->interfaces as $interface){
			$interface->setName($this->name);
		}
	}

	/**
	 * @return Server
	 */
	public function getServer() : Server{
		return $this->server;
	}

	/**
	 * @param string $address
	 * @param int    $port
	 * @param string $payload
	 */
	public function sendPacket(string $address, int $port, string $payload){
		foreach($this->advancedInterfaces as $interface){
			$interface->sendRawPacket($address, $port, $payload);
		}
	}

	/**
	 * Blocks an IP address from the main interface. Setting timeout to -1 will block it forever
	 *
	 * @param string $address
	 * @param int    $timeout
	 */
	public function blockAddress(string $address, int $timeout = 300){
		foreach($this->advancedInterfaces as $interface){
			$interface->blockAddress($address, $timeout);
		}
	}

    /**
     * Unblocks an IP address in the main interface.
     * 
     * @param string $address
     */
	public function unblockAddress(string $address){
		foreach($this->advancedInterfaces as $interface){
			$interface->unblockAddress($address);
		}
	}

    /**
     * @param int $playerProtocol
     * 
     * @return int
     */
	public final static function convertProtocol(int $playerProtocol) : int{
		switch ($playerProtocol) {
			case ProtocolInfo::PROTOCOL_361:
				return ProtocolInfo::PROTOCOL_361;
			case ProtocolInfo::PROTOCOL_360:
				return ProtocolInfo::PROTOCOL_360;
			case ProtocolInfo::PROTOCOL_354:
			    return ProtocolInfo::PROTOCOL_354;
			case ProtocolInfo::PROTOCOL_353:
			case ProtocolInfo::PROTOCOL_352:
			case ProtocolInfo::PROTOCOL_351:
			    return ProtocolInfo::PROTOCOL_351;
			case ProtocolInfo::PROTOCOL_350:
			    return ProtocolInfo::PROTOCOL_350;
			case ProtocolInfo::PROTOCOL_342:
			    return ProtocolInfo::PROTOCOL_342;
			case ProtocolInfo::PROTOCOL_340:
			    return ProtocolInfo::PROTOCOL_340;
			case ProtocolInfo::PROTOCOL_332:
			    return ProtocolInfo::PROTOCOL_332;
			case ProtocolInfo::PROTOCOL_331:
			    return ProtocolInfo::PROTOCOL_331;
			case ProtocolInfo::PROTOCOL_330:
			    return ProtocolInfo::PROTOCOL_330;
			case ProtocolInfo::PROTOCOL_313:
			case ProtocolInfo::PROTOCOL_312:
			case ProtocolInfo::PROTOCOL_311:
			    return ProtocolInfo::PROTOCOL_311;
			case ProtocolInfo::PROTOCOL_310:
			    return ProtocolInfo::PROTOCOL_310;
			case ProtocolInfo::PROTOCOL_291:
			case ProtocolInfo::PROTOCOL_290:
			    return ProtocolInfo::PROTOCOL_290;
			case ProtocolInfo::PROTOCOL_282:
			    return ProtocolInfo::PROTOCOL_282;
			case ProtocolInfo::PROTOCOL_281:
			case ProtocolInfo::PROTOCOL_280:
			    return ProtocolInfo::PROTOCOL_280;
			case ProtocolInfo::PROTOCOL_274:
			    return ProtocolInfo::PROTOCOL_274;
			case ProtocolInfo::PROTOCOL_273:
			    return ProtocolInfo::PROTOCOL_273;
			case ProtocolInfo::PROTOCOL_271:
			    return ProtocolInfo::PROTOCOL_271;
			case ProtocolInfo::PROTOCOL_270:
			case ProtocolInfo::PROTOCOL_261:
			case ProtocolInfo::PROTOCOL_260:
			    return ProtocolInfo::PROTOCOL_260;
			case ProtocolInfo::PROTOCOL_250:
			case ProtocolInfo::PROTOCOL_240:
				return ProtocolInfo::PROTOCOL_240;
			case ProtocolInfo::PROTOCOL_224:
			case ProtocolInfo::PROTOCOL_223:
			case ProtocolInfo::PROTOCOL_222:
			case ProtocolInfo::PROTOCOL_221:
				return ProtocolInfo::PROTOCOL_221;
			case ProtocolInfo::PROTOCOL_220:
				return ProtocolInfo::PROTOCOL_220;
			case ProtocolInfo::PROTOCOL_200:
				return ProtocolInfo::PROTOCOL_200;
			case ProtocolInfo::PROTOCOL_201:
			case ProtocolInfo::PROTOCOL_160:
			case ProtocolInfo::PROTOCOL_150:
			case ProtocolInfo::PROTOCOL_141:
			case ProtocolInfo::PROTOCOL_140:
			    return ProtocolInfo::PROTOCOL_140;
			case ProtocolInfo::PROTOCOL_137:
			case ProtocolInfo::PROTOCOL_136:
			case ProtocolInfo::PROTOCOL_135:
			    return ProtocolInfo::PROTOCOL_135;
			case ProtocolInfo::PROTOCOL_134:
			case ProtocolInfo::PROTOCOL_133:
			    return ProtocolInfo::PROTOCOL_133;
			case ProtocolInfo::PROTOCOL_132:
				return ProtocolInfo::PROTOCOL_132;
			case ProtocolInfo::PROTOCOL_131:
			    return ProtocolInfo::PROTOCOL_131;
			case ProtocolInfo::PROTOCOL_130:
			    return ProtocolInfo::PROTOCOL_120;
			default:
				return ProtocolInfo::PROTOCOL_110;
		}
	}

    /**
     * @param int $playerProtocol
     * 
     * @return int
     */
	public static function convertChunkProtocol(int $playerProtocol) : int{
		switch ($playerProtocol) {
		    case ProtocolInfo::PROTOCOL_361:
			case ProtocolInfo::PROTOCOL_360:
				return ProtocolInfo::PROTOCOL_360;
			case ProtocolInfo::PROTOCOL_354:
			case ProtocolInfo::PROTOCOL_351:
			case ProtocolInfo::PROTOCOL_350:
			case ProtocolInfo::PROTOCOL_342:
			case ProtocolInfo::PROTOCOL_340:
			case ProtocolInfo::PROTOCOL_332:
			case ProtocolInfo::PROTOCOL_331:
			    return ProtocolInfo::PROTOCOL_331;
			case ProtocolInfo::PROTOCOL_330:
			case ProtocolInfo::PROTOCOL_311:
			case ProtocolInfo::PROTOCOL_310:
			    return ProtocolInfo::PROTOCOL_310;
			case ProtocolInfo::PROTOCOL_290:
			case ProtocolInfo::PROTOCOL_282:
			case ProtocolInfo::PROTOCOL_280:
			    return ProtocolInfo::PROTOCOL_280;
			case ProtocolInfo::PROTOCOL_274:
			case ProtocolInfo::PROTOCOL_273:
			case ProtocolInfo::PROTOCOL_271:
			case ProtocolInfo::PROTOCOL_260:
			case ProtocolInfo::PROTOCOL_240:
			case ProtocolInfo::PROTOCOL_221:
			case ProtocolInfo::PROTOCOL_220:
			case ProtocolInfo::PROTOCOL_200:
			case ProtocolInfo::PROTOCOL_140:
			case ProtocolInfo::PROTOCOL_135:
			case ProtocolInfo::PROTOCOL_133:
			case ProtocolInfo::PROTOCOL_132:
			case ProtocolInfo::PROTOCOL_131:
			case ProtocolInfo::PROTOCOL_120:
			    return ProtocolInfo::PROTOCOL_120;
			default:
				return ProtocolInfo::PROTOCOL_110;
		}
	}

    /**
     * @param int $playerProtocol
     * 
     * @return int
     */
	public static function convertCraftingProtocol(int $playerProtocol) : int{
		switch ($playerProtocol) {
		    case ProtocolInfo::PROTOCOL_361:
				return ProtocolInfo::PROTOCOL_361;
			case ProtocolInfo::PROTOCOL_360:
				return ProtocolInfo::PROTOCOL_360;
			case ProtocolInfo::PROTOCOL_354:
			case ProtocolInfo::PROTOCOL_351:
			case ProtocolInfo::PROTOCOL_350:
			    return ProtocolInfo::PROTOCOL_350;
			case ProtocolInfo::PROTOCOL_342:
			case ProtocolInfo::PROTOCOL_340:
			case ProtocolInfo::PROTOCOL_332:
			case ProtocolInfo::PROTOCOL_331:
			    return ProtocolInfo::PROTOCOL_331;
			case ProtocolInfo::PROTOCOL_330:
			    return ProtocolInfo::PROTOCOL_330;
			case ProtocolInfo::PROTOCOL_311:
			case ProtocolInfo::PROTOCOL_310:
			    return ProtocolInfo::PROTOCOL_310;
			case ProtocolInfo::PROTOCOL_290:
			case ProtocolInfo::PROTOCOL_282:
			case ProtocolInfo::PROTOCOL_280:
			    return ProtocolInfo::PROTOCOL_280;
			case ProtocolInfo::PROTOCOL_274:
			case ProtocolInfo::PROTOCOL_273:
			case ProtocolInfo::PROTOCOL_271:
			case ProtocolInfo::PROTOCOL_260:
			case ProtocolInfo::PROTOCOL_240:
			case ProtocolInfo::PROTOCOL_221:
			case ProtocolInfo::PROTOCOL_220:
			case ProtocolInfo::PROTOCOL_200:
			case ProtocolInfo::PROTOCOL_140:
			case ProtocolInfo::PROTOCOL_135:
			case ProtocolInfo::PROTOCOL_133:
			case ProtocolInfo::PROTOCOL_132:
			case ProtocolInfo::PROTOCOL_131:
			case ProtocolInfo::PROTOCOL_120:
			    return ProtocolInfo::PROTOCOL_120;
			default:
				return ProtocolInfo::PROTOCOL_110;
		}
	}

}
