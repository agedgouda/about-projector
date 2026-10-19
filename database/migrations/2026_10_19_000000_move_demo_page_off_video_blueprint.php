<?php

use Illuminate\Database\Migrations\Migration;
use Statamic\Facades\Entry;

// /demo now redirects to the app (routes/web.php) and the video_page blueprint is gone.
// Moves any entry still on it to the plain pages blueprint and drops the video fields.
return new class extends Migration
{
    public function up()
    {
        Entry::query()->where('collection', 'pages')->where('blueprint', 'video_page')->get()->each(function ($entry) {
            $entry->blueprint('pages')->template(null);

            foreach (['template', 'headline', 'subhead', 'cta_text', 'cta_url', 'more_heading', 'videos', 'demo_url'] as $field) {
                $entry->remove($field);
            }

            $entry->save();
        });
    }

    public function down()
    {
        // Not reversible: the video page blueprint and template were removed.
    }
};
