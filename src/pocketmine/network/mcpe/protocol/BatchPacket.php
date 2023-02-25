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

use ErrorException;
use Generator;
use InvalidArgumentException;
use pocketmine\utils\Binary;


use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\mcpe\NetworkSession;
use UnexpectedValueException;
use function assert;
use function chr;
use function get_class;
use function ord;
use function strlen;
use function zlib_decode;
use function zlib_encode;
use function mb_strpos;
use const ZLIB_ENCODING_RAW;

class BatchPacket extends DataPacket{
	public const NETWORK_ID = 0xfe;

	/** @var string */
	public $payload = "";
	/** @var bool */
	public $compressionEnabled = true;
	/** @var int */
	protected $compressionLevel = 7;

	public function canBeBatched() : bool{
		return false;
	}

	public function canBeSentBeforeLogin() : bool{
		return true;
	}

	protected function decodeHeader(){
		$pid = (ord($this->get(1)));
		assert($pid === static::NETWORK_ID);
	}

	protected function decodePayload(){
		$this->payload = $this->getRemaining();
	}

	protected function encodeHeader(){
		($this->buffer .= chr(static::NETWORK_ID));
	}

	protected function encodePayload(){
	    if($this->compressionEnabled){
	    	$payload = zlib_encode($this->payload, ($this->getProtocol() >= ProtocolInfo::PROTOCOL_406 ? ZLIB_ENCODING_RAW : ZLIB_ENCODING_DEFLATE), $this->compressionLevel);
	    }else{
	        $payload = $this->payload;
	    }
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
		    ($this->buffer .= $payload);
		}else{
		    $this->putString($payload);
		}
	}

	/**
	 * @param DataPacket $packet
	 */
	public function addPacket(DataPacket $packet){
		if(!$packet->canBeBatched()){
			throw new InvalidArgumentException(get_class($packet) . " cannot be put inside a BatchPacket");
		}
		if(!$packet->isEncoded){
			$packet->encode();
		}

		$this->payload .= Binary::writeUnsignedVarInt(strlen($packet->buffer)) . $packet->buffer;
	}

	/**
	 * @return Generator
	 */
	public function getPackets(){
		$stream = new NetworkBinaryStream($this->payload);
		$count = 0;
		while(!$stream->feof()){
			if($count++ >= 500){
				throw new UnexpectedValueException("Too many packets in a single batch");
			}
			yield $stream->getString();
		}
	}

	public function getCompressionLevel() : int{
		return $this->compressionLevel;
	}

	public function setCompressionLevel(int $level){
		$this->compressionLevel = $level;
	}

	public function handle(NetworkSession $session) : bool{
	    $data = $this->payload;
	    try{
	        $data = zlib_decode($data, 1024 * 1024 * 2); //Max 2MB
	    }catch(ErrorException $e){ //zlib decode error
	        if($session->isCompressionEnabled() && $this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
		        $data = "";
	        }else{
	            if($session->isCompressionEnabled()){
	                if($this->getProtocol() < ProtocolInfo::PROTOCOL_110){
	                    if(!(PacketPool::getPacket($data, $session->getProtocol())) instanceof UnknownPacket){
	                        $data = Binary::writeUnsignedVarInt(strlen($data)) . $data; // for <= 1.0
	                    }
	                }
	            }
	        }
	    }
        $this->payload = $data;

        if($this->payload === ""){
            return false;
        }

		foreach($this->getPackets() as $buf){
			$pk = PacketPool::getPacket($buf, $session->getProtocol());
            $pk->setProtocol($session->getProtocol());

			if(!$pk->canBeBatched()){
				throw new UnexpectedValueException("Received invalid " . get_class($pk) . " inside BatchPacket");
			}

			$session->handleDataPacket($pk);
		}

		return true;
	}
}
