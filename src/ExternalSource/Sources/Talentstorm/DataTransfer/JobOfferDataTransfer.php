<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\DataTransfer;

use DVC\JobsImporterToPlentaBasic\ExternalSource\DataTransferInterface;
use Symfony\Component\Serializer\Annotation\SerializedPath;

class JobOfferDataTransfer implements DataTransferInterface
{
    public int $id;

    public string $label;

    public string $slug;

    #[SerializedPath('[additional][applicationFormUrl]')]
    public string $applicationFormUrl;

    public ?string $yearsOfExperience;

    public bool $isPublished;

    public \DateTimeInterface $creationDate;

    public \DateTimeInterface $lastModificationDate;

    public EmploymentDataTransfer $employment;

    public JobTypeDataTransfer $jobtype;

    // Description
    public string $descIntroductionTitle;
    public string $descIntroduction;
    public string $descJobProfileTitle;
    public string $descJobProfile;
    public string $descApplicantProfileTitle;
    public string $descApplicantProfile;
    public string $descOfferTitle;
    public string $descOffer;
    public string $description;

    public array $jobofferLocations = [];

    public string $startFromType;
    public ?string $startFromDate;
    public string $limitationType;
    public ?string $limitationDate;

    public ?string $image;

    public function getJobofferLocations(): array
    {
        return $this->jobofferLocations;
    }

    public function addJobofferLocations(JobLocationDataTransfer $jobofferLocation): void
    {
        $this->jobofferLocations[] = $jobofferLocation;
    }

    public function removeJobofferLocations(JobLocationDataTransfer $jobofferLocation): void
    {
        // not used
    }
}
