<?php
use Illuminate\Support\Facades\Config;

Route::middleware('CheckLogin')->domain(Config::get('constants.ADMIN_URL'))->namespace('Admin')->group(function () {

    /*管理员列表*/
    Route::get('/admin/index' , 'AdminController@index')->name('admin.index');

    /*管理员编辑*/
    Route::get('/admin/editor' , 'AdminController@editorAdmin')->name('admin.editor');

    /*管理员添加*/
    Route::get('/admin/add-admin' , 'AdminController@editorAdmin')->name('admin.add-admin');

    /*管理员状态*/
    Route::get('/admin/admin-status' , 'AdminController@adminStatus')->name('admin.admin-status');

    Route::post('/admin/update-data' , 'AdminController@adminUpdateData')->name('admin.update-data');

    /*管理员用户组*/
    Route::get('/admin/admin-group' , 'AdminController@adminGroup')->name('admin.admin-group');

    /*用户组编辑*/
    Route::get('/admin/add-group' , 'AdminController@groupEditor')->name('admin.add-group');

    Route::post('/admin/group-created' , 'AdminController@groupCreated')->name('admin.group-created');


    /*导航列表*/
    Route::get('/admin/rule-route' , 'AdminController@ruleRoute')->name('admin.rule-route');

    /*导航编辑页面*/
    Route::get('/admin/navigation-editor' , 'AdminController@navigationEditor')->name('admin.navigation-editor');

    /*导航保存*/
    Route::post('/admin/navigation-update' , 'AdminController@navigationUpdate')->name('admin.navigation-update');


    /*路由列表*/
    Route::get('/admin/route-list' , 'AdminController@routeList')->name('admin.route-list');

    /*路由详情页面*/
    Route::get('/admin/route-editor' , 'AdminController@routeEditor')->name('admin.route-editor');

    /*路由编辑操作*/
    Route::post('/admin/editor-rule-data' , 'AdminController@editorRuleData')->name('admin.editor-rule-data');


    Route::get('/admin/authorization' , 'AdminController@authorization')->name('admin.authorization');

    Route::post('/admin/group-rule-data' , 'AdminController@groupRuleData')->name('admin.group-rule-data');
    
    //错误路由
    Route::get('/admin/rule-error' , 'AdminController@ruleErrors')->name('admin.rule-error');

});
