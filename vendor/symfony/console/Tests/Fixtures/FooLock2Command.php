<?php

namespace MolliePrefix;

use MolliePrefix\Symfony\Component\Console\Command\Command;
use MolliePrefix\Symfony\Component\Console\Command\LockableTrait;
use MolliePrefix\Symfony\Component\Console\Input\InputInterface;
use MolliePrefix\Symfony\Component\Console\Output\OutputInterface;
class FooLock2Command extends \MolliePrefix\Symfony\Component\Console\Command\Command
{
    use LockableTrait;
    protected function configure()
    {
        $this->setName('foo:lock2');
    }
    protected function execute(\MolliePrefix\Symfony\Component\Console\Input\InputInterface $input, \MolliePrefix\Symfony\Component\Console\Output\OutputInterface $output)
    {
        try {
            $this->lock();
            $this->lock();
        } catch (\LogicException $e) {
            return 1;
        }
        return 2;
    }
}
\class_alias('MolliePrefix\\FooLock2Command', 'FooLock2Command', \false);
