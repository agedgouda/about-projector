<?php

use Illuminate\Database\Migrations\Migration;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Fields\Field;
use Statamic\Fieldtypes\Bard;
use Statamic\Fieldtypes\Bard\Augmentor;

// Creates the /client-reporting page with its starting copy. Skipped if it already exists, so CP edits are never overwritten.
return new class extends Migration
{
    public function up()
    {
        if (Entry::find('client-reporting')) {
            return;
        }

        $bard = (new Bard)->setField(new Field('body', ['type' => 'bard', 'save_html' => false]));
        $augmentor = new Augmentor($bard);
        $toBard = fn (string $html) => $augmentor->renderHtmlToProsemirror($html)['content'];

        Entry::make()
            ->id('client-reporting')
            ->collection('pages')
            ->blueprint('page')
            ->slug('client-reporting')
            ->data([
                'title' => 'Client Reporting',
                'template' => 'page',
                'meta_description' => 'Manage team execution with clear Kanban boards and generate custom client status reports on demand.',
                'sections' => [
                    [
                        'type' => 'page_hero',
                        'enabled' => true,
                        'headline' => 'Keep Projects Moving & Clients Informed',
                        'subhead' => 'Manage team execution with clear Kanban boards and generate custom client status reports on demand.',
                        'cta_text' => 'Try Projector Free →',
                        'cta_url' => 'https://projecthq.app/register?ref=client-reporting',
                        'image_note' => 'Dual visual showing internal Kanban board on left and clean PDF client report on right',
                    ],
                    [
                        'type' => 'feature_block',
                        'enabled' => true,
                        'headline' => 'Built for Internal Teams and External Stakeholders',
                        'body' => $toBard(
                            '<p>Separate team execution deadlines from client promises.</p><ul>'.
                            '<li><p><strong>Dual Due Dates:</strong> Track internal team targets alongside external client due dates to prevent missed expectations.</p></li>'.
                            '<li><p><strong>Custom Kanban Boards:</strong> Organize workflows with custom columns, priority tags, and campaign calendar views.</p></li>'.
                            '<li><p><strong>In-Line Collaboration:</strong> Use threaded comments and @mentions to discuss details right where the work happens.</p></li>'.
                            '<li><p><strong>Pending Invitations:</strong> Assign tasks to contractors or team members before they accept their invite link.</p></li>'.
                            '</ul>'
                        ),
                        'image_note' => 'Close-up UI card showing Internal vs External due date fields',
                        'image_left' => false,
                    ],
                    [
                        'type' => 'feature_block',
                        'enabled' => true,
                        'headline' => 'Instant Conversational Reporting & White-Label Exports',
                        'body' => $toBard(
                            '<p>Stop spending Friday afternoons building client update decks.</p><ul>'.
                            '<li><p><strong>Plain-English Queries:</strong> Request reports using plain language (e.g., <em>"export high priority tasks due next week as a PDF"</em>).</p></li>'.
                            '<li><p><strong>Multi-Format Exports:</strong> Generate clean reports in Excel, CSV, or formatted PDF calendars instantly.</p></li>'.
                            '<li><p><strong>White-Label Branding:</strong> Add your company logo and custom colors to PDF exports.</p></li>'.
                            '</ul>'
                        ),
                        'image_note' => 'Generated PDF status report preview with custom logo branding',
                        'image_left' => true,
                    ],
                    [
                        'type' => 'final_cta',
                        'enabled' => true,
                        'headline' => 'Ready to Automate Your Client Updates?',
                        'cta_text' => 'Get Started Free',
                        'cta_url' => 'https://projecthq.app/register?ref=client-reporting',
                    ],
                ],
            ])
            ->save();

        $tree = Collection::find('pages')->structure()->in('default');
        $tree->append(Entry::find('client-reporting'))->save();
    }

    public function down()
    {
        Entry::find('client-reporting')?->delete();
    }
};
