<?php
/*
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace alxmsl\CronManager;
use LogicException;

/**
 * Crontab class
 * @author alxmsl
 * @date 7/23/14
 */ 
final class Crontab {
    /**
     * @var string crontab user name
     */
    private $user = '';

    /**
     * @var string crontab file cache
     */
    private $crontab = '';

    /**
     * @var CrontabCommand[] current crontab commands
     */
    private $commands = [];

    /**
     * @param string $user crontab user name, or empty for current user
     * @param string $fileName crontab file or empty, if need current crontab
     */
    public function __construct($user = '', $fileName = '') {
        $this->user = trim($user);
        $this->load($fileName);
    }

    /**
     * Add crontab command method
     * @param CrontabCommand $Command crontab command
     * @return $this self instance
     */
    public function add(CrontabCommand $Command) {
        $this->commands[] = $Command;
        return $this;
    }

    /**
     * Delete commands from current crontab
     * @param string $expression preg expression for command deletion
     * @return $this self instance
     */
    public function delete($expression) {
        $this->commands = array_filter($this->commands, function(CrontabCommand $Command) use ($expression) {
            return !preg_match($expression, sprintf('%s %s', $Command->getEnvironment(), $Command->getCommand()));
        });
        return $this;
    }

    /**
     * Get commands from current crontab method
     * @param string $expression preg expression for command finding
     * @return CrontabCommand[] found commands
     */
    public function get($expression) {
        return array_filter($this->commands, function(CrontabCommand $Command) use ($expression) {
            return preg_match($expression, sprintf('%s %s', $Command->getEnvironment(), $Command->getCommand()));
        });
    }

    /**
     * Get all crontab commands
     * @return CrontabCommand[] crontab commands
     */
    public function getAll() {
        return $this->commands;
    }

    /**
     * Save current commands array to user's crontab file
     * @return $this self instance
     */
    public function save() {
        if (!empty($this->commands)) {
            if (empty($this->user)) {
                $handle = popen('crontab -', 'w');
            } else {
                $handle = popen(sprintf('crontab -u %s -', $this->user), 'w');
            }
            if ($handle !== false) {
                foreach ($this->commands as $Command) {
                    fwrite($handle, sprintf("%s\n", (string) $Command));
                }
                pclose($handle);
            }
        } else {
            if (empty($this->user)) {
                $result = `crontab -r 2>/dev/null`;
            } else {
                $result = `crontab -u $this->user -r 2>/dev/null`;
            }
        }
        return $this;
    }

    /**
     * Save current commands array to needed file
     * @param string $fileName filename
     * @return $this self instance
     * @throws LogicException when crontab file already locked
     */
    public function saveTo($fileName = '') {
        if (!empty($this->commands)) {
            $handle = fopen($fileName, 'w');
            if ($handle !== false) {
                if (flock($handle, LOCK_EX)) {
                    foreach ($this->commands as $Command) {
                        fwrite($handle, sprintf("%s\n", (string) $Command));
                    }
                } else {
                    throw new LogicException(sprintf('file %s already locked', $fileName));
                }
                fclose($handle);
            }
        } else {
            file_put_contents($fileName, '');
        }
        return $this;
    }

    /**
     * Clear current commands method
     * @return $this self instance
     */
    public function clear() {
        $this->commands = [];
        return $this;
    }

    /**
     * Load current crontab method
     * @param string $fileName crontab file or empty, if need current crontab
     * @return $this self instance
     */
    private function load($fileName = '') {
        if (empty($fileName)) {
            if (empty($this->user)) {
                $this->crontab = `crontab -l 2>/dev/null`;
            } else {
                $this->crontab = `crontab -u $this->user -l 2>/dev/null`;
            }
        } else {
            $this->crontab = file_get_contents($fileName);
        }
        $this->parse();
        return $this;
    }

    /**
     * Parse crontab file method
     * @return $this self instance
     */
    private function parse() {
        foreach (explode("\n", $this->crontab) as $line) {
            $command = trim($line);
            if (!empty($command)) {
                $Command = new CrontabCommand();
                $Command->unserialize($command);
                $this->commands[] = $Command;
            }
        }
        return $this;
    }
}
 