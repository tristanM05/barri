<?php

namespace App\Entity;


use Doctrine\ORM\Mapping\Table as Table;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity(repositoryClass="App\Repository\CaisseRepository", readOnly=true)
 * @Table(name="view_caisse")
 */
class Caisse
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $total;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2, nullable=true)
     */
    private $clientId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $journalId;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
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

    public function setTotal(?string $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(?string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getJournalId(): ?int
    {
        return $this->journalId;
    }

    public function setJournalId(?int $journalId): self
    {
        $this->journalId = $journalId;

        return $this;
    }

    public function getCb(): ?string
    {
        return $this->cb;
    }

    public function setCb(string $cb): self
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
