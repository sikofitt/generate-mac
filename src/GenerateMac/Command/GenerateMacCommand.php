<?php declare(strict_types=1);

/*
 * Copyright (c) 2018  https://sikofitt.com sikofitt@sikofitt.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Sikofitt\GenerateMac\Command;

use Sikofitt\GenerateMac\Mac;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\{
    InvalidArgumentException,
    RuntimeException
};
use Symfony\Component\Console\Input\{
    InputInterface,
    InputOption
};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateMacCommand extends Command
{
    private const SEPARATOR_NAMES = [
        'colon',
        'dash',
        'none',
    ];

    public function configure(): void
    {
        $this
          ->setName('generate-mac')
          ->addOption('count', 'c', InputOption::VALUE_REQUIRED, 'Generate {count} mac addresses.')
          ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output in this format instead of a string. [json, plain, string]')
          ->addOption('separator', 's', InputOption::VALUE_REQUIRED, 'The separator to use for mac addresses. [colon, dash, none]')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \Exception
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = (int)($input->getOption('count') ?? 1);

        if ($count <= 0) {
            throw new RuntimeException('$count should be a positive number greater than zero.');
        }

        $separatorName = strtolower($input->getOption('separator') ?? 'colon');

        if (!\in_array($separatorName, self::SEPARATOR_NAMES, true)) {
            throw new InvalidArgumentException('Separator must be one of "colon", "none", or "dash"');
        }

        $outputFormat = strtolower($input->getOption('output') ?? 'string');

        $io = new SymfonyStyle($input, $output);

        $separator = match($separatorName) {
            'colon' => Mac::SEPARATOR_COLON,
            'dash' => Mac::SEPARATOR_DASH,
            'none' => Mac::SEPARATOR_NONE,
            default => Mac::SEPARATOR_COLON,
        };

        $mac = new Mac($separator);

        $macAddresses = $mac->getMacAddresses($count);

        if(empty($macAddresses)) {
            return Command::FAILURE;
        }

        switch ($outputFormat) {
            case 'string':
            default:
                $io->comment(sprintf('Generated %d mac addresses', $count));
                $io->comment(implode(PHP_EOL, $macAddresses));
                break;
            case 'json':
                $io->writeln(\json_encode($macAddresses, JSON_PRETTY_PRINT));
                break;
            case 'plain':
                $io->writeln($macAddresses, SymfonyStyle::OUTPUT_PLAIN | SymfonyStyle::VERBOSITY_NORMAL);
                break;
        }

        return Command::SUCCESS;
    }
}
