<script type="text/javascript">
$(document).ready(function() {
	// Cursorposition in einem Textfeld bestimmen
	$.fn.getCaretPosition = function() {
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
	}

	// Cursorposition in einem Textfeld an eine bestimme Position setzen
	$.fn.setCaretPosition = function(pos) {
		this.each(function(index, elem) {
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

	var caretPosition = 0;
	var textarea = $('#{$emoticons_field_id}');

	$('.icons a').click(function(e) {
		var currentValue = textarea.val();
		var textBeforeSelection = currentValue.substr(0, caretPosition);
		var textAfterSelection = currentValue.substr(caretPosition);		
		caretPosition+= $(this).attr('title').length + 2;

		textarea.val(textBeforeSelection + ' ' + $(this).attr('title') + ' ' + textAfterSelection).focus().setCaretPosition(caretPosition);

		e.preventDefault();
	});

	// Aktuelle Cursorposition speichern
	textarea.blur(function() {
		caretPosition = textarea.getCaretPosition();
	});
});
</script>
<div class="icons">
{foreach $emoticons as $row}
	<a href="#" title="{$row.code}"><img src="{$row.img}" width="{$row.width}" height="{$row.height}" alt="{$row.description}" title="{$row.description}"></a>
{/foreach}
</div>