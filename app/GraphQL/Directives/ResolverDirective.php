<?php

namespace App\GraphQL\Directives;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Exceptions\DefinitionException;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldResolver;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Support\Utils;
use ReflectionFunction;

class ResolverDirective extends BaseDirective implements FieldResolver
{

    /**
     * @param FieldValue $fieldValue
     *
     * @return FieldValue
     * @throws DefinitionException
     */
    public function resolveField(FieldValue $fieldValue): callable
    {
        [$className, $methodName] = $this->getMethodArgumentParts('name');
        $namespacedClassName = $this->namespaceClassName(
            $className,
            $fieldValue->parentNamespaces()
        );

        $resolver = Utils::constructResolver($namespacedClassName, $methodName);
        return static function ($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo) use ($resolver) {
                $newArgs = [];
                foreach ((new ReflectionFunction($resolver))->getParameters() as $param) {
                    if (array_key_exists($param->name, $args)) {
                        $newArgs[] = $args[$param->name];
                        continue;
                    }
                    $newArgs[] = match ($param->name) {
                        'root' => $root,
                        'context' => $context,
                        'resolveInfo' => $resolveInfo,
                        default => null,
                    };
                }
                return $resolver(...$newArgs);
            };

    }

    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'SDL'
directive @resolver(
  name: String!
) on FIELD_DEFINITION
SDL;
    }

    public function name(): string
    {
        return /** @lang GraphQL */ <<<'SDL'
directive @resolver(
  name: String!
) on FIELD_DEFINITION
SDL;
    }
}
