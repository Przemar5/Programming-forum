<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class CurrentUserPassword extends Constraint
{
    public string $message;

    public function __construct(
        ?string $message = 'The value "{{ value }}" is not valid.', 
        $options = null, 
        ?array $groups = null, 
        $payload = null
    ) {
        parent::__construct($options, $groups, $payload);
        
        $this->message = $message;
    }
}
