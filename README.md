Cron manager class for PHP
==========

    use alxmsl\CronManager\CrontabCommand;
    use alxmsl\CronManager\CronManager;

    $NewCommand = new CrontabCommand();
    $NewCommand->unserialize('23 */2 * * * echo "Running at 0:23, 2:23, 4:23 etc."');
    $NewCommand->setEnvironment('/usr/bin/env REVISION=4');

    $Manager = CronManager::getInstance(`whoami`);
    $Manager->addCommands([$NewCommand]);
    $Manager->update();

License
-------
Copyright © 2014 Alexey Maslov <alexey.y.maslov@gmail.com>
This work is free. You can redistribute it and/or modify it under the
terms of the Do What The Fuck You Want To Public License, Version 2,
as published by Sam Hocevar. See the COPYING file for more details.
