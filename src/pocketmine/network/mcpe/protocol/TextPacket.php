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

use pocketmine\network\mcpe\multiversion\MultiversionEnums;
use pocketmine\network\mcpe\NetworkSession;
use function chr;
use function count;
use function ord;

class TextPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::TEXT_PACKET;

	public const TYPE_RAW = 0;
	public const TYPE_CHAT = 1;
	public const TYPE_TRANSLATION = 2;
	public const TYPE_POPUP = 3;
	public const TYPE_JUKEBOX_POPUP = 4;
	public const TYPE_TIP = 5;
	public const TYPE_SYSTEM = 6;
	public const TYPE_WHISPER = 7;
	public const TYPE_ANNOUNCEMENT = 8;
	public const TYPE_JSON_WHISPER = 9;
	public const TYPE_JSON = 10;
	public const TYPE_JSON_ANNOUNCEMENT = 11;

	/** @var int */
	public $type;
	/** @var bool */
	public $needsTranslation = false;
	/** @var string */
	public $sourceName;
	/** @var string */
	public $sourceThirdPartyName = "";
	/** @var int */
	public $sourcePlatform = 0;
	/** @var string */
	public $message;
	/** @var string[] */
	public $parameters = [];
	/** @var string */
	public $xboxUserId = "";
	/** @var string */
	public $platformChatId = "";

	protected function decodePayload(){
		$this->type = MultiversionEnums::getMessageType($this->getProtocol(), (ord($this->get(1))));
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
	    	$this->needsTranslation = (($this->get(1) !== "\x00"));
		}
		switch($this->type){
			case self::TYPE_CHAT:
			case self::TYPE_WHISPER:
			/** @noinspection PhpMissingBreakStatementInspection */
			case self::TYPE_ANNOUNCEMENT:
				$this->sourceName = $this->getString();
				if($this->getProtocol() >= ProtocolInfo::PROTOCOL_200 && $this->getProtocol() < ProtocolInfo::PROTOCOL_290){
		        	$this->sourceThirdPartyName = $this->getString();
		        	$this->sourcePlatform = $this->getVarInt();
				}
			case self::TYPE_RAW:
			case self::TYPE_TIP:
			case self::TYPE_SYSTEM:
			case self::TYPE_JSON_WHISPER:
			case self::TYPE_JSON:
			case self::TYPE_JSON_ANNOUNCEMENT:
				$this->message = $this->getString();
				break;
			case self::TYPE_POPUP:
			    if($this->getProtocol() < ProtocolInfo::PROTOCOL_134){
			        $this->sourceName = $this->getString();
			        $this->message = $this->getString();
			        break;
			    }
			case self::TYPE_TRANSLATION:
			case self::TYPE_JUKEBOX_POPUP:
				$this->message = $this->getString();
				$count = $this->getUnsignedVarInt();
				for($i = 0; $i < $count; ++$i){
					$this->parameters[] = $this->getString();
				}
				break;
		}

        if($this->getProtocol() >= ProtocolInfo::PROTOCOL_134){
	    	$this->xboxUserId = $this->getString();
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_200){
	        	$this->platformChatId = $this->getString();
	    	}
		}
	}

	protected function encodePayload(){
		($this->buffer .= chr(MultiversionEnums::getMessageTypeId($this->getProtocol(), $this->type)));
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_130){
	    	($this->buffer .= ($this->needsTranslation ? "\x01" : "\x00"));
		}
		switch($this->type){
			case self::TYPE_CHAT:
			case self::TYPE_WHISPER:
			/** @noinspection PhpMissingBreakStatementInspection */
			case self::TYPE_ANNOUNCEMENT:
				$this->putString($this->sourceName);
				if($this->getProtocol() >= ProtocolInfo::PROTOCOL_200 && $this->getProtocol() < ProtocolInfo::PROTOCOL_290){
		        	$this->putString($this->sourceThirdPartyName);
		        	$this->putVarInt($this->sourcePlatform);
				}
			case self::TYPE_RAW:
			case self::TYPE_TIP:
			case self::TYPE_SYSTEM:
			case self::TYPE_JSON_WHISPER:
			case self::TYPE_JSON:
			case self::TYPE_JSON_ANNOUNCEMENT:
				$this->putString($this->message);
				break;
			case self::TYPE_POPUP:
			    if($this->getProtocol() < ProtocolInfo::PROTOCOL_134){
			        $this->putString($this->sourceName);
			        $this->putString($this->message);
			        break;
			    }
			case self::TYPE_TRANSLATION:
			case self::TYPE_JUKEBOX_POPUP:
				$this->putString($this->message);
				$this->putUnsignedVarInt(count($this->parameters));
				foreach($this->parameters as $p){
					$this->putString($p);
				}
				break;
		}

        if($this->getProtocol() >= ProtocolInfo::PROTOCOL_134){
	    	$this->putString($this->xboxUserId);
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_200){
	        	$this->putString($this->platformChatId);
	    	}
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleText($this);
	}
}
