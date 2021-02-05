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
			//alert(name);
			//alert(id);
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
					alert('Zmieniono nazwe kategorii');
					Income.get();
				} else {
					alert('Nie zmieniono nazwy');
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
			//alert(name);
			//alert(id);
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
			alert(id);
			$('#removeModal').modal('hide');
			
			$.post('/Settings/removeIncomeCategory', {
				name: name, id: id
			}, function(data) {
				if (data == 'true') {
					alert('Usunięto');
					Income.get();
				} else {
					alert('Nie usunięto');
				}
			});
		} 
	}
	
	
}