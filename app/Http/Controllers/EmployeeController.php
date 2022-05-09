<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class EmployeeController extends Controller
{
    function index()
    {
        return view('index');
    }

    function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required | image | mimes:jpeg,jpg,png',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages()
            ]);
        } else {
            $employee = new Employee;
            $employee->name = $request->input('name');

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $fileName = time() . '.' . $ext;
                $file->move('uploads/', $fileName);
                $employee->image = $fileName;
            }
            $employee->save();

            return response()->json([
                'status' => 200,
                'message' => 'added correctly',
            ]);
        }
    }

    function show()
    {
        $employee = Employee::all();
        return response()->json([
            'employee' => $employee,
        ]);
    }

    public function edit($id)
    {
        $employee = Employee::find($id);
        if ($employee) {
            return response()->json([
                'status' => 200,
                'employee' => $employee,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "employee nai",
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            $employee = Employee::find($id);
            if ($employee) {
                $employee->name = $request->input('name');
                $employee->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'updated well',
                ]);
            }
        }
    }

    public function destroy($id)
    {
        $employee = Employee::find($id);
        $oldImg = 'uploads/' . $employee->image;
        if (File::exists($oldImg)) {
            File::delete($oldImg);
        }
        $employee->delete();

        return response()->json([
            'status' => 200,
            'message' => 'deleted well',
        ]);
    }
}
