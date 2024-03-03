<?php

if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Danh sách khen thưởng'
];

layout('header', 'admin', $data);
layout('sidebar', 'admin', $data);
layout('breadcrumb', 'admin', $data);

$groupId = getGroupId();

$permissionData = getPermissionData($groupId);

// Xử lý lọc dữ liệu
$filter = '';
if (isGet()) {
    $body = getBody();

         // Xử lý lọc theo từ khóa
    if(!empty($body['keyword'])) {
        $keyword = $body['keyword'];
        
        if(!empty($filter) && strpos($filter, 'WHERE') >= 0) {
            $operator = 'AND';
        }else {
            $operator = 'WHERE';
        }

        $filter .= " $operator bonus.nameStudent LIKE '%$keyword%'";

    }
       //Xử lý lọc theo class
       if(!empty($body['class_id'])) {
           $classId = $body['class_id'];
   
           if(!empty($filter) && strpos($filter, 'WHERE') >= 0) {
               $operator = 'AND';
           }else {
               $operator = 'WHERE';
           }
   
           $filter .= " $operator bonus.class_id = $classId";
   
       }


    //Lọc theo năm học
    if(!empty($body['year_id'])) {
        $yearId = $body['year_id'];

        if(!empty($filter) && strpos($filter, 'WHERE') >= 0) {
            $operator = 'AND';
        }else {
            $operator = 'WHERE';
        }

        $filter .= " $operator bonus.schoolYear_id = $yearId";

    }
}

/// Xử lý phân trang

$allbonus = getRows("SELECT id FROM bonus $filter");
// 1. Xác định số lượng bản ghi trên 1 trang
$perPage = _PER_PAGE; // Mỗi trang có 3 bản ghi

//2. Tính số trang
$maxPage = ceil($allbonus / $perPage);


// 3. Xử lý số trang dựa vào phương thức GET
if(!empty(getBody()['page'])) {
    $page = getBody()['page'];
    if($page < 1 and $page > $maxPage) {
        $page = 1;
    }
}else {
    $page = 1;
}


$offset = ($page - 1) * $perPage;
// Truy vấn lấy tất cả dữ liệu
$listAllbonus = getRaw("SELECT bonus.id, nameStudent, class.name, year, bonusDay, bonusContent
    FROM bonus INNER JOIN class ON bonus.class_id = class.id INNER JOIN schoolyear ON bonus.schoolYear_id = schoolyear.id
    $filter LIMIT $offset, $perPage");


// Truy vấn lấy ra danh sách nhóm
$allClass = getRaw("SELECT id, class.name FROM class ORDER BY name");
$allSchoolYear = getRaw("SELECT id, year FROM schoolyear");

// Xử lý query string tìm kiếm với phân trang
$queryString = null;
if (!empty($_SERVER['QUERY_STRING'])) {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryString = str_replace('module=bonus','', $queryString);
    $queryString = str_replace('&page='.$page, '', $queryString);
    $queryString = trim($queryString, '&');
    $queryString = '&'.$queryString;
}

$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');

?>

<div class="container">

        <?php
            getMsg($msg, $msgType);
        ?>
       
    <p>
        <?php if(checkPermission($permissionData, 'bonus', 'add')): ?>
        <a href="<?php echo getLinkAdmin('bonus', 'add'); ?>" class="btn btn-success"><i class="fa fa-plus"></i> Thêm khen thưởng</a>
        <?php endif; ?>
    </p>
    <hr/>
    <!-- Tìm kiếm , Lọc dưz liệu -->
    <form action="" method="get">
        <div class="row">
        <div class="col-2">

                <div class="form-group">
                    <select name="class_id" id="" class="form-select">
                        <option value="">Chọn lớp học</option>
                       <?php

                        if(!empty($allClass)) {
                            foreach($allClass as $item) {
                        ?>
                               <option value="<?php echo $item['id'] ?>" <?php  echo (!empty($classId) && $classId == $item['id'])?'selected':false; ?>>Lớp: <?php echo $item['name'] ?></option> 
                        
                        <?php
                            }
                        }
                        ?>
                    </select>
                </div>
           </div>


           <div class="col-2">
                <div class="form-group">
                    <select name="year_id" id="" class="form-select">
                        <option value="">Chọn năm học</option>
                       <?php

                        if(!empty($allSchoolYear)) {
                            foreach($allSchoolYear as $item) {
                        ?>
                               <option value="<?php echo $item['id'] ?>" <?php  echo (!empty($yearId) && $yearId == $item['id'])?'selected':false; ?>><?php echo $item['year'] ?></option> 
                        
                        <?php
                            }
                        }
                        ?>
                    </select>
                </div>
           </div>
           <div class="col-4">
                <input type="search" name="keyword" class="form-control" placeholder="Nhập từ khóa tìm kiếm..." value="<?php echo (!empty($keyword))? $keyword:false; ?>">
           </div>
           <div class="col">
            <button type="submit" class="btn btn-primary ">Tìm kiếm</button>
           </div>
        </div>
        <input type="hidden" name="module" value="bonus">
    </form>


    <table class="table table-bordered" style="margin-top: 30px;">
        <thead>
            <tr>
                <th wìdth="5%">ID</th>
                <th width=15%%>Họ tên</th>
                <th width=8%>Lớp</th>
                <th width=12%>Năm học</th>
                <th width=15%>Ngày khen thưởng</th>
                <th>Nội dung khen thưởng</th>
                <th wìdth="3%">Sửa</th>
                <th wìdth="3%">Xóa</th>
            </tr>
        </thead>
        <tbody>

            <?php
                if(!empty($listAllbonus)):
                    $count = 0; // Hiển thi số thứ tự
                    foreach($listAllbonus as $item):
                        $count ++;

            ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td>
                    <a href=""><?php echo $item['nameStudent']; ?></a>
                </td>
                <td><?php echo $item['name'] ?></td>
                <td><?php echo $item['year'] ?></td>
                <td><?php echo $item['bonusDay'] ?></td>
                <td><?php echo $item['bonusContent'] ?></td>

                <?php if(checkPermission($permissionData, 'bonus', 'edit')): ?>
                <td class="text-center"><a href="<?php echo getLinkAdmin('bonus','edit',['id' => $item['id']]); ?>" class="btn btn-warning btn-sm" ><i class="fa fa-edit"></i> Sửa</a></td>
                <?php endif; ?>
                <?php if(checkPermission($permissionData, 'bonus', 'delete')): ?>
                <td class="text-center"><a href="<?php echo getLinkAdmin('bonus','delete',['id' => $item['id']]); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa không ?')"><i class="fa fa-trash"></i> Xóa</a></td>
                <?php endif; ?>
            <?php endforeach; else: ?>
                <tr>
                    <td colspan="8">
                        <div class="alert alert-danger text-center">Không tìm thấy thông tin khen thưởng</div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <nav aria-label="Page navigation example" class="d-flex justify-content-center">
        <ul class="pagination pagination-sm">
            <?php
                if($page > 1) {
                    $prePage = $page - 1;
                   echo '<li class="page-item"><a class="page-link" href="'._WEB_HOST_ROOT_ADMIN.'/?module=bonus'.$queryString. '&page='.$prePage.'">Pre</a></li>';
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
                <a class="page-link" href="<?php echo _WEB_HOST_ROOT_ADMIN.'?module=bonus'.$queryString.'&page='.$index;  ?>"> <?php echo $index;?> </a>
            </li>
            <?php  } ?>
            
            <?php
                if($page < $maxPage) {
                    $nextPage = $page + 1;
                    echo '<li class="page-item"><a class="page-link" href="'._WEB_HOST_ROOT_ADMIN.'?module=bonus'.$queryString.'&page='.$nextPage.'">Next</a></li>';
                }
            ?>
        </ul>
    </nav> 

</div>

<?php
layout('footer', 'admin');