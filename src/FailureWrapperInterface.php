<?php

namespace Cheppers\LintReport;

/**
 * Interface FailureWrapperInterface.
 *
 * @package Cheppers\LintReport
 */
interface FailureWrapperInterface
{
    /**
     * FileWrapper constructor.
     *
     * @param array $failure
     */
    public function __construct(array $failure);

    /**
     * @return string
     */
    public function severity();

    /**
     * @return string
     */
    public function source();

    /**
     * @return int
     */
    public function line();

    /**
     * @return int
     */
    public function column();

    /**
     * @return string
     */
    public function message();
}
