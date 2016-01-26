<?php

namespace Console;

use Console\Command\RunCommand;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class Application extends BaseApplication
{
    /**
     * @var \Kernel
     */
    protected $kernel;

    /**
     * @param $kernel
     */
    public function __construct(\Kernel $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct('app', 'dev-master');

        $this->setDefaultCommand('run');
    }

    /**
     * @inheritdoc
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->kernel->boot();

        return parent::doRun($input, $output);
    }

    /**
     * @return \Kernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @inheritdoc
     */
    public function get($name)
    {
        $command = parent::get($name);

        if ($command instanceof ContainerAwareInterface) {
            $command->setContainer($this->kernel->getContainer());
        }

        return $command;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultCommands()
    {
        return [new RunCommand()] + parent::getDefaultCommands();
    }
}
