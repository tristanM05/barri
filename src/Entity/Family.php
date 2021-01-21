<?php

namespace App\Entity;

use App\Entity\Client;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FamilyRepository")
 */
class Family
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(max=50,maxMessage="Le nom de la famille doit être inférieur à 50 caractères")
     */
    private $wording;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="families")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Subfamily", mappedBy="family", orphanRemoval=true)
     */
    private $subfamilies;

    public function __construct()
    {
        $this->subfamilies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): self
    {
        $this->wording = $wording;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection|Subfamily[]
     */
    public function getSubfamilies(): Collection
    {
        return $this->subfamilies;
    }

    public function addSubfamily(Subfamily $subfamily): self
    {
        if (!$this->subfamilies->contains($subfamily)) {
            $this->subfamilies[] = $subfamily;
            $subfamily->setFamily($this);
        }

        return $this;
    }

    public function removeSubfamily(Subfamily $subfamily): self
    {
        if ($this->subfamilies->contains($subfamily)) {
            $this->subfamilies->removeElement($subfamily);
            // set the owning side to null (unless already changed)
            if ($subfamily->getFamily() === $this) {
                $subfamily->setFamily(null);
            }
        }

        return $this;
    }
}
