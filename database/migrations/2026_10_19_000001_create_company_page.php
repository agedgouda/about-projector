<?php

use Illuminate\Database\Migrations\Migration;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Fields\Field;
use Statamic\Fieldtypes\Bard;
use Statamic\Fieldtypes\Bard\Augmentor;

// Creates the /company page about Kaufman International, who built Projector. Skipped if it already exists, so CP edits are never overwritten.
return new class extends Migration
{
    public function up()
    {
        if (Entry::find('company')) {
            return;
        }

        $bard = (new Bard)->setField(new Field('body', ['type' => 'bard', 'save_html' => false]));
        $augmentor = new Augmentor($bard);
        $toBard = fn (string $html) => $augmentor->renderHtmlToProsemirror($html)['content'];

        Entry::make()
            ->id('company')
            ->collection('pages')
            ->blueprint('page')
            ->slug('company')
            ->data([
                'title' => 'Company',
                'template' => 'page',
                'meta_description' => 'Projector is built by Kaufman International, a software company that builds custom software, automation, and AI systems.',
                'sections' => [
                    [
                        'type' => 'page_hero',
                        'enabled' => true,
                        'headline' => 'Built by Kaufman International',
                        'subhead' => $toBard('<p>Projector is designed and engineered by <a href="https://kaufmaninternational.com">Kaufman International</a>, a software company that builds custom software, automation, and AI systems.</p>'),
                    ],
                    [
                        'type' => 'feature_block',
                        'enabled' => true,
                        'headline' => 'Mission-Critical Engineering',
                        'body' => $toBard(
                            '<p>AI can generate code quickly, but reliable software takes more: systems thinking, sound architecture, and an understanding of how people and processes work together. That approach is how Projector was built.</p>'
                        ),
                    ],
                    [
                        'type' => 'feature_block',
                        'enabled' => true,
                        'headline' => 'What Kaufman International Does',
                        'body' => $toBard(
                            '<ul>'.
                            '<li><p><strong>AI:</strong> Put your company\'s legacy knowledge to work.</p></li>'.
                            '<li><p><strong>Custom Software:</strong> Turn your ideas into an application.</p></li>'.
                            '<li><p><strong>Automation:</strong> Connect your systems so you can stay on mission.</p></li>'.
                            '<li><p><strong>Websites:</strong> From simple informational sites to custom WordPress plugins.</p></li>'.
                            '</ul>'
                        ),
                    ],
                    [
                        'type' => 'feature_block',
                        'enabled' => true,
                        'headline' => 'Proven Engineering Experience',
                        'body' => $toBard(
                            '<p>Kaufman International is led by President Jeff Kaufman, who has worked in technology since the 1990s building web applications, leading development teams, and solving complex technical problems. He holds a BS in Physics from UC Irvine and brings experience in software engineering, data engineering, and applied machine learning.</p>'.
                            '<p>His website work includes projects for Anheuser-Busch, Coca-Cola, and the NCAA.</p>'
                        ),
                    ],
                    [
                        'type' => 'final_cta',
                        'enabled' => true,
                        'headline' => 'Have Something to Build?',
                        'cta_text' => 'Book a Technical Review',
                        'cta_url' => 'https://kaufmaninternational.com/contact/',
                    ],
                ],
            ])
            ->save();

        $tree = Collection::find('pages')->structure()->in('default');
        $tree->append(Entry::find('company'))->save();
    }

    public function down()
    {
        Entry::find('company')?->delete();
    }
};
