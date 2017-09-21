<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\FileForm;
use App\Http\Models\FileDAO;
use Session;
use App\Http\Services\LibService;
use Exception;
use App\Http\Services\BaseService;
class FileService extends BaseService {
    private $fileDao;
    private $libService;
    public function __construct() {
        $this->fileDao = new FileDAO();
        $this->libService = new LibService();
    }
    
    public function createFile(FileForm $addForm){
        // upload
        $file = $this->libService->uploadFile($addForm->getDataFile(), 'merchant/'.$addForm->getFolderName(),$addForm->getType());
        $fileDao = new FileDAO();
        $fileDao->type =$addForm->getType();
        $fileDao->name = $addForm->getName();
        $fileDao->file = $file;
        $fileDao->extension = $addForm->getExtension();
        $fileDao->merchantId = $addForm->getMerchantId();
        $fileDao->created_by = $addForm->getCreatedBy();
        $fileDao->status = $addForm->getStatus();;
        $fileDao = $this->fileDao->saveResultId($fileDao);
        return $fileDao;
    }
    
    /*
     * get list rating setup
     */
    public function searchListData(FileForm $searchForm){
        return $this->fileDao->getList($searchForm);
    }
    
    public function countList(FileForm $searchForm){
        $searchForm->setPageSize(null);
        return count($this->fileDao->getList($searchForm));
    }
    
    public function findDataByHashcode($hashcode){
        return $this->fileDao->findByHashcode($hashcode);
    }
    /*
     * delete data
     */
    public function deleteData($hashcode){
        $file = $this->findDataByHashcode($hashcode);
        if($file!=null){
            $this->libService->deleteFile($file->file);
            $file->status = env('COMMON_STATUS_DELETED');
            $this->fileDao->saveResultId($file);
        }
    }
    
    public function updateData(FileForm $updateForm){
        $file = $this->findDataByHashcode($updateForm->getHashcode());
        if($file!=null){
            $file->status = $updateForm->getStatus();
            $file->fileLog = $updateForm->getFileLog();
            $this->fileDao->saveResultId($file);
        }
    }
}

