<?php

use Illuminate\Database\Migrations\Migration;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Fields\Field;
use Statamic\Fieldtypes\Bard;
use Statamic\Fieldtypes\Bard\Augmentor;

// Creates the /integrations page with its starting copy. Skipped if it already exists, so CP edits are never overwritten.
return new class extends Migration
{
    public function up()
    {
        if (Entry::find('integrations')) {
            return;
        }

        $bard = (new Bard)->setField(new Field('body', ['type' => 'bard', 'save_html' => false]));
        $augmentor = new Augmentor($bard);
        $toBard = fn (string $html) => $augmentor->renderHtmlToProsemirror($html)['content'];

        Entry::make()
            ->id('integrations')
            ->collection('pages')
            ->blueprint('page')
            ->slug('integrations')
            ->data([
                'title' => 'Integrations',
                'template' => 'page',
                'meta_description' => 'Projector syncs with Slack and Dropbox so you can capture tasks, import files, and generate client reports without switching tabs.',
                'sections' => [
                    [
                        'type' => 'page_hero',
                        'enabled' => true,
                        'headline' => 'Work Where You Already Work',
                        'subhead' => 'Projector syncs directly with Slack and Dropbox so you can capture tasks, import files, and generate client reports without switching tabs.',
                        'cta_text' => 'Try Projector Free →',
                        'cta_url' => 'https://projecthq.app/register?ref=integrations',
                        'image_note' => 'Split graphic showing Slack notification card on left and Dropbox folder sync on right',
                    ],
                    [
                        'type' => 'feature_block',
                        'enabled' => true,
                        'headline' => 'Turn Slack Conversations Into Real Work',
                        'body' => $toBard(
                            '<p>Stop losing action items in fast-moving chat channels.</p><ul>'.
                            '<li><p><strong>Slash Commands:</strong> Type <code>/task</code>, <code>/events</code>, or <code>/report</code> directly inside any channel to create work or export data instantly.</p></li>'.
                            '<li><p><strong>Message Shortcuts:</strong> Hover over any Slack message to turn an idea or directive into an assigned Kanban task.</p></li>'.
                            '<li><p><strong>Daily Digest DMs:</strong> Receive a personalized summary at 8:00 AM local time with your open and upcoming deliverables.</p></li>'.
                            '<li><p><strong>Multi-Agency Support:</strong> Connect one agency Slack workspace across multiple client organizations effortlessly.</p></li>'.
                            '</ul>'
                        ),
                        'image_note' => 'GIF showing /task slash command populating a Projector task board',
                        'image_left' => false,
                    ],
                    [
                        'type' => 'feature_block',
                        'enabled' => true,
                        'headline' => 'Drag, Drop, and Auto-File via Dropbox',
                        'body' => $toBard(
                            '<p>Keep your project documents organized without manual uploads.</p><ul>'.
                            '<li><p><strong>Bound Folders:</strong> Link specific Dropbox subfolders directly to Projector projects.</p></li>'.
                            '<li><p><strong>Auto-Classification:</strong> Drop a file into a <code>/meeting-notes/</code> subfolder to automatically classify and format it.</p></li>'.
                            '<li><p><strong>Spreadsheet Sync:</strong> Upload spreadsheets to trigger Projector\'s Smart Import pipeline automatically.</p></li>'.
                            '</ul>'
                        ),
                        'image_note' => 'Diagram showing file dropped in Dropbox folder appearing on Projector document tab',
                        'image_left' => true,
                    ],
                    [
                        'type' => 'final_cta',
                        'enabled' => true,
                        'headline' => 'Connect Your Workspace in Minutes',
                        'cta_text' => 'Get Started Free',
                        'cta_url' => 'https://projecthq.app/register?ref=integrations',
                    ],
                ],
            ])
            ->save();

        $tree = Collection::find('pages')->structure()->in('default');
        $tree->append(Entry::find('integrations'))->save();
    }

    public function down()
    {
        Entry::find('integrations')?->delete();
    }
};
