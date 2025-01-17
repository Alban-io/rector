<?php

declare(strict_types=1);

namespace Rector\Architecture\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Php\TypeAnalyzer;
use Rector\PhpParser\Node\Manipulator\ClassManipulator;
use Rector\PhpParser\Node\Manipulator\ClassMethodManipulator;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * @see \Rector\Architecture\Tests\Rector\Class_\ConstructorInjectionToActionInjectionRector\ConstructorInjectionToActionInjectionRectorTest
 */
final class ConstructorInjectionToActionInjectionRector extends AbstractRector
{
    /**
     * @var ClassManipulator
     */
    private $classManipulator;

    /**
     * @var TypeAnalyzer
     */
    private $typeAnalyzer;

    /**
     * @var Param[]
     */
    private $propertyFetchToParams = [];

    /**
     * @var Param[]
     */
    private $propertyFetchToParamsToRemoveFromConstructor = [];

    /**
     * @var ClassMethodManipulator
     */
    private $classMethodManipulator;

    public function __construct(
        ClassManipulator $classManipulator,
        TypeAnalyzer $typeAnalyzer,
        ClassMethodManipulator $classMethodManipulator
    ) {
        $this->classManipulator = $classManipulator;
        $this->typeAnalyzer = $typeAnalyzer;
        $this->classMethodManipulator = $classMethodManipulator;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('', [
            new CodeSample(
                <<<'PHP'
final class SomeController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function default()
    {
        $products = $this->productRepository->fetchAll();
    }
}
PHP
                ,
                <<<'PHP'
final class SomeController
{
    public function default(ProductRepository $productRepository)
    {
        $products = $productRepository->fetchAll();
    }
}
PHP
            ),
        ]);
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        $this->reset();

        // only in controllers
        if (! $this->isName($node, '*Controller')) {
            return null;
        }

        if ($node->isAbstract()) {
            return null;
        }

        $constructMethod = $node->getMethod('__construct');
        // no constructor, nothing to do
        if ($constructMethod === null) {
            return null;
        }

        // traverse constructor dependencies and names of their properties
        $this->collectPropertyFetchToParams($constructMethod);

        // replace them in property fetches with particular class methods and use variable instead
        foreach ($node->getMethods() as $classMethod) {
            if ($this->isName($classMethod, '__construct')) {
                continue;
            }

            if (! $classMethod->isPublic()) {
                continue;
            }

            foreach ($this->propertyFetchToParams as $propertyFetchName => $param) {
                $this->changePropertyUsageToParameter($classMethod, $propertyFetchName, $param);
            }
        }

        // collect all property fetches that are relevant to original constructor properties
        $this->traverseNodesWithCallable($node->stmts, function (Node $node) {
            if (! $node instanceof PropertyFetch) {
                return null;
            }

            // only scan non-action methods
            /** @var ClassMethod $methodNode */
            $methodNode = $node->getAttribute(AttributeKey::METHOD_NODE);
            if ($methodNode->isPublic()) {
                return null;
            }

            $usedPropertyFetchName = $this->getName($node);
            if (isset($this->propertyFetchToParams[$usedPropertyFetchName])) {
                unset($this->propertyFetchToParamsToRemoveFromConstructor[$usedPropertyFetchName]);
            }
        });

        $this->removeUnusedPropertiesAndConstructorParams($node, $constructMethod);

        return $node;
    }

    private function reset(): void
    {
        $this->propertyFetchToParams = [];
        $this->propertyFetchToParamsToRemoveFromConstructor = [];
    }

    private function collectPropertyFetchToParams(ClassMethod $classMethod): void
    {
        foreach ((array) $classMethod->stmts as $constructorStmt) {
            $propertyToVariable = $this->resolveAssignPropertyToVariableOrNull($constructorStmt);
            if ($propertyToVariable === null) {
                continue;
            }

            [$propertyFetchName, $variableName] = $propertyToVariable;

            $param = $this->classManipulator->findMethodParamByName($classMethod, $variableName);
            if ($param === null) {
                continue;
            }

            // random type, we cannot autowire in action
            if ($param->type === null) {
                continue;
            }

            $paramType = $this->getName($param->type);
            if ($paramType === null) {
                continue;
            }

            if ($this->typeAnalyzer->isPhpReservedType($paramType)) {
                continue;
            }

            // it's a match
            $this->propertyFetchToParams[$propertyFetchName] = $param;
        }

        $this->propertyFetchToParamsToRemoveFromConstructor = $this->propertyFetchToParams;
    }

    private function changePropertyUsageToParameter(ClassMethod $classMethod, string $propertyName, Param $param): void
    {
        $currentlyAddedLocalVariables = [];

        $this->traverseNodesWithCallable((array) $classMethod->stmts, function (Node $node) use (
            $propertyName,
            $param,
            &$currentlyAddedLocalVariables
        ): ?Variable {
            if (! $node instanceof PropertyFetch) {
                return null;
            }

            if (! $this->isName($node->var, 'this')) {
                return null;
            }

            if ($this->isName($node, $propertyName)) {
                $currentlyAddedLocalVariables[] = $param;

                /** @var string $paramName */
                $paramName = $this->getName($param);
                return new Variable($paramName);
            }

            return null;
        });

        foreach ($currentlyAddedLocalVariables as $param) {
            // is param already present?
            foreach ($classMethod->params as $existingParam) {
                if ($this->areNamesEqual($existingParam, $param)) {
                    continue 2;
                }
            }

            $classMethod->params[] = $param;
        }
    }

    private function removeUnusedPropertiesAndConstructorParams(Class_ $class, ClassMethod $classMethod): void
    {
        $this->removeAssignsFromConstructor($classMethod);
        foreach ($this->propertyFetchToParamsToRemoveFromConstructor as $propertyFetchName => $param) {
            $this->changePropertyUsageToParameter($classMethod, $propertyFetchName, $param);
        }
        $this->classMethodManipulator->removeUnusedParameters($classMethod);
        $this->removeUnusedProperties($class);
        $this->removeConstructIfEmpty($class, $classMethod);
    }

    /**
     * @param Node $node
     * @return string[]|null
     */
    private function resolveAssignPropertyToVariableOrNull(Node $node): ?array
    {
        if ($node instanceof Expression) {
            $node = $node->expr;
        }

        if (! $node instanceof Assign) {
            return null;
        }

        if (! $node->var instanceof PropertyFetch) {
            return null;
        }

        if (! $node->expr instanceof Variable) {
            return null;
        }

        $propertyFetchName = $this->getName($node->var);
        $variableName = $this->getName($node->expr);
        if ($propertyFetchName === null) {
            return null;
        }

        if ($variableName === null) {
            return null;
        }

        return [$propertyFetchName, $variableName];
    }

    private function removeAssignsFromConstructor(ClassMethod $classMethod): void
    {
        foreach ((array) $classMethod->stmts as $key => $constructorStmt) {
            $propertyFetchToVariable = $this->resolveAssignPropertyToVariableOrNull($constructorStmt);
            if ($propertyFetchToVariable === null) {
                continue;
            }

            [$propertyFetchName, ] = $propertyFetchToVariable;
            if (! isset($this->propertyFetchToParamsToRemoveFromConstructor[$propertyFetchName])) {
                continue;
            }

            // remove the assign
            unset($classMethod->stmts[$key]);
        }
    }

    private function removeUnusedProperties(Class_ $class): void
    {
        foreach (array_keys($this->propertyFetchToParamsToRemoveFromConstructor) as $propertyFetchName) {
            /** @var string $propertyFetchName */
            $this->classManipulator->removeProperty($class, $propertyFetchName);
        }
    }

    private function removeConstructIfEmpty(Class_ $class, ClassMethod $constructClassMethod): void
    {
        if ($constructClassMethod->stmts !== []) {
            return;
        }

        $this->removeNodeFromStatements($class, $constructClassMethod);
    }
}
