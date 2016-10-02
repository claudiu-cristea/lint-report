<?php

use Cheppers\LintReport\Reporter\VerboseReporter;

/**
 * Class TaskScssLintRunTest.
 */
// @codingStandardsIgnoreStart
class ReportVerboseTest extends ReportTestBase
{
    // @codingStandardsIgnoreEnd

    /**
     * {@inheritdoc}
     */
    protected $reporterName = 'verbose';

    /**
     * {@inheritdoc}
     */
    protected $reporterClass = VerboseReporter::class;

    /**
     * {@inheritdoc}
     */
    protected $reporterOutputExtension = 'txt';
}
