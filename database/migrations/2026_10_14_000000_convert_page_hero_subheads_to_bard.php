<?php

use Illuminate\Database\Migrations\Migration;
use Statamic\Facades\Entry;
use Statamic\Fields\Field;
use Statamic\Fieldtypes\Bard;
use Statamic\Fieldtypes\Bard\Augmentor;

// The page hero subhead changed from a plain textarea to Bard.
// Converts any subhead still stored as a string; already-converted ones are skipped.
return new class extends Migration
{
    public function up()
    {
        $bard = (new Bard)->setField(new Field('subhead', ['type' => 'bard', 'save_html' => false]));
        $augmentor = new Augmentor($bard);

        Entry::query()->where('collection', 'pages')->get()->each(function ($entry) use ($augmentor) {
            $sections = $entry->get('sections');
            $changed = false;

            foreach ($sections ?? [] as $i => $section) {
                $subhead = $section['subhead'] ?? null;

                if (($section['type'] ?? null) !== 'page_hero' || ! is_string($subhead) || trim($subhead) === '') {
                    continue;
                }

                $html = '<p>'.nl2br(e(trim($subhead)), false).'</p>';
                $sections[$i]['subhead'] = $augmentor->renderHtmlToProsemirror($html)['content'];
                $changed = true;
            }

            if ($changed) {
                $entry->set('sections', $sections)->save();
            }
        });
    }

    public function down()
    {
        // Not reversible: Bard content may now contain brand colors that plain text can't represent.
    }
};
