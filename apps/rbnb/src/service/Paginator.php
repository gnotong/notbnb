<?php
declare(strict_types=1);

namespace App\service;

use Doctrine\ORM\EntityManagerInterface;

class Paginator
{
    private string $entityClass;
    private int $currentPage;
    private int $limit = 10;
    private EntityManagerInterface $manager;
    private string $routeName;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getData(): array
    {
        $offset = $this->currentPage * $this->limit - $this->limit;

        $repo = $this->manager->getRepository($this->entityClass);

        return $repo->findBy([], [], $this->limit, $offset);
    }

    public function getPages(): int
    {
        $repo = $this->manager->getRepository($this->entityClass);

        $total = count($repo->findAll());

        return (int)ceil($total / $this->limit);
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function setCurrentPage(int $currentPage): self
    {
        $this->currentPage = $currentPage;
        return $this;
    }
    
    public function getLimit(): int
    {
        return $this->limit;
    }
    
    public function setLimit(int $limit): self 
    {
        $this->limit = $limit;
        return $this;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function setEntityClass(string $entityClass): self
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function setRouteName(string $routeName): self
    {
        $this->routeName = $routeName;
        return $this;
    }
}