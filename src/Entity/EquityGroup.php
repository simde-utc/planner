<?php
/**
 * Created by
 * corentinhembise
 * 2019-04-08
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class EquityGroup extends Group
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="equityGroup")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setEquityGroup($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getEquityGroup() === $this) {
                $user->setEquityGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'equity_group';
    }
}