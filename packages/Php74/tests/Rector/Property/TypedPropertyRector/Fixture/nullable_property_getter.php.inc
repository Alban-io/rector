<?php

namespace Rector\Php74\Tests\Rector\Property\TypedPropertyRector\Fixture;

final class ClassWithNullablePropertyGetter
{
    /**
     * @var int|null
     */
    private $countOrNull;

    public function countOrNull(): ?int
    {
        return $this->countOrNull;
    }
}

?>
-----
<?php

namespace Rector\Php74\Tests\Rector\Property\TypedPropertyRector\Fixture;

final class ClassWithNullablePropertyGetter
{
    /**
     * @var int|null
     */
    private ?int $countOrNull;

    public function countOrNull(): ?int
    {
        return $this->countOrNull;
    }
}

?>
