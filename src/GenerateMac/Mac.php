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
    /**
     * Private mac address prefixes that are used
     * internally or with virtual machines and containers.
     */
    private const UNAVAILABLE_LOCAL_PREFIXES = [
        '02bb01', // Octothorpe
        '02aa3c', // Olivetti Telecomm SPA (olteco)
        '02608c', // 3com
        '027001', // Racal-datacom
        '021c7c', // Perq Systems
        '02e6d3', // Nixdorf Computer
        '020701', // Racal-datacom
        '029d8e', // Cardiac Recorders
        '0270b3', // Data Recall
        '02cf1c', // Communication Machinery
        '02c08c', // 3com
        '0270b0', // M/a-com Companies
        '026086', // Logic Replacement TECH.
        '525400', // QEMU virtual NIC
        'aa0000', // Digital Equipment
        'aa0001', // Digital Equipment
        'aa0002', // Digital Equipment
        'aa0003', // Digital Equipment
        'aa0004', // Digital Equipment
        'deadca', // PearPC virtual NIC
    ];

    /**
     * Reserved mac prefixes for private devices.
     */
    private const AVAILABLE_PREFIXES = [
          'x2xxxx',
          'x6xxxx',
          'xaxxxx',
          'xexxxx',
    ];

    public const SEPARATOR_COLON = 0;
    public const SEPARATOR_DASH = 1;
    public const SEPARATOR_NONE = 2;

    /**
     * @internal
     * @var bool  For testing that we get a prefix that is not used.
     */
    protected $isTest = false;

    /**
     * @var int  The mac address separator, can be self::SEPARATOR_*
     */
    private $separator;

    /**
     * @var bool  If we care if we get an already used prefix or not.
     */
    private $unique;

    /**
     * Mac constructor.
     *
     * @param int $separator  The mac address separator, one of ':', '-', or ''
     * @param bool $unique  Whether or not we care if we get a non unique prefix.
     */
    public function __construct(int $separator = self::SEPARATOR_COLON, bool $unique = true)
    {
        $this->setUnique($unique);
        $this->setSeparator($separator);
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

        if ($this->getUnique()) {
            while ($this->isTaken($prefix)) {
                $prefix = $this->generateString($template);
            }
        }

        $prefix .= $this->generateString('xxxxxx');

        return \trim($this->insertSeparator($prefix));
    }

    /**
     * Note: if count is 1 it will still be returned as an array. [0 => $macAddress]
     *
     * @param int $count  The number of mac addresses to generate.
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
     * @param bool $unique
     *
     * @return \Sikofitt\GenerateMac\Mac
     */
    public function setUnique(bool $unique = true): Mac
    {
        $this->unique = $unique;

        return $this;
    }

    /**
     * @return bool
     */
    public function getUnique(): bool
    {
        return $this->unique;
    }

    /**
     * @param int $separator
     *
     * @return \Sikofitt\GenerateMac\Mac
     */
    public function setSeparator(int $separator): Mac
    {
        if (!\in_array($separator, [self::SEPARATOR_COLON, self::SEPARATOR_DASH, self::SEPARATOR_NONE], true)) {
            throw new \InvalidArgumentException('Separator is invalid.  Acceptable values: ":", "-", or ""');
        }

        $this->separator = $separator;

        return $this;
    }

    /**
     * @return int
     */
    public function getSeparator(): int
    {
        return $this->separator;
    }

    /**
     * Test to see if we have a unique prefix.
     *
     * @param string $prefix  The current prefix.
     *
     * @return bool
     */
    private function isTaken(string $prefix): bool
    {
        return \in_array($prefix, self::UNAVAILABLE_LOCAL_PREFIXES, true);
    }

    /**
     * Generates a string
     *
     * @param string $template  The template to use xexxxx.
     *
     * @throws \Exception
     * @return string
     */
    private function generateString(string $template): string
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

    private function getSeparatorAsString(): string
    {
        switch($this->getSeparator()) {
            default:
            case self::SEPARATOR_COLON:
                return ':';
            case self::SEPARATOR_DASH:
                return '-';
            case self::SEPARATOR_NONE:
                return '';
        }
    }

    /**
     * Inserts the chosen separator.
     *
     * @param string $macAddress
     *
     * @return string
     */
    private function insertSeparator(string $macAddress): string
    {

        return implode($this->getSeparatorAsString(), str_split($macAddress, 2));
    }
}
