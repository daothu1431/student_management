<?php

if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Danh sách điểm'
];

layout('header', 'admin', $data);
layout('sidebar', 'admin', $data);
layout('breadcrumb', 'admin', $data);

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

        $filter .= " $operator score.fullname LIKE '%$keyword%'";

    }
       //Xử lý lọc theo class
       if(!empty($body['class_id'])) {
           $classId = $body['class_id'];
   
           if(!empty($filter) && strpos($filter, 'WHERE') >= 0) {
               $operator = 'AND';
           }else {
               $operator = 'WHERE';
           }
   
           $filter .= " $operator score.class_id = $classId";
   
       }

    // Lọc theo kỳ học
    if(!empty($body['semester_id'])) {
        $semesterId = $body['semester_id'];

        if(!empty($filter) && strpos($filter, 'WHERE') >= 0) {
            $operator = 'AND';
        }else {
            $operator = 'WHERE';
        }

        $filter .= " $operator score.semester_id = $semesterId";

    }

    //Lọc theo năm học
    if(!empty($body['year_id'])) {
        $yearId = $body['year_id'];

        if(!empty($filter) && strpos($filter, 'WHERE') >= 0) {
            $operator = 'AND';
        }else {
            $operator = 'WHERE';
        }

        $filter .= " $operator score.schoolYear_id = $yearId";

    }
}

/// Xử lý phân trang

$allscore = getRows("SELECT id FROM score $filter");
// 1. Xác định số lượng bản ghi trên 1 trang
$perPage = _PER_PAGE; // Mỗi trang có 3 bản ghi

//2. Tính số trang
$maxPage = ceil($allscore / $perPage);


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
$listAllscore = getRaw("SELECT score.id, score.fullname, class.name, semester.name as se_name, schoolyear.year
    FROM score 
    INNER JOIN class ON score.class_id = class.id
    INNER JOIN semester ON score.semester_id = semester.id
    INNER JOIN schoolyear ON score.schoolYear_id = schoolyear.id 
    $filter ORDER BY score.class_id LIMIT $offset, $perPage");

$allStudent = getRaw("SELECT * FROM student");

// Truy vấn lấy ra danh sách nhóm
$allClass = getRaw("SELECT id, class.name FROM class ORDER BY name");
$allSemester = getRaw("SELECT id, name FROM semester ORDER BY name");
$allSchoolYear = getRaw("SELECT id, year FROM schoolyear");

// Xử lý query string tìm kiếm với phân trang
$queryString = null;
if (!empty($_SERVER['QUERY_STRING'])) {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryString = str_replace('module=score','', $queryString);
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
        <a href="<?php echo getLinkAdmin('score', 'add'); ?>" class="btn btn-success"><i class="fa fa-plus"></i> Nhập điểm</a>
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
                    <select name="semester_id" id="" class="form-select">
                        <option value="">Chọn học kỳ</option>
                       <?php

                        if(!empty($allSemester)) {
                            foreach($allSemester as $item) {
                        ?>
                               <option value="<?php echo $item['id'] ?>" <?php  echo (!empty($semesterId) && $semesterId == $item['id'])?'selected':false; ?>><?php echo $item['name'] ?></option> 
                        
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
        <input type="hidden" name="module" value="score">
    </form>


    <table class="table table-bordered" style="margin-top: 30px;">
        <thead>
            <tr>
                <th wìdth="5%">ID</th>
                <th>Họ tên</th>
                <th>Lớp</th>
                <th>Học kỳ</th>
                <th>Năm học</th>
                <th>Điểm</th>
                <th wìdth="3%">Sửa</th>
                <th wìdth="3%">Xóa</th>
            </tr>
        </thead>
        <tbody>

            <?php
                if(!empty($listAllscore)):
                    $count = 0; // Hiển thi số thứ tự
                    foreach($listAllscore as $item):
                        $count ++;

            ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td>
                    <a href=""><?php echo $item['fullname']; ?></a>
                </td>
                <td><?php echo $item['name'] ?></td>
                <td><?php echo $item['se_name'] ?></td>
                <td><?php echo $item['year'] ?></td>
                <td class="text-center"><a href="<?php echo getLinkAdmin('score','edit',['id' => $item['id']]); ?>" class="btn btn-primary btn-sm"><i class="nav-icon fas fa-solid fa-eye"></i> Xem</a></td>
                <td class="text-center"><a href="<?php echo getLinkAdmin('score','edit',['id' => $item['id']]); ?>" class="btn btn-warning btn-sm" ><i class="fa fa-edit"></i> Sửa</a></td>
                <td class="text-center"><a href="<?php echo getLinkAdmin('score','delete',['id' => $item['id']]); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa không ?')"><i class="fa fa-trash"></i> Xóa</a></td>
            
            <?php endforeach; else: ?>
                <tr>
                    <td colspan="8">
                        <div class="alert alert-danger text-center">Không tìm thấy thông tin học sinh</div>
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
                   echo '<li class="page-item"><a class="page-link" href="'._WEB_HOST_ROOT_ADMIN.'/?module=score'.$queryString. '&page='.$prePage.'">Pre</a></li>';
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
                <a class="page-link" href="<?php echo _WEB_HOST_ROOT_ADMIN.'?module=score'.$queryString.'&page='.$index;  ?>"> <?php echo $index;?> </a>
            </li>
            <?php  } ?>
            
            <?php
                if($page < $maxPage) {
                    $nextPage = $page + 1;
                    echo '<li class="page-item"><a class="page-link" href="'._WEB_HOST_ROOT_ADMIN.'?module=score'.$queryString.'&page='.$nextPage.'">Next</a></li>';
                }
            ?>
        </ul>
    </nav> 

</div>

<?php
layout('footer', 'admin');