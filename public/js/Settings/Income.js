class Income
{
	static edit() 
	{
		$('#incomes div.error').remove();
		var inputs = $('#incomes').serializeArray();
		
		if (inputs.length > 0) {
			var dataAsText = inputs[0]['value'];
			var dataAsValue = dataAsText.split('|');
			var name = dataAsValue[0];
			var id = dataAsValue[1];

			$('#incomesModalName').val(name);
			$('#incomesModalId').val(id);
			
			$('#incomesModal').modal({backdrop: 'static'}, 'show');
		} else {
			$('#incomes').append('<div class="error"> Choose one option! </div>');
		}
	}
	
	static editExecute() 
	{
		var inputs = $('#incomesModalForm').serializeArray();
		
		if (inputs.length > 0) {
			var name = inputs[0]['value'];
			var id = inputs[1]['value'];
			
			$('#incomesModal').modal('hide');
			
			$.post('/Settings/updateIncomeCategory', {
				name: name, id: id
			}, function(data) {
				if (data == 'true') {
					Income.#showInfoModal('Edycja kategorii', 'Zmieniono nazwę kategorii', 'success');
					Income.get();
				} else {
					Income.#showInfoModal('Edycja kategorii', 'Nie zmieniono nazwy kategorii', 'danger');
				}
			});
		} 
	}
	
	static get() 
	{
		$.post('/Settings/getIncomesCategories', function (data) {
			data = JSON.parse(data);
				
			var id;
			var name;
			var text = 
			`<div class="row mx-0"> 
				<div class="col btn btn-info p-0" onclick="Income.edit()"> Edytuj </div>
					<div class="col btn btn-danger p-0 ml-2" onclick="Income.remove()"> Usuń </div> 
			</div>
			<div class="col btn btn-info p-0 mt-2" onclick="Income.add()"> Dodaj nową kategorię </div>`;
					
			$('#incomes').text('');
				
			for (var category in data) {
				
				id = data[category]['id'];
				name = data[category]['name'];
				
				if (name == 'Another') {
					continue;
				}
				
				var dataAsText = name + '|' + id;
				
				var text2 = 
				`<div class="form-check">
					<label class="col-form-label" >
						<input type="radio" name="incomes_category" value="` + dataAsText + `" /> ` +
						name +
					`</label>
				</div>`;
					
				$('#incomes').append(text2);
			}
				
			$('#incomes').append(text);
		});
	}
	
	static remove()
	{
		$('#incomes div.error').remove();
		var inputs = $('#incomes').serializeArray();
		
		if (inputs.length > 0) {
			var dataAsText = inputs[0]['value'];
			var dataAsValue = dataAsText.split('|');
			var name = dataAsValue[0];
			var id = dataAsValue[1];
		
			$('#removeModalName').val(name);
			$('#removeModalId').val(id);
			$('#removeModal').modal({backdrop: 'static'}, 'show');
		} else {
			$('#incomes').append('<div class="error"> Choose one option! </div>');
		}
	}
	
	static removeExecute()
	{
		var inputs = $('#removeIncomesModalForm').serializeArray();
		
		if (inputs.length > 0) {
			var name = inputs[0]['value'];
			var id = inputs[1]['value'];
			$('#removeModal').modal('hide');
			
			$.post('/Settings/removeIncomeCategory', {
				id: id
			}, function(data) {
				if (data == 'true') {
					Income.#showInfoModal('Usuwanie kategorii', 'Usunięto kategorię', 'success');
					Income.get();
				} else {
					Income.#showInfoModal('Usuwanie kategorii', 'Nie usunięto kategorii', 'danger');
				}
			});
		} 
	}
	
	static add()
	{
		$('#addIncomesModal input').val('');
		$('#addIncomesModal').modal({backdrop: 'static'}, 'show');
	}
	
	static addExecute()
	{
		$('#addIncomesModal').modal('hide');
		var inputs = $('#addIncomesModalForm').serializeArray();
		
		var name = inputs[0]['value'];
		
		$.post('/Settings/addIncomesCategory', {
			name: name
		}, function(data) {
			if (data == 'true') {
				Income.#showInfoModal('Dodawanie kategorii', 'Dodano kategorię', 'success');
				Income.get();
			} else {
				Income.#showInfoModal('Dodawanie kategorii', 'Nie dodano kategorii', 'danger');
			}
		});
	}
	
	static #showInfoModal(header, content, type) {
	
		$('#headerModal').text(header);
		$('#contentModal').text(content);
		$('#contentModal').removeClass().addClass('alert alert-' + type);
		$('#infoModal').modal({backdrop: 'static'}, 'show');
	}
	
}
