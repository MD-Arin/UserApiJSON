<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Validator;

class UserController extends Controller
{

    public function createUser(Request $request)
    {
        /*
         * Takes in Four Parameters:
         *  First Name as first_name
         *  Second Name as second_name
         *  Email as email
         *  Password as password
         */

        $user = new User;

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'required',
            'email' => 'required|unique:users'

        ]);
        if($validator->fails())
        {
            return response()->json([$validator->errors()], 400);
        }else{
            $first_name = $request->first_name;
            $second_name = $request->last_name;

            $name = $first_name . " " . $second_name;

            $user->name = $name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->user_id = random_int(123456789,999999999);
            $user->save();

            $result = ['id' => $user->id, 'name' =>$user->name, 'email' => $user->email, 'password' => $user->password, 'user_id' => $user->user_id];
            return response()->json(["Success" => "Saved Successfully", $result], 200);
        }


    }

    public function deleteUser(Request $request)
    {
        /*
         * Takes in a 2 parameters:
         *  Email address as email
         *  Password as password
         */
        $user = new User;

        $validator = Validator::make($request->all(), [
           'email' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()], 400);
        }else{

            if(!$user->select('id')->where('email', $request->email)->value('id')){
                return response()->json(["Error" => "User Does Not Exist"], 404);
            }

            if(Hash::check($request->password, $user->select('password')->where('email', $request->email)->value('password'))){
                $id = $user->select('id')->where('email', $request->email)->value('id');

                $user->find($id)->delete();

                return response()->json(["Success" => 'User Deleted'], 200);
            }
            return response()->json(["Error" => 'Wrong User Password'], 403);
        }

    }

    public function updateUserPassword(Request $request)
    {
        $user = new User;

        $validator = Validator::make($request->all(), [
            'email' =>'required',
            'password' => 'required',
            'new_password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        if($request->password === $request->new_password){
            return response()->json(['Error' => 'New Password Cannot be the same as Old Password'], 403);
        }
        if(Hash::check($request->password, $user->select('password')->where('email', $request->email)->value('password'))){
            $user->where('email', $request->email)->update(['password' => bcrypt($request->new_password)]);

            return response()->json(['Success' => 'User Password Updated'], 200);
        }
        return response()->json(['Error' => 'Wrong User Password'], 403);
    }

    public function updateUserEmail(Request $request)
    {
        /*
         * Takes Three Parameters
         *  Email Address as email
         *  Password as password
         *  New Email Address as new_email
         */
        $user = new User;

        $validator = Validator::make($request->all(), [
           'email' => 'required',
            'password' => 'required',
            'new_email' => 'required|unique:users'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        if(Hash::check($request->password, $user->select('password')->where('email', $request->email)->value('password'))){
            $user->where('email', $request->email)->update(['email' => $request->new_email]);

            return response()->json(['Success' => 'Email Updated', 'New Email' => $user->email], 200);
        }
        return response()->json(['Error' => 'Wrong User Password'], 403);
    }

    public function updateUserName(Request $request)
    {
        /*
         * Takes a minimum of 3 parameters and a maximum of 5:
         *  Email address as email
         *  Password as password
         *  New First Name as new_first_name
         *  New Last Name as new_last_name
         *  Both New First Name and New Last Name as new_first_name and new_last_name
         */
        $user = new User;

        $validator = Validator::make($request->all(), [
           'email' => 'required',
            'password' => 'required',
            'new_first_name' => 'sometimes',
            'new_last_name' =>'sometimes'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        if($request->new_first_name === null && $request->new_last_name === null){
            return response()->json(['Error' => 'Either Fill In new_first_name or new_last_name or Both'], 403);
        }
        if(Hash::check($request->password, $user->select('password')->where('email', $request->email)->value('password'))){
            if($request->new_first_name !== null && $request->new_last_name === null){
                $name = explode(" ", $user->select('name')->where('email', $request->email)->value('name'));

                $last_name = end($name);

                $new_name = $request->new_first_name . " " .$last_name;

                $user->where('email', $request->email)->update(['name' => $new_name]);

                return response()->json(['Success' => 'First Name Updated', 'New Name' => $user->select('name')->where('email', $request->email)->value('name')], 200);
            }elseif ($request->new_first_name === null && $request->new_last_name !== null){
                $name = explode(" ", $user->select('name')->where('email', $request->email)->value('name'));

                $first_name = reset($name);

                $new_name = $first_name . " " . $request->new_last_name;

                $user->where('email', $request->email)->update(['name' => $new_name]);

                return response()->json(['Success' => 'Last Name Updated', 'New Name' => $user->select('name')->where('email', $request->email)->value('name')], 200);
            }elseif ($request->new_first_name !== null && $request->new_last_name !== null){
                $new_name = $request->new_first_name . " " . $request->new_last_name;
                $user->where('email', $request->email)->update(['name' => $new_name]);

                return response()->json(['Success' => 'Full Name Updated', 'New Name' => $user->select('name')->where('email', $request->email)->value('name')], 200);
            }
        }
        return response()->json(['Error' => 'Wrong User Password'], 403);
    }

    public function getUserByEmail(Request $request)
    {
        /*
         * Takes one parameter
         *  Email address as email
         */

        $user = new User;

        $validator = Validator::make($request->all(), [
           'email' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if($user->select('email')->where('email', $request->email)->value('email')){
            $found_user = $user->select("*")->where('email', $request->email);

            return response()->json(['Success' => 'User Found', $found_user->get()], 200);
        }
        return response()->json(['Error' => 'User Not Found'], 404);

    }



}
