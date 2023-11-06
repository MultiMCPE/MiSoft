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

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\types\ChunkPosition;
use function count;
use function pack;

class LevelChunkPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::LEVEL_CHUNK_PACKET;

    /** @var ChunkPosition */
    private $chunkPosition;
	/** @var int */
	private $subChunkCount;
	/** @var bool */
	private $cacheEnabled;
	/** @var int[] */
	private $usedBlobHashes = [];
	/** @var string */
	private $extraPayload;

    //this appears large enough for a world height of 1024 blocks - it may need to be increased in the future
    private const MAX_BLOB_HASHES = 64;

	public static function withoutCache(ChunkPosition $chunkPosition, int $subChunkCount, string $payload) : self{
		$result = new self;
		$result->chunkPosition = $chunkPosition;
		$result->subChunkCount = $subChunkCount;
		$result->extraPayload = $payload;

		$result->cacheEnabled = false;

		return $result;
	}

	public static function withCache(ChunkPosition $chunkPosition, int $subChunkCount, array $usedBlobHashes, string $extraPayload) : self{
		(static function(int ...$hashes){})(...$usedBlobHashes);
		$result = new self;
		$result->chunkPosition = $chunkPosition;
		$result->subChunkCount = $subChunkCount;
		$result->extraPayload = $extraPayload;

		$result->cacheEnabled = true;
		$result->usedBlobHashes = $usedBlobHashes;

		return $result;
	}

    /**
     * @var ChunkPosition
     */
    public function getChunkPosition() : ChunkPosition{
        return $this->chunkPosition;
    }

	/**
	 * @return int
	 */
	public function getSubChunkCount() : int{
		return $this->subChunkCount;
	}

	/**
	 * @return bool
	 */
	public function isCacheEnabled() : bool{
		return $this->cacheEnabled;
	}

	/**
	 * @return int[]
	 */
	public function getUsedBlobHashes() : array{
		return $this->usedBlobHashes;
	}

	/**
	 * @return string
	 */
	public function getExtraPayload() : string{
		return $this->extraPayload;
	}

	protected function decodePayload() : void{
	    $this->chunkPosition = ChunkPosition::read($this);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_360){
	    	$this->subChunkCount = $this->getUnsignedVarInt();
	    	$this->cacheEnabled = (($this->get(1) !== "\x00"));
	    	if($this->cacheEnabled){
				$count = $this->getUnsignedVarInt();
				if($count > self::MAX_BLOB_HASHES){
					throw new \InvalidArgumentException("Expected at most " . self::MAX_BLOB_HASHES . " blob hashes, got " . $count);
				}
				for($i = 0; $i < $count; ++$i){
			    	$this->usedBlobHashes[] = (Binary::readLLong($this->get(8)));
		    	}
		    }
		}
		$this->extraPayload = $this->getString();
	}

	protected function encodePayload() : void{
	    $this->chunkPosition->write($this);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_360){
	    	$this->putUnsignedVarInt($this->subChunkCount);
	    	($this->buffer .= ($this->cacheEnabled ? "\x01" : "\x00"));
	    	if($this->cacheEnabled){
		    	$this->putUnsignedVarInt(count($this->usedBlobHashes));
		    	foreach($this->usedBlobHashes as $hash){
			    	($this->buffer .= (pack("VV", $hash & 0xFFFFFFFF, $hash >> 32)));
		    	}
			}
		}
		$this->putString($this->extraPayload);
	}

	public function handle(NetworkSession $handler) : bool{
		return $handler->handleLevelChunk($this);
	}
}
