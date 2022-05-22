<?php

namespace App\Entity;

use App\Repository\EmailVerificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EmailVerificationRepository::class)
 */
class EmailVerification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="emailVerifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $notified_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $verified_at;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $verification_type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getNotifiedAt(): ?\DateTimeImmutable
    {
        return $this->notified_at;
    }

    public function setNotifiedAt(?\DateTimeImmutable $notified_at): self
    {
        $this->notified_at = $notified_at;

        return $this;
    }

    public function getVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->verified_at;
    }

    public function setVerifiedAt(?\DateTimeImmutable $verified_at): self
    {
        $this->verified_at = $verified_at;

        return $this;
    }

    public function isVerificationType(): ?bool
    {
        return $this->verification_type;
    }

    public function setVerificationType(?bool $verification_type): self
    {
        $this->verification_type = $verification_type;

        return $this;
    }
}
