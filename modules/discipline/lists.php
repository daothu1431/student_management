<?php

if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Danh sách kỷ luật'
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

        $filter .= " $operator nameStudent LIKE '%$keyword%'";

    }
       //Xử lý lọc theo class
       if(!empty($body['class_id'])) {
           $classId = $body['class_id'];
   
           if(!empty($filter) && strpos($filter, 'WHERE') >= 0) {
               $operator = 'AND';
           }else {
               $operator = 'WHERE';
           }
   
           $filter .= " $operator discipline.class_id = $classId";
   
       }


    //Lọc theo năm học
    if(!empty($body['year_id'])) {
        $yearId = $body['year_id'];

        if(!empty($filter) && strpos($filter, 'WHERE') >= 0) {
            $operator = 'AND';
        }else {
            $operator = 'WHERE';
        }

        $filter .= " $operator discipline.schoolYear_id = $yearId";

    }
}

/// Xử lý phân trang

$alldiscipline = getRows("SELECT id FROM discipline $filter");
// 1. Xác định số lượng bản ghi trên 1 trang
$perPage = _PER_PAGE; // Mỗi trang có 3 bản ghi

//2. Tính số trang
$maxPage = ceil($alldiscipline / $perPage);


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
$listAlldiscipline = getRaw("SELECT discipline.id, nameStudent, class.name, year, day, reason, rank FROM discipline INNER JOIN class ON discipline.class_id = class.id
INNER JOIN schoolyear ON discipline.schoolYear_id = schoolyear.id
    $filter LIMIT $offset, $perPage");


// Truy vấn lấy ra danh sách nhóm
$allClass = getRaw("SELECT id, class.name FROM class ORDER BY name");
$allSchoolYear = getRaw("SELECT id, year FROM schoolyear");

// Xử lý query string tìm kiếm với phân trang
$queryString = null;
if (!empty($_SERVER['QUERY_STRING'])) {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryString = str_replace('module=discipline','', $queryString);
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
        <?php if(checkPermission($permissionData, 'discipline', 'add')): ?>
        <a href="<?php echo getLinkAdmin('discipline', 'add'); ?>" class="btn btn-success"><i class="fa fa-plus"></i> Thêm kỷ luật</a>
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
        <input type="hidden" name="module" value="discipline">
    </form>


    <table class="table table-bordered" style="margin-top: 30px;">
        <thead>
            <tr>
                <th wìdth="1%">ID</th>
                <th width=15%>Họ tên</th>
                <th width=5%>Lớp</th>
                <th width=10%>Năm học</th>
                <th width=15%>Ngày kỷ luật</th>
                <th width=25%>Lý do</th>
                <th width=10%>Mức kỷ luật</th>
                <th wìdth="1%">Sửa</th>
                <th wìdth=1%>Xóa</th>
            </tr>
        </thead>
        <tbody>

            <?php
                if(!empty($listAlldiscipline)):
                    $count = 0; // Hiển thi số thứ tự
                    foreach($listAlldiscipline as $item):
                        $count ++;

            ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td>
                    <a href=""><?php echo $item['nameStudent']; ?></a>
                </td>
                <td><?php echo $item['name'] ?></td>
                <td><?php echo $item['year'] ?></td>
                <td><?php echo $item['day'] ?></td>
                <td><a href=""><?php echo $item['reason'] ?></a></td>
                <td><a href="#" class="btn btn-info btn-sm"><?php echo $item['rank'] ?></a></td>

                <?php if(checkPermission($permissionData, 'discipline', 'edit')): ?>
                <td class="text-center"><a href="<?php echo getLinkAdmin('discipline','edit',['id' => $item['id']]); ?>" class="btn btn-warning btn-sm" ><i class="fa fa-edit"></i> Sửa</a></td>
                <?php endif; ?>
                <?php if(checkPermission($permissionData, 'discipline', 'delete')): ?>
                <td class="text-center"><a href="<?php echo getLinkAdmin('discipline','delete',['id' => $item['id']]); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa không ?')"><i class="fa fa-trash"></i> Xóa</a></td>
                <?php endif; ?>
            <?php endforeach; else: ?>
                <tr>
                    <td colspan="9">
                        <div class="alert alert-danger text-center">Không tìm thấy thông tin kỷ luật</div>
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
                   echo '<li class="page-item"><a class="page-link" href="'._WEB_HOST_ROOT_ADMIN.'/?module=discipline'.$queryString. '&page='.$prePage.'">Pre</a></li>';
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
                <a class="page-link" href="<?php echo _WEB_HOST_ROOT_ADMIN.'?module=discipline'.$queryString.'&page='.$index;  ?>"> <?php echo $index;?> </a>
            </li>
            <?php  } ?>
            
            <?php
                if($page < $maxPage) {
                    $nextPage = $page + 1;
                    echo '<li class="page-item"><a class="page-link" href="'._WEB_HOST_ROOT_ADMIN.'?module=discipline'.$queryString.'&page='.$nextPage.'">Next</a></li>';
                }
            ?>
        </ul>
    </nav> 

</div>

<?php
layout('footer', 'admin');