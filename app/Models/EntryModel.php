<?php

namespace App\Models;

use Statamic\Eloquent\Entries\UuidEntryModel;

// String ids; new entries (CP create, Duplicate) get a UUID since the id column has no default.
class EntryModel extends UuidEntryModel
{
}
