<?php
namespace VIEW;
use SERVICE\DisplayService;
use SERVICE\ViewService;
use MYB\MODELS\userModels;
class IndexView extends ViewService
{

    public function __construct ($route_info)
    {

        parent::__construct($route_info);
    }

    public function Index()
    {
        $user_pdo = new userModels();
        $user_pdo->index();
        //$this->display();
        $display =  new DisplayService();
        $display->display();
        /*$lexical = new LexicalService();
        $lexical->template('asd
        {foreach $this->a $val}
        cc
        {endforeach}{for $i = 1 $i < 4  $i ++}
        asdsad
        {endfor}
        {switch ss}
        {case 1}
        {break}
        {endswitch}
        {if $k mod 2 <> 0 and  $this->k eq s}{else}ss{endif}  {$a=1}{$a = 1}{$a ++}{$this->ss}  {break}  {include sss.php}');*/


    }
    public function user()
    {
        print_r($this->lang);
    }
    public function admin()
    {
        echo "admin";
    }
}