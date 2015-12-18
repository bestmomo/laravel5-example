<?php

/*
 * This file is part of PhpSpec, A php toolset to drive emergent
 * design by specification.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpSpec\CodeAnalysis;

final class StaticRejectingNamespaceResolver implements NamespaceResolver
{
    /**
     * @var NamespaceResolver
     */
    private $namespaceResolver;

    public function __construct(NamespaceResolver $namespaceResolver)
    {
        $this->namespaceResolver = $namespaceResolver;
    }

    public function analyse($code)
    {
        $this->namespaceResolver->analyse($code);
    }

    public function resolve($typeAlias)
    {
        $this->guardScalarTypeHints($typeAlias);

        return $this->namespaceResolver->resolve($typeAlias);
    }

    /**
     * @param $typeAlias
     * @throws \Exception
     */
    private function guardScalarTypeHints($typeAlias)
    {
        if (in_array($typeAlias, array('int', 'float', 'string', 'bool'))) {
            throw new DisallowedScalarTypehintException("Scalar type $typeAlias cannot be resolved within a namespace");
        }
    }
}
