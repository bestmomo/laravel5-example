<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides helpers to display a table.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Саша Стаменковић <umpirsky@gmail.com>
 * @author Abdellatif Ait boudad <a.aitboudad@gmail.com>
 */
class Table
{
    /**
     * Table headers.
     *
     * @var array
     */
    private $headers = array();

    /**
     * Table rows.
     *
     * @var array
     */
    private $rows = array();

    /**
     * Column widths cache.
     *
     * @var array
     */
    private $columnWidths = array();

    /**
     * Number of columns cache.
     *
     * @var array
     */
    private $numberOfColumns;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var TableStyle
     */
    private $style;

    private static $styles;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;

        if (!self::$styles) {
            self::$styles = self::initStyles();
        }

        $this->setStyle('default');
    }

    /**
     * Sets a style definition.
     *
     * @param string     $name  The style name
     * @param TableStyle $style A TableStyle instance
     */
    public static function setStyleDefinition($name, TableStyle $style)
    {
        if (!self::$styles) {
            self::$styles = self::initStyles();
        }

        self::$styles[$name] = $style;
    }

    /**
     * Gets a style definition by name.
     *
     * @param string $name The style name
     *
     * @return TableStyle A TableStyle instance
     */
    public static function getStyleDefinition($name)
    {
        if (!self::$styles) {
            self::$styles = self::initStyles();
        }

        if (!self::$styles[$name]) {
            throw new \InvalidArgumentException(sprintf('Style "%s" is not defined.', $name));
        }

        return self::$styles[$name];
    }

    /**
     * Sets table style.
     *
     * @param TableStyle|string $name The style name or a TableStyle instance
     *
     * @return Table
     */
    public function setStyle($name)
    {
        if ($name instanceof TableStyle) {
            $this->style = $name;
        } elseif (isset(self::$styles[$name])) {
            $this->style = self::$styles[$name];
        } else {
            throw new \InvalidArgumentException(sprintf('Style "%s" is not defined.', $name));
        }

        return $this;
    }

    /**
     * Gets the current table style.
     *
     * @return TableStyle
     */
    public function getStyle()
    {
        return $this->style;
    }

    public function setHeaders(array $headers)
    {
        $headers = array_values($headers);
        if (!empty($headers) && !is_array($headers[0])) {
            $headers = array($headers);
        }

        $this->headers = $headers;

        return $this;
    }

    public function setRows(array $rows)
    {
        $this->rows = array();

        return $this->addRows($rows);
    }

    public function addRows(array $rows)
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }

        return $this;
    }

    public function addRow($row)
    {
        if ($row instanceof TableSeparator) {
            $this->rows[] = $row;

            return $this;
        }

        if (!is_array($row)) {
            throw new \InvalidArgumentException('A row must be an array or a TableSeparator instance.');
        }

        $this->rows[] = array_values($row);

        return $this;
    }

    public function setRow($column, array $row)
    {
        $this->rows[$column] = $row;

        return $this;
    }

    /**
     * Renders table to output.
     *
     * Example:
     * +---------------+-----------------------+------------------+
     * | ISBN          | Title                 | Author           |
     * +---------------+-----------------------+------------------+
     * | 99921-58-10-7 | Divine Comedy         | Dante Alighieri  |
     * | 9971-5-0210-0 | A Tale of Two Cities  | Charles Dickens  |
     * | 960-425-059-0 | The Lord of the Rings | J. R. R. Tolkien |
     * +---------------+-----------------------+------------------+
     */
    public function render()
    {
        $this->calculateNumberOfColumns();
        $this->rows = $this->buildTableRows($this->rows);
        $this->headers = $this->buildTableRows($this->headers);

        $this->renderRowSeparator();
        if (!empty($this->headers)) {
            foreach ($this->headers as $header) {
                $this->renderRow($header, $this->style->getCellHeaderFormat());
                $this->renderRowSeparator();
            }
        }
        foreach ($this->rows as $row) {
            if ($row instanceof TableSeparator) {
                $this->renderRowSeparator();
            } else {
                $this->renderRow($row, $this->style->getCellRowFormat());
            }
        }
        if (!empty($this->rows)) {
            $this->renderRowSeparator();
        }

        $this->cleanup();
    }

    /**
     * Renders horizontal header separator.
     *
     * Example: +-----+-----------+-------+
     */
    private function renderRowSeparator()
    {
        if (0 === $count = $this->numberOfColumns) {
            return;
        }

        if (!$this->style->getHorizontalBorderChar() && !$this->style->getCrossingChar()) {
            return;
        }

        $markup = $this->style->getCrossingChar();
        for ($column = 0; $column < $count; ++$column) {
            $markup .= str_repeat($this->style->getHorizontalBorderChar(), $this->getColumnWidth($column)).$this->style->getCrossingChar();
        }

        $this->output->writeln(sprintf($this->style->getBorderFormat(), $markup));
    }

    /**
     * Renders vertical column separator.
     */
    private function renderColumnSeparator()
    {
        $this->output->write(sprintf($this->style->getBorderFormat(), $this->style->getVerticalBorderChar()));
    }

    /**
     * Renders table row.
     *
     * Example: | 9971-5-0210-0 | A Tale of Two Cities  | Charles Dickens  |
     *
     * @param array  $row
     * @param string $cellFormat
     */
    private function renderRow(array $row, $cellFormat)
    {
        if (empty($row)) {
            return;
        }

        $this->renderColumnSeparator();
        foreach ($this->getRowColumns($row) as $column) {
            $this->renderCell($row, $column, $cellFormat);
            $this->renderColumnSeparator();
        }
        $this->output->writeln('');
    }

    /**
     * Renders table cell with padding.
     *
     * @param array  $row
     * @param int    $column
     * @param string $cellFormat
     */
    private function renderCell(array $row, $column, $cellFormat)
    {
        $cell = isset($row[$column]) ? $row[$column] : '';
        $width = $this->getColumnWidth($column);
        if ($cell instanceof TableCell && $cell->getColspan() > 1) {
            // add the width of the following columns(numbers of colspan).
            foreach (range($column + 1, $column + $cell->getColspan() - 1) as $nextColumn) {
                $width += $this->getColumnSeparatorWidth() + $this->getColumnWidth($nextColumn);
            }
        }

        // str_pad won't work properly with multi-byte strings, we need to fix the padding
        if (function_exists('mb_strwidth') && false !== $encoding = mb_detect_encoding($cell)) {
            $width += strlen($cell) - mb_strwidth($cell, $encoding);
        }

        if ($cell instanceof TableSeparator) {
            $this->output->write(sprintf($this->style->getBorderFormat(), str_repeat($this->style->getHorizontalBorderChar(), $width)));
        } else {
            $width += Helper::strlen($cell) - Helper::strlenWithoutDecoration($this->output->getFormatter(), $cell);
            $content = sprintf($this->style->getCellRowContentFormat(), $cell);
            $this->output->write(sprintf($cellFormat, str_pad($content, $width, $this->style->getPaddingChar(), $this->style->getPadType())));
        }
    }

    /**
     * Calculate number of columns for this table.
     */
    private function calculateNumberOfColumns()
    {
        if (null !== $this->numberOfColumns) {
            return;
        }

        $columns = array(0);
        foreach (array_merge($this->headers, $this->rows) as $row) {
            if ($row instanceof TableSeparator) {
                continue;
            }

            $columns[] = $this->getNumberOfColumns($row);
        }

        return $this->numberOfColumns = max($columns);
    }

    private function buildTableRows($rows)
    {
        $unmergedRows = array();
        for ($rowKey = 0; $rowKey < count($rows); ++$rowKey) {
            $rows = $this->fillNextRows($rows, $rowKey);

            // Remove any new line breaks and replace it with a new line
            foreach ($rows[$rowKey] as $column => $cell) {
                $rows[$rowKey] = $this->fillCells($rows[$rowKey], $column);
                if (!strstr($cell, "\n")) {
                    continue;
                }
                $lines = explode("\n", $cell);
                foreach ($lines as $lineKey => $line) {
                    if ($cell instanceof TableCell) {
                        $line = new TableCell($line, array('colspan' => $cell->getColspan()));
                    }
                    if (0 === $lineKey) {
                        $rows[$rowKey][$column] = $line;
                    } else {
                        $unmergedRows[$rowKey][$lineKey][$column] = $line;
                    }
                }
            }
        }

        $tableRows = array();
        foreach ($rows as $rowKey => $row) {
            $tableRows[] = $row;
            if (isset($unmergedRows[$rowKey])) {
                $tableRows = array_merge($tableRows, $unmergedRows[$rowKey]);
            }
        }

        return $tableRows;
    }

    /**
     * fill rows that contains rowspan > 1.
     *
     * @param array $rows
     * @param int   $line
     *
     * @return array
     */
    private function fillNextRows($rows, $line)
    {
        $unmergedRows = array();
        foreach ($rows[$line] as $column => $cell) {
            if ($cell instanceof TableCell && $cell->getRowspan() > 1) {
                $nbLines = $cell->getRowspan() - 1;
                $lines = array($cell);
                if (strstr($cell, "\n")) {
                    $lines = explode("\n", $cell);
                    $nbLines = count($lines) > $nbLines ? substr_count($cell, "\n") : $nbLines;

                    $rows[$line][$column] = new TableCell($lines[0], array('colspan' => $cell->getColspan()));
                    unset($lines[0]);
                }

                // create a two dimensional array (rowspan x colspan)
                $unmergedRows = array_replace_recursive(array_fill($line + 1, $nbLines, ''), $unmergedRows);
                foreach ($unmergedRows as $unmergedRowKey => $unmergedRow) {
                    $value = isset($lines[$unmergedRowKey - $line]) ? $lines[$unmergedRowKey - $line] : '';
                    $unmergedRows[$unmergedRowKey][$column] = new TableCell($value, array('colspan' => $cell->getColspan()));
                }
            }
        }

        foreach ($unmergedRows as $unmergedRowKey => $unmergedRow) {
            // we need to know if $unmergedRow will be merged or inserted into $rows
            if (isset($rows[$unmergedRowKey]) && is_array($rows[$unmergedRowKey]) && ($this->getNumberOfColumns($rows[$unmergedRowKey]) + $this->getNumberOfColumns($unmergedRows[$unmergedRowKey]) <= $this->numberOfColumns)) {
                foreach ($unmergedRow as $cellKey => $cell) {
                    // insert cell into row at cellKey position
                    array_splice($rows[$unmergedRowKey], $cellKey, 0, array($cell));
                }
            } else {
                $row = $this->copyRow($rows, $unmergedRowKey - 1);
                foreach ($unmergedRow as $column => $cell) {
                    if (!empty($cell)) {
                        $row[$column] = $unmergedRow[$column];
                    }
                }
                array_splice($rows, $unmergedRowKey, 0, array($row));
            }
        }

        return $rows;
    }

    /**
     * fill cells for a row that contains colspan > 1.
     *
     * @param array $row
     * @param int   $column
     *
     * @return array
     */
    private function fillCells($row, $column)
    {
        $cell = $row[$column];
        if ($cell instanceof TableCell && $cell->getColspan() > 1) {
            foreach (range($column + 1, $column + $cell->getColspan() - 1) as $position) {
                // insert empty value into rows at column position
                array_splice($row, $position, 0, '');
            }
        }

        return $row;
    }

    /**
     * @param array $rows
     * @param int   $line
     *
     * @return array
     */
    private function copyRow($rows, $line)
    {
        $row = $rows[$line];
        foreach ($row as $cellKey => $cellValue) {
            $row[$cellKey] = '';
            if ($cellValue instanceof TableCell) {
                $row[$cellKey] = new TableCell('', array('colspan' => $cellValue->getColspan()));
            }
        }

        return $row;
    }

    /**
     * Gets number of columns by row.
     *
     * @param array $row
     *
     * @return int
     */
    private function getNumberOfColumns(array $row)
    {
        $columns = count($row);
        foreach ($row as $column) {
            $columns += $column instanceof TableCell ? ($column->getColspan() - 1) : 0;
        }

        return $columns;
    }

    /**
     * Gets list of columns for the given row.
     *
     * @param array $row
     *
     * @return array()
     */
    private function getRowColumns($row)
    {
        $columns = range(0, $this->numberOfColumns - 1);
        foreach ($row as $cellKey => $cell) {
            if ($cell instanceof TableCell && $cell->getColspan() > 1) {
                // exclude grouped columns.
                $columns = array_diff($columns, range($cellKey + 1, $cellKey + $cell->getColspan() - 1));
            }
        }

        return $columns;
    }

    /**
     * Gets column width.
     *
     * @param int $column
     *
     * @return int
     */
    private function getColumnWidth($column)
    {
        if (isset($this->columnWidths[$column])) {
            return $this->columnWidths[$column];
        }

        foreach (array_merge($this->headers, $this->rows) as $row) {
            if ($row instanceof TableSeparator) {
                continue;
            }

            $lengths[] = $this->getCellWidth($row, $column);
        }

        return $this->columnWidths[$column] = max($lengths) + strlen($this->style->getCellRowContentFormat()) - 2;
    }

    /**
     * Gets column width.
     *
     * @param int $column
     *
     * @return int
     */
    private function getColumnSeparatorWidth()
    {
        return strlen(sprintf($this->style->getBorderFormat(), $this->style->getVerticalBorderChar()));
    }

    /**
     * Gets cell width.
     *
     * @param array $row
     * @param int   $column
     *
     * @return int
     */
    private function getCellWidth(array $row, $column)
    {
        if (isset($row[$column])) {
            $cell = $row[$column];
            $cellWidth = Helper::strlenWithoutDecoration($this->output->getFormatter(), $cell);
            if ($cell instanceof TableCell && $cell->getColspan() > 1) {
                // we assume that cell value will be across more than one column.
                $cellWidth = $cellWidth / $cell->getColspan();
            }

            return $cellWidth;
        }

        return 0;
    }

    /**
     * Called after rendering to cleanup cache data.
     */
    private function cleanup()
    {
        $this->columnWidths = array();
        $this->numberOfColumns = null;
    }

    private static function initStyles()
    {
        $borderless = new TableStyle();
        $borderless
            ->setHorizontalBorderChar('=')
            ->setVerticalBorderChar(' ')
            ->setCrossingChar(' ')
        ;

        $compact = new TableStyle();
        $compact
            ->setHorizontalBorderChar('')
            ->setVerticalBorderChar(' ')
            ->setCrossingChar('')
            ->setCellRowContentFormat('%s')
        ;

        $styleGuide = new TableStyle();
        $styleGuide
            ->setHorizontalBorderChar('-')
            ->setVerticalBorderChar(' ')
            ->setCrossingChar(' ')
            ->setCellHeaderFormat('%s')
        ;

        return array(
            'default' => new TableStyle(),
            'borderless' => $borderless,
            'compact' => $compact,
            'symfony-style-guide' => $styleGuide,
        );
    }
}
