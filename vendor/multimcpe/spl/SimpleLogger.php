<?php

/*
 * PocketMine Standard PHP Library
 * Copyright (C) 2018 PocketMine Team <https://github.com/pmmp/PocketMine-SPL>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
*/

declare(strict_types=1);

class SimpleLogger implements \Logger{
	public function emergency(mixed $message) : void{
		$this->log(LogLevel::EMERGENCY, $message);
	}

	public function alert(mixed $message) : void{
		$this->log(LogLevel::ALERT, $message);
	}

	public function critical(mixed $message) : void{
		$this->log(LogLevel::CRITICAL, $message);
	}

	public function error(mixed $message) : void{
		$this->log(LogLevel::ERROR, $message);
	}

	public function warning(mixed $message) : void{
		$this->log(LogLevel::WARNING, $message);
	}

	public function notice(mixed $message) : void{
		$this->log(LogLevel::NOTICE, $message);
	}

	public function info(mixed $message) : void{
		$this->log(LogLevel::INFO, $message);
	}

	public function debug(mixed $message) : void{
		$this->log(LogLevel::DEBUG, $message);
	}

	public function log(mixed $level, mixed $message) : void{
		echo "[" . strtoupper($level) . "] " . $message . PHP_EOL;
	}

	public function logException(\Throwable $e, $trace = null){
		$this->critical($e->getMessage());
		echo $e->getTraceAsString();
	}
}
