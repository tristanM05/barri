<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserConfigurationRepository")
 */
class UserConfiguration
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxsupertop;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxtop;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Client", inversedBy="client", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaxsupertop(): ?int
    {
        return $this->maxsupertop;
    }

    public function setMaxsupertop(?int $maxsupertop): self
    {
        $this->maxsupertop = $maxsupertop;

        return $this;
    }

    public function getMaxtop(): ?int
    {
        return $this->maxtop;
    }

    public function setMaxtop(?int $maxtop): self
    {
        $this->maxtop = $maxtop;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
