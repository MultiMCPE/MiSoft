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
 * @link https://vk.com/maruselplay
 * @link https://github.com/MaruselPlay
 *
 *
 */

namespace pocketmine\scheduler;

use pocketmine\Server;
use Closure;
use Threaded;

class AsyncClosureTask extends AsyncTask{

	private Threaded $closureStorage;
  private bool $hasOnCompletionClosure = false;

	public function __construct(Closure $asyncClosure, Closure $onCompletionClosure = null){
		$this->closureStorage = new Threaded();
    $this->closureStorage[] = $asyncClosure;

		if($onCompletionClosure !== null){
      $this->hasOnCompletionClosure = true;
			$onCompletionClosure = Closure::bind($onCompletionClosure, $this);
			$this->storeLocal($onCompletionClosure);
		}
	}

	public function onRun(): void{
    $closure = $this->closureStorage->shift();
		($closure)();
	}

	public function onCompletion(Server $server): void{
    if(!$this->hasOnCompletionClosure){
      return;
    }
    $closure = $this->fetchLocal();
    if($closure === null){
      return;
    }
    ($closure)($server);
	}
}
