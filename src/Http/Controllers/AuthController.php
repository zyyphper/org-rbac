<?php

namespace Encore\OrgRbac\Http\Controllers;

use Encore\Admin\Http\Controllers\AuthController as BaseAuthController;
use Encore\OrgRbac\Duty\Duty;
use Illuminate\Http\Request;

class AuthController extends BaseAuthController
{
    /**
     * Handle a login request.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function postLogin(Request $request)
    {
        $this->loginValidator($request->all())->validate();

        $credentials = $request->only([$this->username(), 'password']);
        $remember = $request->get('remember', false);

        if ($this->guard()->attempt($credentials, $remember)) {
            if (config('admin.single_device_login')) {
                $this->guard()->logoutOtherDevices($credentials['password']);
            }
            return $this->sendLoginResponse($request);
        }

        return back()->withInput()->withErrors([
            $this->username() => $this->getFailedLoginMessage(),
        ]);
    }

    protected function sendLoginResponse(Request $request)
    {
        admin_toastr(trans('admin.login_successful'));
        $request->session()->regenerate();
        //init user main duty id
        Duty::load()->init();

        return redirect()->intended($this->redirectPath());
    }

    public function dutySelect(Request $request)
    {
        $dutyId = $request->get('duty_id');
        Duty::load()->setId($dutyId);
        return true;
    }
}
