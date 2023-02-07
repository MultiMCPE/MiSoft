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

use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkSession;

class UseItemPacket extends DataPacket{

	const NETWORK_ID = ProtocolInfo::USE_ITEM_PACKET;

	public $x;
	public $y;
	public $z;
	public $blockId;
	public $face;

	/** @var Vector3 */
	public $playerPos;
	/** @var Vector3 */
	public $clickPos;

	public $slot;
	/** @var Item */
	public $item;

	public function decodePayload(){
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->blockId = $this->getUnsignedVarInt();
		$this->face = $this->getVarInt();
		$this->clickPos = $this->getVector3();
		$this->playerPos = $this->getVector3();
		$this->slot = $this->getVarInt();
		$this->item = $this->getSlot($this->getProtocol());
	}

	public function encodePayload(){
		$this->putUnsignedVarInt($this->blockId);
		$this->putUnsignedVarInt($this->face);
		$this->putVector3($this->clickPos);
		$this->putVector3($this->playerPos);
		$this->putVarInt($this->slot);
		$this->putSlot($this->item, $this->getProtocol());
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleUseItem($this);
	}
}
