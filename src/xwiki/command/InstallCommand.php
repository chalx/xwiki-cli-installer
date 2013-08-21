<?php

namespace XWiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XWiki\Config\XWikiVersion;
use XWiki\Exceptions\FileNotFoundException;
use XWiki\Exceptions\IOReadException;
use XWiki\Exceptions\VersionNotFoundException;
use XWiki\Helper\Progress;
use XWiki\Http\Client;
use XWiki\IO\Adapter\DataStream;
use XWiki\IO\Adapter\FileStream;
use XWiki\IO\DataReadStream;
use XWiki\IO\DataWriteStream;

class InstallCommand extends Command
{
    public function configure()
    {
        $this->setName('install')
            ->setDescription('Install an xwiki')
            ->addArgument('version', InputArgument::REQUIRED, 'Version of xwiki')
            ->addOption("xem", null, InputOption::VALUE_NONE, "If you want xem version");
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $version = $input->getArgument('version');
        $xem = $input->getOption('xem');
        $xwikiVersion = new XWikiVersion();
        $downloadURL = null;
        try {
            $downloadURL = $xwikiVersion->getXWikiVersionUrl($version, $xem);
            $client = new Client($downloadURL);
            $response = $client->send()->getResponse();
            if ($response->isOk()) {
                $progress = new Progress();
                $streamSize = $response['Content-Length'];

                $writeStream = new DataWriteStream(new FileStream("temp.zip", FileStream::WRITE_FILE_OR_CREATE));
                $readStream = new DataReadStream(new DataStream($response->getStream()));
                $output->writeln("<info>Download {$version}</info>");
                $progress->start(100);
                $writeStream->writeStream($readStream, function ($readed) use ($progress, $streamSize) {
                    $done = ($readed/$streamSize) * 100;
                    $progress->advance((int)$done, false);
                });
                $progress->finish();
            }
        } catch (VersionNotFoundException $ex) {
            $output->writeln("<error>{$ex->getMessage()}</error>");
        } catch (FileNotFoundException $ex) {
            $output->writeln("<error>Version file not found</error>");
        } catch (IOReadException $ex) {
            $output->writeln("<error>{$ex->getMessage()}</error>");
        } catch (\NullPointerException $ex) {
            $output->writeln("<error>Server is down</error>");
        }
    }
}