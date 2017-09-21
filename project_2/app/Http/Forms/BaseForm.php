<?php
namespace App\Http\Forms;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class BaseForm{
    private $id;
    private $createdDate;
    private $updatedDate;
    private $hashcode;
    private $pageSize;
    private $shopId;
    private $status;
    private $createdAt;
    private $updatedAt;
    private $merchantId;
    private $createdBy;
    private $updatedBy;
    private $partnerId;
    private $uDetailId;
    private $merchantHashcode;
    private $partnerHashcode;
    private $pageIndex = null;
    private $listId;
    private $keySearch;
    private $dateNow;
            
    function getId() {
        return $this->id;
    }

    function getCreatedDate() {
        return $this->createdDate;
    }

    function getUpdatedDate() {
        return $this->updatedDate;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCreatedDate($createdDate) {
        $this->createdDate = $createdDate;
    }

    function setUpdatedDate($updatedDate) {
        $this->updatedDate = $updatedDate;
    }
    
    function getHashcode() {
        return $this->hashcode;
    }

    function setHashcode($hashcode) {
        $this->hashcode = $hashcode;
    }
    
    function getPageSize() {
        return $this->pageSize;
    }

    function setPageSize($pageSize) {
        $this->pageSize = $pageSize;
    }
    
    function getCreatedAt() {
        return $this->createdAt;
    }

    function getUpdatedAt() {
        return $this->updatedAt;
    }

    function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }
    
    function getShopId() {
        return $this->shopId;
    }

    function setShopId($shopId) {
        $this->shopId = $shopId;
    }

    function getStatus() {
        return $this->status;
    }

    function getMerchantId() {
        return $this->merchantId;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setMerchantId($merchantId) {
        $this->merchantId = $merchantId;
    }

    function getCreatedBy() {
        return $this->createdBy;
    }

    function getUpdatedBy() {
        return $this->updatedBy;
    }

    function setCreatedBy($createdBy) {
        $this->createdBy = $createdBy;
    }

    function setUpdatedBy($updatedBy) {
        $this->updatedBy = $updatedBy;
    }
    
    function getPartnerId() {
        return $this->partnerId;
    }

    function setPartnerId($partnerId) {
        $this->partnerId = $partnerId;
    }

    function getUDetailId() {
        return $this->uDetailId;
    }

    function setUDetailId($uDetailId) {
        $this->uDetailId = $uDetailId;
    }
    
    function getMerchantHashcode() {
        return $this->merchantHashcode;
    }

    function getPartnerHashcode() {
        return $this->partnerHashcode;
    }

    function setMerchantHashcode($merchantHashcode) {
        $this->merchantHashcode = $merchantHashcode;
    }

    function setPartnerHashcode($partnerHashcode) {
        $this->partnerHashcode = $partnerHashcode;
    }

    function getPageIndex() {
        return $this->pageIndex;
    }

    function setPageIndex($pageIndex) {
        $this->pageIndex = $pageIndex;
    }

    function getListId() {
        return $this->listId;
    }

    function setListId($listId) {
        $this->listId = $listId;
    }

    function getKeySearch() {
        return $this->keySearch;
    }

    function setKeySearch($keySearch) {
        $this->keySearch = $keySearch;
    }

    function getDateNow() {
        return $this->dateNow;
    }

    function setDateNow($dateNow) {
        $this->dateNow = $dateNow;
    }


    
}
