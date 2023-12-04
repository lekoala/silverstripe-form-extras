<?php

/**
 * A simple wrapper for displaying messages in a more friendly way
 *
 * @author Koala
 */
class AlertMessageField extends LiteralField
{
    protected static $count = 0;
    protected $alertType;

    public function __construct($content, $type = 'info', $name = null)
    {
        self::$count++;
        if ($name === null) {
            $name = 'AlertMessageField' . self::$count;
        }
        $this->alertType = $type;
        parent::__construct($name, $content);
    }

    public function FieldHolder($properties = array())
    {
        if ($this->content instanceof ViewableData) {
            $context = $this->content;

            if ($properties) {
                $context = $context->customise($properties);
            }

            $content = $context->forTemplate();
        } else {
            $content = $this->content;
        }
        $type = $this->alertType;
        $name = $this->name;
        $content = '<div class="message ' . $type . ' ' . $this->extraClass() . '" id="' . $name . '">' . $content . '</div>';
        return $content;
    }
}
