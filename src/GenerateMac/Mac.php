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

namespace Sikofitt\GenerateMac;

class Mac
{
    private const UNAVAILABLE_LOCAL_PREFIXES = [
        '02bb01', // Octothorpe
        '02aa3c', // Olivetti Telecomm SPA (olteco)
        '02608c', // 3com
        '027001', // Racal-datacom
        '021c7c', //Perq Systems
        '02e6d3', //Nixdorf Computer
        '020701', //Racal-datacom
        '029d8e', //Cardiac Recorders
        '0270b3', //Data Recall
        '02cf1c', //Communication Machinery
        '02c08c', //3com
        '0270b0', //M/a-com Companies
        '026086', //Logic Replacement TECH.
        '525400', //QEMU virtual NIC
        'aa0000', // Digital Equipment
        'aa0001', // Digital Equipment
        'aa0002', // Digital Equipment
        'aa0003', // Digital Equipment
        'aa0004', // Digital Equipment
        'deadca', // PearPC virtual NIC
    ];

    private const AVAILABLE_PREFIXES = [
          'x2xxxx',
          'x6xxxx',
          'xaxxxx',
          'xaxxxx',
    ];

    /**
     * @var bool
     */
    protected $isTest = false;

    /**
     * @var string
     */
    private $separator;

    /**
     * @var bool
     */
    private $unique;

    /**
     * Mac constructor.
     *
     * @param string $separator
     * @param bool $unique
     */
    public function __construct(string $separator = ':', bool $unique = true)
    {
        if (!\in_array($separator, [':', '', '-'], true)) {
            throw new \InvalidArgumentException('Separator is invalid.  Acceptable values: ":", "-", or ""');
        }

        $this->unique = $unique;
        $this->separator = $separator;
    }

    /**
     * @throws \Exception
     * @return string
     */
    public function getMacAddress(): string
    {
        $template = $this->shuffle();
        $prefix = $this->generateString($template);

        if ($this->isTest) {
            $prefix = '02bb01';
        }

        if ($this->unique) {
            while ($this->isTaken($prefix)) {
                $prefix = $this->generateString($template);
            }
        }

        $prefix .= $this->generateString('xxxxxx');

        return \trim($this->insertSeparator($prefix));
    }

    /**
     * @param int $count
     *
     * @throws \Exception
     * @return array
     */
    public function getMacAddresses(int $count): array
    {
        $macAddresses = [];

        for ($i=0;$i<$count;$i++) {
            $macAddresses[] = $this->getMacAddress();
        }

        return $macAddresses;
    }

    /**
     * @param string $prefix
     *
     * @return bool
     */
    private function isTaken(string $prefix): bool
    {
        return \in_array($prefix, self::UNAVAILABLE_LOCAL_PREFIXES, true);
    }

    /**
     * @param string $template
     *
     * @throws \Exception
     * @return mixed|string
     */
    private function generateString(string $template)
    {
        $bytes = sodium_bin2hex(\random_bytes(32));

        while (false !== $pos = \strpos($template, 'x')) {
            $replacement = $bytes[\random_int(0, \strlen($bytes) -1)];
            $template = substr_replace($template, $replacement, $pos, 1);
        }

        return $template;
    }

    /**
     * @return string
     */
    private function shuffle(): string
    {
        $prefixes = self::AVAILABLE_PREFIXES;
        \shuffle($prefixes);
        return \current($prefixes);
    }

    /**
     * @param string $macAddress
     *
     * @return string
     */
    private function insertSeparator(string $macAddress): string
    {
        return implode($this->separator, str_split($macAddress, 2));
    }
}
