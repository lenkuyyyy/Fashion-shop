<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreUserRequest; 
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{

    // Hiển thị danh sách người dùng, có thể tìm kiếm theo tên hoặc email
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::whereIn('status', ['active', 'inactive'])
            ->whereDoesntHave('role', function ($query) {
                $query->where('name', 'admin'); // loại bỏ admin
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.users', compact('users', 'search'));
    }



    // Trả về view để hiển thị form tạo người dùng mới
    public function create()
    {
        
        $roles = \App\Models\Role::all(); // Lấy danh sách tất cả vai trò
        return view('admin.users.create', compact('roles'));
    }

    // Xử lý lưu người dùng mới vào database
    public function store(StoreUserRequest $request)
    {
        $data = $request->only(['name', 'email', 'phone_number', 'address', 'status', 'role_id']);
        $data['password'] = bcrypt($request->password); // Mã hóa mật khẩu


        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được tạo!');
    }

    // Hiển thị chi tiết người dùng
    public function show(User $user)
    {
       
    }

    // Trả về view để chỉnh sửa thông tin người dùng
    public function edit(User $user)
    {
        $roles = \App\Models\Role::all(); 
        return view('admin.users.update', compact('user', 'roles'));
    }

    // Xử lý cập nhật thông tin người dùng
    public function update(UpdateUserRequest $request, User $user)
    {
        // Lấy các trường cần cập nhật
        $data = $request->only(['name', 'email', 'phone_number', 'address', 'status', 'role_id']);


        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được cập nhật!');
    }

    public function destroy(User $user)
    {
        // Không cho phép cấm admin
        if ($user->role && $user->role->name === 'admin') {
            return redirect()->route('admin.users.index')->with('error', 'Không thể cấm quản trị viên');
        }

        // Đổi trạng thái sang 'banned'
        $user->status = 'banned';
        $user->save();

        // Xóa mềm
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã bị cấm!');
    }
    
    public function banned(Request $request): View
    {
        $query = User::onlyTrashed() // lấy user bị xóa mềm
            ->whereHas('role', function ($q) {
                $q->where('name', '!=', 'admin'); 
            });

        // Nếu có tham số tìm kiếm (theo name hoặc email)
        if ($request->has('q') && !empty($request->q)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        $bannedUsers = $query->orderBy('deleted_at', 'desc')->paginate(10);

        return view('admin.users.banned', compact('bannedUsers'));
    }
    // Khôi phục
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore(); 
        // Cập nhật trạng thái về 'active'
        $user->status = 'active';
        $user->save();

        return redirect()->route('admin.users.banned')->with('success', 'Người dùng đã được khôi phục thành công!');
    }


    // Xóa vĩnh viễn
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        return redirect()->route('admin.users.banned')->with('success', 'Đã xóa vĩnh viễn người dùng!');
    }

}
