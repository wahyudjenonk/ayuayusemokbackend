$(function() {
	loadUrl(host+'beranda');
});

var today = new Date();
var dd = today.getDate();
var mm = today.getMonth()+1; //January is 0!
var yyyy = today.getFullYear();
if(dd<10){dd='0'+dd} 
if(mm<10){mm='0'+mm}
today = yyyy+'-'+mm+'-'+dd;
function chart_na(id_selector,type,title,subtitle,title_y,data_x,data_y,satuan){
	switch(type){
	case "column":
	$('#'+id_selector).highcharts({
			chart: {
				type: type
			},
			title: {
				text: title
			},
			subtitle: {
				text: subtitle
			},
			xAxis: {
				type: 'category'
			},
			yAxis: {
				title: {
					text: title_y
				}

			},
			legend: {
				enabled: false
			},
			plotOptions: {
				series: {
					borderWidth: 0,
					dataLabels: {
						enabled: true,
						format: '{point.y:.1f}'
					}
				}
			},

			tooltip: {
				headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
				pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b> of total<br/>'
			},

			series: data_x
			
			
			
        });
		break;
		case "line" :
			$('#'+id_selector).highcharts({
				title: {
				text: title,
				x: -20 //center
				},
				subtitle: {
					text: subtitle,
					x: -20
				},
				xAxis: {
					categories: data_y
					//categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
				},
				yAxis: {
					title: {
						text: ''
					},
					plotLines: [{
						value: 0,
						width: 1,
						color: '#808080'
					}]
				},
				tooltip: {
					valueSuffix: ''
				},
				
				series: data_x
				
				/*[{
					name: 'Tokyo',
					data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
				}]*/
			});
		break;
		case "pie":
			 $('#'+id_selector).highcharts({
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: title
				},
				tooltip: {
					pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: false
						},
						showInLegend: true
					}
				},
				series: data_y
			});
		break;
	}
}
function loadUrl(urls){
	//$("#konten").empty();
    $("#konten").empty().addClass("loading");
   // $("#konten").html("").addClass("loading");
	$.get(urls,function (html){
	    $("#konten").html(html).removeClass("loading");
    });
}

function getClientHeight(){
	var theHeight;
	if (window.innerHeight)
		theHeight=window.innerHeight;
	else if (document.documentElement && document.documentElement.clientHeight) 
		theHeight=document.documentElement.clientHeight;
	else if (document.body) 
		theHeight=document.body.clientHeight;
	
	return theHeight;
}

var divcontainer;
function windowFormPanel(html,judul,width,height){
	divcontainer = $('#jendela');
	$(divcontainer).unbind();
	$('#isiJendela').html(html);
    $(divcontainer).window({
		title:judul,
		width:width,
		height:height,
		autoOpen:false,
		top: Math.round(getClientHeight()/2)-(height/2),
		left: Math.round(getClientWidth()/2)-(width/2),
		modal:true,
		maximizable:false,
		minimizable: false,
		collapsible: false,
		closable: true,
		resizable: false,
	    onBeforeClose:function(){	   
			$(divcontainer).window("close",true);
			//$(divcontainer).window("destroy",true);
			//$(divcontainer).window('refresh');
			return true;
	    }		
    });
    $(divcontainer).window('open');       
}
function windowFormClosePanel(){
    $(divcontainer).window('close');
	//$(divcontainer).window('refresh');
}

var container;
function windowForm(html,judul,width,height){
    container = "win"+Math.floor(Math.random()*9999);
    $("<div id="+container+"></div>").appendTo("body");
    container = "#"+container;
    $(container).html(html);
    $(container).css('padding','5px');
    $(container).window({
       title:judul,
       width:width,
       height:height,
       autoOpen:false,
       maximizable:false,
       minimizable: false,
	   collapsible: false,
       resizable: false,
       closable:true,
       modal:true,
	   onBeforeClose:function(){	   
			$(container).window("close",true);
			$(container).window("destroy",true);
			return true;
	   }
    });
    $(container).window('open');        
}
function closeWindow(){
    $(container).window('close');
    $(container).html("");
}


function getClientWidth(){
	var theWidth;
	if (window.innerWidth) 
		theWidth=window.innerWidth;
	else if (document.documentElement && document.documentElement.clientWidth) 
		theWidth=document.documentElement.clientWidth;
	else if (document.body) 
		theWidth=document.body.clientWidth;

	return theWidth;
}


function genGrid(modnya, divnya, lebarnya, tingginya, par1){
	if(lebarnya == undefined){
		lebarnya = getClientWidth()-250;
	}
	if(tingginya == undefined){
		tingginya = getClientHeight()-300
	}

	var kolom ={};
	var frozen ={};
	var judulnya;
	var param={};
	var urlnya;
	var urlglobal="";
	var url_detil="";
	var post_detil={};
	var fitnya;
	var klik=false;
	var doble_klik=false;
	var pagesizeboy = 10;
	var singleSelek = true;
	var nowrap_nya = true;
	var footer=false;
	var row_number=true;
	
	switch(modnya){
		case "reservasi_data":
			judulnya = "Data Reservation";
			urlnya = "reservation_data";
			fitnya = true;
			param=par1;
			row_number=false;
			urlglobal = host+'backoffice-Data/'+urlnya;
			kolom[modnya] = [
				{field:'flag',title:'Status',width:100, halign:'center',align:'center',
					formatter:function(value,rowData,rowIndex){
						if(rowData.flag=='R'){
							return 'Reservation';
						}else if(rowData.flag=='CN'){
							return 'Confirmation';
						}else if(rowData.flag=='CI'){
							return 'Checkin';
						}else if(rowData.flag=='C'){
							return 'Cancel';
						}
						else{
							return 'Checkout';
						}
					},
					styler:function(value,rowData,rowIndex){
						if(rowData.flag=='C'){return 'background:red;color:navy;'}
					}
				},
				{field:'id',title:'Set',width:100, halign:'center',align:'center',
					formatter:function(value,rowData,rowIndex){
						var modnya="reservation";
						var sts="edit";
						if(rowData.flag=='R'){
							return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_form(\''+modnya+'\',\''+sts+'\',\''+rowData.id+'\')">Set Confirm</a>';
						}else if(rowData.flag=='CN'){
							return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_form(\''+modnya+'\',\''+sts+'\',\''+rowData.id+'\')">Set CheckIn</a>';
						}else if(rowData.flag=='CI'){
							return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_form(\''+modnya+'\',\''+sts+'\',\''+rowData.id+'\')">Set CheckOut</a>';
						}else if(rowData.flag=='C'){
							return 'Was Canceled';
						}
						else{
							return 'Checkout';
						}
					},
					styler:function(value,rowData,rowIndex){
						if(rowData.flag=='C'){return 'background:red;color:navy;'}
					}
				},
				{field:'costumer_name',title:'Costumer',width:150, halign:'center',align:'left'},
				{field:'address',title:'Address',width:250, halign:'center',align:'left'},
				{field:'reservation_date',title:'Reserv. Date',width:150, halign:'center',align:'center'},
				{field:'confirm_date',title:'Confirm Date',width:150, halign:'center',align:'center'},
				{field:'check_in_date',title:'Check In Date',width:150, halign:'center',align:'center'}
			]
		break;
		case "cl_facility_unit":
			judulnya = "";
			urlnya = "cl_facility_unit";
			fitnya = true;
			urlglobal = host+'backoffice-Data/'+urlnya;
			kolom[modnya] = [
				{field:'facility_name',title:'Facility Name',width:250, halign:'center',align:'left'},
				{field:'unit',title:'Unit',width:200, halign:'center',align:'left'},
				{field:'create_date',title:'Create Date',width:150, halign:'center',align:'center'}
			]
		break;
		case "cl_room_type":
			judulnya = "";
			urlnya = modnya;
			fitnya = true;
			urlglobal = host+'backoffice-Data/'+urlnya;
			kolom[modnya] = [
				{field:'room_type',title:'Room Type',width:350, halign:'center',align:'left'},
				{field:'description',title:'Description',width:200, halign:'center',align:'left'},
				{field:'create_date',title:'Create Date',width:150, halign:'center',align:'center'}
			]
		break;
		case "cl_compulsary_periodic_payment":
			judulnya = "";
			urlnya = modnya;
			fitnya = true;
			urlglobal = host+'backoffice-Data/'+urlnya;
			kolom[modnya] = [
				{field:'compulsary_periodic_payment',title:'Compulsary Per. Payment',width:350, halign:'center',align:'left'},
				{field:'description',title:'Description',width:200, halign:'center',align:'left'},
				{field:'create_date',title:'Create Date',width:150, halign:'center',align:'center'}
			]
		break;
		case "confirm_independent":
			judulnya = "";
			urlnya = "confirmation_independent";
			fitnya = true;
			urlglobal = host+'backoffice-Data/'+urlnya;
			frozen[modnya] = [	
				{field:'id',title:'Action',width:120, halign:'center',align:'center',
					formatter:function(value,rowData,rowIndex){
						if(rowData.flag=='P'){
							return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_detil(\''+modnya+'\','+value+')">Confirm & View</a>';
						}else{
							return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_detil(\''+modnya+'\','+value+')">View Detail</a>';
						}
					}
				},
				{field:'flag',title:'Status',width:120, halign:'center',align:'left',
					formatter:function(value,rowData,rowIndex){
						if(value=='P'){return 'Waiting Confirmation';}
						else if(value=='C'){return 'Conf. Canceled';}
						else{return 'Confirmation Done';}
					},
					styler:function(value,rowData,rowIndex){
						if(value=='P'){return 'background:yellow;color:navy;'}
						else if(value=='C'){return 'background:red;color:#ffffff;'}
					}
				},
				{field:'confirm_kode',title:'Confirm Code',width:120, halign:'center',align:'center'},
				{field:'date_confirm',title:'Date Confirm',width:120, halign:'center',align:'center'},
				{field:'date_transfer',title:'Transfer Date',width:150, halign:'center',align:'center'}
			];
			kolom[modnya] = [
				
				{field:'no_invoice',title:'No Invoice',width:120, halign:'center',align:'center'},
				{field:'date_invoice',title:'Date Invoice',width:120, halign:'center',align:'center'},
				{field:'nama',title:'Name',width:200, halign:'center',align:'left'},
				{field:'apartment_name',title:'Property',width:250, halign:'center',align:'left'},
				{field:'grand_total',title:'Total Invoice',width:120, halign:'center',align:'right',
					formatter:function(value,rowData,rowIndex){
						return NumberFormat(value);
					},
				},
				{field:'total_pay',title:'Total Pay',width:120, halign:'center',align:'right',
					formatter:function(value,rowData,rowIndex){
						return NumberFormat(value);
					},
				}
			]
		break;
		case "confirm_package":
			judulnya = "";
			urlnya = "confirmation_package";
			fitnya = true;
			urlglobal = host+'backoffice-Data/'+urlnya;
			frozen[modnya] = [	
				{field:'id',title:'Action',width:120, halign:'center',align:'center',
					formatter:function(value,rowData,rowIndex){
						if(rowData.flag=='P'){
							return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_detil(\''+modnya+'\','+value+')">Confirm & View</a>';
						}else{
							return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_detil(\''+modnya+'\','+value+')">View Detail</a>';
						}
					}
				},
				{field:'flag',title:'Status',width:120, halign:'center',align:'left',
					formatter:function(value,rowData,rowIndex){
						if(value=='P'){return 'Waiting Confirmation';}
						else if(value=='C'){return 'Conf. Canceled';}
						else{return 'Confirmation Done';}
					},
					styler:function(value,rowData,rowIndex){
						if(value=='P'){return 'background:yellow;color:navy;'}
						else if(value=='C'){return 'background:red;color:#ffffff;'}
					}
				},
				{field:'confirm_kode',title:'Confirm Code',width:120, halign:'center',align:'center'},
				{field:'date_confirm',title:'Date Confirm',width:120, halign:'center',align:'center'},
				{field:'date_transfer',title:'Transfer Date',width:150, halign:'center',align:'center'}
			];
			kolom[modnya] = [
				
				{field:'no_invoice',title:'No Invoice',width:120, halign:'center',align:'center'},
				{field:'date_invoice',title:'Date Invoice',width:120, halign:'center',align:'center'},
				{field:'nama',title:'Name',width:200, halign:'center',align:'left'},
				{field:'package_name',title:'Package',width:250, halign:'center',align:'left'},
				{field:'apartment_name',title:'Property',width:250, halign:'center',align:'left'},
				{field:'total',title:'Total Invoice',width:120, halign:'center',align:'right',
					formatter:function(value,rowData,rowIndex){
						return NumberFormat(value);
					},
				},
				{field:'total_pay',title:'Total Pay',width:120, halign:'center',align:'right',
					formatter:function(value,rowData,rowIndex){
						return NumberFormat(value);
					},
				}
			]
		break;
		case "registration":
			judulnya = "";
			urlnya = "registration";
			fitnya = true;
			urlglobal = host+'backoffice-Data/'+urlnya;
			frozen[modnya] = [	
				{field:'id',title:'Activation',width:150, halign:'center',align:'center',
					formatter:function(value,rowData,rowIndex){
						return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_detil(\''+modnya+'\','+value+')">Detail</a>';
					},
					styler:function(value,rowData,rowIndex){
						if(value=='P'){return 'background:red;color:#ffffff;'}
					}
				},
				{field:'flag',title:'Status',width:150, halign:'center',align:'left',
					formatter:function(value,rowData,rowIndex){
						if(value=='P'){return 'Waiting Konfirmation';}
						else{return 'Member';}
					},
					styler:function(value,rowData,rowIndex){
						if(value=='P'){return 'background:red;color:#ffffff;'}
					}
				},
				{field:'name',title:'Name',width:200, halign:'center',align:'left'},
				{field:'email',title:'Email',width:200, halign:'center',align:'left'},
				{field:'id_number',title:'Number ID',width:150, halign:'center',align:'left'},
				
			]
			kolom[modnya] = [	
				{field:'place_of_birth',title:'Place Birth',width:120, halign:'center',align:'left'},
				{field:'date_of_birth',title:'Date Birth',width:100, halign:'center',align:'left'},
				{field:'address',title:'Address',width:200, halign:'center',align:'left'},
				{field:'city',title:'City',width:150, halign:'center',align:'left'},
				{field:'state',title:'State',width:200, halign:'center',align:'left'},
				{field:'zip_code',title:'Zip Code',width:100, halign:'center',align:'left'},
				{field:'phone_home',title:'Phn. Home',width:100, halign:'center',align:'left'},
				{field:'phone_mobile',title:'Phn. Mobile',width:100, halign:'center',align:'left'},
				{field:'company_name',title:'Company',width:150, halign:'center',align:'left'},
				{field:'company_address',title:'Company Addr',width:200, halign:'center',align:'left'},
				{field:'company_phone',title:'Company Phn',width:100, halign:'center',align:'left'},
			]
		break;
		case "member":
			judulnya = "";
			urlnya = "member";
			fitnya = true;
			urlglobal = host+'backoffice-Data/'+urlnya;
			kolom[modnya] = [	
				{field:'pwd',title:'View',width:150, halign:'center',align:'center',
					formatter:function(value,rowData,rowIndex){
							return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_detil(\''+modnya+'\',\''+rowData.member_user+'\')">Detail</a>';
					},
					styler:function(value,rowData,rowIndex){
						if(value=='P'){return 'background:red;color:#ffffff;'}
					}
				},
				{field:'flag_member',title:'Status',width:150, halign:'center',align:'left',
					formatter:function(value,rowData,rowIndex){
						if(value==1){return 'Member Active';}
						else{return 'Member Not Active';}
					},
					styler:function(value,rowData,rowIndex){
						if(value!=1){return 'background:red;color:#ffffff;'}
					}
				},
				{field:'member_user',title:'Member User',width:200, halign:'center',align:'left'},
				{field:'name',title:'Name',width:200, halign:'center',align:'left'},
				{field:'email_address',title:'Email',width:200, halign:'center',align:'left'},
				{field:'id_number',title:'Number ID',width:150, halign:'center',align:'left'}
			]
		break;
		case "property":
			judulnya = "";
			urlnya = "property";
			fitnya = true;
			urlglobal = host+'backoffice-Data/'+urlnya;
			kolom[modnya] = [	
				{field:'id',title:'Detail',width:100, halign:'center',align:'center',
					formatter:function(value,rowData,rowIndex){
						return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_detil(\''+modnya+'\','+value+')">Detail</a>';
					}
				},
				{field:'flag',title:'Approval',width:100, halign:'center',align:'center',
					formatter:function(value,rowData,rowIndex){
						if(value=='P'){
							return "<img src='"+host+"__assets/easyui/themes/icons/cancel.png'";
						}else{
							return "<img src='"+host+"__assets/easyui/themes/icons/ok.png'";
						}
						
					}
				},
				{field:'longi',title:'Verification',width:100, halign:'center',align:'center',
					formatter:function(value,rowData,rowIndex){
						if(rowData.flag=='F'){
							return "<img src='"+host+"__assets/easyui/themes/icons/ok.png'";
						}else{
							return "<img src='"+host+"__assets/easyui/themes/icons/cancel.png'";
						}
						
					}
				},
				{field:'nama',title:'Name',width:150, halign:'center',align:'left'},
				{field:'apartment_name',title:'Apartment Name',width:200, halign:'center',align:'left'},
				{field:'apartment_address',title:'Apartment Address',width:250, halign:'center',align:'left'},
				{field:'unit_number',title:'Unit Number',width:100, halign:'center',align:'right'},
				{field:'unit_size_nett',title:'Unit Size Net',width:100, halign:'center',align:'right'},
				{field:'unit_size_gross',title:'Unit Size Gross',width:100, halign:'center',align:'right'},
				{field:'number_of_room',title:'Number Of Room',width:100, halign:'center',align:'right'},
			]
		break;
		case "housekeeping":
		case "linen":
		case "check":
		case "hosting":
		case "full_host":
		case "services":
			param['mod']=modnya;
			judulnya = "";
			urlnya = "services_all";
			fitnya = true;
			nowrap_nya=false;
			urlglobal = host+'backoffice-Data/'+urlnya;
			kolom[modnya] = [	
				/*{field:'id',title:'Set Pricing',width:100, halign:'center',align:'center',
					formatter:function(value,rowData,rowIndex){
						return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_detil(\''+modnya+'\','+value+')">Set</a>';
					}
				},*/
				{field:'name',title:'Services Name',width:350, halign:'center',align:'left'},
				//{field:'code',title:'Code',width:80, halign:'center',align:'left'},
				
				{field:'desc_services_eng',title:'Desc. Eng',width:350, halign:'center',align:'left'},
				{field:'desc_services_ind',title:'Desc. Ind',width:350, halign:'center',align:'left'}
			]
		break;
		case "invoice":
		case "planning":
			judulnya = "";
			urlnya = "invoice";
			fitnya = true;
			urlglobal = host+'backoffice-Data/'+urlnya;
			if(modnya=='invoice'){
				kolom[modnya] = [	
					{field:'id',title:'Detail',width:100, halign:'center',align:'center',
						formatter:function(value,rowData,rowIndex){
							return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_detil(\''+modnya+'\','+value+')">Detail</a>';
						}
					},
					{field:'flag',title:'Status',width:100, halign:'center',align:'left',
						formatter:function(value,rowData,rowIndex){
							if(value=='P')return 'Waiting Pay';
							else if(value=='PL')return 'Planning Set';
							else if(value=='C')return 'Cancel Invoice';
							else if(value=='CP')return 'Cancel Payment';
							else return 'Finish';
						},
						styler:function(value,rowData,rowIndex){
							if(value=='P'){return 'background:green;color:yellow;'}
							else if(value=='PL')return 'background:#6DBCED;color:#ffffff;';
							else if(value=='C')return 'background:red;color:#ffffff;';
							else if(value=='CP')return 'background:red;color:#ffffff;';
						}
					},
					{field:'name',title:'Owner Name',width:220, halign:'center',align:'left'},
					{field:'no_invoice',title:'No Invoice',width:200, halign:'center',align:'left'},
					{field:'method_payment',title:'Method Payment',width:200, halign:'center',align:'left'},
					{field:'date_invoice',title:'Inv. Date',width:150, halign:'center',align:'center'},
					{field:'grand_total',title:'Grand Total',width:120, halign:'center',align:'right',
						formatter:function(value,rowData,rowIndex){
							return NumberFormat(value);
						}
					}
				]
			}else{
				kolom[modnya] = [	
					{field:'id',title:'Set Plan',width:200, halign:'center',align:'center',
						formatter:function(value,rowData,rowIndex){
							return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_detil(\''+modnya+'\','+value+')">Set</a>';
						}
					},
					
					{field:'name',title:'Owner Name',width:300, halign:'center',align:'left'},
					{field:'no_invoice',title:'No Invoice',width:200, halign:'center',align:'center'},
					{field:'date_invoice',title:'Inv. Date',width:150, halign:'center',align:'center'},
				]				
			}
		break;
		case "reservation":
			urlnya = "invoice_package";
			fitnya = true;
			urlglobal = host+'backoffice-Data/'+urlnya;
			kolom[modnya] = [	
				{field:'id',title:'Reservation',width:150, halign:'center',align:'center',
					formatter:function(value,rowData,rowIndex){
						return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_detil(\''+modnya+'\','+value+')">Set Reservation</a>';
					}
				},
				{field:'apartment_name',title:'Unit Name',width:320, halign:'center',align:'left'},
				{field:'name',title:'Owner Name',width:250, halign:'center',align:'left'},
				{field:'no_invoice',title:'No Invoice',width:200, halign:'center',align:'center'}
				
			]
		break;
		case "invoice_package":
		case "planning_package":
		case "planning_package_own":
			judulnya = "";
			if(modnya=='invoice_package')urlnya = "invoice_package";
			else if(modnya=='planning_package') urlnya = "invoice_package_planning";
			else urlnya = "invoice_package_planning_own";
			fitnya = true;
			urlglobal = host+'backoffice-Data/'+urlnya;
			if(modnya=='invoice_package'){
				kolom[modnya] = [	
						{field:'id',title:'Detail',width:100, halign:'center',align:'center',
							formatter:function(value,rowData,rowIndex){
								return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_detil(\''+modnya+'\','+value+')">Detail</a>';
							}
						},
						{field:'flag',title:'Status',width:100, halign:'center',align:'left',
							formatter:function(value,rowData,rowIndex){
								if(value=='P')return 'Waiting Pay';
								else if(value=='PL')return 'Planning Set';
								else if(value=='C')return 'Cancel Invoice';
								else if(value=='CP')return 'Cancel Payment';
								else return 'Finish';
							},
							styler:function(value,rowData,rowIndex){
								if(value=='P'){return 'background:green;color:yellow;'}
								else if(value=='PL')return 'background:#6DBCED;color:#ffffff;';
								else if(value=='C')return 'background:red;color:#ffffff;';
								else if(value=='CP')return 'background:red;color:#ffffff;';
							}
						},
						{field:'package_name',title:'Package Name',width:220, halign:'center',align:'left'},
						{field:'name',title:'Owner Name',width:220, halign:'center',align:'left'},
						{field:'no_invoice',title:'No Invoice',width:200, halign:'center',align:'left'},
						{field:'method_payment',title:'Method Payment',width:200, halign:'center',align:'left'},
						{field:'date_invoice',title:'Inv. Date',width:150, halign:'center',align:'center'},
						
					]
			}else{
				if(modnya=='planning_package'){
				kolom[modnya] = [	
						{field:'id',title:'Set Planning',width:120, halign:'center',align:'center',
							formatter:function(value,rowData,rowIndex){
								return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_detil(\''+modnya+'\','+value+')">Set Plan</a>';
							}
						},
						{field:'package_name',title:'Package Name',width:220, halign:'center',align:'left'},
						{field:'name',title:'Owner Name',width:220, halign:'center',align:'left'},
						{field:'no_invoice',title:'No Invoice',width:200, halign:'center',align:'left'},
						{field:'date_invoice',title:'Inv. Date',width:150, halign:'center',align:'center'},
						
					]
				}else{
					kolom[modnya] = [	
						{field:'id',title:'View Planning',width:120, halign:'center',align:'center',
							formatter:function(value,rowData,rowIndex){
								return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_detil(\''+modnya+'\','+value+')">View Plan</a>';
							}
						},
						{field:'package_name',title:'Package Name',width:220, halign:'center',align:'left'},
						{field:'name',title:'Owner Name',width:220, halign:'center',align:'left'},
						{field:'no_invoice',title:'No Invoice',width:200, halign:'center',align:'left'},
						{field:'date_invoice',title:'Inv. Date',width:150, halign:'center',align:'center'},
						
					]
				}

			}
		break;
	}
	if(par1=='tree'){
		grid_nya=$("#"+divnya).treegrid({
			title:judulnya,
			height:tingginya,
			width:lebarnya,
			rownumbers:row_number,
			iconCls:'database',
			fit:fitnya,
			striped:true,
			pagination:true,
			remoteSort: false,
			showFooter:footer,
			singleSelect:singleSelek,
			url: urlglobal,		
			nowrap: nowrap_nya,
			pageSize:pagesizeboy,
			pageList:[10,20,30,40,50,75,100,200],
			queryParams:param,
			idField: 'id',
			treeField: 'name',
			frozenColumns:[
				frozen[modnya]
			],
			columns:[
				kolom[modnya]
			],
			onLoadSuccess:function(d){
				$('.btn_grid').linkbutton();
			},
			onClickRow:function(rowIndex,rowData){
			 
			},
			onDblClickRow:function(rowIndex,rowData){
				
			},
			toolbar: '#tb_'+modnya,
			rowStyler: function(index,row){
				if(modnya == 'reservasi'){
					if (row.flag == 1){
						return 'background-color:#C5FFC2;'; // return inline style
					}else if(row.flag == 0){
						return 'background-color:#FFD1BB;'; // return inline style
					}
				}
				
			},
			onLoadSuccess: function(data){
				/*if(data.total == 0){
					var $panel = $(this).datagrid('getPanel');
					var $info = '<div class="info-empty" style="margin-top:20%;">Data Tidak Tersedia</div>';
					$($panel).find(".datagrid-view").append($info);
					//$('#edit').linkbutton({disabled:true});
					//$('#del').linkbutton({disabled:true});
				}else{
					$($panel).find(".datagrid-view").append('');
				}*/
			},
		});
	}
	else{
		grid_nya=$("#"+divnya).datagrid({
			title:judulnya,
			height:tingginya,
			width:lebarnya,
			rownumbers:row_number,
			iconCls:'database',
			fit:fitnya,
			striped:true,
			pagination:true,
			remoteSort: false,
			showFooter:footer,
			singleSelect:singleSelek,
			url: urlglobal,		
			nowrap: nowrap_nya,
			pageSize:pagesizeboy,
			pageList:[10,20,30,40,50,75,100,200],
			queryParams:param,
			frozenColumns:[
				frozen[modnya]
			],
			columns:[
				kolom[modnya]
			],
			onLoadSuccess:function(d){
				$('.btn_grid').linkbutton();
			},
			onClickRow:function(rowIndex,rowData){
			 
			},
			onDblClickRow:function(rowIndex,rowData){
				
			},
			toolbar: '#tb_'+modnya,
			rowStyler: function(index,row){
				if(modnya == 'reservasi'){
					if (row.flag == 1){
						return 'background-color:#C5FFC2;'; // return inline style
					}else if(row.flag == 0){
						return 'background-color:#FFD1BB;'; // return inline style
					}
				}
				
			},
			onLoadSuccess: function(data){
				if(data.total == 0){
					var $panel = $(this).datagrid('getPanel');
					var $info = '<div class="info-empty" style="margin-top:20%;">Data Tidak Tersedia</div>';
					$($panel).find(".datagrid-view").append($info);
					//$('#edit').linkbutton({disabled:true});
					//$('#del').linkbutton({disabled:true});
				}else{
					$($panel).find(".datagrid-view").append('');
				}
			},
		});
	}
}


function genform(type, modulnya, submodulnya, stswindow, tabel){
	var urlpost = host+'backoffice-form/'+submodulnya;
	var urldelete = host+'backoffice-simpan/'+submodulnya+'/delete';
	var id_tambahan = "";
	var table = submodulnya;
	/*switch(submodulnya){
		case "cl_facility_unit":
			table = "warkom";
			urlpost = host+'backend/getdisplay/get-form/'+submodulnya;
		break;
		
	}*/
	
	switch(type){
		case "add":
			if(stswindow == undefined){
				$('#grid_nya_'+submodulnya).hide();
				$('#detil_nya_'+submodulnya).empty().show().addClass("loading");
			}
			$.post(urlpost, {'editstatus':'add', 'ts':table, 'id_tambahan':id_tambahan }, function(resp){
				if(stswindow == 'windowform'){
					windowForm(resp, judulwindow, lebar, tinggi);
				}else if(stswindow == 'windowpanel'){
					windowFormPanel(resp, judulwindow, lebar, tinggi);
				}else{
					$('#detil_nya_'+submodulnya).show();
					$('#detil_nya_'+submodulnya).html(resp).removeClass("loading");
				}
			});
		break;
		case "edit":
		case "delete":
		
			var row = grid_nya.datagrid('getSelected');
			if(row){
				if(type=='edit'){
					if(stswindow == undefined){
						$('#grid_nya_'+submodulnya).hide();
						$('#detil_nya_'+submodulnya).empty().show().addClass("loading");	
					}
					$.post(urlpost, { 'editstatus':'edit', id:row.id }, function(resp){
						if(stswindow == 'windowform'){
							windowForm(resp, judulwindow, lebar, tinggi);
						}else if(stswindow == 'windowpanel'){
							windowFormPanel(resp, judulwindow, lebar, tinggi);
						}else{
							$('#detil_nya_'+submodulnya).show();
							$('#detil_nya_'+submodulnya).html(resp).removeClass("loading");
						}
					});
				}else if(type=='delete'){
					//if(confirm("Anda Yakin Menghapus Data Ini ?")){
					$.messager.confirm('Homtel BackOfiice','Anda Yakin Menghapus Data Ini ?',function(re){
						if(re){
							loadingna();
							$.post(urldelete, {id:row.id, 'sts_crud':'delete'}, function(r){
								if(r==1){
									winLoadingClose();
									$.messager.alert('Homtel BackOfiice',"Data Terhapus",'info');
									grid_nya.datagrid('reload');								
								}else{
									winLoadingClose();
									console.log(r)
									$.messager.alert('Homtel BackOfiice',"Gagal Menghapus Data",'error');
								}
							});	
						}
					});	
					//}
				}
				
			}
			else{
				$.messager.alert('Roger Salon',"Select Row In Grid",'error');
			}
		break;
		
	}
}

function genTab(div, mod, sub_mod, tab_array, div_panel, judul_panel, mod_num, height_panel, height_tab, width_panel, width_tab){
	var id_sub_mod=sub_mod.split("_");
	if(typeof(div_panel)!= "undefined" || div_panel!=""){
		$(div_panel).panel({
			width:(typeof(width_panel) == "undefined" ? getClientWidth()-268 : width_panel),
			height:(typeof(height_panel) == "undefined" ? getClientHeight()-100 : height_panel),
			title:judul_panel,
			//fit:true,
			tools:[{
					iconCls:'icon-cancel',
					handler:function(){
						$('#grid_nya_'+id_sub_mod[1]).show();
						$('#detil_nya_'+id_sub_mod[1]).hide();
						$('#grid_'+id_sub_mod[1]).datagrid('reload');
					}
			}]
		}); 
	}
	
	$(div).tabs({
		title:'AA',
		height: (typeof(height_tab) == "undefined" ? getClientHeight()-190 : height_tab),
		width: (typeof(width_tab) == "undefined" ? getClientWidth()-280 : width_tab),
		plain: false,
		fit:true,
		onSelect: function(title){
				var isi_tab=title.replace(/ /g,"_");
				var par={};
				console.log(isi_tab);
				$('#'+isi_tab.toLowerCase()).html('').addClass('loading');
				urlnya = host+'index.php/content-tab/'+mod+'/'+isi_tab.toLowerCase();
				$(div_panel).panel({title:title});
				
				switch(mod){
					case "kasir":
						var lantainya = title.split(" ");
						var lantainya = lantainya.length-1;
						
						par['posisi_lantai'] = lantainya;
						urlnya = host+'kasir-lantai/';
					break;
					case "pengaturan":
						
					break;
				}
				$.post(urlnya,par,function(r){
					$('#'+isi_tab.toLowerCase()).removeClass('loading').html(r);
				});
		},
		selected:0
	});
	
	if(tab_array.length > 0){
		for(var x in tab_array){
			var isi_tab=tab_array[x].replace(/ /g,"_");
			$(div).tabs('add',{
				title:tab_array[x],
				content:'<div style="padding: 5px;"><div id="'+isi_tab.toLowerCase()+'" style="height: 200px;">'+isi_tab.toLowerCase()+'zzzz</div></div>'
			});
		}
		var tab = $(div).tabs('select',0);
	}
}

function kumpulAction(type, p1, p2, p3, p4, p5){
	var param = {};
	switch(type){
		case "reservation":
			grid = $('#grid_reservasi').datagrid('getSelected');
			$.post(host+'backend/simpan_data/tbl_reservasi_confirm', { 'id':grid.id, 'confirm':p1 }, function(rsp){
				if(rsp == 1){
					$.messager.alert('Roger Salon',"Confirm OK",'info');
				}else{
					$.messager.alert('Roger Salon',"Failed Confirm",'error');
				}
				$('#grid_reservasi').datagrid('reload');	
			} );
		break;
		case "detail-meja":
			param['id_meja'] = p1;
			param['status_meja'] = p2;
			param['nomor_meja'] = p3;
			
			$('#konten').html('').addClass('loading');
			$.post(host+'detail-meja', param, function(r){
				$('#konten').removeClass('loading').html(r);
			});
		break;
		case "hapus-item":
			var row = $('#pes_kasir').datagrid('getSelected');
			if(row){
				$.post(host+'hapus-item', { 'id':row.id, 'editstatus':'edit', 'id_meja':p1, 'tbl_produk_id':row.tbl_produk_id }, function(resp){
					if(resp == 1){
						$('#pes_kasir').datagrid('reload');
						$.post(host+'total-pesanan', { 'id_meja':p1 }, function(resp){
							var parsing = $.parseJSON(resp);
							$('#total_qty').val(parsing.tot_qty);
							$('#total_hrg').val(NumberFormat(parsing.tot_harga));
						});
					}else{
						$.messager.alert('Error','Error System','error');
					}
				});
			}else{
				$.messager.alert('Error','Pilih Data List Pesanan!','error');
			}
		break;
		case "selesai-transaksi":
			loadingna();
			$.post(host+'selesai-transaksi', { 'id_meja':p1, 'nomor_meja':p2 }, function(resp){
				winLoadingClose();
				windowForm(resp, 'Pembayaran Transaksi', 500, 600);
			});
		break;
		case "kalkulasi":
			console.log(parseInt(jml_uang));
			
			var tot_byr = $('#tot_byr_bnr').val();
			
			if($('#jumlah_uang_'+p2).val()){
				var jml_uang = $('#jumlah_uang_'+p2).val();
			}else{
				var jml_uang = 0;
			}
			
			var uang_trm = (parseInt(p1) + parseInt(jml_uang));
			var uang_kmb = (parseInt(uang_trm) - parseInt(tot_byr));
			
			$('#jumlah_uang_'+p2).val(uang_trm);
			$('#jml_uang_'+p2).val(NumberFormat(uang_trm));
			$('#jumlah_kembalian_'+p2).val(uang_kmb);
			$('#uang_kembalian_'+p2).val(NumberFormat(uang_kmb));
			
			return false;
		break;
		case "reset_jmluang_kembalian":
			$('#jumlah_uang_'+p1).val('');
			$('#jml_uang_'+p1).val('');
			$('#jumlah_kembalian_'+p1).val('');
			$('#uang_kembalian_'+p1).val('');
		break;
		case "tutup-transaksi":
			submit_form('form_pembayaran_transaksi',function(r){
				loadingna();
				if(r==1){
					$.messager.alert('JResto Soft',"Data Tersimpan",'info');
					loadUrl(host+'kasir');
					winLoadingClose();
				}else{
					$.messager.alert('JResto Soft', "Gagal", 'error');
					console.log(r);
					winLoadingClose();
				}
				closeWindow2();
			});
		break;		
	}
}	

function submit_form(frm,func){
	var url = jQuery('#'+frm).attr("url");
    jQuery('#'+frm).form('submit',{
            url:url,
            onSubmit: function(){
                  return $(this).form('validate');
            },
            success:function(data){
				//$.unblockUI();
                if (func == undefined ){
                     if (data == "1"){
                        pesan('Data Sudah Disimpan ','Sukses');
                    }else{
                         pesan(data,'Result');
                    }
                }else{
                    func(data);
                }
            },
            error:function(data){
				//$.unblockUI();
                 if (func == undefined ){
                     pesan(data,'Error');
                }else{
                    func(data);
                }
            }
    });
}

function fillCombo(url, SelID, value, value2, value3, value4){
	//if(Ext.get(SelID).innerHTML == "") return false;
	if (value == undefined) value = "";
	if (value2 == undefined) value2 = "";
	if (value3 == undefined) value3 = "";
	if (value4 == undefined) value4 = "";
	
	$('#'+SelID).empty();
	$.post(url, {"v": value, "v2": value2, "v3": value3, "v4": value4},function(data){
		$('#'+SelID).append(data);
	});

}

function formatDate(date) {
	console.log(date);
	var y = date.getFullYear();
    var m = date.getMonth()+1;
    var d = date.getDate();
    return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
}
function parserDate(s){
	if (!s) return new Date();
    var ss = s.split('-');
    var y = parseInt(ss[0],10);
    var m = parseInt(ss[1],10);
    var d = parseInt(ss[2],10);
    if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
        return new Date(y,m-1,d)
    } else {
        return new Date();
    }
}


function clear_form(id){
	$('#'+id).find("input[type=text], textarea,select").val("");
	//$('.angka').numberbox('setValue',0);
}

var divcontainerz;
function windowLoading(html,judul,width,height){
    divcontainerz = "win"+Math.floor(Math.random()*9999);
    $("<div id="+divcontainerz+"></div>").appendTo("body");
    divcontainerz = "#"+divcontainerz;
    $(divcontainerz).html(html);
    $(divcontainerz).css('padding','5px');
    $(divcontainerz).window({
       title:judul,
       width:width,
       height:height,
       autoOpen:false,
       modal:true,
       maximizable:false,
       resizable:false,
       minimizable:false,
       closable:false,
       collapsible:false,  
    });
    $(divcontainerz).window('open');        
}
function winLoadingClose(){
    $(divcontainerz).window('close');
    //$(divcontainer).html('');
}
function loadingna(){
	windowLoading("<img src='"+host+"__assets/img/loading.gif' style='position: fixed;top: 50%;left: 50%;margin-top: -10px;margin-left: -25px;'/>","Please Wait",200,100);
}

function NumberFormat(value) {
	
    var jml= new String(value);
    if(jml=="null" || jml=="NaN") jml ="0";
    jml1 = jml.split("."); 
    jml2 = jml1[0];
    amount = jml2.split("").reverse();

    var output = "";
    for ( var i = 0; i <= amount.length-1; i++ ){
        output = amount[i] + output;
        if ((i+1) % 3 == 0 && (amount.length-1) !== i)output = '.' + output;
    }
    //if(jml1[1]===undefined) jml1[1] ="00";
   // if(isNaN(output))  output = "0";
    return output; // + "." + jml1[1];
}

function showErrorAlert (reason, detail) {
		var msg='';
		if (reason==='unsupported-file-type') { msg = "Unsupported format " +detail; }
		else {
			console.log("error uploading file", reason, detail);
		}
		$('<div class="alert"> <button type="button" class="close" data-dismiss="alert">&times;</button>'+ 
		 '<strong>File upload error</strong> '+msg+' </div>').prependTo('#alerts');
	}
function konversi_pwd_text(id){
	if($('input#'+id)[0].type=="password")$('input#'+id)[0].type = 'text';
	else $('input#'+id)[0].type = 'password';
}
function gen_editor(id){
	tinymce.init({
		  selector: id,
		  height: 200,
		  plugins: [
				"advlist autolink lists link image charmap print preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu paste jbimages"
		    ],
			
		  // ===========================================
		  // PUT PLUGIN'S BUTTON on the toolbar
		  // ===========================================
		  menubar: true,
		  toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent ",
			
		  // ===========================================
		  // SET RELATIVE_URLS to FALSE (This is required for images to display properly)
		  // ===========================================
			
		  relative_urls: false
		});
		
		tinyMCE.execCommand('mceRemoveControl', true, id);
		tinyMCE.execCommand('mceAddControl', true, id);
	
}
function cariData(acak){
	var post_search = {};
	post_search['kat'] = $('#kat_'+acak).val();
	post_search['key'] = $('#key_'+acak).val();
	if($('#kat_'+acak).val()!=''){
		grid_nya.datagrid('reload',post_search);
	}else{
		$.messager.alert('Aldeaz Back-Office',"Pilih Kategori Pencarian",'error');
	}
	//$('#grid_'+typecari).datagrid('reload', post_search);
}
function get_detil(mod,id_data){
	switch(mod){
		case "cetak_bast":
			openWindowWithPost(host+'backoffice-Cetak',{mod:mod,id:id_data});
			//openWindowWithPost(host+'backoffice-Cetak',{mod:mod,id:id_data});
		break;
		case "kirim_gudang":
			$.post(host+'backoffice-form/remark',{mod:mod,id:id_data},function(r){
				windowForm(r,'Pesan Gudang',580,250);
			});
		break;
		case "cancel_pesanan":
			$.messager.confirm('Cancel Order','Yakin Ingin MengCancel Order Ini? ',function(r){
				if (r){
					$.post(host+'backoffice-form/remark',{mod:mod,id:id_data},function(r){
						windowForm(r,'Manajemen Order',580,250);
					});
				}
			});
		break;
		case "set_packing":
		case "set_kirim":
			$.post(host+'backoffice-form/remark',{mod:mod,id:id_data},function(r){
				windowForm(r,'Manajemen Order',580,250);
			});
		break;
		
		case "rekap_penjualan":
		case "detil_penjualan":
		case "lap_bast":
		case "lap_kwitansi":
			$('#isi_laporan_'+id_data).html('').addClass('loading');
			$.post(host+'backoffice-GetDetil',{mod:mod,tgl_mulai:$('#tgl_mulai_'+id_data).datebox('getValue'),tgl_akhir:$('#tgl_akhir_'+id_data).datebox('getValue')},function(r){
				$('#isi_laporan_'+id_data).removeClass('loading').html(r);
			});
			
		break;
		case "pricing_detil":
		case "package_detil":
			$('#service_list').hide();
			$('#service_detil').html('').show().addClass("loading");
			$.post(host+'backoffice-GetDetil',{mod:mod,id:id_data},function(r){
				$('#service_detil').html(r).removeClass("loading");
			});
		break;
		default:
			$('#grid_nya_'+mod).hide();
			$('#detil_nya_'+mod).html('').show().addClass("loading");
			$.post(host+'backoffice-GetDetil',{mod:mod,id:id_data},function(r){
				$('#detil_nya_'+mod).html(r).removeClass("loading");
			});
		break;
	}
	
}
function get_form_tree(sts,mod){
	var row = grid_nya.treegrid('getSelected');
	var param={};
	console.log(row);
	if(sts !='add_new'){
		if(row){
			param['flag_tree']=row.flag_tree;
			if (sts=='add'){
				if(row.flag_tree!='C'){
					param['editstatus']='add';
					param['pid']=row.id;
				}else{
					$.messager.alert('HOMTEL Back-Office',"No Add Child",'error');
					return false;
				}
			}else if(sts=='edit'){
				param['editstatus']='edit';
				param['id']=row.id;
				param['pid']=row.pid;
			}else{
				param['id']=row.id;
				param['editstatus']='delete';
				$.messager.confirm('Homtel Backoffice','Anda Yakin Menghapus Data Ini ?',function(re){
					if(re){
						$.post(host+'backoffice-simpan/'+mod+'/delete',param,function(r){
							if(r==1){
								$.messager.alert('Homtel Back-Office',"Data Was Deleted ",'info');
								grid_nya.treegrid('reload');
							}else{
								$.messager.alert('Homtel Back-Office',"Failed Deleted ",'error');
							}
						});
					}
				});
				return false;
			}
		}else{
			$.messager.alert('HOMTEL Back-Office',"Select Row In Grid",'error');
		}
	}else{
		param['editstatus']='add_new';
	}
	
	$.post(host+'backoffice-form/'+mod,param,function(r){
		windowForm(r,'HOMTEL Services',800,600);
	});
}

function get_form_price(sts,par_id,pr_id){
	param={};
	param['editstatus']=sts;
	param['id_parent']=par_id;
	param['id_price']=pr_id;
	if(sts=='delete'){
		$.messager.confirm('Homtel Backoffice','Anda Yakin Menghapus Data Ini ?',function(re){
			if(re){
				param['id']=pr_id;
				$.post(host+'backoffice-simpan/pricing/delete',param,function(r){
					if(r==1){
						$.messager.alert('Homtel Back-Office',"Data Was Deleted ",'info');
						get_detil('pricing_detil',id_services);
					}else{
						$.messager.alert('Homtel Back-Office',"Failed Deleted ",'error');
					}
				});
			}
		});
		return false;
		
	}
	$.post(host+'backoffice-form/pricing',param,function(r){
		windowForm(r,'HOMTEL Services',800,500);
	});
}

function simpan_form(id_form,id_cancel,msg){
	if ($('#'+id_form).form('validate')){
		submit_form(id_form,function(r){
			console.log(r)
			if(r==1){
				$.messager.alert('Homtel Back-Office',msg,'info');
				$('#'+id_cancel).trigger('click');
				grid_nya.datagrid('reload');
			}else{
				console.log(r);
				$.messager.alert('Homtel Back-Office',"Tdak Dapat Menyimpan Data",'error');
			}
		});
	}else{
		$.messager.alert('Homtel Back-Office',"Isi Data Yang Kosong ",'info');
	}
}
var div_id_plan_acak;
function get_form_plan(mod,id,jml_row,acak,par1){
	div_id_plan_acak=acak;
	param={};
	if(mod=='planning'){
		param['id_detil_trans']=id;
		param['jml_row']=jml_row;
		param['mod']='planning_detil';
	}else if(mod=='planning_package'){
		param['id_detil_trans']=id;
		param['id_header']=par1;
		param['jml_row']=jml_row;
		param['mod']='planning_package_detil';
	}else if(mod=='planning_package_own'){
		param['id_detil_trans']=id;
		param['id_header']=par1;
		param['jml_row']=jml_row;
		param['mod']='planning_package_own_detil';
	}
	else{
		param['id']=id;
		param['mod']='package_item';
	}
	$('#form_plan_'+acak).empty().addClass('loading');
	$.post(host+'backoffice-GetDetil',param,function(r){
		$('#form_plan_'+acak).html(r).removeClass('loading');
	});
	console.log(id,jml_row)
}
function get_form(mod,sts,id,par1,par2){
	param={};
	if(sts=='edit_flag'){param['editstatus']='edit';}else{param['editstatus']=sts;}
	var width_na=800;
	var height_na=350;
	switch(mod){
		case "planning":
		case "planning_package":
			param['id']=id;
			if(sts!='edit_flag'){
				param['detil_id']=detil_id;
				
				param['total_row']=total_row_plan;
				if(mod=='planning_package')param['header_id']=header_id;
			}
			else param['flag']='F';
		break;
		case "package":
			param['services_id']=par1;
			param['id']=id;
		break;
		case "package_item":
			//param['services_id']=par1;
			param['id_header']=par1;
			param['id']=id;
		break;
		case "reservation":
			//param['services_id']=par1;
			width_na=800;
			height_na=550;
			if(sts=='add'){
				param['id_detil']=par1;
				param['id_trans']=id;
				param['id_pack_header']=par2;
			}else{
				param['id']=id;
			}
		break;
	}
	if(sts=='delete'){
		$.messager.confirm('Homtel Backoffice','Are You Sure Delete This Data?',function(re){
			if(re){
				$.post(host+'backoffice-simpan/'+mod+'/'+sts,param,function(r){
					if(r==1){
						$.messager.alert('Homtel Back-Office',"Data Was Deleted ",'info');
						switch(mod){
							case "planning":get_form_plan('planning',detil_id,total_row_plan,div_id_plan_acak);break;
							case "planning_package":get_form_plan('planning_package',detil_id,total_row_plan,div_id_plan_acak,header_id);break;
							case "package":get_detil('package_detil',par1);break;
							case "package_item":get_form_plan('package',par1,'',div_id_plan_acak);break;
						}
					}else{
						$.messager.alert('Homtel Back-Office',"Failed Deleted ",'error');
					}
				});
			}
		});
		return false;
		
	}
	if(sts=='edit_flag'){
		$.messager.confirm('Homtel Backoffice','Are You Sure ?',function(re){
			if(re){
				$.post(host+'backoffice-simpan/'+mod+'/edit',param,function(r){
					if(r==1){
						$.messager.alert('Homtel Back-Office',"Data Updated ",'info');
						switch(mod){
							case "planning":get_form_plan('planning',detil_id,total_row_plan,div_id_plan_acak);break;
							case "planning_package":get_form_plan('planning_package',detil_id,total_row_plan,div_id_plan_acak,header_id);break;
						}
					}else{
						$.messager.alert('Homtel Back-Office',"Failed Updated ",'error');
					}
				});
			}
		});
		return false;
		
	}
	$.post(host+'backoffice-form/'+mod,param,function(r){
			windowForm(r,'HOMTEL Services',width_na,height_na);
	});
}
function formatDate(date) {
	var bulan=date.getMonth() +1;
	var tgl=date.getDate();
	if(bulan < 10){
		bulan='0'+bulan;
	}
	
	if(tgl < 10){
		tgl='0'+tgl;
	}
	return date.getFullYear() + "-" + bulan + "-" + tgl;
}	
function set_flag(mod,msgr,id,msg,act){
	var param={};
	switch (mod){
		case "flag_confirm":
		case "flag_confirm_cancel":
			if(mod=='flag_confirm_cancel')param['flag']='C';
			else param['flag']='F';
			param['id']=id;
			mod="confirmation_pay";
		break;
		case "flag_confirm_pack":
		case "flag_confirm_pack_cancel":
			if(mod=='flag_confirm_pack_cancel')param['flag']='C';
			else param['flag']='F';
			param['id']=id;
			mod="confirmation_pay_pack";
		break;
	}
	if(msgr==true){
		$.messager.confirm('Homtel Backoffice','Are You Sure ?',function(re){
			if(re){
				$.post(host+'Backoffice-Status/'+mod,param,function(r){
					if(r==1){
						$.messager.alert('Homtel Back-Office',msg,'info');
						$('#'+act).trigger('click');
						grid_nya.datagrid('reload');
					}else{
						$.messager.alert('Homtel Back-Office','Failed Update','error');
					}
				});
			}
		});
	}else{
		$.post(host+'Backoffice-Status/'+mod,param,function(r){
			if(r==1){
				$.messager.alert('Homtel Back-Office',msg,'info');
				$('#'+act).trigger('click');
				grid_nya.datagrid('reload');
			}else{
				$.messager.alert('Homtel Back-Office','Failed Update','error');
			}
		});
	}
}
function get_report(mod,acak){
	var param={};
	switch (mod){
		case "report_inv_paid":
		case "report_inv_unpaid":
			param['start_date']=$('#start_date_'+acak).datebox('getValue');
			param['end_date']=$('#end_date_'+acak).datebox('getValue');
			param['type_trans']=$('#type_transaction_'+acak).val();
		break;
	}
	$('#isi_report_'+acak).addClass('loading').html('');
	$.post(host+'Backoffice-Report/'+mod,param,function(r){
		$('#isi_report_'+acak).removeClass('loading').html(r)
	});
	
	
}
function myparser(s){
    if (!s) return new Date();
    var ss = (s.split('-'));
    var y = parseInt(ss[0],10);
    var m = parseInt(ss[1],10);
    var d = parseInt(ss[2],10);
    if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
        return new Date(y,m-1,d);
    } else {
        return new Date();
    }
}
function get_kalender(id_trans,id_detil,acak){
	$('#isi_tab_'+acak).html('').addClass('loading');
	$.post(host+'backoffice-GetDetil',{ 'id_trans':id_trans,'id_detil':id_detil,mod:'kalender_reservasi' },function(r){
		$('#isi_tab_'+acak).removeClass('loading').html(r);
	});
}
function gen_kalender(id_div,height,data_kalender,mulai){
	$('#'+id_div).fullCalendar({
		height: height,
        header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,listMonth'
			},
			
			defaultDate: mulai,
			navLinks: true, // can click day/week names to navigate views
			selectable: true,
			selectHelper: true,
			/*views: {
				listDay: { buttonText: 'list day' },
				listWeek: { buttonText: 'list week' }
			},*/
			/*select: function(start, end) {
				var title = prompt('Event Title:');
				var eventData;
				if (title) {
					eventData = {
						title: title,
						start: start,
						end: end
					};
					//$('#cek_in_{$acak}').fullCalendar('renderEvent', eventData, true); // stick? = true
				}
				//$('#cek_in_{$acak}').fullCalendar('unselect');
			},
			*/
			editable: true,
			
			eventLimit: true, // allow "more" link when too many events
			events: data_kalender,
			eventClick: function(calEvent, jsEvent, view) {
				console.log(calEvent);
				if(calEvent.flag_set=='on'){
					get_form('reservation','edit',calEvent.id_na);
				}else{
					$.messager.alert('Homtel Back-Office','Not For Sale','info');
				}
				//console.log(jsEvent);
				//console.log(view);
				//console.log(calEvent.title);
				
				/*alert('Event: ' + calEvent.title);
				alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
				alert('View: ' + view.name);

				// change the border color just for fun
				$(this).css('border-color', 'red');
				*/

			}
    })
}