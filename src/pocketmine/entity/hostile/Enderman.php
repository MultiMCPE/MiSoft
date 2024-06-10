<?php

/**
 *
 *      ___                                          _
 *    /   | ____ ___  ______ _____ ___  ____ ______(_)___  ___
 *   / /| |/ __ `/ / / / __ `/ __ `__ \/ __ `/ ___/ / __ \/ _ \
 *  / ___ / /_/ / /_/ / /_/ / / / / / / /_/ / /  / / / / /  __/
 * /_/  |_\__, /\__,_/\__,_/_/ /_/ /_/\__,_/_/  /_/_/ /_/\___/
 *          /_/
 *
 * @author - MaruselPlay
 * @link https://github.com/MaruselPlay/Aquamarine-Private
 * VK - https://vk.com/maruselplay
 *
 *
 */

namespace pocketmine\entity\neutral;

use pocketmine\entity\Monster;
use pocketmine\Player;
use pocketmine\item\Item as ItemItem;
use pocketmine\block\Block;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\level\particle\PortalParticle;
use pocketmine\math\Vector3;
use pocketmine\level\generator\biome\Biome;
use pocketmine\level\weather\Weather;
use pocketmine\entity\behavior\{AngerBehavior, StrollBehavior, RandomLookaroundBehavior, LookAtPlayerBehavior};

class Enderman extends Neutral {
	const NETWORK_ID = self::ENDERMAN;

	public float $width = 0.6;
	public float $height = 2.9;
	public bool $shouldTeleport = false;
	public ?Block $block = null;

	public function initEntity(){
		$this->addBehavior(new AngerBehavior($this, 6));
		$this->addBehavior(new StrollBehavior($this));
		$this->addBehavior(new LookAtPlayerBehavior($this));
		$this->addBehavior(new RandomLookaroundBehavior($this));
    $this->setMaxHealth(40);
		parent::initEntity();
	}

  public function getDamage(): int{
    return match($this->getLevel()->getServer()->getDifficulty()){
      1 => 4,
      2 => 7,
      3 => 10,
      default => 0
    };
  }

	public function attack(EntityDamageEvent $source): bool{
		if($source->getCause() === EntityDamageEvent::CAUSE_PROJECTILE){
			$this->shouldTeleport = true;
			return false;
		}
		return parent::attack($source);
	}

	public function randomFloat($min = -0.8, $max = 0.8){
		return $min + mt_rand() / mt_getrandmax() * ($max - $min);
	}

	public function entityBaseTick(int $tickDiff = 1): bool{
		$hasUpdate = parent::entityBaseTick($tickDiff);

		$rainyWeather = [Weather::RAIN, Weather::RAINY_THUNDER, Weather::THUNDER];
		if($this->insideOfWater() or (in_array($this->getLevel()->getWeather()->getWeather(), $rainyWeather) and $this->getLevel()->getBiomeId($this->x, $this->z) !== Biome::DESERT and !$this->hasHeadBlock())){
	    $this->attack(new EntityDamageEvent($this, EntityDamageEvent::CAUSE_DROWNING, 1));
			$this->shouldTeleport = true;
		}

		if(mt_rand(0, 200) === 1 or $this->shouldTeleport){
			$x = $this->getFloorX() + mt_rand(-8, 8);
			$z = $this->getFloorZ() + mt_rand(-8, 8);
			$y = $this->getLevel()->getHighestBlockAt($x, $z) + 1;
			for($i = 0; $i < 30; $i++){
				$this->getLevel()->addParticle(new PortalParticle(new vector3($x + $this->randomFloat(), $y + $this->randomFloat(-0.8, 2.5), $z + $this->randomFloat())));
			}
			for($i = 0; $i < 30; $i++){
				$this->getLevel()->addParticle(new PortalParticle(new vector3($x + $this->randomFloat(), $y + $this->randomFloat(-0.8, 2.5), $z + $this->randomFloat())));
			}
			$this->getLevel()->addSound(new EndermanTeleportSound($this));
			$this->teleport(new Vector3($x, $y, $z));
			$this->getLevel()->addSound(new EndermanTeleportSound($this));
			$this->shouldTeleport = false;
		}

		if(mt_rand(0, 1000) === 0 and !$this->hasBlock()){
			$direction = $this->getDirectionVector()->multiply(1.5);
			$targetBlock = $this->getLevel()->getBlock($this->add($direction->x, $direction->y, $direction->z));
			$allowedBlocks = [1, 2, 3, 4, 5, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 24, 35, 37, 38, 39, 40, 41, 42, 43, 46, 47, 48, 56, 57, 58, 73, 74, 79, 81, 82, 86, 87, 88, 89, 91, 98, 99, 100, 103];
			if(in_array($targetBlock->getId(), $allowedBlocks)){
				$this->setDataProperty(self::DATA_ENDERMAN_HELD_ITEM_ID, self::DATA_TYPE_SHORT, $targetBlock->getId());
				$this->setDataProperty(self::DATA_ENDERMAN_HELD_ITEM_DAMAGE, self::DATA_TYPE_SHORT, $targetBlock->getDamage());
				$this->block = $targetBlock;
				$this->getLevel()->setBlock($targetBlock, Block::get(0, 0));
			}
		}elseif(mt_rand(0, 1000) === 0 and $this->hasBlock()){
			$direction = $this->getDirectionVector()->multiply(1.5);
			$targetBlock = $this->getLevel()->getBlock($this->add($direction->x, $direction->y, $direction->z));
			if($targetBlock->getId() === 0){
				$this->setDataProperty(self::DATA_ENDERMAN_HELD_ITEM_ID, self::DATA_TYPE_SHORT, 0);
				$this->setDataProperty(self::DATA_ENDERMAN_HELD_ITEM_DAMAGE, self::DATA_TYPE_SHORT, 0);
				$this->getLevel()->setBlock($targetBlock, $this->block);
				$this->block = null;
			}
		}

		return $hasUpdate;
	}

	public function hasBlock(): bool{
		return $this->block !== null;
	}

  public function getMaxAttackDistance(): float{
    return 1;
  }

  public function getMaxArgDistance(): int{
    return 30;
  }

  public function getDrops(): array{
		$cause = $this->lastDamageCause;
		$drops = [];
    $lootingL = 0;
    if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
      if($damager instanceof Player){
				$lootingL = $damager->getInventory()->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING);
			}
    }
    $drops[] = ItemItem::get(ItemItem::ENDER_PEARL, 0, mt_rand(0, 2 + $lootingL));
		return $drops;
	}

	public function getName(): string{
		return "Enderman";
	}

  public function getXpDropAmount(): int{
    return 5;
  }
}
