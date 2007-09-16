// Emoticons in <textarea> einfügen
function emoticon(field_id, emotion)
{
	$('#' + field_id)[0].focus();
	$('#' + field_id)[0].value+= ' ' + emotion + ' ';
}