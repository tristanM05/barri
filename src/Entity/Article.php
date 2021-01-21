<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @ORM\Table(name="article", indexes={@ORM\Index(columns={"designation"}, flags={"fulltext"})})
 * @UniqueEntity(fields = {"number","client"},message ="Ce code barre est déja utilisé")
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     * @Assert\Length(min="1",minMessage="Le numéro d'identification doit faire minimum 1 caractère")
     * @Assert\Type("numeric")
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $designation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $describing;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $costprice;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $referenceprice;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $specialprice;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $productiondate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $leftdate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isvisible;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Salepoint", inversedBy="articles",fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $salepoint;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductStatus", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $productStatus;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Subfamily", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subfamily;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Images", mappedBy="article", orphanRemoval=true, cascade={"persist"})
     */
    private $images;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateLimit;

    public function getInterval(){
        $now = new \DateTime('now');
        $now->setTime(0,0,0);
        $diff = $this->endDate->diff($now);
        return $diff->days;
    }

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    public function getDescribing(): ?string
    {
        return $this->describing;
    }

    public function setDescribing(?string $describing): self
    {
        $this->describing = $describing;

        return $this;
    }

    public function getCostprice(): ?string
    {
        return $this->costprice;
    }

    public function setCostprice(?string $costprice): self
    {
        $this->costprice = $costprice;

        return $this;
    }

    public function getReferenceprice(): ?string
    {
        return $this->referenceprice;
    }

    public function setReferenceprice(string $referenceprice): self
    {
        $this->referenceprice = $referenceprice;

        return $this;
    }

    public function getSpecialprice(): ?string
    {
        return $this->specialprice;
    }

    public function setSpecialprice(?string $specialprice): self
    {
        $this->specialprice = $specialprice;

        return $this;
    }

    public function getProductiondate(): ?\DateTimeInterface
    {
        return $this->productiondate;
    }

    public function setProductiondate(?\DateTimeInterface $productiondate): self
    {
        $this->productiondate = $productiondate;

        return $this;
    }

    public function getLeftdate(): ?\DateTimeInterface
    {
        return $this->leftdate;
    }

    public function setLeftdate(?\DateTimeInterface $leftdate): self
    {
        $this->leftdate = $leftdate;

        return $this;
    }

    public function getIsvisible(): ?bool
    {
        return $this->isvisible;
    }

    public function setIsvisible(?bool $isvisible): self
    {
        $this->isvisible = $isvisible;

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

    public function getSalepoint(): ?Salepoint
    {
        return $this->salepoint;
    }

    public function setSalepoint(?Salepoint $salepoint): self
    {
        $this->salepoint = $salepoint;

        return $this;
    }

    public function getProductStatus(): ?ProductStatus
    {
        return $this->productStatus;
    }

    public function setProductStatus(?ProductStatus $productStatus): self
    {
        $this->productStatus = $productStatus;

        return $this;
    }

    public function getSubfamily(): ?Subfamily
    {
        return $this->subfamily;
    }

    public function setSubfamily(?Subfamily $subfamily): self
    {
        $this->subfamily = $subfamily;

        return $this;
    }

    /**
     * @return Collection|Images[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setArticle($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getArticle() === $this) {
                $image->setArticle(null);
            }
        }

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getDateLimit(): ?\DateTimeInterface
    {
        return $this->dateLimit;
    }

    public function setDateLimit(?\DateTimeInterface $dateLimit): self
    {
        $this->dateLimit = $dateLimit;

        return $this;
    }

}
