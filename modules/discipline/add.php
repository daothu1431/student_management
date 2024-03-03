<?php

if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Thêm kỷ luật'
];

layout('header', 'admin', $data);
layout('sidebar', 'admin', $data);
layout('breadcrumb', 'admin', $data);

// Truy vấn lấy ra danh sách nhóm
$allClass = getRaw("SELECT id, name FROM class ORDER BY name");

// Truy vấn lấy ra danh sách học kỳ
$allSemester = getRaw("SELECT id, name FROM semester ORDER BY name");

// Truy vấn lấy ra danh sách năm học
$allSchoolYear = getRaw("SELECT id, year FROM schoolyear");

// Xử lý thêm người dùng
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


   // Validate chọn nhóm
   if(empty(trim($body['class_id']))) {
    $errors['class_id']['required'] = '** Vui lòng chọn lớp học !';
   }

   
   // Kiểm tra mảng error
  if(empty($errors)) {
    // không có lỗi nào
    $dataInsert = [
        'nameStudent' => $body['nameStudent'],
        'class_id' => $body['class_id'],
        'schoolYear_id' => $body['schoolYear_id'],
        'day' => date('Y-m-d'),
        'reason' => $body['reason'],       
        'rank' => $body['rank'],       
    ];

    $insertStatus = insert('discipline', $dataInsert);
    if ($insertStatus) {
        setFlashData('msg', 'Thêm quyết định kỷ luật học sinh thành công');
        setFlashData('msg_type', 'success');
        redirect('?module=discipline&action=lists');
    }else {
    setFlashData('msg', 'Hệ thống đang gặp sự cố, vui lòng thử lại sau');
    setFlashData('msg_type', 'danger');
    redirect('?module=discipline&action=add'); 
    }

  }else {
    // Có lỗi xảy ra
    setFlashData('msg', 'Vui lòng kiểm tra chính xác thông tin nhập vào');
    setFlashData('msg_type', 'danger');
    setFlashData('errors', $errors);
    setFlashData('old', $body);  // giữ lại các trường dữ liệu hợp lê khi nhập vào
    redirect('?module=discipline&action=add'); 
  }

}
$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
   

?>
    <div class="container">
        <hr/>
        <?php
            getMsg($msg, $msgType);
        ?>

        <form action="" method="post">
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
                                    <option value="<?php echo $item['id'] ?>" <?php  echo (!empty($classId) && $classId == $item['id'])?'selected':false; ?>>Lớp: <?php echo $item['name'] ?></option> 
                                
                                <?php
                                    }
                                }
                                ?>
                        </select>
                        <?php echo form_error('group_id', $errors, '<span class="error">', '</span>'); ?>
                    </div>


                    <div class="form-group">
                        <label for="">Năm học</label>
                        <select name="schoolYear_id" id="" class="form-select">
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
                        <?php echo form_error('group_id', $errors, '<span class="error">', '</span>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="">Lý do kỷ luật</label>
                        <textarea name="reason" class="form-control" ></textarea>
                    </div>

                    <div class="form-group">
                        <label for="">Mức kỷ luật</label>
                        <input type="text" name="rank" class="form-control">
                    </div>
                    
                </div>             

            <div class="col">
                <div class="btn-row">
                    <button type="submit" class="btn btn-primary">Thêm mới</button>
                    <a style="margin-left: 20px " href="<?php echo getLinkAdmin('discipline', 'lists') ?>" class="btn btn-success"><i class="fa fa-forward"></i></a>
                </div>
            </div>
        </form>
    </div>


<?php
layout('footer', 'admin');





