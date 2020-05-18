<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordReset
{
    private ?string $old = null;

    /**
     * @Assert\Length(
     *     min="8",
     *     minMessage="New password must have at least 8 characters"
     * )
     */
    private ?string $new = null;

    /**
     * @Assert\EqualTo(
     *     propertyPath="new",
     *     message="Those passwords does not match."
     * )
     */
    private ?string $confirm = null;

    public function getOld(): ?string
    {
        return $this->old;
    }

    public function setOld(string $old): self
    {
        $this->old = $old;

        return $this;
    }

    public function getNew(): ?string
    {
        return $this->new;
    }

    public function setNew(string $new): self
    {
        $this->new = $new;

        return $this;
    }

    public function getConfirm(): ?string
    {
        return $this->confirm;
    }

    public function setConfirm(string $confirm): self
    {
        $this->confirm = $confirm;

        return $this;
    }
}
