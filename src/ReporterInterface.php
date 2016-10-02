<?php

namespace Cheppers\LintReport;

use Cheppers\LintReport\ReportWrapperInterface;
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
     * @return string|OutputInterface
     */
    public function getDestination();

    /**
     * @param string|OutputInterface $destination
     *
     * @return $this
     */
    public function setDestination($destination);

    /**
     * @return string
     */
    public function getDestinationMode();

    /**
     * @param string $destinationMode
     *
     * @return $this
     */
    public function setDestinationMode($destinationMode);

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
     * @return $this
     */
    public function generate();
}
