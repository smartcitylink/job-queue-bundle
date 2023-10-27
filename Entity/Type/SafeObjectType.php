<?php

namespace JMS\JobQueueBundle\Entity\Type;

use Doctrine\DBAL\Types\JsonType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class SafeObjectType extends JsonType
{
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    public function getSQLDeclaration(array $fieldDeclaration, \Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $platform->getBlobTypeDeclarationSQL($fieldDeclaration);
    }

    public function getName()
    {
        return 'jms_job_safe_object';
    }
}