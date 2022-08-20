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

namespace pocketmine\level\particle;

use pocketmine\math\Vector3;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;

use pocketmine\network\mcpe\multiversion\ParticleEnums;

use pocketmine\Server;

use function is_null;

class GenericParticle extends Particle{

	protected $id;
	protected $data;
	protected $customSpawnName = null;

	public function __construct(Vector3 $pos, $id, $data = 0){
		parent::__construct($pos->x, $pos->y, $pos->z);
		$this->id = $id;
		$this->data = $data;
	}

	public function spawnFor($players){
		foreach($players as $playerId => $player){
		    $pk = null;
			if(($protocol = $player->getProtocol()) >= ProtocolInfo::PROTOCOL_360 && !is_null($this->customSpawnName)){
				$pk = new SpawnParticleEffectPacket();
				$pk->position = $this->asVector3();
				$pk->particleName = $this->customSpawnName;
			} else {
			    $id = ParticleEnums::getParticleArrayByProtocol($protocol);
			    if(isset($id[$this->id])){
		        	$pk = new LevelEventPacket();
		        	$pk->evid = LevelEventPacket::EVENT_ADD_PARTICLE_MASK | $id[$this->id];
		        	$pk->position = $this->asVector3();
		        	$pk->data = $this->data;
			    }
			}

			if(!is_null($pk)){
			    $player->dataPacket($pk);
			}

			unset($players[$playerId]);
		}
	}

}
