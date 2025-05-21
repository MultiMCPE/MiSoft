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

namespace pocketmine\scheduler;

use pocketmine\Server;
use pocketmine\thread\ThreadSafeClosure;
use Closure;

class AsyncClosureTask extends AsyncTask{

	private ThreadSafeClosure $closure;
	private bool $hasOnCompletionClosure = false;

	public function __construct(Closure $asyncClosure, Closure $onCompletionClosure = null){
		$this->closure = new ThreadSafeClosure($asyncClosure, $this);

		if($onCompletionClosure !== null){
			$onCompletionClosure = Closure::bind($onCompletionClosure, $this);
			$this->storeLocal($onCompletionClosure);
			$this->hasOnCompletionClosure = true;
		}
	}

	public function onRun(): void{
		$this->closure->execute();
		unset($this->closure);
	}

	public function onCompletion(Server $server): void{
		if(!$this->hasOnCompletionClosure){
			return;
		}

		$closure = $this->fetchLocal();
		if($closure === null){
			return;
		}
		$closure($server);

		unset($closure);
		unset($this->closure);
		unset($this->hasOnCompletionClosure);
	}
}
