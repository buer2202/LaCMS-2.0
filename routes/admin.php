<?php
// 栏目
Route::patch('category/status/{id}', 'CategoryController@setStatus')->name('admin.category.status');
Route::patch('category/navshow/{id}', 'CategoryController@setNavshow')->name('admin.category.navshow');

Route::get('category/document/{id}', 'CategoryController@document')->name('admin.category.document');
Route::post('category/setdocument/{id}', 'CategoryController@setDocument')->name('admin.category.setDocument');

Route::resource('category', 'CategoryController', ['as' => 'admin', 'except' => ['create', 'edit', 'destroy']]);

// 文档
Route::patch('document/setfield/{id}', 'DocumentController@setField')->name('admin.document.setfield');
Route::delete('document/deleterelation/{id}', 'DocumentController@deleteRelation')->name('admin.document.deleteRelation');
Route::resource('document', 'DocumentController', ['as' => 'admin', 'except' => ['show', 'destroy']]);

// ueditor接口
Route::any('ueditor', 'UeditorController@api')->name('admin.ueditor');

// 附件
Route::delete('attachment/clearinvalidrelation', 'AttachmentController@clearInvalidRelation')->name('admin.attachment.clearInvalidRelation');
Route::resource('attachment', 'AttachmentController', ['as' => 'admin', 'only' => ['index', 'update', 'destroy']]);

// 管理员
Route::get('administrator/resetpassword', 'AdministratorController@resetPassword')->name('admin.administrator.resetPassword');
Route::patch('administrator/updatepassword', 'AdministratorController@updatePassword')->name('admin.administrator.updatePassword');
Route::resource('administrator', 'AdministratorController', ['as' => 'admin']);
