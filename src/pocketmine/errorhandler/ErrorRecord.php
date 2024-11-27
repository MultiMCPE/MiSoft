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

namespace pocketmine\errorhandler;

final class ErrorRecord{

	/** @var int */
	private $severity;
	/** @var string */
	private $message;
	/** @var string */
	private $file;
	/** @var int */
	private $line;

	public function __construct(int $severity, string $message, string $file, int $line){
		$this->severity = $severity;
		$this->message = $message;
		$this->file = $file;
		$this->line = $line;
	}

	public function getSeverity() : int{
		return $this->severity;
	}

	public function getMessage() : string{
		return $this->message;
	}

	public function getFile() : string{
		return $this->file;
	}

	public function getLine() : int{
		return $this->line;
	}
}

