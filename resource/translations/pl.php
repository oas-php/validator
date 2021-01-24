<?php declare(strict_types=1);

use OAS\Validator\Constraints\AdditionalItems;
use OAS\Validator\Constraints\AdditionalProperties;
use OAS\Validator\Constraints\AllOf;
use OAS\Validator\Constraints\AnyOf;
use OAS\Validator\Constraints\Constant;
use OAS\Validator\Constraints\Contains;
use OAS\Validator\Constraints\DependentRequired;
use OAS\Validator\Constraints\Enum;
use OAS\Validator\Constraints\ExclusiveMaximum;
use OAS\Validator\Constraints\ExclusiveMinimum;
use OAS\Validator\Constraints\Format;
use OAS\Validator\Constraints\MaxContains;
use OAS\Validator\Constraints\Maximum;
use OAS\Validator\Constraints\MaxItems;
use OAS\Validator\Constraints\MaxLength;
use OAS\Validator\Constraints\MaxProperties;
use OAS\Validator\Constraints\MinContains;
use OAS\Validator\Constraints\Minimum;
use OAS\Validator\Constraints\MinItems;
use OAS\Validator\Constraints\MinLength;
use OAS\Validator\Constraints\MinProperties;
use OAS\Validator\Constraints\MultipleOf;
use OAS\Validator\Constraints\Not;
use OAS\Validator\Constraints\OneOf;
use OAS\Validator\Constraints\Pattern;
use OAS\Validator\Constraints\Required;
use OAS\Validator\Constraints\Schema;
use OAS\Validator\Constraints\Type;
use OAS\Validator\Constraints\UniqueItems;
use Symfony\Component\Validator\Constraints\Unique;

return [
    AdditionalItems::UNEXPECTED_ITEM_MESSAGE =>
        'Indeks {{ index }} nie jest zdefiniowany a dodatkowe elementy nie są dozwolone',

    AdditionalProperties::UNEXPECTED_PROPERTY_MESSAGE =>
        'Atrybut "{{ property }}" nie jest zdefiniowany a dodatkowe atrubuty nie są dozwolone',

    AllOf::NOT_ALL_SCHEMAS_MATCHED_MESSAGE =>
        'Instancja nie pasuje do wszystkich zdefiniowanych schematów',

    AnyOf::NONE_SCHEMAS_MATCHED_MESSAGE =>
        'Instancja nie pasuje do żadnego z schematów',

    Constant::VALUE_MISMATCH_MESSAGE =>
        'Wartość jest różna od zdefiniowanej stałej',

    Contains::NO_ITEM_MATCHES_MESSAGE =>
        'Żaden z elementów nie pasuje do schematu',

    DependentRequired::DEPENDENT_PROPERTIES_MISSING_MESSAGE =>
        'Atrybuty {{ missing_properties }} wymagane przez "{{ property }}" atrubyt muszą być zdefiniowane',

    Format\DateTime::INVALID_FORMAT_MESSAGE =>
        'Wartość {{ value }} nie jest poprawną datą',

    Format\Email::INVALID_FORMAT_MESSAGE =>
        'Wartość {{ value }} nie jest poprawnym e-mailem',

    Format\Hostname::INVALID_HOSTNAME_MESSAGE =>
        'Wartość {{ value }} nie jest poprawną nazwą dla hosta',

    Format\Ip::INVALID_IP_MESSAGE =>
        'Wartość {{ value }} nie jest poprawnym adresem IP',

    Enum::INVALID_CHOICE_MESSAGE =>
        'Możliwe wartości to {{ choices }}, wartość {{ value }} jest niepoprawna',

    ExclusiveMaximum::TOO_HIGH_MESSAGE =>
        'Wartość {{ value }} jest równa lub większa niż {{ compared_value }}',

    Format::FORMAT_MISMATCH_MESSAGE =>
        'Wartość "{{ value }}" nie pasuje do formatu "{{ format }}"',

    ExclusiveMinimum::TOO_LOW_MESSAGE =>
        'Wartość {{ value }} jest równa lub mniejsza niż {{ compared_value }}',

    MaxContains::TOO_MANY_MESSAGE =>
        'Ta kolekcja powinna posiadać co najwyżej jeden element pasujący do schematu'
        . '|Ta kolekcja powinna posiadać co najwyżej {{ max }} elementy pasujące do schematu'
        . '|Ta kolekcja powinna posiadać co najwyżej {{ max }} elementów pasujących do schematu',

    Maximum::TOO_HIGH_MESSAGE =>
        'Wartość {{ value }} przekracza maksymalną {{ compared_value }}',

    MaxItems::TOO_MANY_MESSAGE =>
        'Ta kolekcja powinna posiadć conajmniej jeden element'
        . 'Ta kolekcja powinna posiadć conajmniej {{ limit }} elementy'
        . 'Ta kolekcja powinna posiadć conajmniej {{ limit }} elementów',

    MaxLength::TOO_LONG_MESSAGE =>
        'Wartość "{{ value }}" powinna mieć co najwyżej jeden znak'
        . '|Wartość "{{ value }}" powinna mieć co najwyżej  {{ limit }} znaki'
        . '|Wartość "{{ value }}" powinna mieć co najwyżej  {{ limit }} znaków',

    MaxProperties::TOO_MANY_MESSAGE =>
        'Ten obiekt powinien mieć co najwyżej jeden atrybut'
        . '|Ten obiekt powinien co najwyżej {{ limit }} atrybuty'
        . '|Ten obiekt powinien co najwyżej {{ limit }} atrybutów',

    Minimum::TOO_LOW_MESSAGE =>
        'Wartość {{ value }} jest mniejsza niż zdefiniowane minimum {{ compared_value }}',

    MinContains::TOO_FEW_ERROR =>
        'Ta kolekcja powinna posiadać co najmniej jeden element pasujący do schematu'
        . '|Ta kolekcja powinna posiadać co najmniej {{ min }} elementy pasujące do schematu'
        . '|Ta kolekcja powinna posiadać co najmniej {{ min }} elementów pasujących do schematu',

    MinItems::TOO_FEW_MESSAGE =>
        'Ta kolekcja powinna posiadać co najmniej jeden element'
        . 'Ta kolekcja powinna posiadać co najmniej {{ limit }} elementy'
        . 'Ta kolekcja powinna posiadać co najmniej {{ limit }} elementów',

    MinLength::TOO_SHORT_MESSAGE =>
        'Wartość "{{ value }}" powinna mieć co najwyżej jeden znak'
        . '|Wartość "{{ value }}" powinna mieć co najwyżej {{ limit }} znaki'
        . '|Wartość "{{ value }}" powinna mieć co najwyżej {{ limit }} znaków',

    MinProperties::TOO_FEW_MESSAGE =>
        'Ten obiekt powinien mieć co najmniej jeden atrybut'
        . '|Ten obiekt powinien posiadać co najmniej {{ limit }} atrybuty'
        . '|Ten obiekt powinien posiadać co najmniej {{ limit }} atrybutów',

    MultipleOf::NOT_MULTIPLE_OF_MESSAGE =>
        'Wartość {{ value }} nie jest wielokrotnością {{ multiple_of }}',

    Not::INSTANCE_MATCHES_SCHEMA_MESSAGE =>
        'Instancja pasuje do schematu',

    OneOf::NONE_SCHEMAS_MATCHED_MESSAGE =>
        'Instancja nie pasuje do żadnego z schematów',

    OneOf::MANY_SCHEMAS_MATCHED_MESSAGE =>
        'Instancja pasuje do więcej niż jednego schematu',

    Pattern::PATTERN_MISMATCH_MESSAGE =>
        'Wartość "{{ value }}" nie pasuje do wzorca',

    Required::REQUIRED_PROPERTIES_MISSING_MESSAGE =>
        'Wymagany atrybut {{ required_properties }} musi być zdefiniowany'
        . '|Wymagane atrybuty {{ required_properties }} muszą być zdefiniowane'
        . '|Wymagane atrybuty {{ required_properties }} muszą być zdefiniowane',

    Schema::ALWAYS_INVALID_MESSAGE =>
        'Instancja nigdy nie pasuje do schematu',

    UniqueItems::NOT_UNIQUE_MESSAGE =>
        'Elementy nie są unikalne',

    Format\Uuid::INVALID_UUID_MESSAGE =>
        'Wartość "{{ value }}" nie jest nie jest poprawnym identyfikatorem w formacie UUID',

    Type::INVALID_TYPE_MESSAGE =>
        'Nieprawidłowy typ: oczekiwano "{{ expected }}" zamiast "{{ actual }}"'
];
