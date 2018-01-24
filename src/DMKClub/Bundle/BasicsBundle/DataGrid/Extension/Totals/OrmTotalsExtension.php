<?php
namespace DMKClub\Bundle\BasicsBundle\DataGrid\Extension\Totals;

use Oro\Bundle\DataGridBundle\Extension\Totals\OrmTotalsExtension As OroOrmTotalsExtension;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Extension\Formatter\Property\PropertyInterface;

class OrmTotalsExtension extends OroOrmTotalsExtension
{

    /**
     * {@inheritdoc}
     */
    public function processConfigs(DatagridConfiguration $config)
    {
        $totalRows = $this->validateConfiguration(
            new Configuration(),
            ['totals' => $config->offsetGetByPath(Configuration::TOTALS_PATH)]
            );

        if (!empty($totalRows)) {
            foreach ($totalRows as $rowName => $rowConfig) {
                $this->mergeTotals($totalRows, $rowName, $rowConfig, $config->getName());
            }

            $config->offsetSetByPath(Configuration::TOTALS_PATH, $totalRows);
        }
    }

    /**
     * Get total row frontend data
     *
     * @param array $rowConfig Total row config
     * @param array $data Db result data for current total row config
     * @return array Array with array of columns total values and labels
     */
    protected function getTotalData($rowConfig, $data)
    {
        if (empty($data)) {
            return [];
        }

        $columns = [];
        foreach ($rowConfig['columns'] as $field => $total) {
            $column = [];
            if (isset($data[$field])) {
                $totalValue = $data[$field];
                if (isset($total[Configuration::TOTALS_FORMATTER_KEY])) {
                    $totalValue = $this->applyFrontendFormatting(
                        $totalValue,
                        $total[Configuration::TOTALS_FORMATTER_KEY],
                        $total // NEU
                    );
                }
                $column['total'] = $totalValue;
            }
            if (isset($total[Configuration::TOTALS_LABEL_KEY])) {
                $column[Configuration::TOTALS_LABEL_KEY] =
                    $this->translator->trans($total[Configuration::TOTALS_LABEL_KEY]);
            }
            $columns[$field] = $column;
        };

        return ['columns' => $columns];
    }
    /**
     * Apply formatting to totals values
     *
     * @param mixed|null $val
     * @param string|null $formatter
     * @return string|null
     */
    protected function applyFrontendFormatting($val = null, $formatter = null, $totalConfig = [])
    {
        if (null === $formatter) {
            return $val;
        }

        switch ($formatter) {
            case PropertyInterface::TYPE_DATE:
                $val = $this->dateTimeFormatter->formatDate($val);
                break;
            case PropertyInterface::TYPE_DATETIME:
                $val = $this->dateTimeFormatter->format($val);
                break;
            case PropertyInterface::TYPE_TIME:
                $val = $this->dateTimeFormatter->formatTime($val);
                break;
            case PropertyInterface::TYPE_DECIMAL:
                $val = $this->numberFormatter->formatDecimal($val);
                break;
            case PropertyInterface::TYPE_INTEGER:
                $val = $this->numberFormatter->formatDecimal($val);
                break;
            case PropertyInterface::TYPE_PERCENT:
                $val = $this->numberFormatter->formatPercent($val);
                break;
            case PropertyInterface::TYPE_CURRENCY:
                if (isset($totalConfig[Configuration::TOTALS_DIVISOR_KEY])) {
                    $divisor = (int) $totalConfig[Configuration::TOTALS_DIVISOR_KEY];
                    if ($divisor != 0) {
                        $val = $val / $divisor;
                    }
                }
                $val = $this->numberFormatter->formatCurrency($val);
                break;
        }

        return $val;
    }
}