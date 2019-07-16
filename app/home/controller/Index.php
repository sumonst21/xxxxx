<?php
namespace app\home\controller;
use app\home\controller\Base;
class Index extends Base
{
    public function index()
    {
        return view('index');
    }
}
