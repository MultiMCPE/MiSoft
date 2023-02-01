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

namespace pocketmine\inventory\PETransaction;

use pocketmine\inventory\CraftingGrid;
use pocketmine\Player;
use function get_class;

class TransactionQueue{
    public const DEFAULT_ALLOWED_RETRIES = 5;

    /** @var Player */
    protected $player;
    /** @var \SplQueue[] */
    protected $transactions;
    /** @var CraftingGrid */
    protected $transactionInventory;

    /**
     * @param Player $player
     */
    public function __construct(Player $player){
        $this->player = $player;
        $this->transactionInventory = new class($player, 6) extends CraftingGrid{};
        $this->transactions = new \SplFixedArray(Transaction::PRIORITY_MIN + 1);

        for($i = 0; $i < $this->transactions->getSize(); $i++){
            $this->transactions[$i] = new \SplQueue();
        }
    }

    /**
     * @return Player
     */
    public function getPlayer() : Player{
        return $this->player;
    }

    /**
     * @return CraftingGrid
     */
    public function getInventory() : CraftingGrid{
        return $this->transactionInventory;
    }

    /**
     * @param Transaction $transaction
     */
    public function addTransaction(Transaction $transaction) : void{
        $this->transactions[$transaction->getPriority()]->enqueue($transaction);
    }

    public function onCloseWindow() : void{
        foreach($this->transactions as $i => $transactionQueue){
            while(!$transactionQueue->isEmpty()){
                $transactionQueue->dequeue()->execute($this);
            }
        }

        foreach($this->transactionInventory->getContents() as $item){
            $this->transactionInventory->removeItem($item);
            $this->player->getInventory()->addItem($item);
        }
    }

    public function execute() : void{
		/** @var Transaction[] */
		$failed = [];

        for($i = 0; $i < $this->transactions->getSize(); $i++){
            $transactionQueue = $this->transactions[$i];
            if($transactionQueue->isEmpty()){
                continue;
            }

            while(!$transactionQueue->isEmpty()){
                $transaction = $transactionQueue->dequeue();
                if(!$transaction->execute($this)){
                    $this->player->getServer()->getLogger()->debug("Can't execute " . get_class($transaction) . ": " . $transaction->getLastError());
                    if($transaction->addFailure() >= self::DEFAULT_ALLOWED_RETRIES){
                        /* Transaction failed completely after several retries, hold onto it to send a slot update */
                        $failed[] = $transaction;
                    }else{
                        /* Add the transaction to the back of the queue to be retried on the next tick */
                        $this->addTransaction($transaction);
                    }
                }
            }
        }

		foreach($failed as $f){
			$f->sendSlotUpdate($this->player);
		}
    }
}