<?php

namespace DVC\JobsImporterToPlentaBasic\Repository;

use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel as JobLocationModel;

class JobLocationRepository
{
    public function findOneByExternalId(int $externalId, string $externalSource): ?JobLocationModel
    {
        $model = JobLocationModel::findOneBy(
            ['externalId = ?', 'externalSource = ?'],
            [$externalId, $externalSource],
        );

        return !empty($model) ? $model : null;
    }
}
