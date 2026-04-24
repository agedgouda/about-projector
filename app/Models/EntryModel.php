<?php

namespace App\Models;

use Statamic\Eloquent\Entries\EntryModel as BaseEntryModel;

class EntryModel extends BaseEntryModel
{
    public $incrementing = false;

    protected $keyType = 'string';
}
