<?php

namespace DVC\JobsImporterToPlentaBasic\EventListener\Contao;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Input;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel as JobOfferModel;

/**
 * Bridge legacy insert tag {{job::externalApplicationUrl}} to the new resolver.
 */
#[AsHook('replaceInsertTags')]
class InsertTagListener
{
    public function __invoke(string $tag): string|false
    {
        $chunks = explode('::', $tag, 2);

        if ('job' !== ($chunks[0] ?? null)) {
            return false;
        }

        if (($chunks[1] ?? '') !== 'externalApplicationUrl') {
            return false;
        }

        $alias = Input::get('auto_item', false, true);
        $offer = JobOfferModel::findPublishedByIdOrAlias($alias);

        if (null === $offer) {
            return '';
        }

        return (string) ($offer->externalApplicationUrl ?? '');
    }
}
