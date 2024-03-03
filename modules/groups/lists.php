<?php
$data = [
    'pageTitle' => 'Danh sách nhóm'
];

layout('header', 'admin', $data);
layout('sidebar', 'admin', $data);
layout('breadcrumb', 'admin', $data);

// Xử lý lọc dữ liệu theo tên nhóm người dùng
$filter = '';

if(isGet()) {
    $body = getBody();

    if(!empty($body['keyword'])) {
        $keyword = $body['keyword'];
        
        if(!empty($filter) && strpos($filter, 'WHERE') >= 0) {
            $operator = 'AND';
        }else {
            $operator = 'WHERE';
        }

        $filter .= " $operator name LIKE '%$keyword%'";

    }

}

/// Xử lý phân trang

$allUser = getRows("SELECT id FROM groups $filter");
// 1. Xác định số lượng bản ghi trên 1 trang
$perPage = _PER_PAGE; // Mỗi trang có 3 bản ghi

//2. Tính số trang
$maxPage = ceil($allUser / $perPage); // Làm tròn lên


// 3. Xử lý số trang dựa vào phương thức GET
if(!empty(getBody()['page'])) {
    $page = getBody()['page'];
    if($page < 1 || $page > $maxPage) {
        $page = 1;
    }
}else {
    $page = 1;
}

$offset = ($page - 1) * $perPage;

// Lấy dứ liệu nhóm người dùng
$listGroups = getRaw("SELECT * FROM groups $filter ORDER BY create_at DESC LIMIT $offset, $perPage");

// Xử lý query string tìm kiếm với phân trang
$queryString = null;
if (!empty($_SERVER['QUERY_STRING'])) {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryString = str_replace('module=groups','', $queryString);
    $queryString = str_replace('&page='.$page, '', $queryString);
    $queryString = trim($queryString, '&');
    $queryString = '&'.$queryString;
}


$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');
/////////////////////////////////////////////////////////////////////
?>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
                 
            <a href="<?php echo getLinkAdmin('groups','add') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Thêm nhóm</a>
            <hr/>
            <form action="" method="get">
                <div class="row">
                    <div class="col-9">
                        <input type="search" name="keyword" id="" class="form-control" placeholder="Nhập tên nhóm cần tìm..." value="<?php echo (!empty($keyword))?$keyword:false; ?>">
                    </div>
                    <div class="col-3">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                    </div>
                </div>
                <input type="hidden" name="module" value="groups">
            </form>
            <hr />
            <?php
                    getMsg($msg, $msgType);
                ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width=5%>STT</th>
                        <th>Tên nhóm</th>
                        <th>Thời gian</th>
                        <th width=15%>Phân quyền</th>
                        <th width=10%>Sửa</th>
                        <th width=10%>Xóa</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                        if (!empty($listGroups)) :
                            $count = 0;
                            foreach ($listGroups as $item):
                                $count++;
                    ?>

                    <tr>
                        <td><?php echo $count; ?></td>
                        <td><a href="<?php echo getLinkAdmin('groups','edit',['id' => $item['id']]) ?>"><?php echo $item['name']; ?></a></td>
                        <td><?php echo getDateFormat($item['create_at'],'d/m/Y'); ?></td>
                        <td class="text-center"><a href="<?php echo getLinkAdmin('groups', 'permission', ['id' => $item['id']]) ?>" class="btn btn-info btn-sm" >Phân quyền</a></td>
                        <td class="text-center"><a href="<?php echo getLinkAdmin('groups','edit',['id' => $item['id']]); ?>" class="btn btn-warning btn-sm" ><i class="fa fa-edit"></i> Sửa</a></td>
                        <td class="text-center"><a href="<?php echo getLinkAdmin('groups','delete',['id' => $item['id']]); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa không ?')"><i class="fa fa-trash"></i> Xóa</a></td>
                    </tr>
                    
                    <?php endforeach; else: ?>
                            <tr>
                                <td colspan="6">
                                    <div class="alert alert-danger text-center">Không có nhóm người dùng</div>
                                </td>
                            </tr>
                    <?php endif; ?>
                </tbody>
            </table>


                <!-- Xử lý số trang -->
            <nav aria-label="Page navigation example" class="d-flex justify-content-center">
                <ul class="pagination pagination-sm">
                    <?php
                        if($page > 1) {
                            $prePage = $page - 1;
                            echo '<li class="page-item"><a class="page-link" href="'._WEB_HOST_ROOT_ADMIN.'/?module=groups'.$queryString. '&page='.$prePage.'">Pre</a></li>';
                        }
                    ?>


                    <?php 
                        // Giới hạn số trang
                        $begin = $page - 2;
                        $end = $page + 2;
                        if($begin < 1) {
                            $begin = 1;
                        }
                        if($end > $maxPage) {
                            $end = $maxPage;
                        }
                        for($index = $begin; $index <= $end; $index++){  ?>
                    <li class="page-item <?php echo ($index == $page) ? 'active' : false; ?> ">
                        <a class="page-link" href="<?php echo _WEB_HOST_ROOT_ADMIN.'/?module=groups'.$queryString. '&page='.$index;  ?>"> <?php echo $index;?> </a>
                    </li>
                    <?php  } ?>
                    
                    <?php
                        if($page < $maxPage) {
                            $nextPage = $page + 1;
                            echo '<li class="page-item"><a class="page-link" href="'._WEB_HOST_ROOT_ADMIN.'/?module=groups'.$queryString.'&page='.$nextPage.'">Next</a></li>';
                        }
                    ?>
                </ul>
            </nav>
            <!-- ////////////////////////////////////////////////// -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

<?php
layout('footer', 'admin');