<?php
/*
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace alxmsl\CronManager;
use RuntimeException;

/**
 * Cron manager class
 * @author alxmsl
 * @date 7/27/14
 */ 
final class CronManager {
    /**
     * @var CronManager[] manager instances
     */
    private static $Instances = [];

    /**
     * @param string $userName crontab user name
     * @return CronManager|null manager instance
     */
    public static function getInstance($userName) {
        if (!array_key_exists($userName, self::$Instances)) {
            self::$Instances[$userName] = new self($userName);
        }
        return self::$Instances[$userName];
    }

    /**
     * @var null|Crontab current crontab
     */
    private $CurrentCrontab = null;

    /**
     * @var null|LockerInterface exernal locker instance
     */
    private $Locker = null;

    /**
     * @var string crontab user name
     */
    private $userName = '';

    /**
     * @var CrontabCommand[] added commands
     */
    private $commands = array();

    /**
     * @param string $userName crontab user name
     */
    private function __construct($userName) {
        $this->userName = (string) $userName;
    }

    /**
     * External locker setter
     * @param LockerInterface|null $Locker external locker instance
     * @return CronManager self instance
     */
    public function setLocker(LockerInterface $Locker) {
        $this->Locker = $Locker;
        return $this;
    }

    /**
     * External locker instance getter
     * @return LockerInterface|null external locker instance
     */
    public function getLocker() {
        return $this->Locker;
    }

    /**
     * Add cron commands
     * @param CrontabCommand[] $commands added cron commands
     * @return CronManager self instance
     */
    public function addCommands(array $commands) {
        $this->commands = $commands;
        return $this;
    }

    /**
     * Add cron commands from file
     * @param string $fileName cron commands filename
     * @return CronManager self instance
     */
    public function add($fileName) {
        $Crontab = new Crontab('', $fileName);
        $this->commands = $Crontab->getAll();
        return $this;
    }

    /**
     * Update crontab method
     * @param bool $byArguments update crontab by arguments or by command
     * @throws RuntimeException when lock could not acquire
     */
    public function update($byArguments = false) {
        $result = true;
        if (!is_null($this->Locker)) {
            $result = $this->Locker->lock();
        }

        if ($result) {
            $this->CurrentCrontab = new Crontab();
            foreach ($this->commands as $Command) {
                $expression = $byArguments ? $Command->getArguments() : $Command->getCommand();
                $this->CurrentCrontab->delete(sprintf('!%s!', preg_quote($expression)));
                $this->CurrentCrontab->add($Command);
            }
            $this->CurrentCrontab->save();

            if (!is_null($this->Locker)) {
                $this->Locker->unlock();
            }
        } else {
            throw new RuntimeException('could not acquire update lock');
        }
    }
}
 