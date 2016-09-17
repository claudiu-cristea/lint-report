<?php

namespace Cheppers\LintReport;

/**
 * Interface FilesWrapperInterface.
 *
 * @package Cheppers\LintReport
 */
interface ReportWrapperInterface
{
    /**
     * @var string
     */
    const SEVERITY_ERROR = 'error';

    /**
     * @var string
     */
    const SEVERITY_WARNING = 'warning';

    /**
     * @var string
     */
    const SEVERITY_OK = 'ok';

    /**
     * ReportWrapper constructor.
     *
     * @param array|null $report
     */
    public function __construct(array $report = null);

    /**
     * @return array
     */
    public function getReport();

    /**
     * @param array $report
     *
     * @return $this
     */
    public function setReport($report);

    /**
     * @return int
     */
    public function numOfErrors();

    /**
     * @return int
     */
    public function numOfWarnings();

    /**
     * @return string
     */
    public function highestSeverity();

    /**
     * @return int
     */
    public function countFiles();

    /**
     * @return FileWrapperInterface[]
     */
    public function yieldFiles();
}
