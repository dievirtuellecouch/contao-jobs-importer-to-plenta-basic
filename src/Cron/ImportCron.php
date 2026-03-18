<?php

namespace DVC\JobsImporterToPlentaBasic\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use DVC\JobsImporterToPlentaBasic\Import\Importer;

#[AsCronJob('hourly')]
class ImportCron
{
    public function __construct(
        private Importer $importer,
    ) {
    }

    public function __invoke(): void
    {
        $this->importer->importAll();
    }
}
