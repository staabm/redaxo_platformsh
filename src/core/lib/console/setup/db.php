<?php

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package redaxo\core
 *
 * @internal
 */
class rex_command_setup_db extends rex_console_command
{
    protected function configure()
    {
        $this->setDescription('Initial setup the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->getStyle($input, $output);

        // run setup, if instance not already prepared
        if (rex::isSetup()) {
            $err = '';

            // read initial config
            $configFile = rex_path::coreData('config.yml');
            $config = array_merge(
                rex_file::getConfig(rex_path::core('default.config.yml')),
                rex_file::getConfig($configFile)
            );

            // init db
            $err .= rex_setup::checkDb($config, false);
            $err .= rex_setup_importer::prepareEmptyDb();
            $err .= rex_setup_importer::verifyDbSchema();

            if ($err != '') {
                $io->error($err);
                return 2;
            }

            // mark setup as
            $config['setup'] = false;
            if (rex_file::putConfig($configFile, $config)) {
                $io->success('instance setup successfull');
                return 0;
            }
            $io->error('instance setup failure');
            return 1;
        }

        $io->error('instance setup not necessary');
        return 1;
    }
}
