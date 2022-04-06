<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DecaissementRepository")
 */
class Decaissement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $total;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="decaissements")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $comment;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Journal", inversedBy="decaissements")
     */
    private $journal;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $cb;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $esp;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $chq;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $other;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isEnc;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getUser(): ?Client
    {
        return $this->user;
    }

    public function setUser(?Client $user): self
    {
        $this->user = $user;

        return $this;
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


    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }


    public function getJournal(): ?Journal
    {
        return $this->journal;
    }

    public function setJournal(?Journal $journal): self
    {
        $this->journal = $journal;

        return $this;
    }

    public function getCb(): ?string
    {
        return $this->cb;
    }

    public function setCb(?string $cb): self
    {
        $this->cb = $cb;

        return $this;
    }

    public function getEsp(): ?string
    {
        return $this->esp;
    }

    public function setEsp(?string $esp): self
    {
        $this->esp = $esp;

        return $this;
    }

    public function getChq(): ?string
    {
        return $this->chq;
    }

    public function setChq(?string $chq): self
    {
        $this->chq = $chq;

        return $this;
    }

    public function getOther(): ?string
    {
        return $this->other;
    }

    public function setOther(?string $other): self
    {
        $this->other = $other;

        return $this;
    }

    public function getIsEnc(): ?bool
    {
        return $this->isEnc;
    }

    public function setIsEnc(?bool $isEnc): self
    {
        $this->isEnc = $isEnc;

        return $this;
    }
}
