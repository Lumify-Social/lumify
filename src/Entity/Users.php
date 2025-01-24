<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[Broadcast]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte avec cette adresse email')]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(name: 'password_hash', length: 255, nullable: false)]
    private ?string $password = null; // Correspond à la colonne password_hash

    #[ORM\Column(type: Types::JSON, options: ['default' => '[]'])]
    private array $roles = [];

    public const ROLE_USER = 'ROLE_USER';

    public function __construct()
    {
        $this->roles = [self::ROLE_USER];
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTime();
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password; // Toujours faire référence à password
    }

    public function setPassword(string $password): self
    {
        $this->password = $password; // Symfony comprend que c'est lié à password_hash

        return $this;
    }

    public function getRoles(): array
    {
        // Garantir que le rôle utilisateur de base est toujours présent
        $roles = $this->roles;
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getSalt(): ?string
    {
        // Pas nécessaire car bcrypt ou argon2 sont utilisés
        return null;
    }

    public function eraseCredentials(): void
    {
        // Si des données sensibles sont stockées temporairement, les effacer ici
    }
}
