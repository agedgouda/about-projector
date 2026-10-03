<?php

use Illuminate\Database\Migrations\Migration;
use Statamic\Facades\Entry;
use Statamic\Facades\Markdown;
use Statamic\Fields\Field;
use Statamic\Fieldtypes\Bard;
use Statamic\Fieldtypes\Bard\Augmentor;

// The hero carousel slide description changed from a markdown field to Bard.
// Converts any description still stored as a markdown string; already-converted ones are skipped.
return new class extends Migration
{
    public function up()
    {
        $bard = (new Bard)->setField(new Field('description', ['type' => 'bard', 'save_html' => false]));
        $augmentor = new Augmentor($bard);

        Entry::query()->where('collection', 'pages')->get()->each(function ($entry) use ($augmentor) {
            $sections = $entry->get('sections');
            $changed = false;

            foreach ($sections ?? [] as $i => $section) {
                if (($section['type'] ?? null) !== 'demo') {
                    continue;
                }

                foreach ($section['slides'] ?? [] as $j => $slide) {
                    $description = $slide['description'] ?? null;

                    if (! is_string($description) || trim($description) === '') {
                        continue;
                    }

                    $doc = $augmentor->renderHtmlToProsemirror(Markdown::parse($description));
                    $sections[$i]['slides'][$j]['description'] = $doc['content'];
                    $changed = true;
                }
            }

            if ($changed) {
                $entry->set('sections', $sections)->save();
            }
        });
    }

    public function down()
    {
        // Not reversible: Bard content may now contain brand colors that markdown can't represent.
    }
};
