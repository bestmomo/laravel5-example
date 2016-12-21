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

namespace PhpSpec\Exception\Wrapper;


class InvalidCollaboratorTypeException extends CollaboratorException
{

    public function __construct(\ReflectionParameter $parameter, \ReflectionFunctionAbstract $function)
    {
        $message = sprintf(
            'Collaborator must be an object: argument %s defined in %s. ' .
            'You can create non-object values manually.',
            $parameter->getPosition(),
            $this->fetchFunctionIdentifier($function)
        );
        $this->setCause($function);

        parent::__construct($message);
    }

    private function fetchFunctionIdentifier(\ReflectionFunctionAbstract $function)
    {
        $functionIdentifier = $function->getName();
        if ($function instanceof \ReflectionMethod) {
            $functionIdentifier = sprintf('%s::%s', $function->getDeclaringClass()->getName(), $function->getName());
        }

        return $functionIdentifier;
    }


}
