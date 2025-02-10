<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Loader;

use Mockery\Generator\MockDefinition;

/**
 * @see \Mockery\Tests\Unit\Mockery\LoaderTest
 */
final class EvalLoader implements Loader
{
    public function load(MockDefinition $definition): void
    {
        if (class_exists($definition->getClassName(), false)) {
            return;
        }

        eval('?>' . $definition->getCode());
    }
}
