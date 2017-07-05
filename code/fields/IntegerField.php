<?php

/**
 * IntegerField
 *
 * @author Kalyptus SPRL <thomas@kalyptus.be>
 */
class IntegerField extends TextField
{

    public function extraClass()
    {
        return 'text numeric ' . parent::extraClass();
    }

    public function getAttributes()
    {
        $attributes = [
            'type' => 'numeric',
            'step' => '1',
            'oninput' => "this.value=this.value.replace(/[^0-9]/g,'');",
            'onfocus' => "if(parseInt(this.value)==0){this.value=''}",
            'onblur' => "if(this.value==''){this.value='0'}",
        ];
        return array_merge(
            parent::getAttributes(), $attributes
        );
    }
}
