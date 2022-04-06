<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JournalRepository")
 */
class Journal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalStart;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalEnd;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Encaissement", mappedBy="journal", cascade={"persist"})
     */
    private $encaissements;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Decaissement", mappedBy="journal")
     */
    private $decaissements;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="journals")
     */
    private $client;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalEnc;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalDec;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalCb;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalEsp;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalChq;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalOther;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalEspStart;

    public function __construct()
    {
        $this->encaissements = new ArrayCollection();
        $this->decaissements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTotalStart(): ?string
    {
        return $this->totalStart;
    }

    public function setTotalStart(?string $totalStart): self
    {
        $this->totalStart = $totalStart;

        return $this;
    }

    public function getTotalEnd(): ?string
    {
        return $this->totalEnd;
    }

    public function setTotalEnd(?string $totalEnd): self
    {
        $this->totalEnd = $totalEnd;

        return $this;
    }

    /**
     * @return Collection|Encaissement[]
     */
    public function getEncaissements(): Collection
    {
        return $this->encaissements;
    }

    public function addEncaissement(Encaissement $encaissement): self
    {
        if (!$this->encaissements->contains($encaissement)) {
            $this->encaissements[] = $encaissement;
            $encaissement->setJournal($this);
        }

        return $this;
    }

    public function removeEncaissement(Encaissement $encaissement): self
    {
        if ($this->encaissements->contains($encaissement)) {
            $this->encaissements->removeElement($encaissement);
            // set the owning side to null (unless already changed)
            if ($encaissement->getJournal() === $this) {
                $encaissement->setJournal(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Decaissement[]
     */
    public function getDecaissements(): Collection
    {
        return $this->decaissements;
    }

    public function addDecaissement(Decaissement $decaissement): self
    {
        if (!$this->decaissements->contains($decaissement)) {
            $this->decaissements[] = $decaissement;
            $decaissement->setJournal($this);
        }

        return $this;
    }

    public function removeDecaissement(Decaissement $decaissement): self
    {
        if ($this->decaissements->contains($decaissement)) {
            $this->decaissements->removeElement($decaissement);
            // set the owning side to null (unless already changed)
            if ($decaissement->getJournal() === $this) {
                $decaissement->setJournal(null);
            }
        }

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

    public function getTotalEnc(): ?string
    {
        return $this->totalEnc;
    }

    public function setTotalEnc(?string $totalEnc): self
    {
        $this->totalEnc = $totalEnc;

        return $this;
    }

    public function getTotalDec(): ?string
    {
        return $this->totalDec;
    }

    public function setTotalDec(?string $totalDec): self
    {
        $this->totalDec = $totalDec;

        return $this;
    }

    public function getTotalCb(): ?string
    {
        return $this->totalCb;
    }

    public function setTotalCb(?string $totalCb): self
    {
        $this->totalCb = $totalCb;

        return $this;
    }

    public function getTotalEsp(): ?string
    {
        return $this->totalEsp;
    }

    public function setTotalEsp(?string $totalEsp): self
    {
        $this->totalEsp = $totalEsp;

        return $this;
    }

    public function getTotalChq(): ?string
    {
        return $this->totalChq;
    }

    public function setTotalChq(?string $totalChq): self
    {
        $this->totalChq = $totalChq;

        return $this;
    }

    public function getTotalOther(): ?string
    {
        return $this->totalOther;
    }

    public function setTotalOther(?string $totalOther): self
    {
        $this->totalOther = $totalOther;

        return $this;
    }

    public function getTotalEspStart(): ?string
    {
        return $this->totalEspStart;
    }

    public function setTotalEspStart(?string $totalEspStart): self
    {
        $this->totalEspStart = $totalEspStart;

        return $this;
    }
}
