// Brand text colors for Bard. Mirrors app/Bard/TextColor.php, which renders the front end.
const colors = [
    { name: 'primary', text: 'Primary Color', hex: '#c63615' },
    { name: 'highlight', text: 'Highlight Color', hex: '#dd6c0e' },
];

Statamic.booting(() => {
    Statamic.$bard.addExtension(({ tiptap }) => {
        return tiptap.core.Mark.create({
            name: 'textColor',

            addAttributes() {
                return {
                    color: {
                        default: null,
                        parseHTML: (element) => colors.find((c) => element.classList.contains(`text-color-${c.name}`))?.name ?? null,
                        renderHTML: (attributes) => (attributes.color ? { class: `text-color-${attributes.color}` } : {}),
                    },
                };
            },

            parseHTML() {
                return colors.map((c) => ({ tag: `span.text-color-${c.name}` }));
            },

            renderHTML({ HTMLAttributes }) {
                return ['span', HTMLAttributes, 0];
            },
        });
    });

    // Buttons only appear on Bard fields whose blueprint lists them (textcolorprimary / textcolorhighlight).
    Statamic.$bard.buttons((buttons, button) => {
        return colors.map((c) =>
            button({
                name: `textcolor${c.name}`,
                text: c.text,
                html: `<span style="font-weight:700;color:${c.hex};border-bottom:3px solid ${c.hex};line-height:1">A</span>`,
                active: (editor) => editor.isActive('textColor', { color: c.name }),
                command: (editor) => {
                    const chain = editor.chain().focus();
                    editor.isActive('textColor', { color: c.name })
                        ? chain.unsetMark('textColor').run()
                        : chain.setMark('textColor', { color: c.name }).run();
                },
            }),
        );
    });
});
