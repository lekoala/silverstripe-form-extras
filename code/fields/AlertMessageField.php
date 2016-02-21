<?php

/**
 * A simple wrapper for displaying messages in a more friendly way
 *
 * @author Koala
 */
class AlertMessageField extends LiteralField
{
    protected static $count = 0;

    public function __construct($content, $type = 'info', $name = null)
    {
        self::$count++;
        if ($name === null) {
            $name = 'AlertMessageField' . self::$count;
        }
        $content = '<div class="message '.$type.'" id="'.$name.'">' . $content . '</div>';
        parent::__construct($name, $content);
    }
}
