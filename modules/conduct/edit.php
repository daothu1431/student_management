<?php

if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Cập nhật hạnh kiểm'
];

layout('header', 'admin', $data);
layout('sidebar', 'admin', $data);
layout('breadcrumb', 'admin', $data);



// Truy vấn lấy ra danh sách class
$allClass = getRaw("SELECT id, name as className FROM class ORDER BY id");

// Truy vấn lấy ra danh sách học kỳ
$allSemester = getRaw("SELECT id, name FROM semester ORDER BY name");

// Truy vấn lấy ra danh sách năm học
$allSchoolYear = getRaw("SELECT id, year FROM schoolyear");

$allconduct = getRaw("SELECT conduct.class_id FROM conduct INNER JOIN class ON conduct.class_id = class.id");

// Xử lý hiện dữ liệu cũ của người dùng


$body = getBody();

if(!empty($body['id'])) {
    $conductId = $body['id'];   
    $conductDetail  = firstRaw("SELECT * FROM conduct WHERE id=$conductId");
    if (!empty($conductDetail)) {
        // Tồn tại
        // Gán giá trị conductDetail vào setFalsh
        setFlashData('conductDetail', $conductDetail);
    
    }else {
        redirect('?module=conduct&action=lists');
    }
}


// Xử lý sửa người dùng
if(isPost()) {
    // Validate form
    $body = getBody(); // lấy tất cả dữ liệu trong form
    $errors = [];  // mảng lưu trữ các lỗi
    
    // Valide họ tên: Bắt buộc phải nhập, >=5 ký tự
    if(empty(trim($body['nameStudent']))) {
        $errors['nameStudent']['required'] = '** Bạn chưa nhập họ tên!';
    }else {
        if(strlen(trim($body['nameStudent'])) <= 5) {
        $errors['nameStudent']['min'] = '** Họ tên phải lớn hơn 5 ký tự!';
        }
    }

  
   // Kiểm tra mảng error
  if(empty($errors)) {
    // không có lỗi nào
    $dataUpdate = [
        'nameStudent' => $body['nameStudent'],
        'class_id' => $body['class_id'],
        'semester_id' => $body['semester_id'],
        'schoolYear_id' => $body['schoolYear_id'],
        'level' => $body['level'],
    ];

    $condition = "id=$conductId";
    $updateStatus = update('conduct', $dataUpdate, $condition);
    if ($updateStatus) {
        setFlashData('msg', 'Cập nhật hạnh kiểm thành công');
        setFlashData('msg_type', 'success');
        redirect('admin/?module=conduct&action=lists');
    }else {
    setFlashData('msg', 'Hệ thống đang gặp sự cố, vui lòng thử lại sau');
    setFlashData('msg_type', 'danger');
}

  }else {
    // Có lỗi xảy ra
    setFlashData('msg', 'Vui lòng kiểm tra chính xác thông tin nhập vào');
    setFlashData('msg_type', 'danger');
    setFlashData('errors', $errors);
    setFlashData('old', $body);  // giữ lại các trường dữ liệu hợp lê khi nhập vào
  }

  redirect('?module=conduct&action=edit&id='.$conductId);

}
$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
// $conductDetail = getFlashData('conductDetail');

if (!empty($conductDetail) && empty($old)) {
    $old = $conductDetail;
}


?>
    <div class="container">
        <hr/>
        <?php
            getMsg($msg, $msgType);
        ?>

        <form action="" method="post">
        <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label for="">Họ tên</label>
                        <input type="text" name="nameStudent" id="" class="form-control" value="<?php echo old('nameStudent', $old); ?>">
                        <?php echo form_error('nameStudent', $errors, '<span class="error">', '</span>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="">Lớp học</label>
                        <select name="class_id" id="" class="form-select">
                            <option value="">Chọn lớp học</option>
                            <?php

                                if(!empty($allClass)) {
                                    foreach($allClass as $item) {
                                ?>
                                    <option value="<?php echo $item['id'] ?>" <?php  echo (old('class_id', $old) == $item['id'])?'selected':false; ?>>Lớp: <?php echo $item['className'] ?></option> 
                                
                                <?php
                                    }
                                }
                                ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="">Học kỳ</label>
                        <select name="semester_id" id="" class="form-select">
                            <option value="">Chọn học kỳ</option>
                            <?php

                                if(!empty($allSemester)) {
                                    foreach($allSemester as $item) {
                                ?>
                                     <option value="<?php echo $item['id'] ?>" <?php  echo (old('semester_id', $old) == $item['id'])?'selected':false; ?>><?php echo $item['name'] ?></option> 
                                
                                <?php
                                    }
                                }
                                ?>
                        </select>
                    </div>
                </div>
                <div class="col-4">
                
                    <div class="form-group">
                        <label for="">Năm học</label>
                        <select name="schoolYear_id" id="" class="form-select">
                            <option value="">Chọn năm học</option>
                            <?php

                                if(!empty($allSchoolYear)) {
                                    foreach($allSchoolYear as $item) {
                                ?>
                                     <option value="<?php echo $item['id'] ?>" <?php  echo (old('schoolYear_id', $old) == $item['id'])?'selected':false; ?>><?php echo $item['year'] ?></option> 
                                
                                <?php
                                    }
                                }
                                ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Hạnh kiểm</label>
                        <input type="text" name="level" id="" class="form-control" value="<?php echo old('level', $old); ?>">
                    </div>
                    
                   
                </div>              
            </div>
            <div class="col">
                <div class="btn-row">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a style="margin-left: 10px " href="?module=conduct&action=lists" class="btn btn-success"><i class="fa fa-forward"></i></a>
                    <input type="hidden" name="id" value="<?php echo $conductId; ?>">
                </div>
            </div>
        </form>
    </div>


<?php
layout('footer', 'admin');




