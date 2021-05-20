$(document).ready(function(){

	// Add invitee into table
	$(".add-invitee").click(function(e){
		e.preventDefault();
		var name = $("#inviteeName").val();
		var type = $("#inviteeType").val();
		var email = $("#inviteeEmail").val();
		var markup = "<tr><td><input type='checkbox' name='row-num'></td><td>" + name + "</td><td>" + type + "</td><td>" + email + "</td></tr>";
		if (name.length > 0 && email.length > 0) {
			if (validateEmail(email) == true) {
				$("#tableInvitees tbody").append(markup);
			}
		}
		
		return false;
	});

	// Remove selected table rows
	$(".remove-invitee").click(function(e){
		e.preventDefault();
		$("#tableInvitees").find('input[name="row-num"]').each(function(){
			if($(this).is(":checked")){
				$(this).parents("tr").remove();
			}
		});
	});

});

function validateEmail(Email) {
    var pattern = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;

    return $.trim(Email).match(pattern) ? true : false;
}