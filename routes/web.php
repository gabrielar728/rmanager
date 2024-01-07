<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::post('/user/logout', 'Auth\LoginController@logoutUser')->name('user.logout');

Route::group(['middleware' => 'web'], function () {
// materials
    Route::get('/materiale/adaugare', [
        'uses' => 'MaterialsController@getManageMaterial',
        'as' => 'administrare-materiale',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Magazie', 'Ingineri']]);

    Route::get('/materiale/{id}/editare', [
        'uses' => 'MaterialsController@edit',
        'as' => 'materials.edit',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri', 'Magazie']]);

    Route::patch('/materiale/actualizare/{id}', [
        'uses' => 'MaterialsController@update',
        'as' => 'materials.update',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri', 'Magazie']]);

    Route::post('/stocare-materiale', [
        'uses' => 'MaterialsController@createMaterial',
        'as' => 'createMaterial',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Magazie', 'Ingineri']]);

    Route::get('/afisare-materiale', [
        'uses' => 'MaterialsController@showMaterialInformation',
        'as' => 'showMaterialInformation',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Magazie', 'Ingineri']]);

// articles
    Route::get('/articole/adaugare', [
        'uses' => 'ArticlesController@create',
        'as' => 'creare_articol',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/articole/adaugare/modal_categorie', [
        'uses' => 'ArticlesController@postInsertArticleCategory',
        'as' => 'postInsertArticleCategory',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/articole/adaugare/modal_client', [
        'uses' => 'ArticlesController@postInsertClient',
        'as' => 'postInsertClient',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/articole/adaugare/modal_proces', [
        'uses' => 'ArticlesController@postInsertProcess',
        'as' => 'postInsertProcess',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/articole/adaugare/stocare', [
        'uses' => 'ArticlesController@insert',
        'as' => 'articles-insert',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/articole-editare/{id}', [
        'uses' => 'ArticlesController@edit',
        'as' => 'articles.edit',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/articole/actualizare/{id}', [
        'uses' => 'ArticlesController@update',
        'as' => 'articles.update',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/articole/adaugare/stergeCategorie', [
        'uses' => 'ArticlesController@deleteCategory',
        'as' => 'deleteCategory',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/articole/adaugare/stergeClient', [
        'uses' => 'ArticlesController@deleteClient',
        'as' => 'deleteClient',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);


    Route::get('/articole-informatii-rapoarte', [
        'uses' => 'ArticlesController@reportsIndex',
        'as' => 'informatii-rapoarte-articole',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/articole-informatii-rapoarte/ajax', [
        'uses' => 'ArticlesController@getdata',
        'as' => 'ajaxdata.getdataArticles',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);


    Route::get('/cautaUnitate', [
        'uses' => 'ArticlesController@findUnit',
        'as' => 'findUnit',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/articole/status-activ', [
        'uses' => 'ArticlesController@changeActiveStatus',
        'as' => 'changeArticleActiveStatus',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/articole/status-inactiv', [
        'uses' => 'ArticlesController@changeInactiveStatus',
        'as' => 'changeArticleInactiveStatus',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/articole/verificare-nume', [
        'uses' => 'ArticlesController@verifName',
        'as' => 'verifName',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/articole/printare-articol/{id}', [
        'uses' => 'ArticlesController@printArticle',
        'as' => 'printArticle',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);
//groups
    Route::get('/grupuri/adaugare', [
        'uses' => 'GroupsController@index',
        'as' => 'groups.index',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/grupuri/selectare_articole', [
        'uses' => 'GroupsController@articles_selected',
        'as' => 'articles_selected',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/stocare-grup', [
        'uses' => 'GroupsController@createGroup',
        'as' => 'createGroup',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/afisare-grupuri', [
        'uses' => 'GroupsController@showGroupInformation',
        'as' => 'showGroupInformation',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/grupuri/status-activ/{group_id}', [
        'uses' => 'GroupsController@changeActiveStatus',
        'as' => 'changeGroupActiveStatus',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/grupuri/status-inactiv/{group_id}', [
        'uses' => 'GroupsController@changeInactiveStatus',
        'as' => 'changeGroupInactiveStatus',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/grupuri/sortare-articole', [
        'uses' => 'GroupsController@GroupSort',
        'as' => 'group.sorting',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/grupuri/salvare-sortare-articole', [
        'uses' => 'GroupsController@saveSorting',
        'as' => 'group.saveSorting',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/grupuri/verificare-nume', [
        'uses' => 'GroupsController@verifGroupName',
        'as' => 'verifGroupName',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

// products
    Route::get('/productie/lansare', [
        'uses' => 'ProductsController@index',
        'as' => 'lansare.index',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/productie/afisare/grupuri', [
        'uses' => 'ProductsController@showGroups',
        'as' => 'showGroups',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/productie/afisare/articole', [
        'uses' => 'ProductsController@showArticles',
        'as' => 'showArticle',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/productie/afisare/grupuri&articole', [
        'uses' => 'ProductsController@showGroupsArticles',
        'as' => 'showGroupsArticles',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/productie/lansare/stocare-produse/{item_name}', [
        'uses' => 'ProductsController@createProduct',
        'as' => 'createProduct',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/productie/lansare/asignare-lucrator', [
        'uses' => 'ProductsController@assignedTo',
        'as' => 'assignedTo',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/productie/lansare/data-scadenta', [
        'uses' => 'ProductsController@productionDate',
        'as' => 'productionDate',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/productie/lansare/afisari-produse', [
        'uses' => 'ProductsController@showProductInformation',
        'as' => 'showProductInformation',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/productie/informatii-produse', [
        'uses' => 'ProductsController@reports',
        'as' => 'informatii-produse',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::any('/productie/afisare-articol', [
        'uses' => 'ProductsController@getArticles',
        'as' => 'getArticles',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/productie/{id}/anulare', [
        'uses' => 'ProductsController@cancel',
        'as' => 'products.cancel',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/informatii-rapoarte-produse', [
        'uses' => 'ReportsProductsController@indexReports',
        'as' => 'informatii-rapoarte-produse',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/informatii-rapoarte-produse/ajax', [
        'uses' => 'ReportsProductsController@getdata',
        'as' => 'ajaxdata.getdata',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/produse/printare-produs/{id}', [
        'uses' => 'ReportsProductsController@printProduct',
        'as' => 'printProduct',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/productie/informatii-rapoarte-produse/finalizare-produs/{id}', [
        'uses' => 'ReportsProductsController@finishProductApp',
        'as' => 'finishProductApp',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

// pumps
    Route::get('/pompe/adaugare', [
        'uses' => 'PumpsController@getManagePumps',
        'as' => 'administrare-pompe',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/pompe/stocare', [
        'uses' => 'PumpsController@createPump',
        'as' => 'createPump',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/pompe/afisare', [
        'uses' => 'PumpsController@showPumpInformation',
        'as' => 'showPumpInformation',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::get('/pompe/{id}/editare', [
        'uses' => 'PumpsController@edit',
        'as' => 'pumps.edit',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::patch('/pompe/actualizare/{id}', [
        'uses' => 'PumpsController@update',
        'as' => 'pumps.update',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

// workers
    Route::get('/personal/adaugare', [
        'uses' => 'WorkersController@getManageWorkers',
        'as' => 'administrare-personal',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri', 'HR']]);

    Route::post('/personal/stocare', [
        'uses' => 'WorkersController@createWorker',
        'as' => 'createWorker',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri', 'HR']]);

    Route::get('/personal/afisare', [
        'uses' => 'WorkersController@showWorkerInformation',
        'as' => 'showWorkerInformation',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri', 'HR']]);

    Route::get('/personal/{id}/editare', [
        'uses' => 'WorkersController@edit',
        'as' => 'workers.edit',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri', 'HR']]);

    Route::patch('/personal/actualizare/{id}', [
        'uses' => 'WorkersController@update',
        'as' => 'workers.update',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri', 'HR']]);

    Route::post('/personal/verificare-card', [
        'uses' => 'WorkersController@verifCard',
        'as' => 'verifCard',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri', 'HR']]);

    Route::get('/personal/schimbare-status', [
        'uses' => 'WorkersController@workerStatus',
        'as' => 'workerStatus',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri', 'HR']]);
// users
    Route::get('utilizatori/adaugare', [
        'uses' => 'UsersController@addUsers',
        'as' => 'adaugare-utilizatori',
        'middleware' => 'roles',
        'roles' => ['Admin']]);

    Route::post('/utilizatori/stocare', [
        'uses' => 'UsersController@createUser',
        'as' => 'createUser',
        'middleware' => 'roles',
        'roles' => ['Admin']]);

    Route::get('/acordare-permisii', [
        'uses' => 'UsersController@viewPermission',
        'as' => 'acordare-permisii',
        'middleware' => 'roles',
        'roles' => ['Admin']]);

    Route::post('/assign-roles', [
        'uses' => 'UsersController@postAdminAssignRoles',
        'as' => 'admin.assign',
        'middleware' => 'roles',
        'roles' => ['Admin']]);

    Route::get('/utilizatori/{id}/editare', [
        'uses' => 'UsersController@edit',
        'as' => 'users.edit',
        'middleware' => 'roles',
        'roles' => ['Admin']]);

    Route::patch('/utilizatori/actualizare/{id}', [
        'uses' => 'UsersController@update',
        'as' => 'users.update',
        'middleware' => 'roles',
        'roles' => ['Admin']]);

// Backup routes
    Route::get('backup', [
        'uses' => 'BackupsController@index',
        'as' => 'backup.index',
        'middleware' => 'roles',
        'roles' => ['Admin']]);
    Route::get('backup/create', [
        'uses' => 'BackupsController@create',
        'as' => 'backup.create',
        'middleware' => 'roles',
        'roles' => ['Admin']]);
    Route::get('backup/download/{file_name}', [
        'uses' => 'BackupsController@download',
        'as' => 'backupDownload',
        'middleware' => 'roles',
        'roles' => ['Admin']]);
    Route::get('backup/delete/{file_name}', [
        'uses' => 'BackupsController@delete',
        'as' => 'backupDelete',
        'middleware' => 'roles',
        'roles' => ['Admin']]);


// rulaje iesiri
    Route::get('/rulaje-iesiri', [
        'uses' => 'InventoriesController@getManageOut',
        'as' => 'administrare-rulaje-iesiri',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Magazie', 'Ingineri']]);

    Route::get('/afisare/rulaje-iesiri', [
        'uses' => 'InventoriesController@showOutInformation',
        'as' => 'showOutInformation',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Magazie', 'Ingineri']]);

//Raportare Zilnica
    Route::get('/raportare-zilnica', [
        'uses' => 'DailyReportsController@index',
        'as' => 'raportare-zilnica',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

    Route::post('/raportare-zilnica/ajax', [
        'uses' => 'DailyReportsController@getdata',
        'as' => 'ajaxdata.getdataDailyReports',
        'middleware' => 'roles',
        'roles' => ['Admin', 'Ingineri']]);

// raspberryPi
    Route::prefix('productie')->group(function() {
        Route::get('/', 'RaspberryPiController@index')->name('raspberryPiWorker.home');
        Route::get('/login', 'AuthRaspberryPiWorker\LoginController@showLoginForm')->name('raspberryPiWorker.login');
        Route::post('/login', 'AuthRaspberryPiWorker\LoginController@login')->name('raspberryPiWorker.login.submit');
        Route::get('/selectare-lucratori/{id}/{worker_id}', 'RaspberryPiController@selectWorkers')->name('selectWorkers');
        Route::post('/selectare-muncitori/{id}/{worker_id}', 'RaspberryPiController@addSelectedWorkers')->name('addSelectedWorkers');
        Route::get('/dozare-produs/{id}/{worker_id}', 'RaspberryPiController@productDosage')->name('productDosage');
        Route::get('/{id}/completare/{worker_id}', 'RaspberryPiController@moreResin')->name('moreResin');
        Route::post('/{id}/completare', 'RaspberryPiController@productExtraDosage')->name('productExtraDosage');
        Route::get('/produs/{id}/finalizare', 'RaspberryPiController@finishProduct')->name('finishProduct');
        Route::post('/logout', 'AuthRaspberryPiWorker\LoginController@logout')->name('raspberryPiWorker.logout');
    });


});








