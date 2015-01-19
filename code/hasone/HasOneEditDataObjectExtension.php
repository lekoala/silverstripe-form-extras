<?php

class HasOneEditDataObjectExtension extends DataExtension
{
    const separator = '-_1_-';

    public function onBeforeWrite()
    {
        $changed = $this->owner->getChangedFields();
        $toWrite = array();
        foreach ($changed as $name => $value) {
            if (!strpos($name, self::separator)) {
                // Also skip $name that starts with a separator
                continue;
            }
            $value = (string) $value['after'];
            list($hasone, $key) = explode(self::separator, $name, 2);
            if ($this->owner->has_one($hasone) ||
                $this->owner->belongs_to($hasone)) {
                $rel = $this->owner->getComponent($hasone);

                // Get original:
                $original = (string) $rel->__get($key);
                if ($original !== $value) {
                    $rel->setCastedField($key, $value);
                    $toWrite[$hasone] = $rel;
                }
            }
        }
        foreach ($toWrite as $rel => $obj) {
            $obj->write();
            $key = $rel.'ID';
            if (!$this->owner->$key) {
                $this->owner->$key = $obj->ID;
            }
        }
    }
}