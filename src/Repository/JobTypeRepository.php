<?php

namespace DVC\JobsImporterToPlentaBasic\Repository;

use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicSettingsEmploymentTypeModel as EmploymentTypeModel;

class JobTypeRepository
{
    public function findOneByTitle(?string $title): ?EmploymentTypeModel
    {
        if (empty($title)) {
            return null;
        }
        
        $model = EmploymentTypeModel::findOneBy(
            ['title = ?'],
            [$title],
        );

        return !empty($model) ? $model : null;
    }
}
