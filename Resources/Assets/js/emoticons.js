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

        var r = document.selection.createRange();
        if (r == null) {
            return 0;
        }

        var re = this[0].createTextRange(),
            rc = re.duplicate();
        re.moveToBookmark(r.getBookmark());
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
            var range = elem.createTextRange();
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
jQuery.fn.insertEmoticon = function(textareaId) {
    var caretPosition = 0,
        textarea = $(textareaId),
        $this = $(this);

    $this.click(function (e) {
        e.preventDefault();

        var currentValue = textarea.val(),
            textBeforeSelection = currentValue.substr(0, caretPosition),
            textAfterSelection = currentValue.substr(caretPosition);

        caretPosition += $(this).attr('title').length + 2;

        textarea.val(textBeforeSelection + ' ' + $(this).attr('title') + ' ' + textAfterSelection).focus().setCaretPosition(caretPosition);
    });

    // Aktuelle Cursorposition speichern
    textarea.blur(function () {
        caretPosition = textarea.getCaretPosition();
    });
};

jQuery(document).ready(function($) {
    $('.icons a').insertEmoticon($('.icons').data('emoticons-input'));
});