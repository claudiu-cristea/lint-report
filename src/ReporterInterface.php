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
     * @return ReportWrapperInterface
     */
    public function getReportWrapper();

    /**
     * @param ReportWrapperInterface $reportWrapper
     *
     * @return $this
     */
    public function setReportWrapper(ReportWrapperInterface $reportWrapper);

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
     * @return string|null
     */
    public function getFilePathStyle();

    /**
     * @param string|null $value
     *
     * @return $this
     */
    public function setFilePathStyle($value);

    /**
     * @param array $source
     * @param string|OutputInterface $destination
     * @param string $destinationMode
     *
     * @return $this
     */
    public function generate($source, $destination, $destinationMode = 'w');
}
