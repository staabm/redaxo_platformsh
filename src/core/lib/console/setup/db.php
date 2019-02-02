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

            $configFile = rex_path::coreData('config.yml');
            if (!file_exists($configFile)) {
                throw new Exception(sprintf('Missing required config file "%s" containing db connection settings', $configFile));
            }
            
            // bootstrap addons, to load all required classes for the setup
            require_once rex_path::core('packages.php');

            // read initial config
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
