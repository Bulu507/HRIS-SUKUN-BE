<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('RegistUser', 'UserController@RegistUser');
Route::post('LoginUser', 'UserController@LoginUser');
Route::post('ForgotPassword', 'UserController@ForgotPassword');

Route::get('GetApi', 'UserController@GetApi');

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('Logout', 'UserController@Logout');
    Route::post('EditProfile', 'UserController@EditProfile');
    Route::post('EditUser', 'UserController@EditUser');
    Route::post('DeleteUser', 'UserController@DeleteUser');
    Route::post('ResetPasswordUser', 'UserController@ResetPasswordUser');
    // Route::post('AddDataKaryawan', 'DataKaryawanController@AddDataKaryawan');
    // Route::post('EditDataKaryawan', 'DataKaryawanController@EditDataKaryawan');


    Route::get('GetUser', 'UserController@GetUser');
    Route::get('GetUserMenuAccess', 'UserController@GetUserMenuAccess');
    Route::post('EditMenuAccess', 'UserController@EditMenuAccess');
    Route::get('GetDataKaryawan', 'DataKaryawanController@GetDataKaryawan');


    Route::get('GetAllRole', 'SystemParameterController@GetAllRole');
    Route::get('GetAllUnitPerusahaan', 'SystemParameterController@GetAllUnitPerusahaan');
    Route::get('GetAllDivisi', 'SystemParameterController@GetAllDivisi');
    Route::get('GetAllDepartment', 'SystemParameterController@GetAllDepartment');
    Route::get('GetAllStatusKaryawan', 'SystemParameterController@GetAllStatusKaryawan');
    Route::post('AddUnitPerusahaan', 'SystemParameterController@AddUnitPerusahaan');
    Route::post('AddDivisi', 'SystemParameterController@AddDivisi');
    Route::post('AddDepartment', 'SystemParameterController@AddDepartment');
    Route::post('EditUnitPerusahaan', 'SystemParameterController@EditUnitPerusahaan');
    Route::post('EditDivisi', 'SystemParameterController@EditDivisi');
    Route::post('EditDepartment', 'SystemParameterController@EditDepartment');
    Route::post('SoftDeleteUnitPerusahaan', 'SystemParameterController@SoftDeleteUnitPerusahaan');
    Route::post('SoftDeleteDivisi', 'SystemParameterController@SoftDeleteDivisi');
    Route::post('SoftDeleteDepartment', 'SystemParameterController@SoftDeleteDepartment');

    Route::get('GetAllMenuAccess', 'SystemParameterController@GetAllMenuAccess');


    Route::post('AddEmployee', 'EmployeeController@AddEmployee');
    Route::post('EditEmployee', 'EmployeeController@EditEmployee');
    Route::get('GetAllEmployee', 'EmployeeController@GetAllEmployee');
    Route::get('GetDetailEmployeeById', 'EmployeeController@GetDetailEmployeeById');
    Route::post('DeleteEmployee', 'EmployeeController@DeleteEmployee');
    Route::get('GetAllEmployeePrintPDF', 'EmployeeController@GetAllEmployeePrintPDF');
    Route::get('GetAllEmployeePrintExcel', 'EmployeeController@GetAllEmployeePrintExcel');


    Route::get('GetRecruitment', 'RecruitmentController@GetRecruitment');
    Route::get('GetJobSeeker', 'RecruitmentController@GetJobSeeker');
    Route::get('GetSchedule', 'RecruitmentController@GetSchedule');
    Route::get('GetResult', 'RecruitmentController@GetResult');
    Route::get('GetDetailRecruitment', 'RecruitmentController@GetDetailRecruitment');
    Route::get('GetJobSeekerByRecruitment', 'RecruitmentController@GetJobSeekerByRecruitment');
    Route::get('GetJobSeekerStatus', 'RecruitmentController@GetJobSeekerStatus');
    Route::post('AddRecruitment', 'RecruitmentController@AddRecruitment');
    Route::post('UpdateRecruitment', 'RecruitmentController@UpdateRecruitment');
    Route::post('UpdateStatusRecruitment', 'RecruitmentController@UpdateStatusRecruitment');
    Route::post('DeleteRecruitment', 'RecruitmentController@DeleteRecruitment');
    Route::post('UpdateSchedule', 'RecruitmentController@UpdateSchedule');
    Route::post('UpdateResult', 'RecruitmentController@UpdateResult');
    Route::post('AddJobSeeker', 'RecruitmentController@AddJobSeeker');
    Route::post('UpdateJobSeeker', 'RecruitmentController@UpdateJobSeeker');
    Route::post('DeleteJobSeeker', 'RecruitmentController@DeleteJobSeeker');

    Route::get('GetAllHistoryAttendance', 'AttendanceController@GetAllHistoryAttendance');
    Route::get('GetHistoryAttendanceById', 'AttendanceController@GetHistoryAttendanceById');
    Route::get('GetAllEmployeePermit', 'EmployeePermitController@GetAllEmployeePermit');
    Route::get('GetEmployeePermitById', 'EmployeePermitController@GetEmployeePermitById');
    Route::get('GetAllEmployeeOvertime', 'EmployeeOvertimeController@GetAllEmployeeOvertime');
    Route::get('GetEmployeeOvertimeById', 'EmployeeOvertimeController@GetEmployeeOvertimeById');
    Route::get('GetAllDailyAttendance', 'AttendanceController@GetAllDailyAttendance');
    Route::get('GetDailyAttendanceById', 'AttendanceController@GetDailyAttendanceById');
});
