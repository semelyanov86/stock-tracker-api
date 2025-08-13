<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Email;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Email]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: StockQuery::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $stockQueries;

    public function __construct()
    {
        $this->stockQueries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // No sensitive data to erase
    }

    /**
     * @return Collection<int, StockQuery>
     */
    public function getStockQueries(): Collection
    {
        return $this->stockQueries;
    }

    public function addStockQuery(StockQuery $stockQuery): static
    {
        if (!$this->stockQueries->contains($stockQuery)) {
            $this->stockQueries->add($stockQuery);
            $stockQuery->setUser($this);
        }

        return $this;
    }

    public function removeStockQuery(StockQuery $stockQuery): static
    {
        if ($this->stockQueries->removeElement($stockQuery)) {
            if ($stockQuery->getUser() === $this) {
                $stockQuery->setUser(null);
            }
        }

        return $this;
    }
}
