<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Locked extends Member_Controller
{
    public function index()
    {
        if ($this->data['user']['locked_until'] < time()) {
            return redirect(site_url('dashboard'));
        }
        $this->data['page'] = 'Locked';
        $this->render('locked', $this->data);
    }
}
