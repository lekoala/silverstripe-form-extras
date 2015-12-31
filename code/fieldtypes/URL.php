<?php
/**
 * Stores URLs, in {@link Text} rather than {@link Varchar} columns,
 * due to the varchar length restrictions on various databases.
 */
class URL extends Text
{

    /**
     * (non-PHPdoc)
     * @see DBField::scaffoldFormField()
     */
    public function scaffoldFormField($title = null, $params = null)
    {
        return new URLField($this->name, $title);
    }
}
