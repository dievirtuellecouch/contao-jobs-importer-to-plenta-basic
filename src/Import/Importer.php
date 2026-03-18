<?php

namespace DVC\JobsImporterToPlentaBasic\Import;

use DVC\JobsImporterToPlentaBasic\Event\PreModelPersistentEvent;
use DVC\JobsImporterToPlentaBasic\ExternalSource\ExternalSourceRegistry;
use DVC\JobsImporterToPlentaBasic\ExternalSource\ModelSearchParameter;
use DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\TalentstormSource;
use DVC\JobsImporterToPlentaBasic\ExternalSource\SupportedModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel as JobLocationModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel as JobOfferModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOrganizationModel as OrganizationModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Importer
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ExternalSourceRegistry $externalSourceRegistry,
    ) {
    }

    public function importAll(): void
    {
        $modelClasses = [
            SupportedModel::Organization->value => OrganizationModel::class,
            SupportedModel::Location->value => JobLocationModel::class,
            SupportedModel::Offer->value => JobOfferModel::class,
        ];

        foreach ($this->externalSourceRegistry->getAll() as $source) {

            foreach ($modelClasses as $modelKey => $modelClassName) {
                $items = $source->getReader()->getItemsForIdentifier(SupportedModel::from($modelKey));
                $searchParameters = $source->getSearchParamterForIdentifier(SupportedModel::from($modelKey));
                $findOneBy = new \ReflectionMethod($modelClassName, 'findOneBy');

                if (empty($items)) {
                    continue;
                }

                $importedIdsPerModel = [];

                foreach ($items as $item) {
                    $model = $findOneBy->invoke(
                        null,
                        $searchParameters->getColumns(),
                        $searchParameters->getValuesForItem($item),
                    );

                    if (empty($model)) {
                        $model = new $modelClassName;
                    }

                    $oldModel = clone $model;

                    $source->getTransformer($modelKey)->transform($item, $model);

                    $modelReference = &$model;
                    $oldModelReference = &$oldModel;

                    $preModelPersistEvent = new PreModelPersistentEvent($modelReference, $oldModelReference);
                    $this->eventDispatcher->dispatch($preModelPersistEvent);

                    $model->save();

                    $importedIdsPerModel[] = $model->id;
                }

                foreach ($this->getCallbacks($modelKey) as $callback) {
                    \call_user_func($callback, $importedIdsPerModel);
                }
            }
        }
    }

    private function getCallbacks(string $modelKey): array
    {
        $callbacksPerModel = [
            SupportedModel::Offer->value => [
                [self::class, 'disableOffers']
            ],
        ];

        if (!\array_key_exists($modelKey, $callbacksPerModel)) {
            return [];
        }

        return $callbacksPerModel[$modelKey];
    }

    private function disableOffers(array $importedIds): void
    {
        if (empty($importedIds)) {
            return;
        }

        $itemsToDisable = JobOfferModel::findBy(
            [
                'externalSource != ?',
                \sprintf('id NOT IN (%s)', \join(',', \array_map(fn($id) => \intval($id), $importedIds))),
            ],
            ['']
        );

        if (empty($itemsToDisable)) {
            return;
        }
        
        foreach ($itemsToDisable as $item) {
            $item->published = 0;
            $item->save();
        }
    }
}
