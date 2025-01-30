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
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]  // Spécifie "user_id" au lieu de "username_id"
    private Users $username;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'retweets')]
    #[ORM\JoinColumn(name: 'original_post_id', referencedColumnName: 'id', nullable: true)]
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
