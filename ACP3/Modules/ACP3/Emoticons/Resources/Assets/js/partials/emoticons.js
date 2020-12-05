/**
 * Cursorposition in einem Textfeld bestimmen
 *
 * @returns {*}
 */
jQuery.fn.getCaretPosition = function () {
    if (this[0].selectionStart) {
        return this[0].selectionStart;
    } else if (document.selection) {
        this[0].focus();

        const range = document.selection.createRange();
        if (range == null) {
            return 0;
        }

        const re = this[0].createTextRange(),
            rc = re.duplicate();
        re.moveToBookmark(range.getBookmark());
        rc.setEndPoint('EndToStart', re);

        return rc.text.length;
    }
    return 0;
};

/**
 * Cursorposition in einem Textfeld an eine bestimme Position setzen
 *
 * @param pos
 * @returns {jQuery.fn}
 */
jQuery.fn.setCaretPosition = function (pos) {
    this.each(function (index, elem) {
        if (elem.setSelectionRange) {
            elem.setSelectionRange(pos, pos);
        } else if (elem.createTextRange) {
            const range = elem.createTextRange();
            range.collapse(true);
            range.moveEnd('character', pos);
            range.moveStart('character', pos);
            range.select();
        }
    });
    return this;
};

/**
 *
 * @param textareaId
 */
jQuery.fn.insertEmoticon = function (textareaId) {
    let caretPosition = 0;
    const textarea = $(textareaId),
        $this = $(this);

    $this.click(function (e) {
        e.preventDefault();

        let currentValue = textarea.val(),
            textBeforeSelection = currentValue.substr(0, caretPosition),
            textAfterSelection = currentValue.substr(caretPosition);

        caretPosition += $(this).attr('title').length + 2;

        textarea.val(textBeforeSelection + ' ' + $(this).attr('title') + ' ' + textAfterSelection).focus().setCaretPosition(caretPosition);
    });

    // Aktuelle Cursorposition speichern
    textarea.blur(() => {
        caretPosition = textarea.getCaretPosition();
    });
};

(($) => {
    $('.icons a').insertEmoticon($('.icons').data('emoticons-input'));
})(jQuery);
