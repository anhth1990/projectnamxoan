<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\NotiEmailForm;
use App\Http\Models\NotiEmailDAO;
use Session;
class NotiEmailService extends BaseService {
    private $notiEmailDao;
    public function __construct() {
        $this->notiEmailDao = new NotiEmailDAO();
    }
    
    /*
     * insert noti email
     */
    public function insertNotiEmail(NotiEmailForm $insertForm){
        $objDao = new NotiEmailDAO();
        $objDao->type = $insertForm->getType();
        $objDao->content = $insertForm->getContent();
        $objDao->status = $insertForm->getStatus();
        $objDao->merchantId = $insertForm->getMerchantId();
        return $this->notiEmailDao->saveResultId($objDao);
    }
    
    /*
     * send mail
     */
    public function sendMail($hashcode){
        
        $emailObj = $this->notiEmailDao->findByHashcode($hashcode, null);
        if($emailObj!=null){
            
            $content = json_decode($emailObj->content);
            
            $language = $content->lang;
            $templateName = $content->templateName;
            $title = $content->title;
            $sendTo = $content->sendTo;
            
            $username = "";
            if(isset($content->username)){
                $username=$content->username;
            }
            $password = "";
            if(isset($content->password)){
                $password = $content->password;
            }
            $link = "";
            if(isset($content->linkActive)){
                $link = $content->linkActive;
            }
            $activeCode = "";
            if(isset($content->activeCode)){
                $activeCode = $content->activeCode;
            }
            
            
            
            $cont = view('Email.'.$language.'.'.$templateName,  compact('link','username','password','activeCode'))->render();
            
            if($this->send($sendTo, $title, $cont)){
                $emailObj->status = env('COMMON_STATUS_SUCCESS');
            }else{
                $emailObj->status = env('COMMON_STATUS_FAIL');
            }
            $this->notiEmailDao->saveResultId($emailObj);
        }
    }
    
    public function send($sendTo,$title,$content){
        //require 'Mail/mail.php';
        include base_path().'/app/Http/Services/Mail/mail.php';
        return guiMail($sendTo,$title,$content);
    }
    
}

