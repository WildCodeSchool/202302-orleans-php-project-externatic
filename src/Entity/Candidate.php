<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CandidateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CandidateRepository::class)]
#[Vich\Uploadable]
class Candidate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cvitae = null;

    #[Vich\UploadableField(mapping: 'candidate_file', fileNameProperty: 'cvitae')]
    #[Assert\File(
        maxSize: '1M',
        mimeTypes: [
            'application/pdf',
        ],
    )]
    private ?File $cvitaeFile = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DatetimeInterface $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: JobOffer::class, mappedBy: 'candidates')]
    private Collection $jobOffers;

    #[ORM\OneToOne(inversedBy: 'candidate', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->jobOffers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection<int, JobOffer>
     */
    public function getFavorites(): Collection
    {
        return $this->jobOffers;
    }

    public function addToFavorites(JobOffer $jobOffer): static
    {
        if (!$this->jobOffers->contains($jobOffer)) {
            $this->jobOffers->add($jobOffer);
            $jobOffer->addCandidate($this);
        }

        return $this;
    }

    public function removeFromFavorites(JobOffer $jobOffer): static
    {
        if ($this->jobOffers->removeElement($jobOffer)) {
            $jobOffer->removeCandidate($this);
        }

        return $this;
    }

    public function isFavorite(JobOffer $jobOffer): bool
    {
        if (in_array($jobOffer, $this->getFavorites()->toArray())) {
            return true;
        } else {
            return false;
        }
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function setCvitaeFile(?File $cvitae = null): Candidate
    {
        $this->cvitaeFile = $cvitae;
        if ($cvitae) {
            $this->updatedAt = new DateTime('now');
        }
        return $this;
    }

    public function getCvitaeFile(): ?File
    {
        return $this->cvitaeFile;
    }

    public function getCvitae(): ?string
    {
        return $this->cvitae;
    }

    public function setCvitae(?string $cvitae): self
    {
        $this->cvitae = $cvitae;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
