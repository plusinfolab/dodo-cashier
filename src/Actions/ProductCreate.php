<?php

namespace Plusinfolab\DodoPayments\Actions;

use Plusinfolab\DodoPayments\DodoPayments;

class ProductCreate
{
    private string $apiKey;
    private bool $isLiveMode;

    public function __construct()
    {
    }

    public function create(array $data)
    {
        $this->validateData($data);
        return $this->executeCreateRequest($data);
    }

    protected function validateData(array $data): void
    {
        try {
            $requiredFields = ['name', 'price', 'tax_category'];
            foreach ($requiredFields as $field) {
                if (!array_key_exists($field, $data)) {
                    throw new \InvalidArgumentException("Missing required field: " . $field);
                }
            }

            // Validate price structure
            $priceFields = ['currency', 'price', 'type'];
            foreach ($priceFields as $field) {
                if (!array_key_exists($field, $data['price'])) {
                    throw new \InvalidArgumentException("Missing required price field: " . $field);
                }
            }

            $validPriceTypes = ['one_time_price', 'recurring_price', 'usage_based_price'];
            if (!in_array($data['price']['type'], $validPriceTypes)) {
                throw new \InvalidArgumentException("Invalid price type: " . $data['price']['type']);
            }

            // Additional validation for recurring prices
            if ($data['price']['type'] === 'recurring_price') {
                $recurringFields = [
                    'payment_frequency_interval',
                    'payment_frequency_count',
                    'subscription_period_interval',
                    'subscription_period_count'
                ];
                foreach ($recurringFields as $field) {
                    if (!array_key_exists($field, $data['price'])) {
                        throw new \InvalidArgumentException("Missing required recurring price field: " . $field);
                    }
                }

                $validIntervals = ['Day', 'Week', 'Month', 'Year'];
                if (!in_array($data['price']['payment_frequency_interval'], $validIntervals)) {
                    throw new \InvalidArgumentException("Invalid payment frequency interval: " . $data['price']['payment_frequency_interval']);
                }

                if (!in_array($data['price']['subscription_period_interval'], $validIntervals)) {
                    throw new \InvalidArgumentException("Invalid subscription period interval: " . $data['price']['subscription_period_interval']);
                }
            }

            // Validate tax category
            $validTaxCategories = ['digital_products', 'saas', 'e_book', 'edtech'];
            if (!in_array($data['tax_category'], $validTaxCategories)) {
                throw new \InvalidArgumentException("Invalid tax category: " . $data['tax_category']);
            }

        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Invalid product data: " . $e->getMessage());
        }
    }

    protected function executeCreateRequest(array $data)
    {
        $data['brand_id'] = config('dodo.brand_id');
        if(!config('dodo.brand_id')) {
            throw new \InvalidArgumentException("Dodo Brand ID is not configured.");
        }
        $response = DodoPayments::api('post', 'products', $data);
        return $response;
    }

    // Helper methods for creating different types of products
    public function createOneTimeProduct(string $name, int $price, string $currency = 'USD', array $options = [])
    {
        $data = array_merge([
            'name' => $name,
            'price' => [
                'type' => 'one_time_price',
                'price' => $price,
                'currency' => $currency,
                'discount' => 0,
                'purchasing_power_parity' => true,
                'tax_inclusive' => false,
            ],
            'tax_category' => 'digital_products',
        ], $options);

        return $this->create($data);
    }

    public function createRecurringProduct(string $name, int $price, string $currency = 'USD', string $interval = 'Month', int $intervalCount = 1, array $options = [])
    {
        $data = array_merge([
            'name' => $name,
            'price' => [
                'type' => 'recurring_price',
                'price' => $price,
                'currency' => $currency,
                'discount' => 0,
                'purchasing_power_parity' => true,
                'tax_inclusive' => false,
                'payment_frequency_interval' => $interval,
                'payment_frequency_count' => $intervalCount,
                'subscription_period_interval' => $interval,
                'subscription_period_count' => $intervalCount,
                'trial_period_days' => 0,
            ],
            'tax_category' => 'saas',
        ], $options);

        return $this->create($data);
    }
}