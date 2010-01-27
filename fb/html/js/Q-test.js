$(function(){
	Ext.QuickTips.init();
	
	$('table#tbl-cdr tbody tr').css({cursor: 'pointer'})
		.removeClass('colorBg')
		.click(function(){
			$.post("/index/cdr", {cid: this.id }, function(data){
				data = eval('(' + data + ')');
				$('#' + data.cid).remove();
				$('table#tbl-cdr tbody tr').removeClass('colorBg');
				$('table#tbl-cdr tbody tr:even').addClass('colorBg');
			});
		});
	
	$('table#tbl-cdr tbody tr:even').addClass('colorBg');
	//$('#fun-main-details td.t-bold').css({fontVariant: 'small-caps'});
	//$('#fun-dep-details-header-col th').addClass('t-bold colorBg').css({fontVariant: 'small-caps'});
	//$('#fun-dep-details-header-col th:lt(5)').css({width: '140px'});
	//setupCentriq();
});


function setupCentriq(){
	var frm = Ext.form;
	
	new Ext.Button({ text: 'Add Product', renderTo: 'fun-submit-but-ct'});
	
	new frm.ComboBox({
		allowBlank: false, width: 130, triggerAction: 'all',
		id: 'pdt_cover_el', hiddenName: 'pdt_cover',
		mode: 'local', store: [
		  ['F','FAMILY COVER'],['M','MEMBER ONLY']
		],
		renderTo: 'fun-pdt-cover',
		forceSelection: true,
		listeners: {
			select: updateCentriqScr
		}
	});
	
	new frm.ComboBox({
		allowBlank: false, width: 130, triggerAction: 'all',
		id: 'pdt_opt_el', hiddenName: 'pdt_opt',
		mode: 'local', store: [
		  ['O1','OPTION 1'],['O2','OPTION 2']
		],
		renderTo: 'fun-pdt-opt',
		forceSelection: true,
		listeners: {
			select: updateCentriqScr,
			blur: updateCentriqScr
		}
	});
	
	new frm.ComboBox({
		allowBlank: true, width: 320, triggerAction: 'all',
		id: 'pdt_deps_el', hiddenName: 'pdt_deps',
		mode: 'local', store: [
		  ['1L65','1 - Adult dependant upto age 65 at entry'.toUpperCase() ],
		  ['2L65','2 - Adult dependants upto age 65 at entry'.toUpperCase()],
		  ['3L65','3 - Adult dependants upto age 65 at entry'.toUpperCase()],
		  ['4L65','4 - Adult dependants upto age 65 at entry'.toUpperCase()],
		  
		  ['1O65','1 - Adult dependant  over age 65 at entry'.toUpperCase() ],
		  ['2O65','2 - Adult dependants over age 65 at entry'.toUpperCase()],
		  ['3O65','3 - Adult dependants over age 65 at entry'.toUpperCase()],
		  ['4O65','4 - Adult dependants over age 65 at entry'.toUpperCase()]
		],
		renderTo: 'fun-pdt-deps',
		forceSelection: true,
		listeners: {
			select: updateCentriqScr
		}
	});
}

function updateCentriqScr(){
	
	var pdts = {
		'F-O1': {
			msg: 'Family Cover Option 1',
			price: 30.45,
			deps: 5
		},
		'F-O1-1L65': {
			msg: 'Family Cover Option 1 with <b>(One)</b> Dependant Upto 65 At Entry',
			price: 40.45,
			deps: 6
		},
		'F-O1-2L65': {
			msg: 'Family Cover Option 1 with <b>(Two)</b> Dependants Upto 65 At Entry',
			price: 50.45,
			deps: 7
		},
		'F-O1-3L65': {
			msg: 'Family Cover Option 1 with (Three) Dependants Upto 65 At Entry',
			price: 60.45,
			deps: 8
		},
		'F-O1-4L65': {
			msg: 'Family Cover Option 1 with (Four) Dependants Upto 65 At Entry',
			price: 70.45,
			deps: 9
		},
		
		'F-O2': {
			msg: 'Family Cover Option 2',
			price: 33.75,
			deps: 5
		},
		'F-O2-1L65': {
			msg: 'Family Cover Option 2 with One Dependant Upto 65 At Entry',
			price: 43.75,
			deps: 6
		},
		'F-O2-2L65': {
			msg: 'Family Cover Option 2 with (Two) Dependant Upto 65 At Entry',
			price: 53.75,
			deps: 7
		},
		'F-O2-3L65': {
			msg: 'Family Cover Option 2 with (Three) Dependant Upto 65 At Entry',
			price: 63.75,
			deps: 8
		},
		'F-O2-4L65': {
			msg: 'Family Cover Option 2 with (Four) Dependant Upto 65 At Entry',
			price: 73.75,
			deps: 9
		}
		
	};
	
	
	var deps = Ext.getCmp('pdt_deps_el').value || '',
		opt  = Ext.getCmp('pdt_opt_el').value || '',
		cover= Ext.getCmp('pdt_cover_el').value || '';
	if( !(opt && cover))
		return false;
	
	var pdt = cover + "-" + opt + (deps ? '-' + deps : '');
	pdt = pdts[pdt];
	$('#fun-pdt-cost').text( 'R ' + pdt.price );
	$('#fun-popup-scr').html(pdt.msg);
	addCentricRow(pdt.deps);
}

function addCentricRow(cnt){
	
	$('#fun-dep-details-header-col ~ tr').remove();
	for(var i=1; i<= cnt; i++){
		var row_tpl = "<tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
		row_tpl = $(row_tpl);
		$('td:lt(5)', row_tpl).css({width: 140});
		
		var frm = Ext.form;
		new frm.TextField({
			allowBlank: false,
			name: 'dep_' + i + '_name', id: 'dep_' + i + '_name',
			width: 135,
			maskRe: /[a-z ]/i, renderTo: $('td:nth-child(1)', row_tpl).get(0)
		});
		
		new frm.TextField({
			allowBlank: false,
			name: 'dep_' + i + '_sname', id: 'dep_' + i + '_sname',
			width: 130,
			maskRe: /[a-z ]/i, renderTo: $('td:nth-child(2)', row_tpl).get(0)
		});
		
		new frm.DateField({
			allowBlank: false,
			name: 'dep_' + i + '_dob', id: 'dep_' + i + '_dob',
			format: 'd/M/Y', altFormats: "j/m/Y|j/n/Y|d/m/Y|d/n/Y|j/m/y|j/n/y|d/m/y|d/n/y",
			width: 135, renderTo: $('td:nth-child(3)', row_tpl).get(0)
		});
		
		new frm.TextField({
			allowBlank: false,
			name: 'dep_' + i + '_nid', id: 'dep_' + i + '_nid',
			width: 135,
			maskRe: /[a-z\d]/i, renderTo: $('td:nth-child(4)', row_tpl).get(0)
		});
		
		new frm.ComboBox({
			allowBlank: false, width: 130, triggerAction: 'all',
			name: 'dep_' + i + 'gender_txt', id: 'dep_' + i + '_gender', 
			hiddenName: 'dep_' + i + '_gender',
			mode: 'local', store: [['F','FEMALE'],['M','MALE']],
			renderTo: $('td:nth-child(5)', row_tpl).get(0),
			forceSelection: true
		});
		
		new frm.ComboBox({
			allowBlank: false, width: 135, triggerAction: 'all',
			name: 'dep_' + i + '_relation_txt', id: 'dep_' + i + '_relation', 
			hiddenName: 'dep_' + i + '_relation',
			mode: 'local', store: [['C','CHILD'],['S','SPOUSE'],['P','PARENT'],['O','OTHER']],
			renderTo: $('td:nth-child(6)', row_tpl).get(0),
			forceSelection: true
		});
		
		$('#fun-dep-details table tr:last').after(row_tpl);
	}
}