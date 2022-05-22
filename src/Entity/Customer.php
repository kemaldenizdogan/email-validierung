<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=EmailVerification::class, mappedBy="customer")
     */
    private $emailVerifications;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    public function __construct()
    {
        $this->emailVerifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    /**
     * @return Collection<int, EmailVerification>
     */
    public function getEmailVerifications(): Collection
    {
        return $this->emailVerifications;
    }

    public function addEmailVerification(EmailVerification $emailVerification): self
    {
        if (!$this->emailVerifications->contains($emailVerification)) {
            $this->emailVerifications[] = $emailVerification;
            $emailVerification->setCustomer($this);
        }

        return $this;
    }

    public function removeEmailVerification(EmailVerification $emailVerification): self
    {
        if ($this->emailVerifications->removeElement($emailVerification)) {
            // set the owning side to null (unless already changed)
            if ($emailVerification->getCustomer() === $this) {
                $emailVerification->setCustomer(null);
            }
        }

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }
}
