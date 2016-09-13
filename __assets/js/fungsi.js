$(function() {
	loadUrl(host+'beranda');
});


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
		lebarnya = getClientWidth-250;
	}
	if(tingginya == undefined){
		tingginya = getClientHeight-300
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
	
	switch(modnya){
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
			param['mod']=modnya;
			judulnya = "";
			urlnya = "services";
			fitnya = true;
			urlglobal = host+'backoffice-Data/'+urlnya;
			kolom[modnya] = [	
				{field:'id',title:'Set Pricing',width:100, halign:'center',align:'center',
					formatter:function(value,rowData,rowIndex){
						return '<a href="javascript:void(0);" class="btn btn-small btn-info no-radius" onclick="get_detil(\''+modnya+'\','+value+')">Set</a>';
					}
				},
				{field:'code',title:'Code',width:80, halign:'center',align:'left'},
				{field:'services_name',title:'Services Name',width:200, halign:'center',align:'left'},
				{field:'desc_services_eng',title:'Desc. Eng',width:350, halign:'center',align:'left'},
				{field:'desc_services_ind',title:'Desc. Ind',width:350, halign:'center',align:'left'}
			]
		break;
	}
	
	grid_nya=$("#"+divnya).datagrid({
		title:judulnya,
        height:tingginya,
        width:lebarnya,
		rownumbers:true,
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


function genform(type, modulnya, submodulnya, stswindow, tabel){
	var urlpost = host+'backend/get_form/'+submodulnya+'/form';
	var urldelete = host+'backend/cruddata/'+submodulnya;
	var id_tambahan = "";
	
	switch(submodulnya){
		case "wartakomisi":
			table = "warkom";
			urlpost = host+'backend/getdisplay/get-form/'+submodulnya;
		break;
		case "kebaktianminggu":
			table = "kebming";
			urlpost = host+'backend/getdisplay/get-form/'+submodulnya;
		break;
		case "renunganwarta":
			table = "renwar";
			urlpost = host+'backend/getdisplay/get-form/'+submodulnya;
		break;
		case "renunganharian":
			table = "rema";
			urlpost = host+'backend/getdisplay/get-form/'+submodulnya;
		break;
		case "artikelrohani":
			table = "artiro";
			urlpost = host+'backend/getdisplay/get-form/'+submodulnya;
		break;
		case "sliderberanda":
			table = "sliben";
			urlpost = host+'backend/getdisplay/get-form/'+submodulnya;
		break;
		case "komisikombas":
			table = "kokom";
			urlpost = host+'backend/getdisplay/get-form/'+submodulnya;
		break;
	}
	
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
		
			var row = $("#grid_"+submodulnya).datagrid('getSelected');
			if(row){
				if(type=='edit'){
					if(stswindow == undefined){
						$('#grid_nya_'+submodulnya).hide();
						$('#detil_nya_'+submodulnya).show().addClass("loading");	
					}
					$.post(urlpost, { 'editstatus':'edit', id:row.id, 'ts':table, 'submodul':submodulnya, 'bulan':row.bulan, 'tahun':row.tahun, 'id_tambahan':id_tambahan }, function(resp){
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
					$.messager.confirm('JResto Soft','Anda Yakin Menghapus Data Ini ?',function(re){
						if(re){
							loadingna();
							$.post(urldelete, {id:row.id, 'sts_crud':'delete'}, function(r){
								if(r==1){
									winLoadingClose();
									$.messager.alert('JResto Soft',"Data Terhapus",'info');
									$('#grid_'+submodulnya).datagrid('reload');								
								}else{
									winLoadingClose();
									console.log(r)
									$.messager.alert('JResto Soft',"Gagal Menghapus Data",'error');
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
		
		default:
			$('#grid_nya_'+mod).hide();
			$('#detil_nya_'+mod).html('').show().addClass("loading");
			$.post(host+'backoffice-GetDetil',{mod:mod,id:id_data},function(r){
				$('#detil_nya_'+mod).html(r).removeClass("loading");
			});
		break;
	}
	
}
