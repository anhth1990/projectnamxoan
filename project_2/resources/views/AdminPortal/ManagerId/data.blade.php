<div class="box-header">
            <h3 class="box-title">Danh sách</h3>
            <a href="javascript::(0)" class="btn btn-sm btn-danger pull-right" style="margin-left: 10px" onclick="deleteInfo()" ><i class="fa fa-trash"></i> Xóa thông tin</a> 
            <a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/manager-id/upload-file')}}" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Tải dữ liệu</a>
            
        </div><!-- /.box-header -->
        <div class="box-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Thông tin</th>
                        <th>Trạng thái</th>
                        <th >&nbsp;</th>
                    </tr>
                </thead>
                <tbody class="list-data">
                    @if(isset($listObj) && count($listObj)>0)
                    @foreach($listObj as $key=>$obj)
                    <tr >
                        <td>{{($page-1)*env('PAGE_SIZE')+intval($key)+1}}</td>
                        <td style="width:100px">{{$obj->identity}}</td>
                        <td style="width: 150px">{{$obj->name}} </td>
                        <td>
                            <table class="table table-bordered">
                                @foreach($obj->identityDetail as $objDetail)
                                <tr>
                                    <td>
                                        <i class="fa fa-clock-o"></i>&nbsp;&nbsp; <?php echo date_format(date_create($objDetail->time),"d/m/Y H:i:s"); ?> <br>
                                        <i class="fa fa-link"></i>&nbsp;&nbsp; {{$objDetail->url}} <br>
                                        <i class="fa fa-barcode"></i>&nbsp;&nbsp; {{$objDetail->code}} <br>
                                    </td>
                                    <td style="width:100px">
                                        @if($objDetail->type == 0)
                                            <span class="badge bg-blue">An toàn</span>
                                          @elseif($objDetail->type == 1)
                                            <span class="badge bg-red">Nguy hiểm</span>
                                          @endif

                                    </td>
                                    <td style="width: 50px">
                                        <a  style="cursor: pointer" class="fa fa-trash" onclick="deleteAction($(this))" data-id="{{$objDetail->hashcode}}">Xóa</a>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </td>
                        <td style="width:100px"><?php //echo date_format($obj->updated_at,"d/m/Y H:i:s"); ?>

                            @if(time()-strtotime($obj->last_login)< env('TIME_OFFLINE'))
                                <span class="badge bg-green">Online</span>
                            @else
                                <span class="badge bg-info">Offline</span>
                            @endif
                        </td>
                        <td style="width: 200px">
                            <a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/manager-id/edit/'.$obj->hashcode)}}" class="btn btn-xs btn-primary" style="cursor: pointer"><i class='fa fa-edit'></i>Chỉnh sửa</a>
                            @if($obj->ip!=null)
                            <a href="{{Asset('/'.env('PREFIX_ADMIN_PORTAL').'/manager-id/log/'.$obj->hashcode)}}" class="btn btn-xs btn-primary" style="cursor: pointer"><i class='fa fa-link'></i>{{$obj->ip}}</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
                
            </table>
        </div><!-- /.box-body -->
        <div class="box-footer clearfix">
            @if(isset($listObj) && count($listObj)>0 && $searchForm->getOrderIDOnline()==null)
                        @include('pagination.default', ['paginator' => $listObj])
                      @endif
        </div>