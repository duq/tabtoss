<?php

namespace App\Services;

use App\Models\Bookmark;
use Carbon\CarbonImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BookmarkUrlStatusService
{
    private const REQUEST_TIMEOUT_SECONDS = 2;
    private const CONNECT_TIMEOUT_SECONDS = 2;
    private const STATUS_ERROR = 0;

    public function checkAndUpdate(Bookmark $bookmark): Bookmark
    {
        $status = $this->fetchStatusCode($bookmark->url);

        $bookmark->url_status = $status;
        $bookmark->url_checked_at = CarbonImmutable::now();
        $bookmark->save();

        return $bookmark;
    }

    private function fetchStatusCode(string $url): int
    {
        $client = new Client([
            'timeout' => self::REQUEST_TIMEOUT_SECONDS,
            'connect_timeout' => self::CONNECT_TIMEOUT_SECONDS,
            'http_errors' => false,
            'allow_redirects' => true,
            'headers' => [
                'User-Agent' => 'SaaSykit Bookmark Checker',
            ],
        ]);

        $status = $this->requestStatus($client, 'HEAD', $url);
        if ($status !== null) {
            return $status;
        }

        $status = $this->requestStatus($client, 'GET', $url);
        if ($status !== null) {
            return $status;
        }

        return self::STATUS_ERROR;
    }

    private function requestStatus(Client $client, string $method, string $url): ?int
    {
        try {
            $response = $client->request($method, $url);
        } catch (GuzzleException) {
            return null;
        }

        return $response->getStatusCode();
    }
}
