/**
 * Scope Curator's media selection event to the RichEditor that opened it.
 *
 * Curator uses the same `insert-media` browser event for every CuratorPicker.
 * Without these guards, selecting a featured image can be inserted into the
 * first RichEditor on the form as well.
 */
(() => {
    'use strict'

    if (window.__thtCuratorRichEditorInitialized) {
        return
    }

    window.__thtCuratorRichEditorInitialized = true

    let editorContext = null
    let processing = false

    document.addEventListener('click', (event) => {
        const button = event.target.closest('button')

        if (!button || !button.getAttribute('wire:click')?.includes('attachCuratorMedia')) {
            return
        }

        const wrapper = button.closest('.fi-fo-rich-editor')
        const alpineElement = wrapper?.querySelector('[x-data*="richEditorFormComponent"]')
        const livewireElement = wrapper?.closest('[wire\\:id]')

        if (!alpineElement || !livewireElement) {
            return
        }

        const keyMatch = (alpineElement.getAttribute('x-data') ?? '').match(/key:\s*['"]([^'"]+)['"]/)

        if (!keyMatch) {
            return
        }

        let editorSelection = { type: 'text', anchor: 1, head: 1 }

        try {
            const editor = Alpine.$data(alpineElement)?.getEditor?.()

            if (editor?.state?.selection) {
                editorSelection = {
                    type: 'text',
                    anchor: editor.state.selection.anchor,
                    head: editor.state.selection.head,
                }
            }
        } catch {
            // The default selection is used when the editor is not ready yet.
        }

        editorContext = {
            key: keyMatch[1],
            livewireId: livewireElement.getAttribute('wire:id'),
            editorSelection,
        }
    }, true)

    window.addEventListener('insert-media', (event) => {
        if (processing || !editorContext) {
            return
        }

        let data = event.detail

        if (Array.isArray(data)) {
            data = data[0]
        }

        const { statePath, media } = data ?? {}

        // CuratorPicker fields such as curator_media_id and gallery must only
        // update themselves. This integration handles the RichEditor's body.
        if (!/(^|\.)body$/.test(statePath ?? '') || !media) {
            return
        }

        const item = (Array.isArray(media) ? media : [media])[0]

        if (!item?.url) {
            return
        }

        processing = true

        try {
            window.dispatchEvent(new CustomEvent('run-rich-editor-commands', {
                detail: {
                    key: editorContext.key,
                    livewireId: editorContext.livewireId,
                    commands: [
                        { name: 'focus' },
                        {
                            name: 'setImage',
                            arguments: [{
                                src: item.url,
                                alt: item.alt || item.title || '',
                                title: item.title || '',
                                id: item.id || null,
                            }],
                        },
                    ],
                    editorSelection: editorContext.editorSelection,
                },
            }))
        } finally {
            editorContext = null

            window.setTimeout(() => {
                processing = false
            }, 300)
        }
    })
})()
