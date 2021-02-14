class Expense extends Category
{
	get() 
	{
		var thisObject = this;
		
		$.post('/Settings/getCategories', {
			type: thisObject.formName
		}, function (data) {
			data = JSON.parse(data);
			var id;
			var name;
			var limit;
			var limitDiv;
			var text = 
			`<div class="row mx-0"> 
				<div class="col btn btn-info p-0" onclick="` + thisObject.formName + `.edit()"> Edytuj </div>
					<div class="col btn btn-danger p-0 ml-2" onclick="` + thisObject.formName + `.remove()"> Usuń </div> 
			</div>
			<div class="col btn btn-info p-0 mt-2" onclick="` + thisObject.formName + `.add()"> Dodaj nową kategorię </div>`;
		
			$('#' + thisObject.formName).text('');
				
			for (var category in data) {
				
				id = data[category]['id'];
				name = data[category]['name'];
				limit = data[category]['category_limit'];
				
				if (limit == 0) {
					limitDiv = '';
				} else {
					limitDiv = '<div class="ml-3 font-weight-light"> Limit:  ' + limit + '</div>';
				}
	
				if (name == 'Another') {
					continue;
				}
				
				var dataAsText = name + '|' + id + '|' + limit;
				
				var text2 = 
				`<div class="form-check">
					<label class="col-form-label" >
						<input type="radio" name="category" value="` + dataAsText + `" /> ` +
						name + limitDiv + 
					`</label>
				</div>`;
					
				$('#' + thisObject.formName).append(text2);
			}
				
			$('#' + thisObject.formName).append(text);
		});	
	}
	
	edit() 
	{ 
		$('#' + this.formName + ' div.error').remove();
		
		var inputs = $('#' + this.formName).serializeArray();
		
		if (inputs.length > 0) {
			var dataAsText = inputs[0]['value'];
			var dataAsValue = dataAsText.split('|');
			var name = dataAsValue[0];
			var id = dataAsValue[1];
			var limit = dataAsValue[2];

			$('#' + this.modalFormName + ' input').eq(0).val(name);
			$('#' + this.modalFormName + ' input').eq(1).val(id);
			if (limit != '0.00') {
				$('#' + this.modalFormName + ' input').eq(2).prop('checked', true);
				$('#' + this.modalFormName + ' input').eq(3).val(limit);
				$('#' + this.modalFormName + ' input').eq(3).prop('disabled', false);
			} else {
				$('#' + this.modalFormName + ' input').eq(2).prop('checked', false);
				$('#' + this.modalFormName + ' input').eq(3).val('');
				$('#' + this.modalFormName + ' input').eq(3).prop('disabled', true);
			}
			$('#' + this.modalName).modal({backdrop: 'static'}, 'show');
		} else {
			$('#' + this.formName).append('<div class="error"> Choose one option! </div>');
		}
	}
	
	editExecute() 
	{
		var inputs = $('#' + this.modalFormName).serializeArray();
		
		if (inputs.length > 0) {
			var name = inputs[0]['value'];
			var id = inputs[1]['value'];
			var limit = 0;
			
			if (inputs.length == 4) {
				limit = inputs[3]['value'];
			}
			console.log(limit);
			$('#' + this.modalName).modal('hide');
			
			var thisObject = this;
			
			$.post('/Settings/updateCategory', {
				name: name, id: id, limit: limit, type: thisObject.formName
			}, function(data) {
				if (data == 'true') {
					Category.showInfoModal('Edycja kategorii', 'Zmieniono nazwę kategorii', 'success');
					thisObject.get();
				} else {
					Category.showInfoModal('Edycja kategorii', 'Nie zmieniono nazwy kategorii', 'danger');
					thisObject.get();
				}
			});
		} 
	}
}