<?php

namespace Cheppers\LintReport;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface ReporterInterface.
 *
 * @package Cheppers\LintReport
 */
interface ReporterInterface
{
    /**
     * @return string
     */
    public function getBasePath();

    /**
     * @param string $basePath
     *
     * @return $this
     */
    public function setBasePath($basePath);

    /**
     * @param array $source
     * @param string|OutputInterface $destination
     * @param string $destinationMode
     *
     * @return $this
     */
    public function generate($source, $destination, $destinationMode = 'w');
}
