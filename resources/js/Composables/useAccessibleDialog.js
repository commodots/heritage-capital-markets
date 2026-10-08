import { nextTick, watch } from 'vue'

const focusableSelector = [
    'a[href]',
    'button:not([disabled])',
    'input:not([disabled]):not([type="hidden"])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    '[tabindex]:not([tabindex="-1"])',
].join(',')

export function useAccessibleDialog(isOpen, close) {
    let dialogElement = null
    let previouslyFocusedElement = null

    watch(isOpen, async (open) => {
        if (open) {
            previouslyFocusedElement = document.activeElement
            await nextTick()
            if (isOpen.value) {
                dialogElement?.querySelector(focusableSelector)?.focus()
            }
            return
        }

        const elementToRestore = previouslyFocusedElement
        previouslyFocusedElement = null
        await nextTick()

        if (elementToRestore?.isConnected && typeof elementToRestore.focus === 'function') {
            elementToRestore.focus()
        }
    }, { flush: 'sync' })

    function handleKeydown(event) {
        if (event.key === 'Escape') {
            event.preventDefault()
            close()
            return
        }

        if (event.key !== 'Tab' || !dialogElement) return

        const focusableElements = Array.from(
            dialogElement.querySelectorAll(focusableSelector),
        ).filter((element) => element.getClientRects().length > 0)
        const firstElement = focusableElements[0]
        const lastElement = focusableElements[focusableElements.length - 1]

        if (!firstElement) {
            event.preventDefault()
            dialogElement.focus()
            return
        }

        if (event.shiftKey && (document.activeElement === firstElement || !dialogElement.contains(document.activeElement))) {
            event.preventDefault()
            lastElement.focus()
        } else if (!event.shiftKey && (document.activeElement === lastElement || !dialogElement.contains(document.activeElement))) {
            event.preventDefault()
            firstElement.focus()
        }
    }

    function setDialogElement(element) {
        dialogElement = element
    }

    return {
        handleKeydown,
        setDialogElement,
    }
}
