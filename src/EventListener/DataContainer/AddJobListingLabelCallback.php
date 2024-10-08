<?php

namespace DVC\JobsImporterToPlentaBasic\EventListener\DataContainer;

use Contao\Config;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Date;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCallback(table: 'tl_plenta_jobs_basic_offer', target: 'list.label.label')]
class AddJobListingLabelCallback
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }
    
    public function __invoke(array $row, string $label, DataContainer $dc, array $labels): array
    {
        $result = $row['title'];

        if (!empty($row['externalSource'])) {
            $result .= $this->buildSubLabel($row);
        }

        return [$result];
    }

    private function buildSubLabel(array $row): string
    {
        $externalSourceDisplay = $this->translator->trans('dvc_jobs_importer_to_plenta_basic.external_source.' . $row['externalSource'], [], 'contao_default');
        $importDateDisplay = Date::parse(Config::get('datimFormat'), $row['importDate']);
        
        $displayText = \str_replace(
            ['{source}', '{date}'],
            [$externalSourceDisplay, $importDateDisplay],
            $this->translator->trans('dvc_jobs_importer_to_plenta_basic.job_offer.sub_label', [], 'contao_default')
        );

        return \sprintf(
            '<span class="label-info">%s</span>',
            $displayText
        );
    }
}
