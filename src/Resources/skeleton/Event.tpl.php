<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

class <?= $class_name; ?> extends AbstractEntityEvent
{
    public static function getName(): string
    {
        return '<?= $event_name ?>';
    }
}
