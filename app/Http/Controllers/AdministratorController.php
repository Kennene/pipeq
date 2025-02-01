<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Destination;
use App\Models\User;
use App\Models\Role;
use App\Models\TicketView;

class AdministratorController extends Controller
{
    public function index()
    {
        $destinations = Destination::all();
        $destinations = $destinations->map(function($destination) {
            $destination->translate();
            return $destination;
        });

        $users = User::leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id as user_id', 'users.name as user_name', 'role_user.role_id', 'roles.name as role_name', 'roles.description')
            ->get();
        
        $roles = Role::all();

        $tickets_history = TicketView::getHistory();
        $tickets_history = array_map(function($record) {
            $record->status = __($record->status);
            $record->destination = __($record->destination);
            $record->workstation = __($record->workstation);
            return $record;
        }, $tickets_history);

        return view('administrator.administrator')->with(compact('destinations', 'users', 'roles','tickets_history'));
    }
}
