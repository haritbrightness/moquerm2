<?php

namespace Swissup\DeliveryDate\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Intl\DateTimeFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_ENABLED                    = 'delivery_date/main/enabled';
    const CONFIG_FILTER_PER_SHIPPING_METHOD = 'delivery_date/main/filter_per_shipping_method';
    const CONFIG_SHIPPING_METHODS           = 'delivery_date/main/shipping_methods';

    const CONFIG_DATE_LABEL             = 'delivery_date/main/field_label';
    const CONFIG_DATE_USE_DEFAULT_VALUE = 'delivery_date/main/date_use_default_value';
    const CONFIG_DATE_SHOW              = 'delivery_date/main/date_show';
    const CONFIG_DATE_FORMAT            = 'delivery_date/main/date_format';
    const CONFIG_DATE_FIRSTDAY          = 'general/locale/firstday';

    const CONFIG_TIME_LABEL             = 'delivery_date/main/time_label';
    const CONFIG_TIME_SHOW              = 'delivery_date/main/time_show';
    const CONFIG_TIME_OPTIONS           = 'delivery_date/main/time_options';

    const CONFIG_PROCESSING_END_TIME    = 'delivery_date/first_available/processing_end_time';
    const CONFIG_MIN_DELAY              = 'delivery_date/first_available/min_delay';
    const CONFIG_MAX_DELAY              = 'delivery_date/last_available/max_delay';
    const CONFIG_EXCLUDE_WEEKDAYS       = 'delivery_date/exclude/exclude_weekdays';
    const CONFIG_HOLIDAYS               = 'delivery_date/exclude/holidays';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    public $timeZone;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var DateTimeFactory
     */
    private $dateTimeFactory;

    /**
     * @var \Magento\Config\Model\Config\Backend\Serialized\ArraySerializedFactory
     */
    private $serializedArrayFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timeZone
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param DateTimeFactory $dateTimeFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timeZone,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        DateTimeFactory $dateTimeFactory,
        \Magento\Config\Model\Config\Backend\Serialized\ArraySerializedFactory $serializedArrayFactory
    ) {
        $this->timeZone = $timeZone;
        $this->storeManager = $storeManager;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->serializedArrayFactory = $serializedArrayFactory;
        return parent::__construct($context);
    }

    /**
     * @param  string $key
     * @param  string $scope
     * @return string
     */
    protected function getOption($key, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($key, $scope);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->getOption(self::CONFIG_ENABLED);
    }

    /**
     * @return bool
     */
    public function isFilterPerShippingMethod()
    {
        return (bool) $this->getOption(self::CONFIG_FILTER_PER_SHIPPING_METHOD);
    }

    /**
     * @return array
     */
    public function getShippingMethods()
    {
        return $this->getOption(self::CONFIG_SHIPPING_METHODS);
    }

    /**
     * @return int
     */
    public function getMaxDelayDays()
    {
        return (int) $this->getOption(self::CONFIG_MAX_DELAY);
    }

    /**
     * @return array
     */
    public function getExcludedWeekdays()
    {
        return array_map('intval', explode(',', $this->getOption(self::CONFIG_EXCLUDE_WEEKDAYS)));
    }

    /**
     * @return string
     */
    public function getDateLabel()
    {
        return $this->getOption(self::CONFIG_DATE_LABEL);
    }

    /**
     * @return string
     */
    public function getDateStatus()
    {
        return $this->getOption(self::CONFIG_DATE_SHOW);
    }

    /**
     * @return bool
     */
    public function isDateRequired()
    {
        return $this->getDateStatus() === 'req';
    }

    /**
     * @return bool
     */
    public function useDefaultDateValue()
    {
        return (bool) $this->getOption(self::CONFIG_DATE_USE_DEFAULT_VALUE);
    }

    /**
     * @return string
     */
    public function getFirstDay()
    {
        return $this->getOption(self::CONFIG_DATE_FIRSTDAY);
    }

    /**
     * @return string
     */
    public function getTimeLabel()
    {
        return $this->getOption(self::CONFIG_TIME_LABEL);
    }

    /**
     * @return string
     */
    public function getTimeStatus()
    {
        return $this->getOption(self::CONFIG_TIME_SHOW);
    }

    /**
     * @return bool
     */
    public function isTimeRequired()
    {
        return $this->getTimeStatus() === 'req';
    }

    /**
     * @return string
     */
    public function getTimeOptions($valuesOnly = false)
    {
        $model = $this->serializedArrayFactory->create()
            ->setValue($this->getOption(self::CONFIG_TIME_OPTIONS));
        $model->afterLoad();

        $values = $model->getValue();
        if (!$values) {
            return [];
        }

        $result = [
            [
                'value' => '',
                'label' => __('Please Select'),
            ],
        ];
        foreach ($values as $value) {
            $label = sprintf(
                '%s:%s — %s:%s',
                $value['from']['hour'],
                $value['from']['minute'],
                $value['to']['hour'],
                $value['to']['minute']
            );
            $result[] = [
                'value' => $label,
                'label' => $label,
            ];
        }

        if ($valuesOnly) {
            $result = array_column($result, 'value');
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getMinDelayDays()
    {
        $min = (int) $this->getOption(self::CONFIG_MIN_DELAY);

        $time = $this->getOption(self::CONFIG_PROCESSING_END_TIME);
        list($hour, $minute, $second) = explode(',', $time);
        $now = $this->dateTimeFactory->create();
        $timezoneLocal = $this->timeZone->getConfigTimezone();
        $now->setTimezone(new \DateTimeZone($timezoneLocal));
        $_now = $this->dateTimeFactory->create();
        $_now->setTimezone(new \DateTimeZone($timezoneLocal));
        $_now->setTime($hour, $minute, $second);
        if ($now >= $_now) {
            $min++;
        }

        return $min;
    }

    /**
     * @param  string $dateString
     * @param  string|false $format
     * @return string
     */
    public function formatMySqlDateTime($dateString, $format = false)
    {
        $format = $format ? $format : $this->getDateFormat();
        $dateTime = \DateTime::createFromFormat($format, $dateString);
        if ($dateTime) {
            $format = \Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT;
            $dateTime->setTime(0, 0, 0);
            $dateTime = $dateTime->format($format);
        }
        return $dateTime;
    }

    /**
     * @param string $date
     * @return string
     */
    public function getFormattedDate($date)
    {
        if (!$date) {
            return '';
        }

        $date = $this->dateTimeFactory->create($date);
        $dateFormat = $this->getDateFormat();
        $date = $date->format($dateFormat);
        return $date;
    }

    /**
     * @return mixed
     */
    public function getDateFormat($forJsCalendar = false)
    {
        $format = $this->getOption(self::CONFIG_DATE_FORMAT);

        if (empty($format)) {
            $format = $this->timeZone->getDateFormatWithLongYear();
        }

        // convert pattern to date format:
        //  dd.MM.Y => d.m.Y (uk_UA) and so on
        $mapping = [
            'dd' => 'd',
            // 'M' => 'm', // don't use literal monthes (Arabic locale fix)
            // 'F' => 'm', // don't use literal monthes (Arabic locale fix)
            'MM' => 'm',
            'yyyy' => 'Y',  // @see mage/calendar.js#430
            'yy' => 'Y',    // @see mage/calendar.js#430
            'y' => 'Y',     // @see mage/calendar.js#430
            'G' => '',      // ar_SA locale returns pattern with era: 'd‏/M‏/y GGGGG'
        ];
        $format = str_replace(array_keys($mapping), $mapping, $format);

        // strip right-to-left marker as it brakes input value
        $format = preg_replace('/(\x{200e}|\x{200f})/u', '', $format);

        if ($forJsCalendar) {
            $mapping = [
                'm' => 'mm',
                'M' => 'MMM',   // Jan through Dec.          @see mage/calendar.js#424
                'F' => 'MMMM',  // January through December. @see mage/calendar.js#423
                'd' => 'dd',    // 01 to 31
                'j' => 'd',     // 1 to 31
                'l' => 'EEEE',  // Sunday through Saturday. @see mage/calendar.js#418
                'D' => 'EEE',   // Sun through Sat.         @see mage/calendar.js#419
                'z' => 'o',     // 0 through 365
            ];
            $format = str_replace(array_keys($mapping), $mapping, $format);
        }

        return trim($format);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * @param  array   $years
     * @param  array   $months
     * @param  array   $days
     * @param  integer $offset
     * @return array of timestamps
     */
    protected function timestamps($years = [], $months = [], $days = [], $offset = 1)
    {
        if (0 == $years) {
            $_year = date("Y");
            $years = range($_year - 1, $_year + 6);
        }
        if (!is_array($years)) {
            $years = [$years];
        }
        if (0 == $months) {
            $months = range(1, 12);
        }
        if (!is_array($months)) {
            $months = [$months];
        }
        if (!is_array($days)) {
            $days = [$days];
        }
        $offset = (int) $offset;
        $result = [];
        foreach ($years as $year) {
            foreach ($months as $month) {
                foreach ($days as $day) {
                    for ($i = 0; $i < $offset; $i++) {
                        $result[] = strtotime(
                            $year  . '-' . $month . '-' . $day . ' +' . $i . ' days'
                        ) * 1000;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getHolidays()
    {
        $model = $this->serializedArrayFactory->create()
            ->setValue($this->getOption(self::CONFIG_HOLIDAYS));
        $model->afterLoad();

        $_holidays = $model->getValue();
        if (!$_holidays) {
            return [];
        }

        $holidays = [];
        foreach ($_holidays as $_h) {
            if (isset($_h['day']) && isset($_h['month']) && isset($_h['year'])) {
                $offset = isset($_h['offset']) && 0 != $_h['offset'] ? $_h['offset'] : 1;
                $holidays = array_merge($holidays, $this->timestamps(
                    $_h['year'],
                    $_h['month'],
                    $_h['day'],
                    $offset
                ));
            }
        }
        $holidays = array_filter($holidays);
        $holidays = array_unique($holidays);
        return $holidays;
    }
}
