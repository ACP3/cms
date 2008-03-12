// Emoticons in <textarea> einfügen
function emoticon(fieldId, emotion)
{
	$('#' + fieldId).focus();
	var currentVal = $('#' + fieldId).val();
	$('#' + fieldId).val(currentVal + ' ' + emotion + ' ');
}