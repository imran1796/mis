<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Hash;
use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class UserController extends Controller

{
    protected $userService;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $users = $this->userService->getAllUsers();
        return view('users.index', compact('users'));
    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {
        $roles = Role::where('name', '!=', 'System Admin')->pluck('name', 'name')->all();
        // $branches = Branch::all();
        // $departments = Department::all();
        // $designations = Designation::all();
        // $principles = Principle::all();


        // if (Auth::user()->getRoleNames()->toArray()[0] == 'System Admin') {
        //     $companies = Company::with('principles')->get();
        // } else {
        //     $companies = Company::with('principles')->where('id', Auth::user()->company_id)->get();
        // }


        return view('users.create', compact('roles'));
    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

        // dd($request->roles);
        \DB::beginTransaction();
        try {
            $input = $request->all();
            // $verificationPassword = $input['password'];
            $input['password'] = Hash::make($input['password']);



            $user = User::create($input);
            $user->assignRole($request->input('roles'));
            // $user->notify(new CustomVerifyEmail([$user->name, $user->email, $verificationPassword]));
            
            \DB::commit();

            return response()->json(['success' => 'Successfully Created User'], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error creating User: ' . $e->getMessage());
            return response()->json(['error' => "Error creating user: " . $e->getMessage()], 500);
        }
    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

     public function edit($id)
     {
         $user = User::findOrFail($id);
         $roles = Role::where('name', '!=', 'System Admin')->pluck('name', 'name')->all();
         $userRoles = $user->roles->pluck('name')->toArray();
     
         return view('users.edit', compact('user', 'roles', 'userRoles'));
     }
     



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

     public function update(Request $request, $id)
     {
         $this->validate($request, [
             'name' => 'required',
             'email' => 'required|email|unique:users,email,' . $id,
             'password' => 'same:confirm-password',
             'roles' => 'required'
         ]);
     
         \DB::beginTransaction();
         try {
             $user = User::findOrFail($id);
     
             // Only update password if provided
             $input = $request->all();
             if (!empty($input['password'])) {
                 $input['password'] = Hash::make($input['password']);
             } else {
                 unset($input['password']);
             }
     
             $user->update($input);
     
             // Update user roles
             $user->syncRoles($request->input('roles'));
     
             \DB::commit();
             return response()->json(['success' => 'Successfully Updated User'], 200);
     
         } catch (\Exception $e) {
             \DB::rollBack();
             Log::error('Error updating User: ' . $e->getMessage());
             return response()->json(['error' => "Error updating user: " . $e->getMessage()], 500);
         }
     }
     



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
        User::find($id)->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }

    public function verifyUser($id)
    {
        $user = User::findOrFail($id);

        $user->email_verified_at = now();
        $user->save();

        return redirect()->back()->with('success', 'User Verified Successfully.');
    }
}
