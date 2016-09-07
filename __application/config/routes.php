<?php defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'backend';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Routing Core
$route['backoffice'] = 'backend';
$route['backoffice-masuk'] = 'login';
$route['backoffice-keluar'] = 'login/logout';
$route['Backoffice-Grid/(:any)'] = 'backend/get_grid/$1';
$route['backoffice-form/(:any)'] = 'backend/get_form/$1';
$route['backoffice-Data/(:any)'] = 'backend/getdata/$1';
$route['backoffice-GetDetil'] = 'backend/get_konten';
$route['backoffice-simpan/(:any)/(:any)'] = 'backend/simpandata/$1/$2';
// Modul Kasir
$route['beranda'] = 'backend/modul/beranda/main';



/*
$route['backoffice-combo'] = 'backend/get_combo';
$route['backoffice-simpan/(:any)/(:any)'] = 'backend/simpandata/$1/$2';
$route['backoffice-delete'] = 'backend/simpandata';
$route['backoffice-upload'] = 'backend/upload';
$route['backoffice-hapusFile'] = 'backend/hapus_file';
$route['backoffice-GetDetil'] = 'backend/get_konten';
$route['backoffice-Cetak'] = 'backend/cetak';
$route['backoffice-SetFlag'] = 'backend/set_flag';
$route['backoffice-Dashboard'] = 'backend/get_konten';
$route['backoffice-GetDataChart'] = 'backend/get_chart';
$route['backoffice-laporan/(:any)'] = 'backend/get_form/$1';

$route['kasir-lantai'] = 'backend/modul/kasir/kasir_lantai';
$route['detail-meja'] = 'backend/modul/kasir/detail_meja';
$route['detail-meja'] = 'backend/modul/kasir/detail_meja';
$route['trx-penjualan'] = "backend/simpandata/transaksi_penjualan";
$route['hapus-item'] = "backend/simpandata/hapus_item_kasir";
$route['tutup-transaksi'] = "backend/simpandata/tutup_transaksi";
$route['total-pesanan'] = "backend/modul/kasir/total_per_meja";
$route['selesai-transaksi'] = "backend/modul/kasir/selesai_transaksi";

*/

/* Routes Front End Routes */



