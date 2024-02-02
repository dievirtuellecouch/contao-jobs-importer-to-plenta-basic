<?php

namespace DVC\JobsImporter\ExternalSource\Sources\Talentstorm\Transformer;

use Contao\Model;
use DataMap\Mapper;
use DVC\JobsImporter\DataMap\Getter\GetDateTimestamp;
use DVC\JobsImporter\ExternalSource\DataTransferInterface;
use DVC\JobsImporter\ExternalSource\DefaultValues\OrganizationDefaultValues;
use DVC\JobsImporter\ExternalSource\TransformerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class OrganizationTransformer implements TransformerInterface
{
    private PropertyAccessor $propertyAccessor;

    public function __construct(
        private OrganizationDefaultValues $organizationDefaultValues,
    ) {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function getMapper(): Mapper
    {
        return new Mapper([
            'name' => 'label',
        ]);
    }

    public function getDefaultMapper(): Mapper
    {
        return new Mapper([
            'tstamp' => new GetDateTimestamp('tstamp'),
            'sameAs' => 'sameAs',
            'logo' => 'logo',
        ]);
    }

    public function transform(DataTransferInterface $dataTransfer, Model &$model): void
    {
        $modelValues = \array_merge(
            $this->getDefaultMapper()->map($this->organizationDefaultValues->getAll()),
            $this->getMapper()->map($dataTransfer),
        );

        foreach ($modelValues as $key => $value) {
            $this->propertyAccessor->setValue($model, $key, $value);
        }
    }
}