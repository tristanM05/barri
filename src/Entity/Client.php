<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 * @UniqueEntity(fields = {"email"},message ="Cet email est déjà utilisé")
 */
class Client implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Length(min=5,  max=100, minMessage="L'email doit faire minimum 5 caractères",           *  maxMessage="L'email doit faire maximum 100 caractères, allowEmptyString=false")
     * @Assert\Email(message = "Veuillez renseigner un email valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\Length(min=6, minMessage="Le mot de passe doit faire minimum 6 caractères,               * allowEmptyString=false")
     */
    private $pass;

    /**
     * @Assert\EqualTo(propertyPath="pass", message="Les mots de passe ne correspondent pas")
     */
    private $passwordVerification;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=6,  max=255, minMessage="L'adresse doit faire minimum 6 caractères",           * maxMessage="L'adresse doit faire maximum 255 caractères, allowEmptyString=false")
     */
    private $adress;

    /**
     * @ORM\Column(type="string", length=15)
     * @Assert\Length(min=5,  max=15, minMessage="Le code postal doit faire minimum 5 caractères",      * maxMessage="Le code postal doit faire maximum 15 caractères, allowEmptyString=false")
     * @Assert\Positive
     */
    private $postcode;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Length(min=1,  max=100, minMessage="La ville doit faire minimum 1 caractères",           * maxMessage="Le ville doit faire maximum 100 caractères, allowEmptyString=false")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Length(min=5,  max=20, minMessage="Le numéro de téléphone doit faire minimum 5           * caractères", maxMessage="Le numéro de téléphone doit faire maximum 20 caractères,                * allowEmptyString=false")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $activation_token;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $reset_token;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Salepoint", mappedBy="client", orphanRemoval=true)
     */
    private $salepoints;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Family", mappedBy="client", orphanRemoval=true)
     */
    private $families;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Subfamily", mappedBy="client", orphanRemoval=true)
     */
    private $subfamilies;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="client", orphanRemoval=true, fetch="EAGER")
     */
    private $articles;

    /**
     * @ORM\Column(type="datetime")
     */
    private $last_action;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ispremium;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $last_name;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserConfiguration", mappedBy="client", cascade={"persist", "remove"})
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StockClient", mappedBy="client", orphanRemoval=true)
     */
    private $stockClients;

    /**
     * @var checkbox|null
     * 
     */
    private $check;



    public function __construct()
    {
        $this->salepoints = new ArrayCollection();
        $this->families = new ArrayCollection();
        $this->subfamilies = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->stockClients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public function setPass(string $pass): self
    {
        $this->pass = $pass;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): self
    {
        $this->postcode = $postcode;

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getRoles()
    {
        return [$this->roles];
    }

    public function setRoles(?string $roles): self
    {
        if($roles === null){
            $this->roles = "ROLE_USER";
        } else{
            $this->roles = $roles;
        }

        return $this;
    }

    /**
     * Get the value of activationToken
     */ 
    public function getActivationToken()
    {
        return $this->activation_token;
    }

    /**
     * Set the value of activationToken
     *
     * @return  self
     */ 
    public function setActivationToken($activation_token)
    {
        $this->activation_token = $activation_token;

        return $this;
    }

    /**
     * Get the value of passwordVerification
     */ 
    public function getPasswordVerification()
    {
        return $this->passwordVerification;
    }

    /**
     * Set the value of passwordVerification
     *
     * @return  self
     */ 
    public function setPasswordVerification($passwordVerification)
    {
        $this->passwordVerification = $passwordVerification;

        return $this;
    }
    
    // public function getRoles(){
    //     return ['ROLE_USER'];
    // }

    public function getPassword(){
        return $this->pass;
    }

    public function getSalt(){
        
    }

    public function getUsername(){
        return $this->email;
    }

    public function eraseCredentials(){
        
    }

    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }

    public function setResetToken(?string $reset_token): self
    {
        $this->reset_token = $reset_token;

        return $this;
    }

    /**
     * @return Collection|Salepoint[]
     */
    public function getSalepoints(): Collection
    {
        return $this->salepoints;
    }

    public function addSalepoint(Salepoint $salepoint): self
    {
        if (!$this->salepoints->contains($salepoint)) {
            $this->salepoints[] = $salepoint;
            $salepoint->setClient($this);
        }

        return $this;
    }

    public function removeSalepoint(Salepoint $salepoint): self
    {
        if ($this->salepoints->contains($salepoint)) {
            $this->salepoints->removeElement($salepoint);
            // set the owning side to null (unless already changed)
            if ($salepoint->getClient() === $this) {
                $salepoint->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Family[]
     */
    public function getFamilies(): Collection
    {
        return $this->families;
    }

    public function addFamily(Family $family): self
    {
        if (!$this->families->contains($family)) {
            $this->families[] = $family;
            $family->setClient($this);
        }

        return $this;
    }

    public function removeFamily(Family $family): self
    {
        if ($this->families->contains($family)) {
            $this->families->removeElement($family);
            // set the owning side to null (unless already changed)
            if ($family->getClient() === $this) {
                $family->setClient(null);
            }
        }

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
            $subfamily->setClient($this);
        }

        return $this;
    }

    public function removeSubfamily(Subfamily $subfamily): self
    {
        if ($this->subfamilies->contains($subfamily)) {
            $this->subfamilies->removeElement($subfamily);
            // set the owning side to null (unless already changed)
            if ($subfamily->getClient() === $this) {
                $subfamily->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setClient($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getClient() === $this) {
                $article->setClient(null);
            }
        }

        return $this;
    }

    

    /**
     * Get the value of last_action
     */ 
    public function getLast_action()
    {
        return $this->last_action;
    }

    /**
     * Set the value of last_action
     *
     * @return  self
     */ 
    public function setLast_action($last_action)
    {
        $this->last_action = $last_action;

        return $this;
    }

    public function getIsPremium(): ?bool
    {
        return $this->ispremium;
    }

    public function setIsPremium(?bool $ispremium): self
    {
        $this->ispremium = $ispremium;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getClient(): ?UserConfiguration
    {
        return $this->client;
    }

    public function setClient(UserConfiguration $client): self
    {
        $this->client = $client;

        // set the owning side of the relation if necessary
        if ($client->getClient() !== $this) {
            $client->setClient($this);
        }

        return $this;
    }

    /**
     * @return Collection|StockClient[]
     */
    public function getStockClients(): Collection
    {
        return $this->stockClients;
    }

    public function addStockClient(StockClient $stockClient): self
    {
        if (!$this->stockClients->contains($stockClient)) {
            $this->stockClients[] = $stockClient;
            $stockClient->setClient($this);
        }

        return $this;
    }

    public function removeStockClient(StockClient $stockClient): self
    {
        if ($this->stockClients->contains($stockClient)) {
            $this->stockClients->removeElement($stockClient);
            // set the owning side to null (unless already changed)
            if ($stockClient->getClient() === $this) {
                $stockClient->setClient(null);
            }
        }

        return $this;
    }
    
        /**
     * Get the value of check
     *
     * @return  string|null
     */ 
    public function getCheck()
    {
        return $this->check;
    }

    /**
     * Set the value of message
     *
     * @param  checkbox|null  $check
     *
     * @return  self
     */ 
    public function setCheck($check)
    {
        $this->check = $check;

        return $this;
    }


}
