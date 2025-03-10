<?php

namespace Phpsa\LaravelCurrencies\Concerns;

use Phpsa\LaravelCurrencies\Amount;
use Phpsa\LaravelCurrencies\Contracts\CurrencyContract as Currency;

trait SerializesAmounts
{
    /**
     * @var callable|null
     */
    protected static $formatter;

    /**
     * @param callable $formatter
     */
    public static function formatUsing(?callable $formatter)
    {
        static::$formatter = $formatter;
    }

    /**
     * @param  array $exported
     * @return Amount|null
     *
     * @throws \Exception
     */
    public static function fromArray($exported)
    {
        if ($exported === null) {
            return;
        }

        static::requiresProperties(['amount', 'currency'], $exported);

        return new static($exported['amount'], static::normalizeCurrency($exported['currency']));
    }

    /**
     * @param  $amount
     * @param  Currency | mixed $currency null
     * @return static
     */
    public static function fromCents($amount, $currency = null)
    {
        return new static($amount / 100, $currency);
    }

    /**
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'amount'    => $this->get(),
            'currency'  => $this->currency()->getCode(),
            'formatted' => $this->toFormat(),
        ];
    }

    /**
     * @return int
     */
    public function toCents()
    {
        return (int) round(($this->get() * 100));
    }

    /**
     * @param  callable|null $formatter
     * @return string
     */
    public function toFormat($formatter = null)
    {
        if ($formatter) {
            return call_user_func($formatter, $this);
        }

        if (static::$formatter) {
            return call_user_func(static::$formatter, $this);
        }

        [$currency, $amount] = [
            $this->currency()->getCode(),
            number_format(
                $this->get(),
                config('currencies.formatting_decimals'),
                config('currencies.decimal_separator'),
                config('currencies.thousands_separator')
            ),
        ];

        return "{$currency} {$amount}";
    }
}
