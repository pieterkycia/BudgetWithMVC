class User 
{
	constructor()
	{
		var thisObject = this;
		$.post('/Settings/getUserData', function(data) {
			data = JSON.parse(data);
			thisObject.name = data['username'];
			thisObject.email = data['email'];
			
			thisObject.get();
		});
	}
	
	get()
	{
		$('#user-form span').eq(0).text(this.name);
		$('#user-form span').eq(1).text(this.email);	
	}
	
	edit()
	{
		$('#user-form div.error').remove();
		
		var inputs = $('#user-form').serializeArray();

		if (inputs.length > 0) {
			var choice = inputs[0]['value'];
			
			switch (choice) {
				case 'name':
					$('#user-modal-name input').val(this.name);
					$('#user-modal-name').modal({backdrop: 'static'}, 'show');
					break;
				case 'email':
					$('#user-modal-email input').val(this.email);
					$('#user-modal-email').modal({backdrop: 'static'}, 'show');
					break;
				case 'password':
					$('#user-modal-password input').val('');
					$('#user-modal-password').modal({backdrop: 'static'}, 'show');
					break;
			}
		} else {
			$('#user-form').append('<div class="error"> Choose one option! </div>');
		}
	}
	
	editName()
	{
		$('#user-modal-name').modal('hide');
		
		var inputs = $('#user-modal-name-form').serializeArray();
		var name = inputs[0]['value'];
		var thisObject = this;
		
		$.post('/Settings/editUserName', {
			name: name
		}, function(data) {
			if (data == 'true') {
				thisObject.name = name;
				Category.showInfoModal('Edycja imienia', 'Zmieniono imię', 'success');
				thisObject.get();
			} else {
				Category.showInfoModal('Edycja imienia', 'Nie zmieniono imienia', 'danger');
			}
		});
	}
	
	editEmail()
	{
		$('#user-modal-email').modal('hide');
		
		var inputs = $('#user-modal-email-form').serializeArray();
		var email = inputs[0]['value'];
		var thisObject = this;
		
		$.post('/Settings/editUserEmail', {
			email: email
		}, function(data) {
			if (data == 'true') {
				thisObject.email = email;
				Category.showInfoModal('Edycja emaila', 'Zmieniono email', 'success');
				thisObject.get();
			} else {
				Category.showInfoModal('Edycja emaila', 'Nie zmieniono emaila', 'danger');
			}
		});
	}
	
	editPassword()
	{
		$('#user-modal-password').modal('hide');
		
		var inputs = $('#user-modal-password-form').serializeArray();
		var password = inputs[0]['value'];
		var thisObject = this;
		
		$.post('/Settings/editUserPassword', {
			password: password
		}, function(data) {
			if (data == 'true') {
				Category.showInfoModal('Edycja hasła', 'Zmieniono hasło', 'success');
				thisObject.get();
			} else {
				Category.showInfoModal('Edycja hasła', 'Nie zmieniono hasła', 'danger');
			}
		});
	}
}
