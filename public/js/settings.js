
function getCategory(formName) {
	$.post('/Settings/get' + formName + 'Categories', function (data){
		data = JSON.parse(data);
		
		var id;
		var name;
		var text2 = 
		`<div class="row mx-0"> 
			<div class="col btn btn-info p-0" onclick="Income.edit()"> Edytuj </div>
			<div class="col btn btn-danger p-0 ml-2" onclick="removeCategory('` + formName + `')"> Usuń </div> 
		</div>
		<div class="col btn btn-info p-0 mt-2" onclick="addCategory('` + formName + `')"> Dodaj nową kategorię </div>`;
			
		$('#' + formName).text('');
		
		for (i in data) {
			id = data[i]['id'];
			name = data[i]['name'];
			
			var text = 
			`<div class="form-check">
				<label class="col-form-label" >
					<input type="radio" name="` + formName + `_category" value="` + name + `" /> 
					<input type="hidden" name="` + formName + `_category" value="` + id + `" /> ` +
					 name +
				`</label>
			</div>`;
			
			$('#' + formName).append(text);
		}
		
		$('#' + formName).append(text2);
	});
}

	$(document).ready(function () {
		Income.get();
		getCategory('Expenses');
		getCategory('Payments');
	});
	
	
	function editCategory(data) {
		var inputs = $('#' + data).serializeArray();
		
		if (inputs.length > 0) {
			var idCategory = inputs[0]['value'];

			$('#' + data + 'ModalName').val($('#' + idCategory).text());
			$('#' + data + 'ModalId').val(idCategory);
			$('#' + data + 'Modal').modal({backdrop: 'static'}, "show");
		} 
	}
	
	function removeCategory(data) {
		alert(data);
	}
	
	function updateCategory(data) {
		var inputs = $('#' + data + 'ModalForm').serializeArray();
		
		if (inputs.length > 0) {
			var name = inputs[0]['value'];
			var id = inputs[1]['value'];
			
			$('#' + data + 'Modal').modal("hide");
			
			$.post('/Settings/updateIncomeCategory', {
				name: name, id: id
			}, function(data) {
				if (data == 'true') {
					alert('Zmieniono nazwe kategorii');
					getCategory('Incomes');
				} else {
					alert('Nie zmieniono nazwy');
				}
			});
		} 
	}
	
	function addCategory(data) {
		$('#addCategoryModal').modal({backdrop: 'static'}, "show");
	}
	
	
	function activeField() {
		if ($('#categoryLimit').attr('disabled') == 'disabled') {
			$('#categoryLimit').removeAttr('disabled');
		} else {
			$('#categoryLimit').attr('disabled', 'disabled');
		}
	}
	
