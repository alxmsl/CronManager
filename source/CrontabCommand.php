<?php
/*
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace alxmsl\CronManager;
use InvalidArgumentException;
use Serializable;

/**
 * Crontab command class
 * @author alxmsl
 * @date 7/23/14
 */ 
final class CrontabCommand implements Serializable {
    /**
     * Crontab execution expression
     */
    const EXPRESSION = '/^([\d\/\-\*,]+\s[\d\/\-\*,]+\s[\d\/\-\*,\?LW]+\s[\d\/\-\*,(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)]+\s[\d\/\-\*,\?L#(mon|tue|wed|thu|fre|sat|sun)]+)/';

    /**
     * @var string command execution expression string
     */
    private $expression = '';

    /**
     * @var string command environment setter
     */
    private $environment = '';

    /**
     * @var string execution command
     */
    private $command = '';

    /**
     * @var bool is a comment string
     */
    private $isComment = false;

    /**
     * Expression setter
     * @param string $expression expression value
     * @return CrontabCommand self instance
     */
    public function setExpression($expression) {
        if (preg_match(self::EXPRESSION, $expression)) {
            $this->expression = trim($expression);
            return $this;
        } else {
            throw new InvalidArgumentException();
        }
    }

    /**
     * Expression getter
     * @return string command expression value
     */
    public function getExpression() {
        return $this->expression;
    }

    /**
     * Command environment setter
     * @param string $environment environment setter command
     * @return CrontabCommand self instance
     */
    public function setEnvironment($environment) {
        $this->environment = trim($environment);
        return $this;
    }

    /**
     * Command environment getter
     * @return string environment setter command
     */
    public function getEnvironment() {
        return $this->environment;
    }

    /**
     * Execution command setter
     * @param string $command execution command
     * @return CrontabCommand self instance
     */
    public function setCommand($command) {
        $this->command = trim($command);
        return $this;
    }

    /**
     * Execution command getter
     * @return string execution command
     */
    public function getCommand() {
        return $this->command;
    }

    /**
     * Is comment string getter
     * @return boolean if command is a comment string
     */
    public function isComment() {
        return $this->isComment;
    }

    /**
     * Serialization method
     * @return string serialized value of the command
     */
    public function serialize() {
        return sprintf('%s %s %s', $this->getExpression(), $this->getEnvironment(), $this->getCommand());
    }

    /**
     * Deserialization method
     * @param string $string serialized value of the command
     */
    public function unserialize($string) {
        $command = trim($string);
        $this->isComment = strpos($command, '#') === 0;
        if (!$this->isComment) {
            $parts = [];
            $found = preg_match(self::EXPRESSION, $command, $parts);
            if ($found) {
                $this->setExpression($parts[1]);
                $this->setCommand(substr($command, strlen($this->expression) + 1));
            }
        }
    }

    /**
     * String casting method
     * @return string serialized value of the command
     */
    public function __toString() {
        return $this->serialize();
    }
}
 