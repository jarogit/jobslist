<?php

namespace App\Service;

use App\Exception\RecruitisApiException;
use App\Dto\PaginateLoaderResultDto;
use GuzzleHttp;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class RecruitisApi
{
    public function __construct(private LoggerInterface $logger, private string $token) {}

    public function getJobs(int $page, int $itemsPerPage): PaginateLoaderResultDto
    {
        $dtoResult = new PaginateLoaderResultDto();

        try {
            $cache = new FilesystemAdapter();
            $data = $cache->get(
                "jobs_{$page}_{$itemsPerPage}",
                function (ItemInterface $item) use ($page, $itemsPerPage) {
                    $item->expiresAfter(3600);

                    return $this->sendRequest('jobs', [
                        'page' => $page,
                        'limit' => $itemsPerPage,
                        'access_state' => 1,    // open positions only
                    ]);
                }
            );

            $dtoResult->items = $data['items'];
            $dtoResult->total = $data['total'];

        } catch(RecruitisApiException $e) {}

        return $dtoResult;
    }

    private function sendRequest(string $endpoint, array $params)
    {
        static $http;

        if (!$http) {
            $http = new GuzzleHttp\Client([
                'base_uri' => 'https://api.recruitis.io/api2/',
                'http_errors' => false,
                'headers' => ['Authorization' => "Bearer {$this->token}"],
            ]);
        }

        $response = $http->get($endpoint, ['query' => $params]);

        $error = null;
        if ($response->getStatusCode() === 200) {
            $json = json_decode((string)$response->getBody(), true);
            if ($json && isset($json['meta']['code'])) {
                if (in_array($json['meta']['code'], ['api.found', 'api.response.null'], true)) {
                    $result = [
                        'total' => $json['meta']['entries_total'],
                        'items' => $json['payload'],
                    ];
                } else {
                    $error = 'recruitis.io response: ' . json_encode($json);
                }
            } else {
                $error = "Unknown recruitis.io response structure: " . $response->getBody();
            }
        } else {
            $error = "recruitis.io response {$response->getStatusCode()} " . $response->getBody();
        }

        if ($error) {
            $this->logger->warning($error);
            throw new RecruitisApiException();
        }

        return $result;
    }
}
