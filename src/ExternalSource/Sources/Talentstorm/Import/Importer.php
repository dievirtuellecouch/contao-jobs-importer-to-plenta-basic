<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\Import;

use Contao\CoreBundle\Monolog\ContaoContext;
use DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\Import\HttpClientFactory;
use Psr\Log\LoggerInterface;

class Importer
{
    const ROUTE_LIST_ALL = 'https://api.talentstorm.de/api/v1/joboffers/basic';

    private $client;

    public function __construct(
        private HttpClientFactory $clientFactory,
        private LoggerInterface $logger,
    ) {
        $this->client = $clientFactory->getClient();
    }

    public function importJobsList(): ?array
    {
        try {
            $response = $this->client->request(
                'GET',
                self::ROUTE_LIST_ALL,
            );

            if ($response->getStatusCode() == 401) {
                $this->logger->info(
                    'Failed to import jobs from TalentStorm. Got "401 Not authorized" response. Please check the API key.', 
                    ['contao' => new ContaoContext(__METHOD__, ContaoContext::ERROR)]
                );
                
                return null;
            }

            if ($response->getStatusCode() == 403) {
                $this->logger->info(
                    'Failed to import jobs from TalentStorm. Got "403 Forbidden" response. Please check the API key.', 
                    ['contao' => new ContaoContext(__METHOD__, ContaoContext::ERROR)]
                );
                
                return null;
            }

            if ($response->getStatusCode() != 200) {
                $this->logger->info(
                    sprintf('Failed to import jobs from TalentStorm. Got response with status %s.', $response->getStatusCode()), 
                    ['contao' => new ContaoContext(__METHOD__, ContaoContext::ERROR)]
                );

                return null;
            }

            return $response->toArray();
        }
        catch (\Exception $e) {
            $this->logger->info(
                'Failed to import jobs from TalentStorm with an unexpected error. See Symfony log for more information.', 
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::ERROR)]
            );
               
            $this->logger->error(
                'Failed to import jobs from TalentStorm with an unexpected error.',
                [
                    'method' => __METHOD__,
                    'error_message' => $e->getMessage(),
                ]
            );
        }

        return null;
    }
}
