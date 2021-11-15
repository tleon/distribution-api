<?php

declare(strict_types=1);

namespace App\Command;

use App\Util\ModuleUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckReposCommand extends Command
{
    protected static $defaultName = 'checkRepos';

    private ModuleUtils $moduleUtils;

    public function __construct(ModuleUtils $moduleUtils)
    {
        parent::__construct();
        $this->moduleUtils = $moduleUtils;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $nativeModules = $this->moduleUtils->getNativeModuleList();
        foreach ($nativeModules as $nativeModule) {
            $this->checkModule($nativeModule, $output);
        }

        return self::SUCCESS;
    }

    private function checkModule(string $module, OutputInterface $output): void
    {
        $output->writeln(sprintf('<info>Checking module %s</info>', $module));
        $versions = $this->moduleUtils->getVersions($module, false);
        if (empty($versions)) {
            $output->writeln(sprintf('<error>No release for module %s</error>', $module));
        }
        foreach ($versions as $version) {
            if ($version['url'] === null) {
                $output->writeln(
                    sprintf('<error>No asset for release %s of module %s</error>', $version['version'], $module)
                );
            }
        }
    }
}
