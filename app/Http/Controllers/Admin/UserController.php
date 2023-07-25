<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\UserServiceInterface;
use App\Utilities\Common;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = $this->userService->searchAndPaginate('name', $request->get('search'));

        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if($request->get('password') != $request->get('password_confirmation')){
            return back()->with('notification', 'ERROR: Confirm password does not match');
        }

        $data = $request->all();
        $data['password'] = bcrypt($request->get('password'));

        // Xử lí file:
        if($request->hasFile('image')){
            $data['avatar'] = Common::uploadFile($request->file('image'),'front/img/user');
        }

        $user = $this->userService->create($data);

        return redirect('admin/user/' . $user->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('admin.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->all();

        // Xử lí mật khẩu
        if ($request->get('password') != null) {
            if ($request->get('password') != $request->get('password_confirmation')) {
                return back()->with('notification', 'ERROR: Confirm password oes not match');
            }

            $data['password'] = bcrypt($request->get('password'));
        }else{
            unset($data['password']);
        }
        // Xử lí file ảnh
        if ($request->hasFile('image')){
            //Thêm file mới:
            $data['avatar'] = Common::uploadFile($request->file('image'),'front/img/user');

            // Xoá file cũ:
            $file_name_old = $request->get('image_old');
            if ($file_name_old != ''){
                unlink('front/img/user' . $file_name_old);
            }
        }

        // Cập nhật dữ liệu
        $this->userService->update($data, $user->id);

        return redirect('admin/user/' . $user->id);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->userService->delete($user->id);

        //Xoá file
        $file_name = $user->avatar;
        if ($file_name != ''){
            unlink('front/img/user/' . $file_name);
        }

        return redirect('admin/user');

    }
}
