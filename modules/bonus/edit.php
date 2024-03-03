<?php

if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Cập nhật khen thưởng'
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

$allbonus = getRaw("SELECT bonus.class_id FROM bonus INNER JOIN class ON bonus.class_id = class.id");

// Xử lý hiện dữ liệu cũ của người dùng


$body = getBody();

if(!empty($body['id'])) {
    $bonusId = $body['id'];   
    $bonusDetail  = firstRaw("SELECT * FROM bonus WHERE id=$bonusId");
    if (!empty($bonusDetail)) {
        // Tồn tại
        // Gán giá trị bonusDetail vào setFalsh
        setFlashData('bonusDetail', $bonusDetail);
    
    }else {
        redirect('?module=bonus&action=lists');
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
        'schoolYear_id' => $body['schoolYear_id'],
        'bonusContent' => $body['bonusContent'],
    ];

    $condition = "id=$bonusId";
    $updateStatus = update('bonus', $dataUpdate, $condition);
    if ($updateStatus) {
        setFlashData('msg', 'Cập nhật khen thưởng thành công');
        setFlashData('msg_type', 'success');
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

  redirect('?module=bonus&action=edit&id='.$bonusId);

}
$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
// $bonusDetail = getFlashData('bonusDetail');

if (!empty($bonusDetail) && empty($old)) {
    $old = $bonusDetail;
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
                        <label for="">Nội dung khen thưởng</label>
                        <textarea name="bonusContent" class="form-control" ><?php echo old('bonusContent', $old); ?></textarea>
                    </div>
                    
                   
                </div>              
            </div>
            <div class="col">
                <div class="btn-row">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a style="margin-left: 10px " href="?module=bonus&action=lists" class="btn btn-success"><i class="fa fa-forward"></i></a>
                    <input type="hidden" name="id" value="<?php echo $bonusId; ?>">
                </div>
            </div>
        </form>
    </div>


<?php
layout('footer', 'admin');





