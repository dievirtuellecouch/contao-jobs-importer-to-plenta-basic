<?php

namespace DVC\JobsImporterToPlentaBasic\DependencyInjection;

use DVC\JobsImporterToPlentaBasic\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Filesystem\Path;

class JobsImporterToPlentaBasicExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(Path::canonicalize(__DIR__ . '../../../config/')));
        $loader->load('services.yaml');

        $configuration = new Configuration();

        $processedConfiguration = $this->processConfiguration($configuration, $configs);

        if (!\array_key_exists('sources', $processedConfiguration) || !is_array($processedConfiguration['sources'])) {
            $sources = [];
        }
        else {
            $sources = $this->initExternalSources($container, $processedConfiguration['sources']);
        }

        $sourceRegistryDefinition = $container->getDefinition(\DVC\JobsImporterToPlentaBasic\ExternalSource\ExternalSourceRegistry::class);
        $sourceRegistryDefinition->setArgument('$configuredSources', $sources);

        $organizationRepositoryDefinition = $container->getDefinition(\DVC\JobsImporterToPlentaBasic\Repository\OrganizationRepository::class);
        $organizationRepositoryDefinition->setArgument('$mappings', $processedConfiguration['mapping']['organization'] ?? []);

        $adjustJobOfferDatePostedEventSubscriberDefinition = $container->getDefinition(\DVC\JobsImporterToPlentaBasic\EventSubscriber\AdjustJobOfferDatePostedEventSubscriber::class);
        $adjustJobOfferDatePostedEventSubscriberDefinition->setArgument('$overrideThreshold', $processedConfiguration['override_date_posted_threshold'] ?? null);
    }

    private function initExternalSources(ContainerBuilder $container, array $sources): array
    {
        if (empty($sources)) {
            return [];
        }

        $result = [];

        foreach ($sources as $sourceConfig) {
            $sourceName = 'jobs_importer_to_plenta_basic.external_source.' . $sourceConfig['type'];

            if (!$container->has($sourceName)) {
                continue;
            }

            $clientDefinition = $container->getDefinition($sourceName . '.client');
            $clientDefinition->setArgument('$apiSecret', $sourceConfig['api_key']);
            $clientDefinition->setArgument('$timeout', $sourceConfig['timeout']);

            $result[] = new Reference($sourceName);
        }

        return $result;
    }
}
