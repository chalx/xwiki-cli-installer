<?php

namespace XWiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    public function configure()
    {
        $this->setName('install')
            ->setDescription('Install an xwiki')
            ->addArgument('version', InputArgument::REQUIRED, 'Version of xwiki');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $version = $input->getArgument('version');
        $output->writeln("<info>{$version}</info>");
    }
}