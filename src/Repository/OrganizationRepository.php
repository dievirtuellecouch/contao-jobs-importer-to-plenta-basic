<?php

namespace DVC\JobsImporterToPlentaBasic\Repository;

use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOrganizationModel as OrganizationModel;

class OrganizationRepository
{
    public function __construct(
        private array $mappings = [],
    ) {
    }

    public function getIdByLabel(?string $label): ?int
    {   
        if (empty($label)) {
            return null;
        }

        $collator = new \Collator('de');

        $mappingForLabel = \array_filter($this->mappings, function($mapping) use ($label, $collator) {
            if (!$mapping['label']) {
                return false;
            }

            return $collator->compare($label, $mapping['label']) === 0;
        });

        if (count($mappingForLabel) == 1) {
            return $mappingForLabel[\array_key_first($mappingForLabel)]['id'] ?? null;
        }

        $element = OrganizationModel::findOneBy(['name = ?'], [$label]);

        return $element->id ?? null;
    }
}
