<?php

namespace GetYourGuide\MarketplaceTest;

use DateTime;
use DateInterval;
use GuzzleHttp\Client;

class ProductResource {

    const PRODUCTS_ENDPOINT= 'http://www.mocky.io/v2/58ff37f2110000070cf5ff16';

    protected $client;

    public function __construct()
    {
        $this->client = $client = new Client();
    }

    public function fetch(
        DateTime $requestedStartTime,
        DateTime $requestedEndTime,
        int $numberOfTravelers
    )
    {
        $data = $this->makeRequest();

        if (!$data || !isset($data['product_availabilities']) || !$data['product_availabilities']) {
            return [];
        }

        $availableProducts = [];

        foreach ($data['product_availabilities'] as $product) {
            $isProductAvailable = $this->isProductAvailable(
                $product,
                $requestedStartTime,
                $requestedEndTime,
                $numberOfTravelers
            );

            if (!$isProductAvailable) {
                $availableProducts[] = $product;
            }
        }

        $groupedProducts = $this->groupAvailableTimesByProduct(
            $availableProducts
        );

        return $this->sort($groupedProducts);
    }

    public function isProductAvailable(
        array $product,
        DateTime $requestedStartTime,
        DateTime $requestedEndTime,
        int $numberOfTravelers
    )
    {
        if ($product['places_available'] < $numberOfTravelers) {
            return false;
        }

        $productStartTime = new DateTime(
            $product['activity_start_datetime']
        );

        if ($productStartTime < $requestedStartTime) {
            return false;
        }

        $productEndTime = $this->calculateActivityEndTime(
            $productStartTime,
            $product['activity_duration_in_minutes']
        );

        if ($productEndTime > $requestedEndTime) {
            return false;
        }

        return true;
    }

    protected function calculateActivityEndTime(
        DateTime $startTime,
        int $activityDurationInMinutes
    )
    {
        $interval = new DateInterval(sprintf(
            'PT%dM',
            $activityDurationInMinutes
        ));

        $endTime = clone $startTime;
        $endTime->add($interval);

        return $endTime;
    }

    protected function groupAvailableTimesByProduct(array $products)
    {
        $data = [];

        foreach ($products as $product) {
            $productId = $product['product_id'];

            $data[$productId]['product_id'] = $productId;
            $data[$productId]['available_starttimes'][] = $product['activity_start_datetime'];
        }

        return array_values($data);
    }

    public function sort($products)
    {
        $products = $this->sortProductIds($products);
        $products = $this->sortAvailableStartTimes($products);

        return $products;
    }

    public function sortProductIds(array $products)
    {
        $productIds = array_column($products, 'product_id');
        array_multisort($productIds, SORT_ASC, $products);

        return $products;
    }

    public function sortAvailableStartTimes(array $products)
    {
        foreach ($products as & $product) {
            sort($product['available_starttimes']);
        }

        return $products;
    }

    public function makeRequest()
    {
        $response = $this->client->get($this::PRODUCTS_ENDPOINT);

        if ($response->getStatusCode() !== 200) {
            return [];
        }

        return json_decode($response->getBody(), true);
    }
}
