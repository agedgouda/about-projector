<?php

use Illuminate\Database\Migrations\Migration;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Fields\Field;
use Statamic\Fieldtypes\Bard;
use Statamic\Fieldtypes\Bard\Augmentor;

// Creates the /meeting-automation page with its starting copy. Skipped if it already exists, so CP edits are never overwritten.
return new class extends Migration
{
    public function up()
    {
        if (Entry::find('meeting-automation')) {
            return;
        }

        $bard = (new Bard)->setField(new Field('body', ['type' => 'bard', 'save_html' => false]));
        $augmentor = new Augmentor($bard);
        $toBard = fn (string $html) => $augmentor->renderHtmlToProsemirror($html)['content'];

        Entry::make()
            ->id('meeting-automation')
            ->collection('pages')
            ->blueprint('page')
            ->slug('meeting-automation')
            ->data([
                'title' => 'Meeting Automation',
                'template' => 'page',
                'meta_description' => 'Capture audio anywhere, convert meeting transcripts into clean notes, and turn static spreadsheets into live project tracking.',
                'sections' => [
                    [
                        'type' => 'page_hero',
                        'enabled' => true,
                        'headline' => 'Turn Raw Meetings & Files Into Actionable Work',
                        'subhead' => 'Capture audio anywhere, convert meeting transcripts into clean notes, and turn static spreadsheets into live project tracking.',
                        'cta_text' => 'Try Projector Free →',
                        'cta_url' => 'https://projecthq.app/register?ref=meeting-automation',
                        'image_note' => 'Hero visual showing raw audio wave converting into structured task cards',
                    ],
                    [
                        'type' => 'feature_block',
                        'enabled' => true,
                        'headline' => 'Capture Meetings Wherever They Happen',
                        'body' => $toBard(
                            '<p>Never rely on handwritten notes or memory again.</p><ul>'.
                            '<li><p><strong>Browser &amp; Mobile Capture:</strong> Record live browser meetings or record on your phone on the go.</p></li>'.
                            '<li><p><strong>Automated Note Conversion:</strong> Projector processes raw meeting transcripts into structured meeting notes, task lists, and calendars.</p></li>'.
                            '<li><p><strong>Multi-Project Routing:</strong> Upload notes covering several projects and Projector routes tasks to the correct boards automatically.</p></li>'.
                            '<li><p><strong>Human-in-the-Loop Review:</strong> Review and tweak proposed task mappings before finalizing them onto your boards.</p></li>'.
                            '</ul>'
                        ),
                        'image_note' => 'Interface screenshot showing Transcript view side-by-side with generated Meeting Notes',
                        'image_left' => false,
                    ],
                    [
                        'type' => 'feature_block',
                        'enabled' => true,
                        'headline' => 'Smart Data Imports from Spreadsheets & Docs',
                        'body' => $toBard(
                            '<p>Migrate spreadsheets and documents without manual reformatting.</p><ul>'.
                            '<li><p><strong>Smart Column Mapping:</strong> Auto-detect column headers from CSV, XLSX, and XLS files.</p></li>'.
                            '<li><p><strong>Document Transformations:</strong> Transform raw specifications or Word docs into structured requirement catalog items.</p></li>'.
                            '<li><p><strong>Needs Review Safety Queue:</strong> Preview new file layouts to verify accuracy before importing thousands of rows.</p></li>'.
                            '</ul>'
                        ),
                        'image_note' => 'Screenshot of the "Needs Review" Smart Import queue',
                        'image_left' => true,
                    ],
                    [
                        'type' => 'final_cta',
                        'enabled' => true,
                        'headline' => 'Stop Manually Writing Meeting Notes',
                        'cta_text' => 'Start Automating Today',
                        'cta_url' => 'https://projecthq.app/register?ref=meeting-automation',
                    ],
                ],
            ])
            ->save();

        $tree = Collection::find('pages')->structure()->in('default');
        $tree->append(Entry::find('meeting-automation'))->save();
    }

    public function down()
    {
        Entry::find('meeting-automation')?->delete();
    }
};
