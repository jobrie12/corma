<?php


namespace Corma\DataObject\Factory;


use Corma\DataObject\Hydrator\ObjectHydratorInterface;
use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\FetchMode;
use Psr\Container\ContainerInterface;

/**
 * Factory that delegates object construction to a PSR-11 compatible dependency injection container.
 * When get() is called on the container with the full class name it must return a new instance of the requested class.
 * If the container does not have a the requested class it will fall back to instantiation with reflection
 *
 * Passing dependencies to any of the methods will bypass the container and directly instantiate the class via reflection.
 */
class PsrContainerObjectFactory extends BaseObjectFactory implements ObjectFactoryInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, ObjectHydratorInterface $hydrator)
    {
        parent::__construct($hydrator);
        $this->container = $container;
    }

    public function create(string $class, array $data = [], array $dependencies = []): object
    {
        if (!$this->container->has($class)) {
            return parent::create($class, $data, $dependencies);
        }

        $instance = $this->container->get($class);
        if (!empty($data)) {
            $this->hydrator->hydrate($instance, $data);
        }
        return $instance;
    }

    public function fetchAll(string $class, ResultStatement $statement, array $dependencies = []): array
    {
        $results = [];
        while ($data = $statement->fetch(FetchMode::ASSOCIATIVE)) {
            $object = $this->create($class, $data, $dependencies);
            $results[] = $object;
        }

        return $results;
    }

    public function fetchOne(string $class, ResultStatement $statement, array $dependencies = []): ?object
    {
        $data = $statement->fetch(FetchMode::ASSOCIATIVE);
        if (!empty($data)) {
            return $this->create($class, $data, $dependencies);
        }
        return null;
    }
}