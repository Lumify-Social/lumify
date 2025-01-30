<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "posts")]
class Repost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Users $username;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    private ?Repost $originalPost = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getUsername(): Users
    {
        return $this->username;
    }

    public function setUsername(Users $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getOriginalPost(): ?Repost
    {
        return $this->originalPost;
    }

    public function setOriginalPost(?Repost $originalPost): self
    {
        $this->originalPost = $originalPost;
        return $this;
    }
}
