<?php

/**
 * Add tooltips to a form
 *
 * @author Koala
 */
class TooltipFieldExtension extends Extension
{
    private static $icon = 'uk-icon-question-circle';

    protected $tooltip;

    public function getTooltip()
    {
        return $this->tooltip;
    }

    public function setTooltip($tooltip)
    {
        $this->tooltip = $tooltip;

        $t = $this->owner->Title();

        if(Controller::has_curr() && Controller::curr() instanceof LeftAndMain) {
            $t .= ' <span title="'.$tooltip.'" class="ui-icon ui-icon-info" style="display:inline-block;"></span>';
        }
        else {
            $t .= ' <i class="'.Config::inst()->get(__CLASS__,'icon').' tooltip" title="'.$tooltip.'"></i>';
        }
        
        $this->owner->setTitle($t);

        return $this->owner;
    }
}