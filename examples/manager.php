<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 *
 * Crontab manager usage example
 * @author alxmsl
 * @date 7/23/14
 */

include('../source/Autoloader.php');

use alxmsl\CronManager\CrontabCommand;
use alxmsl\CronManager\CronManager;

$NewCommand = new CrontabCommand();
$NewCommand->unserialize('23 */2 * * * echo "Running at 0:23, 2:23, 4:23 etc."');
$NewCommand->setEnvironment('/usr/bin/env REVISION=4');

$Manager = CronManager::getInstance(`whoami`);
$Manager->addCommands([$NewCommand]);
$Manager->update();
