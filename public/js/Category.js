class Category
{
	constructor(name)
	{
		this.formName = name;
		this.modalName = name + '-modal';
		this.modalFormName = name + '-modal-form';
		
		this.get();
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

			$('#' + this.modalFormName + ' input').eq(0).val(name);
			$('#' + this.modalFormName + ' input').eq(1).val(id);
			
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
			
			$('#' + this.modalName).modal('hide');
			
			var thisObject = this;
			
			$.post('/Settings/updateCategory', {
				name: name, id: id, type: thisObject.formName
			}, function(data) {
				if (data == 'true') {
					Category.showInfoModal('Edycja kategorii', 'Zmieniono nazwę kategorii', 'success');
					thisObject.get();
				} else {
					Category.showInfoModal('Edycja kategorii', 'Nie zmieniono nazwy kategorii', 'danger');
				}
			});
		} 
	}
	
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
	
				if (name == 'Another') {
					continue;
				}
				
				var dataAsText = name + '|' + id;
				
				var text2 = 
				`<div class="form-check">
					<label class="col-form-label" >
						<input type="radio" name="category" value="` + dataAsText + `" /> ` +
						name + 
					`</label>
				</div>`;
					
				$('#' + thisObject.formName).append(text2);
			}
				
			$('#' + thisObject.formName).append(text);
		});	
	}
	
	remove()
	{
		$('#' + this.formName + ' div.error').remove();
		
		var inputs = $('#' + this.formName).serializeArray();
		
		if (inputs.length > 0) {
			var dataAsText = inputs[0]['value'];
			var dataAsValue = dataAsText.split('|');
			var name = dataAsValue[0];
			var id = dataAsValue[1];
		
			$('#remove-' + this.modalFormName + ' input').eq(0).val(name);
			$('#remove-' + this.modalFormName + ' input').eq(1).val(id);
			$('#remove-' + this.modalName).modal({backdrop: 'static'}, 'show');
		} else {
			$('#' + this.formName).append('<div class="error"> Choose one option! </div>');
		}
	}
	
	removeExecute()
	{
		var inputs = $('#remove-' + this.modalFormName).serializeArray();
		
		if (inputs.length > 0) {
			var name = inputs[0]['value'];
			var id = inputs[1]['value'];
			$('#remove-' + this.modalName).modal('hide');
			
			var thisObject = this;
			
			$.post('/Settings/removeCategory', {
				id: id, type: thisObject.formName
			}, function(data) {
				if (data == 'true') {
					Category.showInfoModal('Usuwanie kategorii', 'Usunięto kategorię', 'success');
					thisObject.get();
				} else {
					Category.showInfoModal('Usuwanie kategorii', 'Nie usunięto kategorii', 'danger');
				}
			});
		} 
	}
	
	add()
	{
		$('#add-' + this.modalFormName + ' input').eq(0).val('');
		$('#add-' + this.modalName).modal({backdrop: 'static'}, 'show');
	}
	
	addExecute()
	{
		$('#add-' + this.modalName).modal('hide');
		
		var inputs = $('#add-' + this.modalFormName).serializeArray();
		
		var name = inputs[0]['value'];
		
		var thisObject = this;
		
		$.post('/Settings/addCategory', {
			name: name, type: thisObject.formName
		}, function(data) {
			if (data == 'true') {
				Category.showInfoModal('Dodawanie kategorii', 'Dodano kategorię', 'success');
				thisObject.get();
			} else {
				Category.showInfoModal('Dodawanie kategorii', 'Nie dodano kategorii', 'danger');
			}
		});
	}
	
	static showInfoModal(header, content, type) {
	
		$('#header-modal').text(header);
		$('#content-modal').text(content);
		$('#content-modal').removeClass().addClass('alert alert-' + type);
		$('#info-modal').modal({backdrop: 'static'}, 'show');
	}
}
