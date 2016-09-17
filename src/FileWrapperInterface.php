<?php

namespace Cheppers\LintReport;

/**
 * Interface FileWrapperInterface.
 *
 * @package Cheppers\LintReport
 */
interface FileWrapperInterface
{
    /**
     * FileWrapper constructor.
     *
     * @param array $file
     */
    public function __construct(array $file);

    /**
     * @return string
     */
    public function filePath();

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
     * @return FailureWrapperInterface[]
     */
    public function yieldFailures();

    /**
     * @return array
     */
    public function stats();
}
