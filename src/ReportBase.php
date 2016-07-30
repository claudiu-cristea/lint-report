<?php

namespace Cheppers\LintReport;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ReportFull.
 *
 * @package Cheppers\LintReport
 */
abstract class ReportBase
{

    /**
     * @var array
     */
    public static $columnMappings = [
        'default' => [
            'severity' => 'severity',
            'source' => 'source',
            'line' => 'line',
            'column' => 'column',
            'message' => 'message',
        ],
        'phpcs' => [
            'severity' => 'type',
            'source' => 'source',
            'line' => 'line',
            'column' => 'column',
            'message' => 'message',
        ],
        'scss-lint' => [
            'severity' => 'severity',
            'source' => 'linter',
            'line' => 'line',
            'column' => 'column',
            'message' => 'reason',
        ],
    ];

    /**
     * Original report to convert.
     *
     * @var array
     */
    protected $source;

    /**
     * Output destination.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $destination = null;

    /**
     * File handler.
     *
     * @var resource
     */
    protected $destinationResource = null;

    /**
     * @var string[]
     */
    protected $filesParents = [];

    /**
     * @var string[]
     */
    protected $errorsParents = [];

    /**
     * @var array
     */
    protected $columnMapping = [
        'linter' => 'source',
        'line' => 'line',
        'column' => 'column',
        'length' => 'length',
        'severity' => 'severity',
        'source' => 'source',
        'message' => 'message',
    ];

    /**
     * @var string
     */
    protected $basePath = '';

    /**
     * @var string|null
     */
    protected $filePathStyle = null;

    /**
     * ReportBase constructor.
     */
    public function __construct()
    {
        $this->setBasePath(getcwd());
    }

    /**
     * @return string[]
     */
    public function getFilesParents()
    {
        return $this->filesParents;
    }

    /**
     * @param string[] $filesParents
     *
     * @return $this;
     */
    public function setFilesParents(array $filesParents)
    {
        $this->filesParents = $filesParents;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getErrorsParents()
    {
        return $this->errorsParents;
    }

    /**
     * @param string[] $errorsParents
     *
     * @return $this
     */
    public function setErrorsParents(array $errorsParents)
    {
        $this->errorsParents = $errorsParents;

        return $this;
    }

    /**
     * @return array
     */
    public function getColumnMapping()
    {
        return $this->columnMapping;
    }

    /**
     * @param array|string $columnMapping
     *
     * @return $this
     */
    public function setColumnMapping($columnMapping)
    {
        if (!$columnMapping) {
            $columnMapping = 'default';
        }

        if (is_string($columnMapping)) {
            if (!isset(static::$columnMappings[$columnMapping])) {
                throw new \InvalidArgumentException();
            }

            $columnMapping = static::$columnMappings[$columnMapping];
        }

        $this->columnMapping = $columnMapping;

        return $this;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     *
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilePathStyle()
    {
        return $this->filePathStyle;
    }

    /**
     * @param string|null $value
     *
     * @return $this
     */
    public function setFilePathStyle($value)
    {
        if (!in_array($value, ['relative', 'absolute', null])) {
            throw new \InvalidArgumentException();
        }

        $this->filePathStyle = $value;

        return $this;
    }

    /**
     * @param array $source
     * @param string|OutputInterface $destination
     * @param string $destination_mode
     *
     * @return $this
     */
    public function generate($source, $destination, $destination_mode = 'w')
    {
        return $this
            ->initSource($source)
            ->initDestination($destination, $destination_mode)
            ->doIt()
            ->closeDestination();
    }

    /**
     * @param array|\Traversable $source
     *
     * @return $this
     */
    protected function initSource($source)
    {
        if (!is_array($source) && !($source instanceof \Traversable)) {
            throw new \InvalidArgumentException('Source is not traversable', 1);
        }

        $this->source = $source;

        return $this;
    }

    /**
     * Initialize the output destination based on the Jar values.
     *
     * @param string|OutputInterface $destination
     * @param string $destination_mode
     *
     * @return $this
     */
    protected function initDestination($destination, $destination_mode)
    {
        $this->destination = $destination;
        if (is_string($this->destination)) {
            $fs = new Filesystem();
            $fs->mkdir(dirname($this->destination));

            $this->destinationResource = fopen($this->destination, $destination_mode);
            $this->destination = new StreamOutput(
                $this->destinationResource,
                OutputInterface::VERBOSITY_NORMAL,
                false
            );
        }

        if (!$this->destination) {
            throw new \InvalidArgumentException();
        }

        return $this;
    }

    /**
     * Close the destination resource if it was opened here.
     *
     * @return $this
     */
    protected function closeDestination()
    {
        if ($this->destinationResource) {
            fclose($this->destinationResource);
        }

        return $this;
    }

    /**
     * Convert the source report.
     *
     * @return $this
     */
    abstract protected function doIt();

    /**
     * @param string $filePath
     *
     * @return string
     */
    protected function normalizeFilePath($filePath)
    {
        $file_path_style = $this->getFilePathStyle();
        if ($file_path_style === null) {
            return $filePath;
        }

        $ds = DIRECTORY_SEPARATOR;
        $isAbsolute = $this->isAbsoluteFilePath($filePath);
        if ($isAbsolute && $file_path_style === 'relative') {
            return preg_replace('@^' . preg_quote($this->getBasePath() . $ds, '@') . '@', '', $filePath);
        } elseif (!$isAbsolute && $file_path_style === 'absolute') {
            return $this->getBasePath() . $ds . $filePath;
        }

        return $filePath;
    }

    /**
     * @param string $file_path
     *
     * @return bool
     */
    protected function isAbsoluteFilePath($file_path)
    {
        $is_win = DIRECTORY_SEPARATOR === '\\';


        return $is_win ? preg_match('@^[a-zA-z]:@', $file_path) : strpos($file_path, '/') === 0;
    }

    /**
     * Get the length of the attribute values grouped by the attribute name.
     *
     * @param array $errors
     *   List of errors.
     *
     * @return array
     *   Key-value pair of attribute name and the max length.
     */
    protected function examineErrors(array $errors)
    {
        $report = [
            // Most serious severity.
            'severity' => 'unknown',
            // List of severities.
            'has' => [],
            // Column widths.
            'widths' => [],
            'stats' => [],
            'occurrences' => 0,
        ];

        $severity_weights = [
            'unknown' => 0,
            'warning' => 1,
            'error' => 2,
        ];

        foreach ($errors as $error) {
            $error = $this->convertErrorToInternalFormat($error);

            if (isset($severity_weights[$error['severity']])
                && $severity_weights[$error['severity']] > $severity_weights[$report['severity']]
            ) {
                $report['severity'] = $error['severity'];
            }

            $report['has'][$error['severity']] = true;

            foreach ($error as $name => $value) {
                $report['widths'] += [$name => 0];
                if (strlen($value) > $report['widths'][$name]) {
                    $report['widths'][$name] = strlen($value);
                }
            }

            $report['stats'] += [
                $error['source'] => [
                    'severity' => $error['severity'],
                    'count' => 0,
                ],
            ];

            $report['stats'][$error['source']]['count']++;

            if ($report['stats'][$error['source']]['count'] > $report['occurrences']) {
                $report['occurrences'] = $report['stats'][$error['source']]['count'];
            }
        }

        return $report;
    }

    /**
     * Set colors.
     *
     * @param string $severity
     *   Severity identifier.
     * @param string $text
     *   Text to decorate.
     *
     * @return string
     *   Decorated text.
     */
    protected function highlightHeaderBySeverity($severity, $text)
    {
        $patterns = [
            'warning' => '<fg=yellow;options=bold>%s</fg=yellow;options=bold>',
            'error' => '<fg=red;options=bold>%s</fg=red;options=bold>',
        ];

        $pattern = isset($patterns[$severity]) ? $patterns[$severity] : '<info>%s</info>';

        return sprintf($pattern, $text);
    }

    /**
     * Set colors.
     *
     * @param string $severity
     *   Severity identifier.
     * @param string $text
     *   Text to decorate.
     *
     * @return string
     *   Decorated text.
     */
    protected function highlightNormalBySeverity($severity, $text)
    {
        $patterns = [
            'warning' => '<fg=yellow>%s</fg=yellow>',
            'error' => '<fg=red>%s</fg=red>',
        ];

        $pattern = isset($patterns[$severity]) ? $patterns[$severity] : '<info>%s</info>';

        return sprintf($pattern, $text);
    }

    /**
     * @param array $error
     *
     * @return array
     */
    protected function convertErrorToInternalFormat(array $error)
    {
        $internal = array_fill_keys($this->getColumnMapping(), '');
        foreach ($this->getColumnMapping() as $dst => $src) {
            if (isset($error[$src])) {
                $internal[$dst] = $error[$src];
            }
        }

        $internal['severity'] = strtolower($internal['severity']);

        return $internal + $error;
    }

    /**
     * @param array $array
     * @param array $parents
     * @param null $key_exists
     *
     * @return mixed|null
     */
    public function &getValue(array $array, array $parents, &$key_exists = null)
    {
        $ref = &$array;
        foreach ($parents as $parent) {
            if (is_array($ref) && array_key_exists($parent, $ref)) {
                $ref = &$ref[$parent];
            } else {
                $key_exists = false;
                $null = null;

                return $null;
            }
        }
        $key_exists = true;

        return $ref;
    }
}
