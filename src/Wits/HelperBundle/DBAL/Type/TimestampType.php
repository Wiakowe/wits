<?php
namespace Wits\HelperBundle\DBAL\Type;

use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class TimestampType extends DateTimeType
{
    public function getName()
    {
        return 'timestamp';
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'TIMESTAMP' .
            (isset($fieldDeclaration['is_nullable']) && $fieldDeclaration['is_nullable']
                ? ' NULL'
                : '');
    }

    public function __toString()
    {
        return 'DateTime';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
