<?php

/**
 * Make files downloadable
 *
 * @author LeKoala <thomas@lekoala.be>
 */
class BetterUploadField extends UploadField
{
    public function canDownload()
    {
        return Permission::check('CMS_ACCESS');
    }
    public function getTemplate()
    {
        return 'forms/BetterUploadField';
    }
}
