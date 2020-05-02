<?php
declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

/**
 * Used to create pagination on data tables
 */
class Paginator
{
    private string                 $entityClass;
    private int                    $currentPage;
    private int                    $limit = 10;
    private EntityManagerInterface $manager;
    private ?string                $routeName;
    private Environment            $twig;
    private string                 $templatePath;

    const ENTITY_CLASS_MISSING_ERROR = 'You have to specify on which entity we have to paginate. 
            Use setEntityClass of your Paginator service';
    const PAGE_NUMBER_MISSING_ERROR  = 'You have to specify the parameter page.
            Use setCurrentPage of your paginator service';

    public function __construct (
        EntityManagerInterface $manager,
        Environment $twig,
        RequestStack $request,
        string $templatePath)
    {
        $request            = $request->getCurrentRequest();
        $this->manager      = $manager;
        $this->twig         = $twig;
        $this->routeName    = $request ? $request->get('_route') : '';
        $this->templatePath = $templatePath;
    }

    /**
     * @throws \Exception
     */
    public function render (): void
    {
        $this->twig->display($this->templatePath, [
            'pages'     => $this->getPages(),
            'page'      => $this->getCurrentPage(),
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * @throws \Exception
     * @return array<Entity>
     */
    public function getData (): array
    {
        if (empty($this->entityClass)) {
            throw new \Exception(self::ENTITY_CLASS_MISSING_ERROR);
        }

        if (empty($this->currentPage)) {
            throw new \Exception(self::PAGE_NUMBER_MISSING_ERROR);
        }

        $offset = $this->currentPage * $this->limit - $this->limit;
        $repo   = $this->manager->getRepository($this->entityClass);

        return $repo->findBy([], [], $this->limit, $offset);
    }

    /**
     * @throws \Exception
     */
    public function getPages (): int
    {
        if (empty($this->entityClass)) {
            throw new \Exception(self::ENTITY_CLASS_MISSING_ERROR);
        }
        $repo  = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        return (int)ceil($total / $this->limit);
    }

    public function getCurrentPage (): int
    {
        return $this->currentPage;
    }

    public function setCurrentPage (int $currentPage): self
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    public function getLimit (): int
    {
        return $this->limit;
    }

    public function setLimit (int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function getEntityClass (): string
    {
        return $this->entityClass;
    }

    public function setEntityClass (string $entityClass): self
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    public function getRouteName (): string
    {
        return $this->routeName;
    }

    public function setRouteName (string $routeName): self
    {
        $this->routeName = $routeName;
        return $this;
    }

    public function getTemplatePath (): string
    {
        return $this->templatePath;
    }

    public function setTemplatePath (string $templatePath): self
    {
        $this->templatePath = $templatePath;
        return $this;
    }
}