<?php

declare(strict_types=1);

namespace Doctrine\ODM\MongoDB\Aggregation\Stage\Search\Compound;

use Doctrine\ODM\MongoDB\Aggregation\Stage\Search\CompoundedSearchOperatorTrait;
use Doctrine\ODM\MongoDB\Aggregation\Stage\Search\CompoundSearchOperatorInterface;
use Doctrine\ODM\MongoDB\Aggregation\Stage\Search\MoreLikeThis;
use Doctrine\ODM\MongoDB\Aggregation\Stage\Search\SupportsCompoundableOperatorsTrait;

/** @internal */
class CompoundedMoreLikeThis extends MoreLikeThis implements CompoundSearchOperatorInterface
{
    use CompoundedSearchOperatorTrait;
    use SupportsCompoundableOperatorsTrait;
}
